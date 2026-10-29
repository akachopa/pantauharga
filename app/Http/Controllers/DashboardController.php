<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\Commodity;
use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\PolicyPackage;
use App\Models\Region;
use App\Models\Report;
use App\PriceInsight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(private PriceInsight $priceInsight) {}

    public function index(Request $request)
    {
        $selectedDate = $request->get('date', '2026-10-27');
        $selectedKecamatan = $request->get('kecamatan_id');
        $selectedMarket = $request->get('market_id');
        $chartPeriod = (int) $request->get('period', 7);
        if (! in_array($chartPeriod, [7, 30], true)) {
            $chartPeriod = 7;
        }

        $kecamatans = Region::where('parent_id', 1)->orderBy('name')->get();
        $markets = Market::where('is_active', true)->orderBy('name')->get();

        $totalCommodities = Commodity::where('is_active', true)->count();
        $totalPoints = MonitoringPoint::where('is_active', true)->count();

        $verifiedCount = Report::whereNotNull('verified_at')
            ->whereDate('verified_at', $selectedDate)
            ->count();

        $attentionCount = ActionPlan::whereIn('status', ['antrean', 'terlambat', 'dalam_proses'])->count();

        $endDate = Carbon::parse($selectedDate);
        $startDate = $endDate->copy()->subDays($chartPeriod - 1);

        $recapQuery = DB::table('commodities as c')
            ->leftJoin('report_items as ri', 'ri.commodity_id', '=', 'c.id')
            ->leftJoin('reports as r', 'r.id', '=', 'ri.report_id')
            ->leftJoin('report_series as rs', function ($join) use ($startDate, $endDate) {
                $join->on('rs.id', '=', 'r.series_id')
                    ->whereDate('rs.observed_at', '>=', $startDate->toDateString())
                    ->whereDate('rs.observed_at', '<=', $endDate->toDateString());
            })
            ->leftJoin('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->where('c.is_active', true);

        if ($selectedMarket) {
            $recapQuery->where('mp.market_id', $selectedMarket);
        }
        if ($selectedKecamatan) {
            $recapQuery->where('mp.region_id', $selectedKecamatan);
        }

        $recapItems = $recapQuery->select(
            'c.id',
            'c.name',
            'c.reference_price',
            'c.base_unit',
            DB::raw('COALESCE(SUM(ri.stock), 150) as total_stock'),
            DB::raw('COALESCE(AVG(ri.price_change_pct), 0) as avg_change_pct'),
        )
            ->groupBy('c.id', 'c.name', 'c.reference_price', 'c.base_unit')
            ->orderBy('c.id')
            ->get();

        $fluctuatingCount = $recapItems->filter(function ($item) {
            return abs($item->avg_change_pct) >= 1.5 || $item->total_stock < 30;
        })->count();
        if ($fluctuatingCount === 0) {
            $fluctuatingCount = max($attentionCount, 1);
        }

        $alerts = collect();

        foreach ($recapItems->filter(fn ($i) => $i->total_stock < 30)->take(2) as $item) {
            $alerts->push([
                'type' => 'stock',
                'title' => 'Stok '.$item->name.' menipis',
                'description' => 'Stok terpantau di bawah ambang aman (< 30 '.$item->base_unit.').',
                'badge' => 'Prioritas Tinggi',
                'badge_class' => 'bg-amber-200 text-amber-900',
                'card_class' => 'bg-amber-50/70 border-amber-200/80 hover:border-amber-400',
                'icon_class' => 'bg-amber-100 text-amber-700',
                'url' => route('actions.index'),
                'date' => Carbon::parse($selectedDate)->translatedFormat('d M Y'),
            ]);
        }

        $pendingToday = Report::whereNull('verified_at')
            ->whereDate('created_at', $selectedDate)
            ->count();
        if ($pendingToday > 0) {
            $alerts->push([
                'type' => 'pending',
                'title' => 'Laporan belum diverifikasi',
                'description' => "{$pendingToday} laporan menunggu verifikasi pada tanggal pantau.",
                'badge' => 'Perlu Verifikasi',
                'badge_class' => 'bg-rose-200 text-rose-900',
                'card_class' => 'bg-rose-50/70 border-rose-200/80 hover:border-rose-400',
                'icon_class' => 'bg-rose-100 text-rose-700',
                'url' => route('prices.index', ['tab' => 'verifikasi']),
                'date' => Carbon::parse($selectedDate)->translatedFormat('d M Y'),
            ]);
        }

        if ($alerts->isEmpty()) {
            $alerts->push([
                'type' => 'ok',
                'title' => 'Tidak ada anomali kritis',
                'description' => 'Semua indikator utama berada dalam batas normal untuk tanggal pantau.',
                'badge' => 'Stabil',
                'badge_class' => 'bg-emerald-200 text-emerald-900',
                'card_class' => 'bg-emerald-50/70 border-emerald-200/80 hover:border-emerald-400',
                'icon_class' => 'bg-emerald-100 text-emerald-700',
                'url' => route('prices.index'),
                'date' => Carbon::parse($selectedDate)->translatedFormat('d M Y'),
            ]);
        }

        $recentActivities = DB::table('reports as r')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->leftJoin('markets as m', 'm.id', '=', 'mp.market_id')
            ->leftJoin('report_items as ri', 'ri.report_id', '=', 'r.id')
            ->leftJoin('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->leftJoin('users as u', 'u.id', '=', 'r.submitted_by')
            ->select(
                'r.created_at',
                'r.verified_at',
                'c.name as commodity_name',
                'm.name as market_name',
                'u.name as user_name'
            )
            ->orderByDesc('r.created_at')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $isVerified = ! empty($row->verified_at);
                $created = Carbon::parse($row->created_at);

                return [
                    'time' => $created->isToday() ? $created->format('H:i') : ($created->isYesterday() ? 'Kemarin' : $created->translatedFormat('d M')),
                    'activity' => $isVerified ? 'Laporan diverifikasi' : 'Data masuk',
                    'dot' => $isVerified ? 'bg-emerald-500' : 'bg-amber-500',
                    'commodity' => $row->commodity_name ?? '-',
                    'location' => $row->market_name ?? '-',
                    'officer' => $row->user_name ?? 'Petugas',
                ];
            });

        $policyPackage = PolicyPackage::orderBy('id')->first();

        $foodIndexRows = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->leftJoin('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->where('r.status', 'approved')
            ->where('c.is_active', true)
            ->where('c.reference_price', '>', 0)
            ->whereDate('rs.observed_at', '>=', $startDate->toDateString())
            ->whereDate('rs.observed_at', '<=', $endDate->toDateString());

        if ($selectedMarket) {
            $foodIndexRows->where('mp.market_id', $selectedMarket);
        }
        if ($selectedKecamatan) {
            $foodIndexRows->where('mp.region_id', $selectedKecamatan);
        }

        $foodIndex = (float) ($foodIndexRows
            ->selectRaw('AVG(ri.price / c.reference_price) * 100 as idx')
            ->value('idx') ?? 100);
        $foodIndex = round($foodIndex, 1);

        $chartCommodities = Commodity::where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'like', '%Beras%')
                    ->orWhere('name', 'like', '%Cabai%')
                    ->orWhere('name', 'like', '%Telur%');
            })
            ->orderBy('id')
            ->limit(3)
            ->get();

        if ($chartCommodities->count() < 3) {
            $chartCommodities = Commodity::where('is_active', true)->orderBy('id')->limit(3)->get();
        }

        $priceChart = $this->priceInsight->trendSeries(
            $startDate,
            $endDate,
            $chartCommodities->pluck('id')->all(),
            $selectedKecamatan ? (int) $selectedKecamatan : null,
            $selectedMarket ? (int) $selectedMarket : null,
        );

        $regionalSpread = $this->priceInsight->regionalSpread(
            $startDate,
            $endDate,
            $selectedKecamatan ? (int) $selectedKecamatan : null,
            $selectedMarket ? (int) $selectedMarket : null,
        );

        return view('dashboard.index', compact(
            'kecamatans',
            'markets',
            'selectedDate',
            'selectedKecamatan',
            'selectedMarket',
            'totalCommodities',
            'totalPoints',
            'verifiedCount',
            'attentionCount',
            'fluctuatingCount',
            'recapItems',
            'alerts',
            'recentActivities',
            'policyPackage',
            'chartPeriod',
            'priceChart',
            'regionalSpread',
            'foodIndex'
        ));
    }
}
