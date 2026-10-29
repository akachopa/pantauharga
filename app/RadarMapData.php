<?php

namespace App;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RadarMapData
{
    /**
     * @return Collection<int, array{
     *     market_id: int,
     *     name: string,
     *     region: ?string,
     *     lat: float,
     *     lng: float,
     *     avg_price: ?float,
     *     total_stock: ?float,
     *     commodity_name: ?string,
     *     change_pct: ?float,
     *     tone: string,
     *     observed_at: ?string
     * }>
     */
    public function markers(
        CarbonInterface $asOf,
        ?int $kecamatanId = null,
        ?int $commodityId = null,
    ): Collection {
        $markets = DB::table('markets as m')
            ->leftJoin('regions as reg', 'reg.id', '=', 'm.region_id')
            ->where('m.is_active', true)
            ->whereNotNull('m.latitude')
            ->whereNotNull('m.longitude')
            ->when($kecamatanId, fn ($q) => $q->where('m.region_id', $kecamatanId))
            ->select('m.id', 'm.name', 'm.latitude', 'm.longitude', 'reg.name as region_name')
            ->orderBy('m.name')
            ->get();

        $observations = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->where('r.status', 'approved')
            ->whereDate('rs.observed_at', '<=', $asOf->toDateString())
            ->when($kecamatanId, fn ($q) => $q->where('mp.region_id', $kecamatanId))
            ->when($commodityId, fn ($q) => $q->where('ri.commodity_id', $commodityId))
            ->select(
                'mp.market_id',
                'c.id as commodity_id',
                'c.name as commodity_name',
                'c.reference_price',
                'ri.price',
                'ri.stock',
                'ri.price_change_pct',
                'rs.observed_at'
            )
            ->orderByDesc('rs.observed_at')
            ->orderByDesc('ri.id')
            ->get()
            ->groupBy('market_id');

        return $markets->map(function (object $market) use ($observations) {
            $rows = $observations->get($market->id, collect());
            $latest = $rows->first();
            $avgPrice = $rows->isNotEmpty() ? (float) $rows->avg('price') : null;
            $totalStock = $rows->isNotEmpty() ? (float) $rows->sum('stock') : null;
            $changePct = $latest ? (float) $latest->price_change_pct : null;
            $vsRef = null;
            if ($latest && (float) $latest->reference_price > 0) {
                $vsRef = (((float) $latest->price / (float) $latest->reference_price) - 1) * 100;
            }

            $tone = 'stable';
            if ($totalStock !== null && $totalStock < 30) {
                $tone = 'stock';
            } elseif (($changePct !== null && abs($changePct) >= 10) || ($vsRef !== null && abs($vsRef) >= 15)) {
                $tone = 'spike';
            }

            return [
                'market_id' => (int) $market->id,
                'name' => $market->name,
                'region' => $market->region_name,
                'lat' => (float) $market->latitude,
                'lng' => (float) $market->longitude,
                'avg_price' => $avgPrice,
                'total_stock' => $totalStock,
                'commodity_name' => $latest->commodity_name ?? null,
                'change_pct' => $changePct,
                'tone' => $tone,
                'observed_at' => $latest->observed_at ?? null,
            ];
        })->values();
    }
}
