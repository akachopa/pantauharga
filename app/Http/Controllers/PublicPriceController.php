<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\CommunityPriceReport;
use App\Models\Market;
use App\Models\Region;
use App\PriceInsight;
use App\PublicPriceInsight;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicPriceController extends Controller
{
    public function __construct(
        private PriceInsight $priceInsight,
        private PublicPriceInsight $publicPriceInsight,
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $commodityCards = $this->cardsWithCitizenReports($filters);

        return view('public.home', $this->sharedViewData($filters) + [
            'commodityCards' => $commodityCards,
            'insights' => $this->insightsFor($commodityCards, $filters)->take(3),
        ]);
    }

    public function markets(Request $request): View
    {
        $filters = $this->filters($request);

        $nearbyCommodityId = $request->integer('nearby_commodity_id') ?: $filters['commodityId'];
        if (! $nearbyCommodityId) {
            $nearbyCommodityId = $this->approvedItemsQuery($filters['kecamatanId'], $filters['marketId'], null)
                ->whereDate('rs.observed_at', $filters['date'])
                ->orderBy('c.name')
                ->value('c.id');
        }

        [$originLat, $originLng, $originSource] = $this->nearbyOrigin($request, $filters['marketId']);
        $nearbyMarkets = $nearbyCommodityId
            ? $this->nearbyMarkets($filters['date'], (int) $nearbyCommodityId, $originLat, $originLng)
            : collect();

        return view('public.markets', $this->sharedViewData($filters) + [
            'nearbyMarkets' => $nearbyMarkets,
            'mapData' => [
                'geojsonUrl' => asset('geo/bengkulu-tengah-kecamatan.geojson'),
                'markets' => $this->mapMarkets($filters['date'], $nearbyCommodityId ? (int) $nearbyCommodityId : null, $nearbyMarkets),
                'origin' => ['lat' => $originLat, 'lng' => $originLng, 'source' => $originSource],
                'regions' => $filters['kecamatans']->mapWithKeys(fn (Region $region): array => [mb_strtoupper($region->name) => $region->id]),
                'selectedRegionId' => $filters['kecamatanId'],
                'filterUrl' => route('public.markets', array_diff_key($request->query(), array_flip(['kecamatan_id', 'page']))),
            ],
            'nearbyCommodity' => $nearbyCommodityId ? $filters['commodities']->firstWhere('id', $nearbyCommodityId) : null,
            'nearbyCommodityId' => $nearbyCommodityId,
            'originSource' => $originSource,
        ]);
    }

    public function trends(Request $request): View
    {
        $filters = $this->filters($request);
        $chartPeriod = (int) $request->get('period', 7);
        if (! in_array($chartPeriod, [7, 30], true)) {
            $chartPeriod = 7;
        }

        $endDate = Carbon::parse($filters['date']);
        $startDate = $endDate->copy()->subDays($chartPeriod - 1);
        $chartCommodityIds = $this->chartCommodityIds(
            $startDate->toDateString(),
            $endDate->toDateString(),
            $filters['kecamatanId'],
            $filters['marketId'],
            $filters['commodityId'],
        );

        $hasChartData = $chartCommodityIds !== [];
        $commodityCards = $this->cardsWithCitizenReports($filters);

        return view('public.trends', $this->sharedViewData($filters) + [
            'chartPeriod' => $chartPeriod,
            'hasChartData' => $hasChartData,
            'priceChart' => $hasChartData
                ? $this->priceInsight->trendSeries($startDate, $endDate, $chartCommodityIds, $filters['kecamatanId'], $filters['marketId'], true)
                : ['days' => [], 'labels' => [], 'series' => [], 'yLabels' => [], 'maxPrice' => 0],
            'highlights' => $this->highlights($commodityCards),
            'insights' => $this->insightsFor($commodityCards, $filters),
        ]);
    }

    public function table(Request $request): View
    {
        $filters = $this->filters($request);

        $priceItems = $this->approvedItemsQuery($filters['kecamatanId'], $filters['marketId'], $filters['commodityId'])
            ->whereDate('rs.observed_at', $filters['date'])
            ->select(
                'ri.id as item_id',
                'c.id as commodity_id',
                'c.name as commodity_name',
                'c.base_unit',
                'ri.price',
                'ri.stock',
                'ri.price_change_pct',
                'm.name as market_name',
                'rs.observed_at'
            )
            ->orderBy('c.name')
            ->orderBy('m.name')
            ->orderByDesc('rs.observed_at')
            ->orderByDesc('ri.id')
            ->paginate(15)
            ->withQueryString();

        return view('public.table', $this->sharedViewData($filters) + [
            'priceItems' => $priceItems,
        ]);
    }

    public function report(Request $request): View
    {
        $filters = $this->filters($request);

        return view('public.report', $this->sharedViewData($filters));
    }

    /**
     * @return array{
     *     date: string,
     *     kecamatanId: ?int,
     *     marketId: ?int,
     *     commodityId: ?int,
     *     query: array<string, mixed>,
     *     hasActiveFilters: bool,
     *     lastUpdated: ?Carbon,
     *     kecamatans: Collection<int, Region>,
     *     markets: Collection<int, Market>,
     *     commodities: Collection<int, Commodity>
     * }
     */
    private function filters(Request $request): array
    {
        $request->validate([
            'date' => ['nullable', 'date'],
            'kecamatan_id' => ['nullable', 'integer', 'exists:regions,id'],
            'market_id' => ['nullable', 'integer', 'exists:markets,id'],
            'commodity_id' => ['nullable', 'integer', 'exists:commodities,id'],
            'nearby_commodity_id' => ['nullable', 'integer', 'exists:commodities,id'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $latestObserved = DB::table('report_series as rs')
            ->join('reports as r', 'r.series_id', '=', 'rs.id')
            ->where('r.status', 'approved')
            ->max('rs.observed_at');

        $date = $request->input('date')
            ?? ($latestObserved ? Carbon::parse($latestObserved)->toDateString() : now()->toDateString());
        $kecamatanId = $request->integer('kecamatan_id') ?: null;
        $marketId = $request->integer('market_id') ?: null;
        $commodityId = $request->integer('commodity_id') ?: null;

        $lastObservedOnDate = $this->approvedItemsQuery($kecamatanId, $marketId, $commodityId)
            ->whereDate('rs.observed_at', $date)
            ->max('rs.observed_at');
        $lastObserved = $lastObservedOnDate ?? $latestObserved;

        return [
            'date' => $date,
            'kecamatanId' => $kecamatanId,
            'marketId' => $marketId,
            'commodityId' => $commodityId,
            'query' => array_filter(
                $request->only(['date', 'kecamatan_id', 'market_id', 'commodity_id']),
                fn ($value): bool => $value !== null && $value !== '',
            ),
            'hasActiveFilters' => $kecamatanId !== null || $marketId !== null || $commodityId !== null || $request->filled('date'),
            'lastUpdated' => $lastObserved ? Carbon::parse($lastObserved) : null,
            'kecamatans' => Region::where('parent_id', 1)->orderBy('name')->get(),
            'markets' => Market::where('is_active', true)->orderBy('name')->get(),
            'commodities' => Commodity::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function sharedViewData(array $filters): array
    {
        return [
            'kecamatans' => $filters['kecamatans'],
            'markets' => $filters['markets'],
            'commodities' => $filters['commodities'],
            'selectedDate' => $filters['date'],
            'selectedKecamatan' => $filters['kecamatanId'],
            'selectedMarket' => $filters['marketId'],
            'selectedCommodity' => $filters['commodityId'],
            'hasActiveFilters' => $filters['hasActiveFilters'],
            'lastUpdated' => $filters['lastUpdated'],
            'publicFilterQuery' => $filters['query'],
            'shortDateLabel' => Carbon::parse($filters['date'])->translatedFormat('d M Y'),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    private function baseCommodityCards(array $filters): Collection
    {
        $endDate = Carbon::parse($filters['date']);

        return $this->commodityCards(
            $filters['date'],
            $endDate->copy()->subDays(6),
            $endDate,
            $filters['kecamatanId'],
            $filters['marketId'],
            $filters['commodityId'],
        );
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    private function cardsWithCitizenReports(array $filters): Collection
    {
        $citizenReportCounts = CommunityPriceReport::query()
            ->whereDate('created_at', now()->toDateString())
            ->whereIn('status', ['pending', 'reviewed'])
            ->selectRaw('commodity_id, COUNT(*) as total')
            ->groupBy('commodity_id')
            ->pluck('total', 'commodity_id');

        return $this->baseCommodityCards($filters)->map(function (array $card) use ($citizenReportCounts): array {
            $card['citizen_report_count'] = (int) $citizenReportCounts->get($card['id'], 0);

            return $card;
        });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $commodityCards
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array<string, mixed>>
     */
    private function insightsFor(Collection $commodityCards, array $filters): Collection
    {
        $endDate = Carbon::parse($filters['date']);

        return $this->publicPriceInsight->build(
            $commodityCards,
            $this->dailyAverages(
                $endDate->copy()->subDays(13),
                $endDate,
                $commodityCards->pluck('id')->all(),
                $filters['kecamatanId'],
                $filters['marketId'],
            ),
        );
    }

    private function approvedItemsQuery(?int $kecamatanId, ?int $marketId, ?int $commodityId): Builder
    {
        $query = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->leftJoin('markets as m', 'm.id', '=', 'mp.market_id')
            ->join('commodities as c', 'c.id', '=', 'ri.commodity_id')
            ->where('r.status', 'approved');

        return $this->applyLocationFilters($query, $kecamatanId, $marketId, $commodityId);
    }

    private function applyLocationFilters(Builder $query, ?int $kecamatanId, ?int $marketId, ?int $commodityId): Builder
    {
        if ($kecamatanId) {
            $query->where('mp.region_id', $kecamatanId);
        }
        if ($marketId) {
            $query->where('mp.market_id', $marketId);
        }
        if ($commodityId) {
            $query->where('c.id', $commodityId);
        }

        return $query;
    }

    /**
     * @return list<int>
     */
    private function chartCommodityIds(
        string $startDate,
        string $endDate,
        ?int $kecamatanId,
        ?int $marketId,
        ?int $commodityId,
    ): array {
        if ($commodityId) {
            $hasData = $this->approvedItemsQuery($kecamatanId, $marketId, $commodityId)
                ->whereDate('rs.observed_at', '>=', $startDate)
                ->whereDate('rs.observed_at', '<=', $endDate)
                ->exists();

            return $hasData ? [$commodityId] : [];
        }

        return $this->approvedItemsQuery($kecamatanId, $marketId, null)
            ->whereDate('rs.observed_at', '>=', $startDate)
            ->whereDate('rs.observed_at', '<=', $endDate)
            ->select('c.id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('c.id')
            ->orderByDesc('total')
            ->orderBy('c.id')
            ->limit(4)
            ->pluck('c.id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function commodityCards(
        string $selectedDate,
        Carbon $sparkStart,
        Carbon $endDate,
        ?int $kecamatanId,
        ?int $marketId,
        ?int $commodityId,
    ): Collection {
        $rows = $this->approvedItemsQuery($kecamatanId, $marketId, $commodityId)
            ->whereDate('rs.observed_at', $selectedDate)
            ->select(
                'c.id',
                'c.name',
                'c.category',
                'c.base_unit',
                'c.reference_price',
            )
            ->selectRaw('AVG(ri.price) as avg_price')
            ->selectRaw('MIN(ri.price) as min_price')
            ->selectRaw('MAX(ri.price) as max_price')
            ->selectRaw('COUNT(DISTINCT mp.market_id) as market_count')
            ->selectRaw('MAX(rs.observed_at) as last_updated')
            ->groupBy('c.id', 'c.name', 'c.category', 'c.base_unit', 'c.reference_price')
            ->orderBy('c.name')
            ->get();

        $previousAverages = $this->previousAverages($selectedDate, $kecamatanId, $marketId, $commodityId);
        $cardIds = $rows->pluck('id')->map(fn ($id): int => (int) $id)->all();
        $sparklines = $this->sparklineSeries($sparkStart, $endDate, $cardIds, $kecamatanId, $marketId);

        return $rows->map(function (object $row) use ($previousAverages, $sparklines): array {
            $avgPrice = (float) $row->avg_price;
            $previous = $previousAverages->get((int) $row->id);
            $changePct = ($previous !== null && $previous > 0)
                ? round((($avgPrice - $previous) / $previous) * 100, 2)
                : null;
            $reference = $row->reference_price !== null ? (float) $row->reference_price : null;
            $vsReferencePct = ($reference !== null && $reference > 0)
                ? round((($avgPrice - $reference) / $reference) * 100, 1)
                : null;

            $sparkline = $sparklines[(int) $row->id] ?? ['points' => '', 'color' => '#0D9462'];

            return [
                'id' => (int) $row->id,
                'name' => $row->name,
                'category' => $row->category,
                'base_unit' => $row->base_unit,
                'avg_price' => $avgPrice,
                'min_price' => (float) $row->min_price,
                'max_price' => (float) $row->max_price,
                'market_count' => (int) $row->market_count,
                'change_pct' => $changePct,
                'reference_price' => $reference,
                'vs_reference_pct' => $vsReferencePct,
                'reference_status' => $this->referenceStatus($vsReferencePct),
                'sparkline' => $sparkline['points'],
                'sparkline_color' => $this->changeColor($changePct, $sparkline['color']),
                'last_updated' => $row->last_updated,
            ];
        })->values();
    }

    /**
     * Average approved price per observed day, without carrying values across empty days.
     *
     * @param  list<int>  $commodityIds
     * @return array<int, array<string, float>>
     */
    private function dailyAverages(
        Carbon $start,
        Carbon $end,
        array $commodityIds,
        ?int $kecamatanId,
        ?int $marketId,
    ): array {
        if ($commodityIds === []) {
            return [];
        }

        return $this->approvedItemsQuery($kecamatanId, $marketId, null)
            ->whereIn('c.id', $commodityIds)
            ->whereDate('rs.observed_at', '>=', $start->toDateString())
            ->whereDate('rs.observed_at', '<=', $end->toDateString())
            ->select('c.id as commodity_id')
            ->selectRaw('DATE(rs.observed_at) as observed_date')
            ->selectRaw('AVG(ri.price) as avg_price')
            ->groupBy('c.id', DB::raw('DATE(rs.observed_at)'))
            ->orderBy('observed_date')
            ->get()
            ->groupBy(fn (object $row): int => (int) $row->commodity_id)
            ->map(fn (Collection $rows): array => $rows
                ->mapWithKeys(fn (object $row): array => [(string) $row->observed_date => (float) $row->avg_price])
                ->all())
            ->all();
    }

    /**
     * @return Collection<int, float>
     */
    private function previousAverages(
        string $selectedDate,
        ?int $kecamatanId,
        ?int $marketId,
        ?int $commodityId,
    ): Collection {
        $rows = $this->approvedItemsQuery($kecamatanId, $marketId, $commodityId)
            ->whereDate('rs.observed_at', '<', $selectedDate)
            ->select('c.id as commodity_id')
            ->selectRaw('DATE(rs.observed_at) as observed_date')
            ->selectRaw('AVG(ri.price) as avg_price')
            ->groupBy('c.id', DB::raw('DATE(rs.observed_at)'))
            ->orderByDesc('observed_date')
            ->get();

        return $rows
            ->groupBy(fn (object $row): int => (int) $row->commodity_id)
            ->map(fn (Collection $group): float => (float) $group->first()->avg_price);
    }

    /**
     * @param  list<int>  $commodityIds
     * @return array<int, array{points: string, color: string}>
     */
    private function sparklineSeries(
        Carbon $start,
        Carbon $end,
        array $commodityIds,
        ?int $kecamatanId,
        ?int $marketId,
    ): array {
        if ($commodityIds === []) {
            return [];
        }

        $trend = $this->priceInsight->trendSeries(
            $start,
            $end,
            $commodityIds,
            $kecamatanId,
            $marketId,
            true,
        );

        $series = [];
        foreach ($trend['series'] as $item) {
            $series[$item['id']] = [
                'points' => $this->sparklinePoints($item['values']),
                'color' => $item['color'],
            ];
        }

        return $series;
    }

    /**
     * @param  list<float>  $values
     */
    private function sparklinePoints(array $values): string
    {
        if ($values === []) {
            return '';
        }

        $min = min($values);
        $max = max($values);
        $span = max($max - $min, 1.0);
        $count = count($values);
        $width = 88.0;
        $height = 28.0;
        $points = [];

        foreach ($values as $index => $value) {
            $x = $count === 1 ? $width / 2 : ($index / ($count - 1)) * $width;
            $y = $height - (($value - $min) / $span) * ($height - 2) - 1;
            $points[] = round($x, 1).','.round($y, 1);
        }

        return implode(' ', $points);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $cards
     * @return array{up: ?array<string, mixed>, down: ?array<string, mixed>, stable: ?array<string, mixed>}
     */
    private function highlights(Collection $cards): array
    {
        $withChange = $cards->filter(fn (array $card): bool => $card['change_pct'] !== null);

        return [
            'up' => $withChange->sortByDesc('change_pct')->first(fn (array $card): bool => $card['change_pct'] > 0),
            'down' => $withChange->sortBy('change_pct')->first(fn (array $card): bool => $card['change_pct'] < 0),
            'stable' => $withChange->sortBy(fn (array $card): float => abs((float) $card['change_pct']))->first(),
        ];
    }

    private function referenceStatus(?float $vsReferencePct): string
    {
        if ($vsReferencePct === null) {
            return 'none';
        }
        if (abs($vsReferencePct) < 2) {
            return 'on';
        }

        return $vsReferencePct > 0 ? 'above' : 'below';
    }

    private function changeColor(?float $changePct, string $fallback): string
    {
        if ($changePct === null || abs($changePct) < 0.05) {
            return $fallback;
        }

        return $changePct > 0 ? '#E11D48' : '#0D9462';
    }

    /**
     * @return array{0: float, 1: float, 2: string}
     */
    private function nearbyOrigin(Request $request, ?int $selectedMarket): array
    {
        if ($request->filled('lat') && $request->filled('lng')) {
            return [(float) $request->input('lat'), (float) $request->input('lng'), 'lokasi'];
        }

        if ($selectedMarket) {
            $market = Market::query()->find($selectedMarket);
            if ($market?->latitude && $market->longitude) {
                return [(float) $market->latitude, (float) $market->longitude, 'pasar'];
            }
        }

        return [-3.745, 102.41, 'kabupaten'];
    }

    /**
     * Active markets with coordinates, plus the approved average price of the chosen commodity.
     *
     * @param  Collection<int, array<string, mixed>>  $nearbyMarkets
     * @return list<array{id: int, name: string, region: ?string, lat: float, lng: float, price: ?float, is_closest: bool, is_cheapest: bool}>
     */
    private function mapMarkets(string $selectedDate, ?int $commodityId, Collection $nearbyMarkets): array
    {
        $prices = $commodityId
            ? $this->approvedItemsQuery(null, null, $commodityId)
                ->whereDate('rs.observed_at', $selectedDate)
                ->whereNotNull('mp.market_id')
                ->groupBy('mp.market_id')
                ->selectRaw('mp.market_id, AVG(ri.price) as avg_price')
                ->pluck('avg_price', 'mp.market_id')
            : collect();
        $nearbyById = $nearbyMarkets->keyBy('id');

        return DB::table('markets as m')
            ->leftJoin('regions as reg', 'reg.id', '=', 'm.region_id')
            ->where('m.is_active', true)
            ->whereNotNull('m.latitude')
            ->whereNotNull('m.longitude')
            ->orderBy('m.name')
            ->get(['m.id', 'm.name', 'm.latitude', 'm.longitude', 'reg.name as region_name'])
            ->map(fn (object $market): array => [
                'id' => (int) $market->id,
                'name' => $market->name,
                'region' => $market->region_name,
                'lat' => (float) $market->latitude,
                'lng' => (float) $market->longitude,
                'price' => $prices->has($market->id) ? round((float) $prices->get($market->id)) : null,
                'is_closest' => (bool) ($nearbyById->get((int) $market->id)['is_closest'] ?? false),
                'is_cheapest' => (bool) ($nearbyById->get((int) $market->id)['is_cheapest'] ?? false),
            ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function nearbyMarkets(string $selectedDate, int $commodityId, float $originLat, float $originLng): Collection
    {
        $distanceSql = '6371 * ACOS(LEAST(1, GREATEST(-1, COS(RADIANS(?)) * COS(RADIANS(m.latitude)) * COS(RADIANS(m.longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(m.latitude)))))';

        $rows = DB::table('markets as m')
            ->join('monitoring_points as mp', 'mp.market_id', '=', 'm.id')
            ->join('report_series as rs', 'rs.point_id', '=', 'mp.id')
            ->join('reports as r', 'r.series_id', '=', 'rs.id')
            ->join('report_items as ri', 'ri.report_id', '=', 'r.id')
            ->where('r.status', 'approved')
            ->where('m.is_active', true)
            ->whereNotNull('m.latitude')
            ->whereNotNull('m.longitude')
            ->whereDate('rs.observed_at', $selectedDate)
            ->where('ri.commodity_id', $commodityId)
            ->select('m.id', 'm.name', 'm.address', 'm.market_days', 'm.latitude', 'm.longitude')
            ->selectRaw('AVG(ri.price) as official_price')
            ->selectRaw($distanceSql.' as distance_km', [$originLat, $originLng, $originLat])
            ->groupBy('m.id', 'm.name', 'm.address', 'm.market_days', 'm.latitude', 'm.longitude')
            ->orderBy('distance_km')
            ->limit(5)
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $average = (float) $rows->avg('official_price');
        $closestId = (int) $rows->first()->id;
        $cheapestId = (int) $rows->sortBy('official_price')->first()->id;

        return $rows->map(function (object $row) use ($average, $closestId, $cheapestId): array {
            $price = (float) $row->official_price;

            return [
                'id' => (int) $row->id,
                'name' => $row->name,
                'address' => $row->address,
                'market_days' => $row->market_days,
                'official_price' => $price,
                'distance_km' => round((float) $row->distance_km, 1),
                'diff_pct' => $average > 0 ? round((($price - $average) / $average) * 100, 1) : 0.0,
                'is_closest' => (int) $row->id === $closestId,
                'is_cheapest' => (int) $row->id === $cheapestId,
            ];
        })->values();
    }
}
