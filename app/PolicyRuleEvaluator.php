<?php

namespace App;

use App\Models\PolicyPackage;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PolicyRuleEvaluator
{
    private const DAILY_CONSUMPTION = 10.0;

    /**
     * @return array{cases: list<array<string, mixed>>, run: array<string, mixed>}
     */
    public function evaluate(?CarbonInterface $asOf = null): array
    {
        $asOf ??= Carbon::parse('2026-10-27');
        $observations = $this->latestObservations($asOf);
        $rules = $this->rules();
        $cases = [];

        foreach ($rules as $rule) {
            foreach ($observations as $row) {
                if (! $this->matchesTarget($rule, $row)) {
                    continue;
                }

                $checks = [];
                foreach ($rule['conditions'] as $condition) {
                    $checks[] = $this->evaluateCondition($condition, $row, $observations, $asOf);
                }

                $passed = $rule['logic'] === 'or'
                    ? collect($checks)->contains(fn (array $check) => $check['passed'])
                    : collect($checks)->every(fn (array $check) => $check['passed']);

                if (! $passed) {
                    continue;
                }

                $primary = collect($checks)->firstWhere('passed', true) ?? $checks[0];
                $cases[] = [
                    'id' => count($cases) + 1,
                    'package_id' => $rule['id'],
                    'package_name' => $rule['name'],
                    'commodity' => $row->commodity_name,
                    'point' => $row->point_name,
                    'observed_value' => $primary['observed'],
                    'threshold' => $primary['threshold'],
                    'status' => 'pending',
                    'recommendation' => $rule['recommendation'],
                    'date' => Carbon::parse($row->observed_at)->translatedFormat('d M Y H:i'),
                ];
            }
        }

        $unique = collect($cases)
            ->unique(fn (array $case) => $case['package_name'].'|'.$case['commodity'].'|'.$case['point'])
            ->values()
            ->all();

        return [
            'cases' => $unique,
            'run' => [
                'id' => (int) $asOf->format('YmdHi'),
                'evaluated_at' => $asOf->translatedFormat('d M Y H:i'),
                'engine' => 'Pantau Rule Engine v1',
                'mode' => 'live',
                'status' => 'completed',
                'matched_count' => count($unique),
                'note' => 'Evaluasi kondisi kebijakan terhadap observasi harga dan stok terbaru',
            ],
        ];
    }

    /**
     * @return Collection<int, object>
     */
    public function latestObservations(CarbonInterface $asOf): Collection
    {
        return DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->leftJoin('regions as reg', 'reg.id', '=', 'mp.region_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->whereDate('rs.observed_at', '<=', $asOf->toDateString())
            ->select(
                'ri.commodity_id',
                'c.name as commodity_name',
                'c.reference_price',
                'mp.id as point_id',
                'mp.name as point_name',
                'mp.region_id',
                'reg.name as region_name',
                'ri.price',
                'ri.stock',
                'ri.price_change_pct',
                'rs.observed_at'
            )
            ->orderByDesc('rs.observed_at')
            ->orderByDesc('ri.id')
            ->get()
            ->unique(fn (object $row) => $row->point_id.'-'.$row->commodity_id)
            ->values();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function rules(): array
    {
        $packages = PolicyPackage::with(['versions.ruleGroups.conditions'])->orderBy('id')->get();
        $rules = [];

        foreach ($packages as $package) {
            $version = $package->versions->sortByDesc('version_no')->first();
            if (! $version) {
                continue;
            }

            $groups = $version->ruleGroups->sortBy('sort_order');
            $conditions = $groups
                ->flatMap(fn ($group) => $group->conditions)
                ->map(function ($condition) {
                    return [
                        'metric' => $condition->metric,
                        'comparator' => $condition->comparator,
                        'threshold' => (float) $condition->threshold,
                        'lookback_days' => (int) $condition->lookback_days,
                    ];
                })
                ->all();

            if ($conditions === []) {
                continue;
            }

            $groupLogic = strtolower((string) $groups->first()?->logic);

            $rules[] = [
                'id' => $package->id,
                'name' => $package->name,
                'logic' => $groupLogic === 'or' ? 'or' : 'and',
                'commodity' => null,
                'region' => null,
                'recommendation' => $version->recommendation ?: 'Tinjau temuan dan koordinasikan intervensi lapangan',
                'conditions' => $conditions,
            ];
        }

        if ($rules !== []) {
            return $rules;
        }

        return [
            [
                'id' => 1,
                'name' => 'Stabilisasi harga',
                'logic' => 'and',
                'commodity' => null,
                'region' => null,
                'recommendation' => 'Verifikasi lapangan dan koordinasi pemasok',
                'conditions' => [
                    ['metric' => 'price_change_pct', 'comparator' => 'gt', 'threshold' => 10, 'lookback_days' => 7],
                    ['metric' => 'stock_days', 'comparator' => 'lt', 'threshold' => 3, 'lookback_days' => 0],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Penguatan pasokan',
                'logic' => 'and',
                'commodity' => null,
                'region' => null,
                'recommendation' => 'Realokasi pasokan antar pasar dan operasi pasar murah',
                'conditions' => [
                    ['metric' => 'stock', 'comparator' => 'lt', 'threshold' => 100, 'lookback_days' => 0],
                    ['metric' => 'region_spread_pct', 'comparator' => 'gt', 'threshold' => 8, 'lookback_days' => 0],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Disiplin pelaporan',
                'logic' => 'and',
                'commodity' => null,
                'region' => 'Talang Empat',
                'recommendation' => 'Kirim pengingat otomatis ke Operator & Verifikator wilayah',
                'conditions' => [
                    ['metric' => 'report_overdue_hours', 'comparator' => 'gt', 'threshold' => 24, 'lookback_days' => 0],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function matchesTarget(array $rule, object $row): bool
    {
        if (! empty($rule['commodity']) && ! str_contains(strtolower($row->commodity_name), strtolower((string) $rule['commodity']))) {
            return false;
        }

        if (! empty($rule['region']) && strcasecmp((string) $row->region_name, (string) $rule['region']) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $condition
     * @return array{passed: bool, observed: string, threshold: string}
     */
    public function evaluateCondition(array $condition, object $row, Collection $observations, CarbonInterface $asOf): array
    {
        $value = $this->metricValue($condition['metric'], $row, $observations, $asOf);
        $passed = $this->compare($value, $condition['comparator'], (float) $condition['threshold']);

        return [
            'passed' => $passed,
            'observed' => $this->formatObserved($condition['metric'], $value),
            'threshold' => $this->formatThreshold($condition['comparator'], (float) $condition['threshold'], $condition['metric']),
        ];
    }

    public function compare(float $value, string $comparator, float $threshold): bool
    {
        return match ($comparator) {
            'gte' => $value >= $threshold,
            'lt' => $value < $threshold,
            'lte' => $value <= $threshold,
            'eq' => abs($value - $threshold) < 0.0001,
            default => $value > $threshold,
        };
    }

    private function metricValue(string $metric, object $row, Collection $observations, CarbonInterface $asOf): float
    {
        return match ($metric) {
            'price_change_pct' => (float) $row->price_change_pct,
            'stock' => (float) $row->stock,
            'stock_days' => (float) $row->stock / self::DAILY_CONSUMPTION,
            'report_overdue_hours' => max(0, $asOf->copy()->endOfDay()->diffInHours(Carbon::parse($row->observed_at), false) * -1),
            'region_spread_pct' => $this->regionSpreadForCommodity((int) $row->commodity_id, $observations),
            default => 0.0,
        };
    }

    private function regionSpreadForCommodity(int $commodityId, Collection $observations): float
    {
        $prices = $observations
            ->where('commodity_id', $commodityId)
            ->groupBy('region_id')
            ->map(fn (Collection $group) => (float) $group->avg('price'));

        if ($prices->count() < 2) {
            return 0.0;
        }

        $avg = (float) $prices->avg();

        return $avg > 0 ? (((float) $prices->max() - (float) $prices->min()) / $avg) * 100 : 0.0;
    }

    private function formatObserved(string $metric, float $value): string
    {
        return match ($metric) {
            'price_change_pct', 'region_spread_pct' => ($value >= 0 ? '+' : '').number_format($value, 2, ',', '.').'%',
            'stock' => number_format($value, 0, ',', '.').' kg',
            'stock_days' => number_format($value, 1, ',', '.').' hari stok',
            'report_overdue_hours' => number_format($value, 0, ',', '.').' jam sejak laporan',
            default => (string) $value,
        };
    }

    private function formatThreshold(string $comparator, float $threshold, string $metric): string
    {
        $symbol = match ($comparator) {
            'gte' => '>=',
            'lt' => '<',
            'lte' => '<=',
            'eq' => '=',
            default => '>',
        };

        $suffix = in_array($metric, ['price_change_pct', 'region_spread_pct'], true) ? '%' : '';

        return $symbol.$threshold.$suffix;
    }
}
