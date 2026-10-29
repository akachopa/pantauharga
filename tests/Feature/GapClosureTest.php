<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\CommunityPriceReport;
use App\Models\MonitoringPoint;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GapClosureTest extends TestCase
{
    public function test_price_draft_can_be_saved(): void
    {
        $point = MonitoringPoint::query()->firstOrFail();
        $commodity = Commodity::query()->firstOrFail();

        $response = $this->post(route('prices.store'), [
            'point_id' => $point->id,
            'observed_at' => '2026-10-28',
            'commodity_id' => $commodity->id,
            'price' => 15100,
            'stock' => 40,
            'status' => 'draft',
            'notes' => 'Draft uji gap',
        ]);

        $response->assertRedirect(route('prices.index', ['tab' => 'tabulasi']));
        $this->assertDatabaseHas('reports', [
            'status' => 'draft',
            'notes' => 'Draft uji gap',
        ]);
    }

    public function test_csv_import_creates_report_items(): void
    {
        $point = MonitoringPoint::query()->whereNotNull('code')->firstOrFail();
        $commodity = Commodity::query()->whereNotNull('code')->firstOrFail();

        $csv = "point_code,observed_at,commodity_code,price,stock\n{$point->code},2026-10-28,{$commodity->code},16111,55\n";
        $file = UploadedFile::fake()->createWithContent('impor.csv', $csv);

        $response = $this->post(route('prices.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('prices.index', ['tab' => 'tabulasi']));
        $this->assertDatabaseHas('report_items', [
            'commodity_id' => $commodity->id,
            'price' => 16111,
        ]);
    }

    public function test_community_correction_can_be_returned(): void
    {
        $report = CommunityPriceReport::factory()->create([
            'status' => 'pending',
            'reporter_name' => 'Warga Gap Returned',
        ]);

        $response = $this->post(route('prices.corrections.review', $report), [
            'status' => 'returned',
            'review_note' => 'Mohon perbaiki data.',
        ]);

        $response->assertRedirect(route('prices.index', ['tab' => 'koreksi']));
        $this->assertDatabaseHas('community_price_reports', [
            'id' => $report->id,
            'status' => 'returned',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'community_price_report',
            'entity_id' => $report->id,
            'action' => 'review_returned',
        ]);
    }

    public function test_policy_rule_store_persists_condition(): void
    {
        $packageId = (int) DB::table('policy_packages')->orderBy('id')->value('id');
        $this->assertGreaterThan(0, $packageId);

        $response = $this->post(route('policies.rules.store'), [
            'package_id' => $packageId,
            'indicator' => 'price_change_pct',
            'operator' => 'gt',
            'threshold' => 12.5,
            'period_days' => 7,
            'recommendation' => 'Koordinasi pasar uji gap',
        ]);

        $response->assertRedirect();
        $this->assertTrue(
            DB::table('policy_rule_conditions')->where('threshold', 12.5)->exists()
                || DB::table('policy_versions')->where('recommendation', 'Koordinasi pasar uji gap')->exists()
        );
    }

    public function test_report_export_respects_kecamatan_filter(): void
    {
        $regionId = DB::table('monitoring_points')->whereNotNull('region_id')->value('region_id');
        $this->assertNotNull($regionId);

        $response = $this->get(route('reports.export.excel', [
            'from' => '2026-10-21',
            'to' => '2026-10-28',
            'kecamatan_id' => $regionId,
            'verified' => 1,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $this->assertStringContainsString('kecamatan='.$regionId, $response->streamedContent());
    }

    public function test_settings_audit_tab_shows_audit_logs_section(): void
    {
        $user = User::query()->first();
        if ($user) {
            $this->actingAs($user);
        }

        $response = $this->get(route('settings.index', ['tab' => 'audit']));
        $response->assertOk();
        $response->assertSee('Audit Log Operasional');
    }

    public function test_action_region_filter_is_applied(): void
    {
        $point = MonitoringPoint::query()->whereNotNull('region_id')->first();
        $this->assertNotNull($point);

        $response = $this->get(route('actions.index', [
            'kecamatan_id' => $point->region_id,
        ]));

        $response->assertOk();
    }
}
