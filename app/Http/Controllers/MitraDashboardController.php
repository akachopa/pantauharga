<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\DataSource;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\User;
use App\PriceReportRecorder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MitraDashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $reports = Report::query()
            ->with(['series.point', 'items.commodity'])
            ->where('submitted_by', $user->id)
            ->latest('id')
            ->paginate(10);

        $statusCounts = Report::query()
            ->where('submitted_by', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $latestNotes = DB::table('verification_events')
            ->whereIn('report_id', $reports->pluck('id'))
            ->orderByDesc('id')
            ->get(['report_id', 'decision', 'note'])
            ->unique('report_id')
            ->keyBy('report_id');

        return view('mitra.dashboard', [
            'user' => $user,
            'businessName' => DB::table('partner_accounts as pa')
                ->join('businesses as b', 'b.id', '=', 'pa.business_id')
                ->where('pa.user_id', $user->id)
                ->value('b.name'),
            'points' => $this->assignedPoints($user),
            'commodities' => Commodity::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'base_unit']),
            'reports' => $reports,
            'latestNotes' => $latestNotes,
            'stats' => [
                'pending' => (int) ($statusCounts['submitted'] ?? 0),
                'approved' => (int) ($statusCounts['approved'] ?? 0),
                'needs_fix' => (int) (($statusCounts['returned'] ?? 0) + ($statusCounts['rejected'] ?? 0)),
            ],
        ]);
    }

    public function store(Request $request, PriceReportRecorder $priceReportRecorder): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'point_id' => ['required', 'integer', Rule::in($this->assignedPoints($user)->pluck('id')->all())],
            'observed_at' => ['required', 'date', 'before_or_equal:today'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.commodity_id' => ['required', 'integer', 'distinct', Rule::exists('commodities', 'id')->where('is_active', true)],
            'items.*.price' => ['required', 'numeric', 'min:1', 'max:100000000'],
            'items.*.stock' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'point_id.required' => 'Pilih lokasi usaha.',
            'point_id.in' => 'Lokasi ini bukan titik pantau yang ditugaskan untuk akun Anda.',
            'observed_at.before_or_equal' => 'Tanggal pantau tidak boleh melewati hari ini.',
            'items.required' => 'Isi minimal satu harga komoditi.',
            'items.*.commodity_id.required' => 'Pilih komoditi pada setiap baris.',
            'items.*.commodity_id.distinct' => 'Komoditi yang sama diisi lebih dari sekali.',
            'items.*.price.required' => 'Isi harga pada setiap baris.',
            'items.*.price.min' => 'Harga harus lebih dari 0.',
        ]);

        $priceReportRecorder->record(
            $user,
            (int) $validated['point_id'],
            $validated['observed_at'],
            (int) DataSource::query()->where('code', 'SRC_MITRA')->value('id'),
            'submitted',
            array_values($validated['items']),
            $validated['notes'] ?? 'Input mitra usaha',
            $request->file('evidence')?->store('report-evidence'),
        );

        return redirect()->route('mitra.dashboard')
            ->with('success', 'Harga terkirim dan menunggu verifikasi petugas dinas.');
    }

    /**
     * @return Collection<int, MonitoringPoint>
     */
    private function assignedPoints(User $user): Collection
    {
        $today = now()->toDateString();

        return MonitoringPoint::query()
            ->where('is_active', true)
            ->whereIn('id', $user->pointAssignments()
                ->where('starts_on', '<=', $today)
                ->where(fn ($query) => $query->whereNull('ends_on')->orWhere('ends_on', '>=', $today))
                ->select('point_id'))
            ->orderBy('name')
            ->get(['id', 'name', 'region_id']);
    }
}
