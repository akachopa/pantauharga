<?php

namespace Tests\Feature;

use App\Models\CommunityPriceReport;
use App\Models\Market;
use App\Models\ReportItem;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommunityPriceReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);
    }

    public function test_valid_report_is_stored_pending_without_changing_official_prices(): void
    {
        $row = $this->firstApprovedPriceRow();
        $officialAverage = $this->officialAverage((int) $row->commodity_id, (int) $row->market_id);
        $officialItemCount = ReportItem::query()->count();
        $reportedPrice = 876543;

        $response = $this->from('/')->post(route('community-reports.store'), [
            'commodity_id' => $row->commodity_id,
            'market_id' => $row->market_id,
            'reported_price' => $reportedPrice,
            'reporter_name' => 'Warga Uji Laporan Harga',
            'phone' => '081234567890',
            'note' => 'Harga kios berbeda dari papan resmi.',
        ]);

        $response->assertRedirect(route('public.report'));
        $response->assertSessionHas('success', 'Laporan harga diterima. Data resmi tidak diubah; petugas akan meninjau masukan Anda.');

        $report = CommunityPriceReport::query()
            ->where('reporter_name', 'Warga Uji Laporan Harga')
            ->where('reported_price', $reportedPrice)
            ->latest('id')
            ->first();

        $this->assertNotNull($report);
        $this->assertSame('pending', $report->status);
        $this->assertSame((int) $row->commodity_id, (int) $report->commodity_id);
        $this->assertSame((int) $row->market_id, (int) $report->market_id);
        $this->assertSame((float) $officialAverage, (float) $report->official_price);
        $this->assertSame($officialItemCount, ReportItem::query()->count());

        $home = $this->get('/?date='.$row->observed_date);
        $home->assertOk();
        $home->assertDontSee('876.543');
        $home->assertViewHas('commodityCards', function ($cards) use ($reportedPrice): bool {
            return $cards->every(fn (array $card): bool => abs($card['avg_price'] - $reportedPrice) > 1);
        });

        $report->delete();
    }

    public function test_citizen_report_does_not_appear_as_official_price(): void
    {
        $row = $this->firstApprovedPriceRow();

        $report = CommunityPriceReport::factory()->create([
            'commodity_id' => $row->commodity_id,
            'market_id' => $row->market_id,
            'reported_price' => 765432,
            'official_price' => 18000,
            'status' => 'pending',
        ]);

        $response = $this->get('/?date='.$row->observed_date);

        $response->assertOk();
        $response->assertDontSee('765.432');
        $response->assertViewHas('commodityCards', function ($cards): bool {
            return $cards->every(fn (array $card): bool => abs($card['avg_price'] - 765432) > 1);
        });

        $table = $this->get('/tabel?date='.$row->observed_date);

        $table->assertOk();
        $table->assertDontSee('765.432');
        $table->assertViewHas('priceItems', function ($items): bool {
            return $items->every(fn ($item): bool => abs((float) $item->price - 765432) > 1);
        });

        $report->delete();
    }

    public function test_rejects_empty_reported_price(): void
    {
        $row = $this->firstApprovedPriceRow();

        $response = $this->from('/')->post(route('community-reports.store'), [
            'commodity_id' => $row->commodity_id,
            'market_id' => $row->market_id,
            'reported_price' => '',
            'reporter_name' => 'Warga Uji Validasi',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'reported_price' => 'Isi harga yang Anda lihat.',
        ]);
        $this->assertDatabaseMissing('community_price_reports', [
            'reporter_name' => 'Warga Uji Validasi',
        ]);
    }

    public function test_rejects_unknown_commodity(): void
    {
        $market = Market::query()->first();

        $response = $this->from('/')->post(route('community-reports.store'), [
            'commodity_id' => 999999,
            'market_id' => $market->id,
            'reported_price' => 17000,
            'reporter_name' => 'Warga Uji Komoditi',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors([
            'commodity_id' => 'Komoditi tidak ditemukan.',
        ]);
        $this->assertDatabaseMissing('community_price_reports', [
            'reporter_name' => 'Warga Uji Komoditi',
        ]);
    }

    public function test_officer_can_review_report_without_changing_official_prices(): void
    {
        $row = $this->firstApprovedPriceRow();
        $officialItemCount = ReportItem::query()->count();
        $report = CommunityPriceReport::factory()->create([
            'commodity_id' => $row->commodity_id,
            'market_id' => $row->market_id,
            'status' => 'pending',
        ]);

        $response = $this->from(route('prices.index', ['tab' => 'koreksi']))
            ->post(route('prices.corrections.review', $report), [
                'status' => 'reviewed',
            ]);

        $response->assertRedirect(route('prices.index', ['tab' => 'koreksi']));
        $response->assertSessionHas('success', 'Laporan warga berhasil ditinjau.');
        $this->assertSame('reviewed', $report->fresh()->status);
        $this->assertSame($officialItemCount, ReportItem::query()->count());

        $report->delete();
    }

    private function firstApprovedPriceRow(): object
    {
        $row = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->where('r.status', 'approved')
            ->select(
                'ri.commodity_id',
                'mp.market_id',
                DB::raw('DATE(rs.observed_at) as observed_date')
            )
            ->first();

        $this->assertNotNull($row);

        return $row;
    }

    private function officialAverage(int $commodityId, int $marketId): float
    {
        $average = DB::table('report_items as ri')
            ->join('reports as r', 'r.id', '=', 'ri.report_id')
            ->join('report_series as rs', 'rs.id', '=', 'r.series_id')
            ->join('monitoring_points as mp', 'mp.id', '=', 'rs.point_id')
            ->where('r.status', 'approved')
            ->where('ri.commodity_id', $commodityId)
            ->where('mp.market_id', $marketId)
            ->avg('ri.price');

        return round((float) $average, 2);
    }
}
