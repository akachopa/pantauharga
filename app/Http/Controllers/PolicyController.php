<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\Commodity;
use App\Models\MonitoringPoint;
use App\Models\PolicyCase;
use App\Models\PolicyPackage;
use App\Models\PolicyRuleCondition;
use App\Models\PolicyRuleGroup;
use App\Models\PolicyVersion;
use App\Models\Region;
use App\PolicyRuleEvaluator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PolicyController extends Controller
{
    public function __construct(private PolicyRuleEvaluator $policyRuleEvaluator) {}

    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'daftar');
        $selectedPkgId = (int) $request->get('pkg', 1);
        $mobileView = $request->get('view', 'daftar');

        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $kecamatans = Region::where('parent_id', 1)->orderBy('name')->get();

        $packages = PolicyPackage::with(['versions.ruleGroups.conditions', 'organization'])
            ->orderBy('id')
            ->get();

        $policyCards = $packages->map(function (PolicyPackage $package) {
            $version = $package->versions->sortByDesc('version_no')->first();
            $conditions = $version?->ruleGroups
                ->flatMap(fn (PolicyRuleGroup $group) => $group->conditions)
                ->map(function (PolicyRuleCondition $condition) {
                    $type = str_contains($condition->metric, 'stock') ? 'stock' : 'price';
                    $symbol = match ($condition->comparator) {
                        'gte' => '≥',
                        'lt' => '<',
                        'lte' => '≤',
                        'eq' => '=',
                        default => '>',
                    };
                    $suffix = in_array($condition->metric, ['price_change_pct', 'region_spread_pct'], true) ? '%' : '';

                    return [
                        'type' => $type,
                        'text' => str_replace('_', ' ', $condition->metric).' '.$symbol.$condition->threshold.$suffix,
                    ];
                })
                ->values()
                ->all() ?? [];

            return [
                'id' => $package->id,
                'name' => $package->name,
                'status' => $version?->state === 'published' ? 'Aktif' : 'Draft',
                'description' => $package->description ?: 'Paket kebijakan evaluasi harga dan stok.',
                'icon_type' => 'trend',
                'commodity' => 'Semua komoditi',
                'region' => 'Semua wilayah',
                'conditions' => $conditions !== [] ? $conditions : [
                    ['type' => 'price', 'text' => 'Harga naik >10% dalam 7 hari'],
                ],
                'recommendation' => $version?->recommendation ?: 'Verifikasi lapangan dan koordinasi pemasok',
                'approval' => $version?->requires_approval ? 'Pimpinan' : 'Admin Utama',
            ];
        })->values()->all();

        if ($policyCards === []) {
            $policyCards = [[
                'id' => 1,
                'name' => 'Stabilisasi harga',
                'status' => 'Draft',
                'description' => 'Menjaga stabilitas harga komoditas strategis.',
                'icon_type' => 'trend',
                'commodity' => 'Semua komoditi',
                'region' => 'Semua wilayah',
                'conditions' => [['type' => 'price', 'text' => 'Harga naik >10% dalam 7 hari']],
                'recommendation' => 'Verifikasi lapangan dan koordinasi pemasok',
                'approval' => 'Pimpinan',
            ]];
        }

        $selectedPackage = collect($policyCards)->firstWhere('id', $selectedPkgId) ?? $policyCards[0];

        $evaluation = $this->policyRuleEvaluator->evaluate();
        $triggeredCases = $evaluation['cases'];
        $evaluationRuns = [
            $evaluation['run'],
        ];

        return view('policies.index', compact(
            'activeTab',
            'mobileView',
            'commodities',
            'kecamatans',
            'packages',
            'policyCards',
            'selectedPackage',
            'triggeredCases',
            'evaluationRuns'
        ));
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id' => 'nullable|integer|exists:policy_packages,id',
            'commodity_id' => 'nullable',
            'region_id' => 'nullable',
            'indicator' => 'required|string|max:64',
            'operator' => 'required|string|max:16',
            'threshold' => 'required|numeric',
            'period_days' => 'required|numeric|min:0',
            'recommendation' => 'required|string|max:1000',
        ]);

        $package = PolicyPackage::query()->find($validated['package_id'] ?? null)
            ?? PolicyPackage::query()->orderBy('id')->first();

        if (! $package) {
            return redirect()->route('policies.index')
                ->with('error', 'Paket kebijakan belum tersedia.');
        }

        DB::transaction(function () use ($validated, $package): void {
            $version = $package->versions()->orderByDesc('version_no')->first();
            if (! $version) {
                $version = PolicyVersion::create([
                    'package_id' => $package->id,
                    'version_no' => 1,
                    'state' => 'draft',
                    'severity' => 'AND',
                    'valid_from' => now(),
                    'cooldown_hours' => 24,
                    'recommendation' => $validated['recommendation'],
                    'requires_approval' => true,
                    'is_demo' => false,
                ]);
            } else {
                $version->update([
                    'recommendation' => $validated['recommendation'],
                ]);
            }

            $group = $version->ruleGroups()->orderBy('sort_order')->first();
            if (! $group) {
                $group = PolicyRuleGroup::create([
                    'version_id' => $version->id,
                    'parent_group_id' => null,
                    'logic' => 'AND',
                    'sort_order' => 1,
                ]);
            }

            $comparator = match ($validated['operator']) {
                'gte', '>=' => 'gte',
                'lt', '<' => 'lt',
                'lte', '<=' => 'lte',
                'eq', '=' => 'eq',
                default => 'gt',
            };

            $existing = $group->conditions()
                ->where('metric', $validated['indicator'])
                ->first();

            $metric = in_array($validated['indicator'], [
                'price_change_pct',
                'stock_days',
                'report_overdue_hours',
                'shipment_delay_hours',
            ], true) ? $validated['indicator'] : 'price_change_pct';

            $payload = [
                'metric' => $metric,
                'comparator' => $comparator,
                'threshold' => $validated['threshold'],
                'baseline' => $metric === 'price_change_pct' ? 'days_ago' : 'none',
                'lookback_days' => (int) $validated['period_days'],
                'max_age_hours' => 48,
                'min_observations' => $metric === 'price_change_pct' ? 2 : 1,
                'price_type' => 'retail',
                'aggregation' => 'point_latest',
            ];

            if ($existing) {
                $existing->update($payload);
            } else {
                PolicyRuleCondition::create([
                    'group_id' => $group->id,
                    ...$payload,
                ]);
            }

            if (is_numeric($validated['region_id'] ?? null)) {
                $exists = DB::table('policy_targets')
                    ->where('version_id', $version->id)
                    ->where('region_id', (int) $validated['region_id'])
                    ->exists();

                if (! $exists) {
                    DB::table('policy_targets')->insert([
                        'version_id' => $version->id,
                        'region_id' => (int) $validated['region_id'],
                        'market_id' => null,
                        'point_id' => null,
                        'commodity_id' => is_numeric($validated['commodity_id'] ?? null) ? (int) $validated['commodity_id'] : null,
                    ]);
                }
            }
        });

        return redirect()->route('policies.index', ['pkg' => $package->id])
            ->with('success', 'Konfigurasi rule kebijakan berhasil diperbarui dan disimpan!');
    }

    public function simulate(Request $request): RedirectResponse
    {
        $matched = count($this->policyRuleEvaluator->evaluate()['cases']);

        return redirect()->route('policies.index', ['tab' => 'terpicu'])
            ->with('success', "Simulasi berhasil dijalankan: Ditemukan {$matched} kondisi komoditas melampaui ambang batas!");
    }

    public function toggle(Request $request, $id): RedirectResponse
    {
        $package = PolicyPackage::query()->findOrFail($id);
        $version = $package->versions()->orderByDesc('version_no')->first();

        if ($version) {
            $version->update([
                'state' => $version->state === 'published' ? 'draft' : 'published',
            ]);
        }

        return redirect()->back()->with('success', 'Status rule berhasil diubah!');
    }

    public function decide(Request $request, $id): RedirectResponse
    {
        $decision = $request->input('decision', 'approved');
        $note = $request->input('note', 'Disetujui oleh pimpinan untuk pelaksanaan intervensi lapangan.');
        $statusText = $decision === 'approved' ? 'disetujui' : 'ditolak';
        $actorId = Auth::id() ?? 1;

        $evaluation = $this->policyRuleEvaluator->evaluate();
        $caseRow = collect($evaluation['cases'])->firstWhere('id', (int) $id)
            ?? collect($evaluation['cases'])->first();

        if (! $caseRow || empty($caseRow['package_id'])) {
            return redirect()->route('policies.index', ['tab' => 'terpicu'])
                ->with('error', 'Kasus kebijakan live tidak ditemukan.');
        }

        DB::transaction(function () use ($caseRow, $decision, $note, $actorId): void {
            $package = PolicyPackage::with('versions')->findOrFail($caseRow['package_id']);
            $version = $package->versions->sortByDesc('version_no')->first();
            if (! $version) {
                return;
            }

            $point = MonitoringPoint::query()
                ->where('name', $caseRow['point'] ?? '')
                ->first()
                ?? MonitoringPoint::query()->orderBy('id')->first();

            $commodity = Commodity::query()
                ->where('name', $caseRow['commodity'] ?? '')
                ->first()
                ?? Commodity::query()->orderBy('id')->first();

            $runId = DB::table('policy_evaluation_runs')->insertGetId([
                'version_id' => $version->id,
                'evaluated_at' => now(),
                'data_cutoff_at' => now(),
                'engine_version' => 'Pantau Rule Engine v1',
                'mode' => 'live',
                'status' => 'completed',
                'definition_snapshot' => json_encode(['case' => $caseRow]),
                'error_message' => null,
            ]);

            $resultId = DB::table('policy_evaluation_results')->insertGetId([
                'run_id' => $runId,
                'point_id' => $point?->id,
                'commodity_id' => $commodity?->id,
                'outcome' => 'matched',
                'evidence_snapshot' => json_encode($caseRow),
                'is_demo' => false,
            ]);

            $caseStatus = $decision === 'approved' ? 'approved' : 'rejected';
            $dedupeKey = 'live-'.$package->id.'-'.($point?->id ?? 0).'-'.($commodity?->id ?? 0).'-'.Str::random(8);

            $policyCase = PolicyCase::create([
                'result_id' => $resultId,
                'dedupe_key' => $dedupeKey,
                'status' => $caseStatus,
                'requested_by' => $actorId,
                'decided_by' => $actorId,
                'decided_at' => now(),
                'decision_note' => $note,
                'created_at' => now(),
            ]);

            DB::table('policy_case_events')->insert([
                'case_id' => $policyCase->id,
                'actor_id' => $actorId,
                'event_type' => $caseStatus,
                'note' => $note,
                'created_at' => now(),
            ]);

            if ($decision === 'approved') {
                ActionPlan::create([
                    'title' => 'Tindak lanjut: '.($caseRow['package_name'] ?? $package->name),
                    'description' => ($caseRow['recommendation'] ?? $version->recommendation).' — '.($caseRow['commodity'] ?? '').' di '.($caseRow['point'] ?? ''),
                    'priority' => 'tinggi',
                    'status' => 'antrean',
                    'assigned_to' => $actorId,
                    'deadline' => now()->addDays(7)->toDateString(),
                    'progress_pct' => 0,
                    'notes' => 'Dibuat otomatis dari keputusan kasus kebijakan #'.$policyCase->id,
                    'is_demo' => false,
                    'policy_case_id' => $policyCase->id,
                    'target_point_id' => $point?->id,
                    'created_at' => now(),
                ]);
            }

            DB::table('audit_logs')->insert([
                'actor_id' => $actorId,
                'entity_type' => 'policy_case',
                'entity_id' => $policyCase->id,
                'action' => 'decide_'.$caseStatus,
                'old_values' => null,
                'new_values' => json_encode(['decision' => $caseStatus, 'note' => $note]),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('policies.index', ['tab' => 'terpicu'])
            ->with('success', "Rekomendasi kebijakan kasus #{$id} berhasil {$statusText}!");
    }
}
