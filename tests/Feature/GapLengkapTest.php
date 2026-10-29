<?php

namespace Tests\Feature;

use App\Models\ActionPlan;
use App\Models\Business;
use App\Models\Commodity;
use App\Models\CommunityPriceReport;
use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\SupplyLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GapLengkapTest extends TestCase
{
    public function test_public_price_cards_show_last_updated(): void
    {
        $date = DB::table('report_series as rs')
            ->join('reports as r', 'r.series_id', '=', 'rs.id')
            ->where('r.status', 'approved')
            ->max('rs.observed_at');

        $this->assertNotNull($date);
        $dateString = substr((string) $date, 0, 10);

        $response = $this->get('/?date='.$dateString);
        $response->assertOk();
        $response->assertSee('Diperbarui', false);
    }

    public function test_correction_audit_trail_is_shown_after_review(): void
    {
        $report = CommunityPriceReport::factory()->create([
            'status' => 'pending',
            'reporter_name' => 'Warga Jejak Gap',
        ]);

        $this->post(route('prices.corrections.review', $report), [
            'status' => 'returned',
            'review_note' => 'Perbaiki harga.',
        ])->assertRedirect();

        $page = $this->get(route('prices.index', ['tab' => 'koreksi']));
        $page->assertOk();
        $page->assertSee('Jejak', false);
        $page->assertSee('review_returned', false);

        $report->delete();
    }

    public function test_report_evidence_route_streams_file_or_404(): void
    {
        $report = Report::query()->orderBy('id')->first();
        $this->assertNotNull($report);

        $this->get(route('prices.evidence', $report))->assertNotFound();

        Storage::disk('local')->put('report-evidence/test-gap.pdf', 'fake-pdf');
        $report->update(['evidence_path' => 'report-evidence/test-gap.pdf']);

        $this->get(route('prices.evidence', $report))->assertOk();

        Storage::disk('local')->delete('report-evidence/test-gap.pdf');
        $report->update(['evidence_path' => null]);
    }

    public function test_action_evidence_route_streams_file(): void
    {
        $action = ActionPlan::query()->orderBy('id')->first();
        $this->assertNotNull($action);

        Storage::disk('local')->put('action-evidence/test-gap.pdf', 'fake-pdf');
        $action->update(['evidence_path' => 'action-evidence/test-gap.pdf']);

        $this->get(route('actions.evidence', $action))->assertOk();

        Storage::disk('local')->delete('action-evidence/test-gap.pdf');
        $action->update(['evidence_path' => null]);
    }

    public function test_radar_page_loads_with_market_markers(): void
    {
        $market = Market::query()->whereNotNull('latitude')->whereNotNull('longitude')->first();
        $this->assertNotNull($market);

        $response = $this->get(route('radar.index', ['date' => '2026-10-27']));
        $response->assertOk();
        $response->assertSee('Radar', false);
        $response->assertSee($market->name, false);
        $response->assertSee('leaflet', false);
    }

    public function test_supply_link_and_shipment_can_be_created(): void
    {
        $business = Business::query()->firstOrFail();
        $point = MonitoringPoint::query()->firstOrFail();
        $commodity = Commodity::query()->firstOrFail();

        $this->post(route('supply.links.store'), [
            'business_id' => $business->id,
            'point_id' => $point->id,
            'commodity_id' => $commodity->id,
            'notes' => 'Relasi uji gap lengkap',
        ])->assertRedirect(route('supply.index', ['tab' => 'relasi']));

        $link = SupplyLink::query()->where('notes', 'Relasi uji gap lengkap')->latest('id')->first();
        $this->assertNotNull($link);

        $this->post(route('supply.shipments.store'), [
            'supply_link_id' => $link->id,
            'scheduled_on' => '2026-10-30',
            'quantity' => 250,
            'unit' => 'kg',
            'status' => 'planned',
            'notes' => 'Kiriman uji',
        ])->assertRedirect(route('supply.index', ['tab' => 'jadwal']));

        $this->assertDatabaseHas('supply_shipments', [
            'supply_link_id' => $link->id,
            'quantity' => 250,
            'status' => 'planned',
        ]);

        $page = $this->get(route('supply.index', ['tab' => 'relasi']));
        $page->assertOk();
        $page->assertSee($business->name, false);
    }
}
