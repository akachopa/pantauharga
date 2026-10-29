<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\MonitoringPoint;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActionPlanController extends Controller
{
    public function index(Request $request)
    {
        $filterPic = $request->get('pic_id');
        $filterPriority = $request->get('priority');
        $filterStatus = $request->get('status');
        $filterRegion = $request->get('kecamatan_id');

        $query = ActionPlan::with(['assignedUser.organization', 'targetPoint.region', 'policyCase']);

        if ($filterPic) {
            $query->where('assigned_to', $filterPic);
        }
        if ($filterPriority) {
            $query->where('priority', $filterPriority);
        }
        if ($filterStatus) {
            $query->where('status', $filterStatus);
        }
        if ($filterRegion) {
            $query->whereHas('targetPoint', function ($pointQuery) use ($filterRegion): void {
                $pointQuery->where('region_id', $filterRegion);
            });
        }

        $actions = $query->orderBy('id', 'desc')->get();
        $selectedId = $request->get('selected', $actions->first()?->id);

        $ditugaskanList = $actions->whereIn('status', ['antrean', 'terlambat'])->values();
        $dalamProsesList = $actions->where('status', 'dalam_proses')->values();
        $selesaiList = $actions->where('status', 'selesai')->values();
        $selectedAction = $actions->firstWhere('id', (int) $selectedId) ?? $actions->first();

        $countAntrean = ActionPlan::where('status', 'antrean')->count();
        $countDalamProses = ActionPlan::where('status', 'dalam_proses')->count();
        $countSelesai = ActionPlan::where('status', 'selesai')->count();
        $countTerlambat = ActionPlan::where('status', 'terlambat')->count();

        $officers = User::all();
        $points = MonitoringPoint::all();
        $kecamatans = Region::where('parent_id', 1)->get();

        $impactComparison = $selectedAction
            ? $this->buildImpactComparison($selectedAction)
            : null;

        return view('actions.index', compact(
            'actions',
            'ditugaskanList',
            'dalamProsesList',
            'selesaiList',
            'selectedAction',
            'selectedId',
            'countAntrean',
            'countDalamProses',
            'countSelesai',
            'countTerlambat',
            'officers',
            'points',
            'kecamatans',
            'filterPic',
            'filterPriority',
            'filterStatus',
            'filterRegion',
            'impactComparison'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:tinggi,sedang,rendah',
            'status' => 'required|in:antrean,dalam_proses,selesai,terlambat',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('action-evidence');
        }

        ActionPlan::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'target_point_id' => $request->target_point_id,
            'deadline' => $request->deadline,
            'progress_pct' => $request->progress_pct ?? 0,
            'notes' => $request->notes,
            'evidence_path' => $evidencePath,
            'policy_case_id' => $request->policy_case_id,
            'is_demo' => false,
            'created_at' => now(),
        ]);

        return redirect()->route('actions.index')->with('success', 'Rencana tindak lanjut baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $action = ActionPlan::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:tinggi,sedang,rendah',
            'status' => 'required|in:antrean,dalam_proses,selesai,terlambat',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $payload = [
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
            'target_point_id' => $request->target_point_id,
            'deadline' => $request->deadline,
            'progress_pct' => $request->progress_pct ?? $action->progress_pct,
            'notes' => $request->notes,
        ];

        if ($request->hasFile('evidence')) {
            $payload['evidence_path'] = $request->file('evidence')->store('action-evidence');
        }

        $action->update($payload);

        return redirect()->route('actions.index')->with('success', 'Data tindak lanjut berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $action = ActionPlan::findOrFail($id);

        if ($request->hasFile('evidence')) {
            $request->validate([
                'evidence' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);
            $action->evidence_path = $request->file('evidence')->store('action-evidence');
        }

        if ($request->has('progress_pct')) {
            $progress = max(0, min(100, (int) $request->progress_pct));
            $status = $progress >= 100 ? 'selesai' : ($progress > 0 ? 'dalam_proses' : 'antrean');
        } else {
            $status = $request->status;
            $progress = $status === 'selesai' ? 100 : $action->progress_pct;
        }

        $action->update([
            'status' => $status,
            'progress_pct' => $progress,
            'evidence_path' => $action->evidence_path,
        ]);

        return redirect()->route('actions.index', array_filter([
            'selected' => $action->id,
            'pic_id' => $request->get('pic_id'),
            'priority' => $request->get('priority'),
            'kecamatan_id' => $request->get('kecamatan_id'),
            'view' => $request->get('view'),
            'mobile' => $request->get('mobile'),
        ]))->with('success', 'Status tindak lanjut berhasil diperbarui!');
    }

    public function destroy($id): RedirectResponse
    {
        ActionPlan::destroy($id);

        return redirect()->route('actions.index')->with('success', 'Tindak lanjut berhasil dihapus!');
    }

    public function evidence(ActionPlan $action): StreamedResponse
    {
        if (! $action->evidence_path || ! Storage::disk('local')->exists($action->evidence_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($action->evidence_path);
    }

    /**
     * @return array{before: ?object, after: ?object}|null
     */
    private function buildImpactComparison(ActionPlan $action): ?array
    {
        if (! $action->target_point_id || ! $action->deadline) {
            return null;
        }

        $before = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->where('rs.point_id', $action->target_point_id)
            ->whereDate('rs.observed_at', '<=', $action->deadline->toDateString())
            ->where('r.status', 'approved')
            ->orderByDesc('rs.observed_at')
            ->select('c.name as commodity_name', 'ri.price', 'ri.stock', 'rs.observed_at')
            ->first();

        $after = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->where('rs.point_id', $action->target_point_id)
            ->whereDate('rs.observed_at', '>', $action->deadline->toDateString())
            ->where('r.status', 'approved')
            ->orderBy('rs.observed_at')
            ->select('c.name as commodity_name', 'ri.price', 'ri.stock', 'rs.observed_at')
            ->first();

        if (! $before && ! $after) {
            return null;
        }

        return [
            'before' => $before,
            'after' => $after,
        ];
    }
}
