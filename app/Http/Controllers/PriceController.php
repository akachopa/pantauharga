<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\CommunityPriceReport;
use App\Models\DataSource;
use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use App\Models\User;
use App\PriceCsvImporter;
use App\Sp2kpClient;
use App\Sp2kpPriceImporter;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PriceController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'tabulasi');
        $selectedDate = $request->get('date', '2026-10-27');
        $selectedMarket = $request->get('market_id');
        $selectedCommodity = $request->get('commodity_id');

        // Master lists
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();
        $points = MonitoringPoint::where('is_active', true)->orderBy('name')->get();
        $dataSources = DataSource::all();

        // Query for Tabulasi table
        $query = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->leftJoin('markets as m', 'm.id', '=', 'mp.market_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->select(
                'ri.id as item_id',
                'ri.report_id',
                'c.id as commodity_id',
                'c.name as commodity_name',
                'c.category as commodity_category',
                'c.icon as commodity_icon',
                'c.base_unit',
                'ri.price',
                'ri.stock',
                'ri.price_change_pct',
                'ri.price_change_amount',
                'r.status as report_status',
                'r.verified_at',
                'r.version_no',
                'r.previous_report_id',
                'r.evidence_path',
                'mp.name as point_name',
                'm.name as market_name',
                'rs.observed_at'
            );

        if ($selectedMarket) {
            $query->where('mp.market_id', $selectedMarket);
        }
        if ($selectedCommodity) {
            $query->where('c.id', $selectedCommodity);
        }
        if ($selectedDate) {
            $query->whereDate('rs.observed_at', $selectedDate);
        }
        if ($activeTab === 'verifikasi') {
            $query->whereIn('r.status', ['submitted', 'draft']);
        }

        $priceItems = $query->orderBy('c.id')->orderBy('rs.observed_at', 'desc')->paginate(15);

        // Verification Tab data: submitted reports
        $pendingReports = Report::with(['series.point.market', 'submitter', 'source', 'items.commodity'])
            ->whereIn('status', ['submitted', 'draft'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Verified reports
        $verifiedReports = Report::with(['series.point.market', 'submitter', 'verifier', 'items.commodity'])
            ->whereIn('status', ['approved', 'rejected', 'returned'])
            ->orderBy('verified_at', 'desc')
            ->limit(10)
            ->get();

        $communityCorrections = CommunityPriceReport::query()
            ->with(['commodity', 'market'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'reviewed' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->limit(40)
            ->get();

        $correctionAuditLogs = DB::table('audit_logs')
            ->where('entity_type', 'community_price_report')
            ->whereIn('entity_id', $communityCorrections->pluck('id'))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('entity_id');

        $canCrawlSp2kp = $this->currentActor()?->hasRole('admin') ?? false;
        $sp2kpPreview = session('sp2kp_preview');

        return view('prices.index', compact(
            'activeTab',
            'markets',
            'commodities',
            'points',
            'dataSources',
            'selectedDate',
            'selectedMarket',
            'selectedCommodity',
            'priceItems',
            'pendingReports',
            'verifiedReports',
            'communityCorrections',
            'correctionAuditLogs',
            'canCrawlSp2kp',
            'sp2kpPreview'
        ));
    }

    public function evidence(Report $report): StreamedResponse
    {
        if (! $report->evidence_path || ! Storage::disk('local')->exists($report->evidence_path)) {
            abort(404);
        }

        return Storage::disk('local')->response($report->evidence_path);
    }

    public function previewSp2kp(Request $request, Sp2kpClient $client, Sp2kpPriceImporter $importer): RedirectResponse
    {
        if (! $this->ensureAdmin()) {
            return $this->denySp2kp();
        }

        $validated = $this->validateSp2kpRequest($request);

        try {
            $classified = $importer->classify($client->fetch(
                $validated['start_date'],
                $validated['end_date'],
                $validated['kode_provinsi'],
                $validated['kode_kab_kota'],
            ));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('prices.index', ['tab' => 'tabulasi'])
                ->with('error', 'Gagal mengambil data SP2KP: '.$exception->getMessage());
        }

        session([
            'sp2kp_preview' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'point_id' => $validated['point_id'],
                'kode_provinsi' => $validated['kode_provinsi'],
                'kode_kab_kota' => $validated['kode_kab_kota'],
                'rows' => array_slice($classified['mapped'], 0, 80),
                'total' => count($classified['mapped']),
                'unmapped' => $classified['unmapped'],
            ],
        ]);

        return redirect()->route('prices.index', [
            'tab' => 'tabulasi',
            'date' => $validated['end_date'],
        ])->with('success', count($classified['mapped']).' baris harga SP2KP siap ditinjau sebelum disimpan.');
    }

    public function importSp2kp(Request $request, Sp2kpPriceImporter $importer): RedirectResponse
    {
        if (! $this->ensureAdmin()) {
            return $this->denySp2kp();
        }

        $validated = $this->validateSp2kpRequest($request);

        try {
            $result = $importer->import(
                $validated['start_date'],
                $validated['end_date'],
                $validated['point_id'],
                (int) $this->currentActor()?->id,
                $validated['kode_provinsi'],
                $validated['kode_kab_kota'],
            );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('prices.index', ['tab' => 'tabulasi'])
                ->with('error', 'Gagal menyimpan data SP2KP: '.$exception->getMessage());
        }

        $message = "Impor SP2KP selesai: {$result['imported']} baris baru, {$result['updated']} diperbarui.";
        if ($result['skipped'] > 0) {
            $message .= ' '.$result['skipped'].' komoditi SP2KP tidak dipetakan.';
        }

        session()->forget('sp2kp_preview');

        return redirect()->route('prices.index', [
            'tab' => 'tabulasi',
            'date' => $validated['end_date'],
        ])->with('success', $message);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'point_id' => 'required|exists:monitoring_points,id',
            'observed_at' => 'required|date',
            'commodity_id' => 'required|exists:commodities,id',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|numeric|min:0',
            'status' => 'nullable|in:draft,submitted',
            'source_id' => 'nullable|exists:data_sources,id',
            'notes' => 'nullable|string|max:255',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $userId = Auth::id() ?? 2;
        $sourceId = (int) ($validated['source_id'] ?? DataSource::query()->where('code', 'SRC_PETUGAS')->value('id') ?? 1);
        $status = $validated['status'] ?? 'submitted';
        $commodity = Commodity::findOrFail($validated['commodity_id']);
        $hetWarning = false;
        if ($commodity->reference_price > 0) {
            $deviation = abs(((float) $validated['price'] / (float) $commodity->reference_price) - 1);
            $hetWarning = $deviation >= 0.15;
        }

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('report-evidence');
        }

        DB::transaction(function () use ($validated, $userId, $sourceId, $status, $commodity, $evidencePath) {
            $series = ReportSeries::create([
                'point_id' => $validated['point_id'],
                'observed_at' => $validated['observed_at'],
            ]);

            $report = Report::create([
                'series_id' => $series->id,
                'source_id' => $sourceId,
                'status' => $status,
                'submitted_by' => $userId,
                'version_no' => 1,
                'is_demo' => false,
                'notes' => $validated['notes'] ?? ($status === 'draft' ? 'Draft input petugas' : 'Input mandiri petugas lapangan'),
                'evidence_path' => $evidencePath,
            ]);

            $prevItem = DB::table('report_items as ri')
                ->join('reports as r', 'r.id', '=', 'ri.report_id')
                ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
                ->where('ri.commodity_id', $validated['commodity_id'])
                ->where('rs.point_id', $validated['point_id'])
                ->where('rs.observed_at', '<', $validated['observed_at'])
                ->orderByDesc('rs.observed_at')
                ->select('ri.price')
                ->first();

            $priceChangeAmount = 0;
            $priceChangePct = 0;
            if ($prevItem && $prevItem->price > 0) {
                $priceChangeAmount = $validated['price'] - $prevItem->price;
                $priceChangePct = round(($priceChangeAmount / $prevItem->price) * 100, 2);
            }

            ReportItem::create([
                'report_id' => $report->id,
                'commodity_id' => $validated['commodity_id'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'unit' => $commodity->base_unit ?? 'kg',
                'price_change_pct' => $priceChangePct,
                'price_change_amount' => $priceChangeAmount,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $message = $status === 'draft'
            ? 'Draft harga dan stok berhasil disimpan.'
            : 'Data harga dan stok berhasil dicatat dan disubmit!';
        if ($hetWarning) {
            $message .= ' Peringatan: harga menyimpang ≥15% dari harga acuan.';
        }

        return redirect()->route('prices.index', ['tab' => 'tabulasi'])
            ->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $item = ReportItem::with('report')->findOrFail($id);
        $oldPrice = (float) $item->price;
        $priceDiff = $validated['price'] - $oldPrice;
        $changePct = $oldPrice > 0 ? round(($priceDiff / $oldPrice) * 100, 2) : 0;

        if ($item->report && $item->report->status === 'approved') {
            DB::transaction(function () use ($item, $validated, $priceDiff, $changePct) {
                $newReport = Report::create([
                    'series_id' => $item->report->series_id,
                    'source_id' => $item->report->source_id,
                    'status' => 'submitted',
                    'submitted_by' => Auth::id() ?? $item->report->submitted_by,
                    'previous_report_id' => $item->report->id,
                    'version_no' => ((int) $item->report->version_no) + 1,
                    'is_demo' => false,
                    'notes' => 'Revisi resmi dari laporan #'.$item->report->id,
                    'evidence_path' => $item->report->evidence_path,
                ]);

                ReportItem::create([
                    'report_id' => $newReport->id,
                    'commodity_id' => $item->commodity_id,
                    'price' => $validated['price'],
                    'stock' => $validated['stock'],
                    'unit' => $item->unit,
                    'price_change_pct' => $changePct,
                    'price_change_amount' => $priceDiff,
                    'notes' => $validated['notes'] ?? 'Revisi harga resmi',
                ]);

                $item->report->update(['status' => 'superseded']);
            });

            return redirect()->back()->with('success', 'Revisi resmi berhasil dibuat (versi baru menunggu verifikasi).');
        }

        $item->update([
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'price_change_amount' => $priceDiff,
            'price_change_pct' => $changePct,
            'notes' => $validated['notes'] ?? $item->notes,
        ]);

        return redirect()->back()->with('success', 'Data harga berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $item = ReportItem::findOrFail($id);
        $reportId = $item->report_id;
        $item->delete();

        // If no items remain, delete report
        if (ReportItem::where('report_id', $reportId)->count() === 0) {
            Report::destroy($reportId);
        }

        return redirect()->back()->with('success', 'Data item harga berhasil dihapus!');
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:approved,rejected,returned',
            'note' => 'required|string|max:500',
        ]);

        $actorId = Auth::id() ?? 4; // Default to verifikator
        $report = Report::findOrFail($id);

        try {
            // Call MySQL stored procedure sp_verify_report
            DB::statement('CALL sp_verify_report(?, ?, ?, ?)', [
                $report->id,
                $actorId,
                $request->decision,
                $request->note,
            ]);
        } catch (Throwable $e) {
            // Fallback manual update if trigger constraints prevent procedure
            $report->update([
                'status' => $request->decision,
                'verified_by' => $request->decision === 'approved' ? $actorId : null,
                'verified_at' => $request->decision === 'approved' ? now() : null,
                'notes' => $report->notes.' | Verifikasi: '.$request->note,
            ]);

            DB::table('verification_events')->insert([
                'report_id' => $report->id,
                'actor_id' => $actorId,
                'decision' => $request->decision,
                'note' => $request->note,
                'created_at' => now(),
            ]);
        }

        $statusLabel = [
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            'returned' => 'dikembalikan untuk perbaikan',
        ][$request->decision];

        if ($request->input('intent') === 'quick') {
            return redirect()->route('prices.index', array_filter([
                'tab' => $request->input('return_tab', 'tabulasi'),
                'date' => $request->input('date'),
                'market_id' => $request->input('market_id'),
                'commodity_id' => $request->input('commodity_id'),
            ]))->with('success', "Status laporan #{$report->id} berhasil {$statusLabel}!");
        }

        return redirect()->route('prices.index', ['tab' => 'verifikasi'])
            ->with('success', "Laporan #{$report->id} berhasil {$statusLabel}!");
    }

    public function import(Request $request, PriceCsvImporter $importer)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $result = $importer->import($request->file('csv_file'), (int) (Auth::id() ?? 2));
        } catch (Throwable $exception) {
            return redirect()->route('prices.index', ['tab' => 'tabulasi'])
                ->with('error', 'Impor CSV gagal: '.$exception->getMessage());
        }

        $message = "Impor CSV selesai: {$result['imported']} baris masuk";
        if ($result['skipped'] > 0) {
            $message .= ", {$result['skipped']} baris dilewati";
        }

        return redirect()->route('prices.index', ['tab' => 'tabulasi'])
            ->with('success', $message.'.');
    }

    public function downloadImportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_impor_harga.csv"',
        ];

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['point_code', 'observed_at', 'commodity_code', 'price', 'stock']);
            fputcsv($handle, ['PT-01', '2026-10-28', 'BERAS_MEDIUM', '14500', '120']);
            fclose($handle);
        }, 'template_impor_harga.csv', $headers);
    }

    /**
     * @return array{start_date: string, end_date: string, point_id: int, kode_provinsi: string, kode_kab_kota: string}
     */
    private function validateSp2kpRequest(Request $request): array
    {
        $validated = $request->validateWithBag('sp2kp', [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'point_id' => ['required', 'exists:monitoring_points,id'],
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        if ($start->diffInDays($end) > 31) {
            throw ValidationException::withMessages([
                'end_date' => 'Rentang crawling SP2KP maksimal 31 hari.',
            ])->errorBag('sp2kp');
        }

        return [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'point_id' => (int) $validated['point_id'],
            'kode_provinsi' => (string) config('services.sp2kp.province_code', '17'),
            'kode_kab_kota' => (string) config('services.sp2kp.regency_code', '1709'),
        ];
    }

    private function currentActor(): ?User
    {
        $user = Auth::user();
        if ($user instanceof User) {
            return $user;
        }

        $simulatedId = session('simulated_user_id', 1);

        return User::query()->find($simulatedId);
    }

    private function ensureAdmin(): bool
    {
        return $this->currentActor()?->hasRole('admin') ?? false;
    }

    private function denySp2kp(): RedirectResponse
    {
        return redirect()->route('prices.index', ['tab' => 'tabulasi'])
            ->with('error', 'Hanya administrator yang dapat mengambil data SP2KP.');
    }
}
