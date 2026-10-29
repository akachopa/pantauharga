<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPricePageTest extends TestCase
{
    public function test_home_renders_public_price_information(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Harga Hari Ini');
        $response->assertSee('Pasar Terdekat');
        $response->assertSee('Tren');
        $response->assertSee('Tabel');
        $response->assertSee('Lapor Harga');
        $response->assertSee('Kabupaten Bengkulu Tengah');
        $response->assertDontSee('Portal informasi publik');
        $response->assertDontSee('Input Data Baru');
        $response->assertDontSee('Selamat pagi');
        $response->assertDontSee('Beralih Peran');
        $response->assertDontSee('Verifikasi');
    }

    public function test_home_builds_commodity_cards_from_approved_reports_only(): void
    {
        $response = $this->get('/?date=2026-10-27');

        $response->assertOk();
        $response->assertViewHas('commodityCards', function ($cards) {
            return $cards->isNotEmpty()
                && $cards->every(fn (array $card): bool => $card['avg_price'] > 0 && $card['avg_price'] !== 987654.0);
        });
    }

    public function test_home_limits_commodity_cards_to_selected_commodity(): void
    {
        $row = $this->firstApprovedPriceRow();

        $response = $this->get('/?date='.Carbon::parse($row->observed_at)->toDateString().'&commodity_id='.$row->commodity_id);

        $response->assertOk();
        $response->assertViewHas('commodityCards', function ($cards) use ($row) {
            return $cards->count() === 1
                && (int) $cards->first()['id'] === (int) $row->commodity_id;
        });
    }

    public function test_home_hides_draft_prices_from_tabulation(): void
    {
        $point = MonitoringPoint::query()->first();
        $commodity = Commodity::query()->first();

        $series = ReportSeries::query()->create([
            'point_id' => $point->id,
            'observed_at' => '2026-10-27 15:00:00',
        ]);
        $report = Report::query()->create([
            'series_id' => $series->id,
            'source_id' => 1,
            'status' => 'draft',
            'submitted_by' => 2,
            'version_no' => 1,
            'is_demo' => false,
            'notes' => 'Draft uji halaman publik',
            'created_at' => now(),
        ]);
        ReportItem::query()->create([
            'report_id' => $report->id,
            'commodity_id' => $commodity->id,
            'price' => 987654,
            'stock' => 10,
            'unit' => $commodity->base_unit ?? 'kg',
            'price_change_pct' => 0,
            'price_change_amount' => 0,
            'created_at' => now(),
        ]);

        $response = $this->get('/?date=2026-10-27');

        $response->assertOk();
        $response->assertDontSee('987.654');
        $response->assertViewHas('commodityCards', function ($cards) {
            return $cards->every(fn (array $card): bool => abs($card['avg_price'] - 987654) > 1);
        });
    }

    public function test_home_limits_tabulation_to_selected_commodity(): void
    {
        $row = $this->firstApprovedPriceRow();

        $response = $this->get('/tabel?date='.Carbon::parse($row->observed_at)->toDateString().'&commodity_id='.$row->commodity_id);

        $response->assertOk();
        $response->assertViewHas('priceItems', function ($items) use ($row) {
            return $items->isNotEmpty()
                && $items->every(fn ($item): bool => (int) $item->commodity_id === (int) $row->commodity_id);
        });
    }

    public function test_home_limits_tabulation_to_selected_market(): void
    {
        $row = $this->firstApprovedPriceRow();

        $response = $this->get('/tabel?date='.Carbon::parse($row->observed_at)->toDateString().'&market_id='.$row->market_id);

        $response->assertOk();
        $response->assertViewHas('priceItems', function ($items) use ($row) {
            return $items->isNotEmpty()
                && $items->every(fn ($item): bool => (string) $item->market_name === (string) $row->market_name);
        });
    }

    public function test_home_lists_nearby_markets_from_approved_prices(): void
    {
        $row = $this->firstApprovedNearbyRow();

        $response = $this->get('/pasar-terdekat?date='.Carbon::parse($row->observed_at)->toDateString().'&nearby_commodity_id='.$row->commodity_id);

        $response->assertOk();
        $response->assertSee('Pasar Terdekat');
        $response->assertSee('Paling dekat');
        $response->assertSee('Paling hemat');
        $response->assertViewHas('nearbyMarkets', function ($markets) {
            return $markets->isNotEmpty()
                && $markets->every(fn (array $market): bool => $market['official_price'] > 0)
                && $markets->contains(fn (array $market): bool => $market['is_closest'])
                && $markets->contains(fn (array $market): bool => $market['is_cheapest']);
        });
    }

    public function test_palm_fruit_bunch_commodity_is_available_for_manual_price_input(): void
    {
        $palmFruit = Commodity::query()->where('code', 'TBS_SAWIT')->first();

        $this->assertNotNull($palmFruit);
        $this->assertTrue((bool) $palmFruit->is_active);
        $this->assertSame('kg', $palmFruit->base_unit);

        $this->get('/prices')->assertOk()->assertSee('TBS Kelapa Sawit');
        $this->get('/')->assertOk()->assertSee('TBS Kelapa Sawit');
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function publicPageProvider(): array
    {
        return [
            'pasar terdekat' => ['/pasar-terdekat', 'Pasar Terdekat'],
            'tren' => ['/tren', 'Grafik harga'],
            'tabel' => ['/tabel', 'Harga per pasar'],
            'lapor harga' => ['/lapor-harga', 'Kirim laporan'],
        ];
    }

    #[DataProvider('publicPageProvider')]
    public function test_public_section_page_renders_on_its_own_url(string $url, string $heading): void
    {
        $response = $this->get($url);

        $response->assertOk();
        $response->assertSee($heading);
        $response->assertSee('Beranda');
        $response->assertDontSee('Input Data Baru');
    }

    public function test_public_section_page_rejects_unknown_filter(): void
    {
        $this->from('/tabel')->get('/tabel?market_id=999999')
            ->assertRedirect('/tabel')
            ->assertSessionHasErrors('market_id');
    }

    public function test_markets_page_provides_kecamatan_map_with_market_points(): void
    {
        $row = $this->firstApprovedNearbyRow();

        $response = $this->get('/pasar-terdekat?date='.Carbon::parse($row->observed_at)->toDateString().'&nearby_commodity_id='.$row->commodity_id);

        $response->assertOk();
        $response->assertSee('Peta pasar Bengkulu Tengah');
        $response->assertViewHas('mapData', function (array $mapData) use ($row): bool {
            $pricedMarket = collect($mapData['markets'])->firstWhere('id', (int) $row->market_id);

            return str_ends_with($mapData['geojsonUrl'], 'geo/bengkulu-tengah-kecamatan.geojson')
                && $pricedMarket !== null
                && $pricedMarket['price'] > 0
                && collect($mapData['markets'])->every(fn (array $market): bool => $market['lat'] !== 0.0 && $market['lng'] !== 0.0);
        });
    }

    public function test_kecamatan_boundary_file_covers_bengkulu_tengah(): void
    {
        $geojson = json_decode((string) file_get_contents(public_path('geo/bengkulu-tengah-kecamatan.geojson')), true);

        $this->assertSame('FeatureCollection', $geojson['type']);
        $this->assertCount(11, $geojson['features']);
        $this->assertContains('Karang Tinggi', array_column(array_column($geojson['features'], 'properties'), 'nama'));
    }

    public function test_home_rejects_unknown_commodity_filter(): void
    {
        $response = $this->from('/')->get('/?commodity_id=999999');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('commodity_id');
    }

    public function test_dashboard_still_renders_internal_workspace(): void
    {
        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Input Data Baru');
        $response->assertSee('Beralih Peran');
    }

    private function firstApprovedPriceRow(): object
    {
        $row = $this->approvedPriceQuery()->first();

        $this->assertNotNull($row);

        return $row;
    }

    private function firstApprovedNearbyRow(): object
    {
        $row = $this->approvedPriceQuery()
            ->whereNotNull('m.latitude')
            ->whereNotNull('m.longitude')
            ->where('m.is_active', true)
            ->first();

        $this->assertNotNull($row);

        return $row;
    }

    private function approvedPriceQuery(): Builder
    {
        return DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->leftJoin('markets as m', 'm.id', '=', 'mp.market_id')
            ->where('r.status', 'approved')
            ->select(
                'ri.commodity_id',
                'mp.market_id',
                'm.name as market_name',
                'rs.observed_at'
            );
    }
}
