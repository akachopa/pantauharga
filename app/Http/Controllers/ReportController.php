<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\Region;
use App\PriceInsight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private PriceInsight $priceInsight) {}

    public function index(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $pointTotal = max(1, MonitoringPoint::query()->count());

        $kecamatans = Region::where('parent_id', 1)->orderBy('name')->get();
        $markets = Market::where('is_active', true)->orderBy('name')->get();
        $commodities = Commodity::where('is_active', true)->orderBy('name')->get();

        $items = $this->filteredItems($filters);

        $chartIds = $filters['commodity_id']
            ? [(int) $filters['commodity_id']]
            : $commodities->filter(function ($commodity) {
                $name = strtolower($commodity->name);

                return str_contains($name, 'beras')
                    || str_contains($name, 'gula')
                    || str_contains($name, 'cabai')
                    || str_contains($name, 'telur');
            })->take(4)->pluck('id')->all();

        if ($chartIds === []) {
            $chartIds = $commodities->take(4)->pluck('id')->all();
        }

        $priceChart = $this->priceInsight->trendSeries(
            $filters['from'],
            $filters['to'],
            $chartIds,
            $filters['kecamatan_id'],
            $filters['market_id'],
            $filters['verified'],
        );

        $regionalSpread = $this->priceInsight->regionalSpread(
            $filters['from'],
            $filters['to'],
            $filters['kecamatan_id'],
            $filters['market_id'],
            $filters['commodity_id'],
            $filters['verified'],
        );

        return view('reports.index', [
            'activeTab' => $request->get('tab', 'harga'),
            'kecamatans' => $kecamatans,
            'markets' => $markets,
            'commodities' => $commodities,
            'selectedKecamatan' => $filters['kecamatan_id'],
            'selectedMarket' => $filters['market_id'],
            'selectedCommodity' => $filters['commodity_id'],
            'onlyVerified' => $filters['verified'],
            'fromDate' => $filters['from'],
            'toDate' => $filters['to'],
            'pointTotal' => $pointTotal,
            'items' => $items,
            'priceChart' => $priceChart,
            'regionalSpread' => $regionalSpread,
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $this->resolveFilters($request);
        $items = $this->filteredItems($filters);
        $fileName = 'Laporan_Pantau_Harga_Bengkulu_Tengah_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($items, $filters) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['LAPORAN PANTAU HARGA KABUPATEN BENGKULU TENGAH']);
            fputcsv($handle, ['Tanggal Ekspor: '.date('d F Y H:i')]);
            fputcsv($handle, [
                'Filter: '.$filters['from']->toDateString().' s/d '.$filters['to']->toDateString()
                .($filters['kecamatan_id'] ? ' | kecamatan='.$filters['kecamatan_id'] : '')
                .($filters['market_id'] ? ' | pasar='.$filters['market_id'] : '')
                .($filters['commodity_id'] ? ' | komoditi='.$filters['commodity_id'] : '')
                .($filters['verified'] ? ' | hanya terverifikasi' : ''),
            ]);
            fputcsv($handle, []);
            fputcsv($handle, ['No', 'Komoditi', 'Kategori', 'Satuan', 'Harga Minimum (Rp)', 'Harga Rata-rata (Rp)', 'Harga Maksimum (Rp)', 'Total Stok Terpantau']);

            $no = 1;
            foreach ($items as $row) {
                fputcsv($handle, [
                    $no++,
                    $row->name,
                    $row->category,
                    $row->base_unit,
                    number_format($row->min_price, 0, ',', '.'),
                    number_format($row->avg_price, 0, ',', '.'),
                    number_format($row->max_price, 0, ',', '.'),
                    number_format($row->total_stock, 0, ',', '.'),
                ]);
            }
            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $items = $this->filteredItems($filters);

        return response()->view('reports.pdf_print', compact('items', 'filters'))
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * @return array{kecamatan_id: ?int, market_id: ?int, commodity_id: ?int, verified: bool, from: Carbon, to: Carbon}
     */
    private function resolveFilters(Request $request): array
    {
        return [
            'kecamatan_id' => $request->filled('kecamatan_id') ? (int) $request->get('kecamatan_id') : null,
            'market_id' => $request->filled('market_id') ? (int) $request->get('market_id') : null,
            'commodity_id' => $request->filled('commodity_id') ? (int) $request->get('commodity_id') : null,
            'verified' => $request->boolean('verified', true),
            'from' => Carbon::parse($request->get('from', '2026-10-21')),
            'to' => Carbon::parse($request->get('to', '2026-10-28')),
        ];
    }

    /**
     * @param  array{kecamatan_id: ?int, market_id: ?int, commodity_id: ?int, verified: bool, from: Carbon, to: Carbon}  $filters
     * @return Collection<int, object>
     */
    private function filteredItems(array $filters): Collection
    {
        $itemsQuery = DB::table('commodities as c')
            ->leftJoin('report_items as ri', 'ri.commodity_id', '=', 'c.id')
            ->leftJoin('reports as r', 'r.id', '=', 'ri.report_id')
            ->leftJoin('report_series as rs', function ($join) use ($filters) {
                $join->on('rs.id', '=', 'r.series_id')
                    ->whereDate('rs.observed_at', '>=', $filters['from']->toDateString())
                    ->whereDate('rs.observed_at', '<=', $filters['to']->toDateString());
            })
            ->leftJoin('monitoring_points as mp', 'mp.id', '=', 'rs.point_id');

        if ($filters['kecamatan_id']) {
            $itemsQuery->where('mp.region_id', $filters['kecamatan_id']);
        }
        if ($filters['market_id']) {
            $itemsQuery->where('mp.market_id', $filters['market_id']);
        }
        if ($filters['commodity_id']) {
            $itemsQuery->where('c.id', $filters['commodity_id']);
        }
        if ($filters['verified']) {
            $itemsQuery->where(function ($query) {
                $query->whereNull('r.id')->orWhere('r.status', 'approved');
            });
        }

        return $itemsQuery
            ->select(
                'c.id',
                'c.name',
                'c.category',
                'c.icon',
                'c.base_unit',
                'c.reference_price',
                DB::raw('COALESCE(MIN(ri.price), c.reference_price * 0.92) as min_price'),
                DB::raw('COALESCE(AVG(ri.price), c.reference_price) as avg_price'),
                DB::raw('COALESCE(MAX(ri.price), c.reference_price * 1.12) as max_price'),
                DB::raw('COALESCE(SUM(ri.stock), 200) as total_stock'),
                DB::raw('COUNT(DISTINCT rs.point_id) as reporting_points'),
                DB::raw('MAX(ri.created_at) as last_updated')
            )
            ->groupBy('c.id', 'c.name', 'c.category', 'c.icon', 'c.base_unit', 'c.reference_price')
            ->orderBy('c.id')
            ->get();
    }
}
