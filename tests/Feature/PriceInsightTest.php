<?php

namespace Tests\Feature;

use App\PolicyRuleEvaluator;
use App\PriceInsight;
use Carbon\Carbon;
use Tests\TestCase;

class PriceInsightTest extends TestCase
{
    public function test_dashboard_renders_live_trend_and_regional_spread(): void
    {
        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Tren Pergerakan Harga');
        $response->assertSee('Selisih harga antar kecamatan');
        $response->assertSee('Vs acuan');
        $response->assertSee('Taba Penanjung');
    }

    public function test_reports_page_uses_date_filters_and_regional_spread(): void
    {
        $response = $this->get('/reports?from=2026-10-21&to=2026-10-28');

        $response->assertOk();
        $response->assertSee('Tren harga komoditi');
        $response->assertSee('Selisih harga antar kecamatan');
        $response->assertSee('Sebaran wilayah');
        $response->assertSee('Cabai Merah Keriting');
    }

    public function test_trend_series_maps_report_items_to_polyline_points(): void
    {
        $chart = app(PriceInsight::class)->trendSeries(
            Carbon::parse('2026-10-21'),
            Carbon::parse('2026-10-28'),
            [5],
        );

        $this->assertSame(8, count($chart['days']));
        $this->assertSame('2026-10-27', $chart['days'][6]);
        $this->assertNotSame('', $chart['series'][0]['points']);
        $this->assertSame(55200.0, $chart['series'][0]['values'][6]);
    }

    public function test_regional_spread_compares_kecamatan_average_to_reference_price(): void
    {
        $rows = app(PriceInsight::class)->regionalSpread(
            Carbon::parse('2026-10-21'),
            Carbon::parse('2026-10-28'),
        );

        $cabaiTaba = $rows->first(function (object $row): bool {
            return $row->commodity_name === 'Cabai Merah Keriting'
                && str_contains($row->region_name, 'Taba Penanjung');
        });

        $this->assertNotNull($cabaiTaba);
        $this->assertSame(52000.0, $cabaiTaba->avg_price);
        $this->assertSame(8.3, $cabaiTaba->vs_reference_pct);
        $this->assertGreaterThan(8.0, $cabaiTaba->region_spread_pct);
    }

    public function test_policy_evaluation_triggers_when_live_price_and_stock_days_cross_threshold(): void
    {
        $evaluation = app(PolicyRuleEvaluator::class)->evaluate();
        $cabaiCases = collect($evaluation['cases'])->where('commodity', 'Cabai Merah Keriting');

        $this->assertSame('live', $evaluation['run']['mode']);
        $this->assertSame($evaluation['run']['matched_count'], count($evaluation['cases']));
        $this->assertNotEmpty($cabaiCases);
        $this->assertTrue($cabaiCases->contains(fn (array $case): bool => str_contains($case['point'], 'Taba Penanjung')));

        $response = $this->get('/policies?tab=terpicu');
        $response->assertOk();
        $response->assertSee('evaluasi data harga live');
        $response->assertSee('Cabai Merah Keriting');
    }

    public function test_rule_comparator_thresholds(): void
    {
        $evaluator = app(PolicyRuleEvaluator::class);

        $this->assertTrue($evaluator->compare(12.5, 'gt', 10));
        $this->assertFalse($evaluator->compare(10, 'gt', 10));
        $this->assertTrue($evaluator->compare(0.8, 'lt', 3));
        $this->assertTrue($evaluator->compare(10, 'gte', 10));
    }
}
