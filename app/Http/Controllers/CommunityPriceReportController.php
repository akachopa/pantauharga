<?php

namespace App\Http\Controllers;

use App\Models\CommunityPriceReport;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunityPriceReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'commodity_id' => ['required', 'integer', 'exists:commodities,id'],
            'market_id' => ['required', 'integer', 'exists:markets,id'],
            'reported_price' => ['required', 'numeric', 'min:1'],
            'reporter_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'commodity_id.required' => 'Pilih komoditi.',
            'commodity_id.exists' => 'Komoditi tidak ditemukan.',
            'market_id.required' => 'Pilih pasar.',
            'market_id.exists' => 'Pasar tidak ditemukan.',
            'reported_price.required' => 'Isi harga yang Anda lihat.',
            'reported_price.numeric' => 'Harga harus berupa angka.',
            'reported_price.min' => 'Harga minimal 1.',
            'reporter_name.required' => 'Isi nama Anda.',
        ]);

        $officialPrice = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->where('r.status', 'approved')
            ->where('ri.commodity_id', $validated['commodity_id'])
            ->where('mp.market_id', $validated['market_id'])
            ->avg('ri.price');

        $report = CommunityPriceReport::query()->create([
            'commodity_id' => $validated['commodity_id'],
            'market_id' => $validated['market_id'],
            'reported_price' => $validated['reported_price'],
            'official_price' => $officialPrice !== null ? round((float) $officialPrice, 2) : null,
            'reporter_name' => $validated['reporter_name'],
            'phone' => $validated['phone'] ?? null,
            'note' => $validated['note'] ?? null,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id() ?? 1,
            'entity_type' => 'community_price_report',
            'entity_id' => $report->id,
            'action' => 'submitted',
            'old_values' => null,
            'new_values' => json_encode(['status' => 'pending', 'reported_price' => $validated['reported_price']]),
            'created_at' => now(),
        ]);

        return redirect()
            ->route('public.report')
            ->with('success', 'Laporan harga diterima. Data resmi tidak diubah; petugas akan meninjau masukan Anda.');
    }

    public function review(Request $request, CommunityPriceReport $communityPriceReport): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:reviewed,dismissed,returned'],
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $communityPriceReport->status;
        $communityPriceReport->update([
            'status' => $validated['status'],
            'review_note' => $validated['review_note'] ?? ($validated['status'] === 'returned' ? 'Mohon perbaiki dan laporkan ulang.' : null),
            'reviewed_at' => now(),
        ]);

        DB::table('audit_logs')->insert([
            'actor_id' => Auth::id() ?? 1,
            'entity_type' => 'community_price_report',
            'entity_id' => $communityPriceReport->id,
            'action' => 'review_'.$validated['status'],
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode([
                'status' => $validated['status'],
                'review_note' => $communityPriceReport->review_note,
            ]),
            'created_at' => now(),
        ]);

        $label = match ($validated['status']) {
            'reviewed' => 'ditinjau',
            'returned' => 'dikembalikan untuk perbaikan',
            default => 'diabaikan',
        };

        return redirect()
            ->route('prices.index', ['tab' => 'koreksi'])
            ->with('success', "Laporan warga berhasil {$label}.");
    }

    public function createOfficialRevision(Request $request, CommunityPriceReport $communityPriceReport): RedirectResponse
    {
        if ($communityPriceReport->status !== 'reviewed') {
            return redirect()
                ->route('prices.index', ['tab' => 'koreksi'])
                ->with('error', 'Hanya koreksi yang sudah ditinjau yang dapat menjadi revisi resmi.');
        }

        $point = MonitoringPoint::query()
            ->where('market_id', $communityPriceReport->market_id)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        if (! $point) {
            return redirect()
                ->route('prices.index', ['tab' => 'koreksi'])
                ->with('error', 'Titik pantau untuk pasar terkait tidak ditemukan.');
        }

        DB::transaction(function () use ($communityPriceReport, $point) {
            $series = ReportSeries::create([
                'point_id' => $point->id,
                'observed_at' => now(),
            ]);

            $report = Report::create([
                'series_id' => $series->id,
                'source_id' => 1,
                'status' => 'submitted',
                'submitted_by' => Auth::id() ?? 4,
                'version_no' => 1,
                'is_demo' => false,
                'notes' => 'Revisi resmi dari koreksi warga #'.$communityPriceReport->id,
            ]);

            $oldPrice = (float) ($communityPriceReport->official_price ?? 0);
            $newPrice = (float) $communityPriceReport->reported_price;
            $diff = $newPrice - $oldPrice;
            $pct = $oldPrice > 0 ? round(($diff / $oldPrice) * 100, 2) : 0;

            ReportItem::create([
                'report_id' => $report->id,
                'commodity_id' => $communityPriceReport->commodity_id,
                'price' => $newPrice,
                'stock' => 0,
                'unit' => $communityPriceReport->commodity?->base_unit ?? 'kg',
                'price_change_pct' => $pct,
                'price_change_amount' => $diff,
                'notes' => 'Dari laporan warga '.$communityPriceReport->reporter_name,
            ]);

            DB::table('audit_logs')->insert([
                'actor_id' => Auth::id() ?? 1,
                'entity_type' => 'community_price_report',
                'entity_id' => $communityPriceReport->id,
                'action' => 'official_revision_created',
                'old_values' => json_encode(['official_price' => $oldPrice]),
                'new_values' => json_encode(['report_id' => $report->id, 'price' => $newPrice]),
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('prices.index', ['tab' => 'verifikasi'])
            ->with('success', 'Revisi resmi dari koreksi warga dibuat dan menunggu verifikasi.');
    }
}
