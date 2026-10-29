<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PriceInsight
{
    /**
     * @param  array<int, int>  $commodityIds
     * @return array{
     *     days: list<string>,
     *     labels: list<array{x: float, text: string}>,
     *     series: list<array{id: int, name: string, color: string, values: list<float>, points: string}>,
     *     yLabels: list<array{y: int, text: string}>,
     *     maxPrice: float
     * }
     */
    public function trendSeries(
        CarbonInterface $start,
        CarbonInterface $end,
        array $commodityIds,
        ?int $kecamatanId = null,
        ?int $marketId = null,
        bool $onlyVerified = false,
    ): array {
        $days = [];
        $cursor = $start->copy()->startOfDay();
        $last = $end->copy()->startOfDay();
        while ($cursor->lte($last)) {
            $days[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $commodities = DB::table('commodities')
            ->whereIn('id', $commodityIds)
            ->orderBy('id')
            ->get(['id', 'name', 'reference_price']);

        $query = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->whereIn('ri.commodity_id', $commodityIds)
            ->whereBetween('rs.observed_at', [$start->toDateString(), $end->copy()->endOfDay()]);

        if ($kecamatanId) {
            $query->where('mp.region_id', $kecamatanId);
        }
        if ($marketId) {
            $query->where('mp.market_id', $marketId);
        }
        if ($onlyVerified) {
            $query->where('r.status', 'approved');
        }

        $rows = $query
            ->select(
                'ri.commodity_id',
                DB::raw('DATE(rs.observed_at) as observed_date'),
                DB::raw('AVG(ri.price) as avg_price')
            )
            ->groupBy('ri.commodity_id', DB::raw('DATE(rs.observed_at)'))
            ->orderBy('observed_date')
            ->get()
            ->groupBy('commodity_id');

        $palette = ['#EF4444', '#0D9462', '#F59E0B', '#3B82F6'];
        $namedColors = [
            'beras' => '#0D9462',
            'cabai' => '#EF4444',
            'cabe' => '#EF4444',
            'telur' => '#F59E0B',
            'gula' => '#EAB308',
        ];

        $series = [];
        $maxPrice = 0.0;

        foreach ($commodities as $index => $commodity) {
            $byDate = ($rows[$commodity->id] ?? collect())->keyBy('observed_date');
            $values = [];
            $lastValue = (float) $commodity->reference_price;

            foreach ($days as $day) {
                if (isset($byDate[$day])) {
                    $lastValue = (float) $byDate[$day]->avg_price;
                }
                $values[] = $lastValue;
                $maxPrice = max($maxPrice, $lastValue);
            }

            $nameKey = strtolower($commodity->name);
            $color = $palette[$index % count($palette)];
            foreach ($namedColors as $needle => $namedColor) {
                if (str_contains($nameKey, $needle)) {
                    $color = $namedColor;
                    break;
                }
            }

            $series[] = [
                'id' => (int) $commodity->id,
                'name' => $commodity->name,
                'color' => $color,
                'values' => $values,
                'points' => '',
            ];
        }

        $maxPrice = max($maxPrice, 10000);
        $yCeiling = (int) (ceil($maxPrice / 10000) * 10000);
        $left = 55;
        $right = 640;
        $top = 25;
        $bottom = 195;
        $plotWidth = $right - $left;
        $plotHeight = $bottom - $top;
        $dayCount = max(count($days) - 1, 1);

        foreach ($series as $index => $item) {
            $points = [];
            foreach ($item['values'] as $i => $value) {
                $x = $left + ($i / $dayCount) * $plotWidth;
                $y = $bottom - ($value / $yCeiling) * $plotHeight;
                $points[] = round($x, 1).','.round($y, 1);
            }
            $series[$index]['points'] = implode(' ', $points);
        }

        $labelIndexes = $this->labelIndexes(count($days));
        $labels = [];
        foreach ($labelIndexes as $i) {
            $x = $left + ($i / $dayCount) * $plotWidth;
            $labels[] = [
                'x' => round($x, 1),
                'text' => Carbon::parse($days[$i])->translatedFormat('d M'),
            ];
        }

        $yLabels = [];
        for ($step = 5; $step >= 0; $step--) {
            $value = (int) ($yCeiling * $step / 5);
            $y = $bottom - ($step / 5) * $plotHeight;
            $yLabels[] = [
                'y' => (int) $y + 4,
                'text' => number_format($value, 0, ',', '.'),
            ];
        }

        return [
            'days' => $days,
            'labels' => $labels,
            'series' => $series,
            'yLabels' => $yLabels,
            'maxPrice' => $yCeiling,
        ];
    }

    /**
     * @return Collection<int, object>
     */
    public function regionalSpread(
        ?CarbonInterface $start = null,
        ?CarbonInterface $end = null,
        ?int $kecamatanId = null,
        ?int $marketId = null,
        ?int $commodityId = null,
        bool $onlyVerified = false,
    ): Collection {
        $query = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->join('regions as reg', 'reg.id', '=', 'mp.region_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->where('c.is_active', true);

        if ($start) {
            $query->whereDate('rs.observed_at', '>=', $start->toDateString());
        }
        if ($end) {
            $query->whereDate('rs.observed_at', '<=', $end->toDateString());
        }
        if ($kecamatanId) {
            $query->where('mp.region_id', $kecamatanId);
        }
        if ($marketId) {
            $query->where('mp.market_id', $marketId);
        }
        if ($commodityId) {
            $query->where('c.id', $commodityId);
        }
        if ($onlyVerified) {
            $query->where('r.status', 'approved');
        }

        $rows = $query
            ->select(
                'c.id as commodity_id',
                'c.name as commodity_name',
                'c.reference_price',
                'c.base_unit',
                'reg.id as region_id',
                'reg.name as region_name',
                DB::raw('AVG(ri.price) as avg_price'),
                DB::raw('MIN(ri.price) as min_price'),
                DB::raw('MAX(ri.price) as max_price'),
                DB::raw('AVG(ri.stock) as avg_stock')
            )
            ->groupBy('c.id', 'c.name', 'c.reference_price', 'c.base_unit', 'reg.id', 'reg.name')
            ->orderBy('c.name')
            ->orderBy('reg.name')
            ->get();

        $spreadByCommodity = $rows->groupBy('commodity_id')->map(function (Collection $group) {
            $min = (float) $group->min('min_price');
            $max = (float) $group->max('max_price');
            $avg = (float) $group->avg('avg_price');

            return $avg > 0 ? round((($max - $min) / $avg) * 100, 1) : 0.0;
        });

        return $rows->map(function (object $row) use ($spreadByCommodity) {
            $reference = (float) $row->reference_price;
            $avg = (float) $row->avg_price;
            $row->vs_reference_pct = $reference > 0 ? round((($avg - $reference) / $reference) * 100, 1) : 0.0;
            $row->region_spread_pct = (float) ($spreadByCommodity[$row->commodity_id] ?? 0);
            $row->avg_price = $avg;
            $row->min_price = (float) $row->min_price;
            $row->max_price = (float) $row->max_price;

            return $row;
        });
    }

    /**
     * @return list<int>
     */
    private function labelIndexes(int $count): array
    {
        if ($count <= 7) {
            return range(0, $count - 1);
        }

        $step = (int) ceil(($count - 1) / 6);
        $indexes = [];
        for ($i = 0; $i < $count; $i += $step) {
            $indexes[] = $i;
        }
        if (end($indexes) !== $count - 1) {
            $indexes[] = $count - 1;
        }

        return $indexes;
    }
}
