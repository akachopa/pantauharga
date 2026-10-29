<?php

namespace Tests\Feature;

use App\PublicPriceInsight;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PublicPriceInsightTest extends TestCase
{
    public function test_price_spike_produces_danger_insight(): void
    {
        $insights = $this->build([$this->card(['change_pct' => 8.0])]);

        $spike = $insights->firstWhere('theme', 'lonjakan');
        $this->assertNotNull($spike);
        $this->assertSame('danger', $spike['tone']);
        $this->assertSame('Cabai Merah naik 8,0%', $spike['title']);
    }

    public function test_change_below_spike_threshold_is_not_a_spike(): void
    {
        $insights = $this->build([$this->card(['change_pct' => 4.9])]);

        $this->assertNull($insights->firstWhere('theme', 'lonjakan'));
    }

    public function test_price_drop_produces_success_insight(): void
    {
        $insights = $this->build([$this->card(['change_pct' => -6.0])]);

        $this->assertSame('success', $insights->firstWhere('theme', 'penurunan')['tone']);
    }

    public function test_rising_streak_requires_consecutive_increases(): void
    {
        $card = $this->card(['id' => 5]);

        $rising = $this->build([$card], [5 => ['2026-10-23' => 40000, '2026-10-24' => 41000, '2026-10-25' => 42000, '2026-10-27' => 44000]]);
        $broken = $this->build([$card], [5 => ['2026-10-23' => 40000, '2026-10-24' => 41000, '2026-10-25' => 40500, '2026-10-27' => 44000]]);

        $this->assertSame('Cabai Merah naik 3 hari pantau berturut-turut', $rising->firstWhere('theme', 'tren_naik')['title']);
        $this->assertNull($broken->firstWhere('theme', 'tren_naik'));
    }

    public function test_reference_gap_produces_above_and_below_insights(): void
    {
        $insights = $this->build([
            $this->card(['id' => 1, 'name' => 'Cabai Merah', 'vs_reference_pct' => 12.0]),
            $this->card(['id' => 2, 'name' => 'Beras Medium', 'vs_reference_pct' => -15.0]),
            $this->card(['id' => 3, 'name' => 'Gula Pasir', 'vs_reference_pct' => 5.0]),
        ]);

        $this->assertSame('Cabai Merah', $insights->firstWhere('theme', 'di_atas_acuan')['commodity']);
        $this->assertSame('Beras Medium', $insights->firstWhere('theme', 'di_bawah_acuan')['commodity']);
        $this->assertCount(2, $insights->whereIn('theme', ['di_atas_acuan', 'di_bawah_acuan']));
    }

    public function test_reference_insight_is_skipped_without_reference_price(): void
    {
        $insights = $this->build([$this->card(['vs_reference_pct' => null, 'reference_price' => null])]);

        $this->assertNull($insights->firstWhere('theme', 'di_atas_acuan'));
    }

    public function test_market_spread_needs_multiple_markets(): void
    {
        $wideSpread = ['min_price' => 40000.0, 'max_price' => 50000.0];

        $manyMarkets = $this->build([$this->card($wideSpread + ['market_count' => 3])]);
        $singleMarket = $this->build([$this->card($wideSpread + ['market_count' => 1])]);

        $this->assertNotNull($manyMarkets->firstWhere('theme', 'selisih_pasar'));
        $this->assertNull($singleMarket->firstWhere('theme', 'selisih_pasar'));
    }

    public function test_citizen_reports_above_threshold_produce_warning(): void
    {
        $insights = $this->build([$this->card(['citizen_report_count' => 3])]);

        $this->assertSame('3 laporan warga tentang Cabai Merah hari ini', $insights->firstWhere('theme', 'laporan_warga')['title']);
    }

    public function test_stable_market_insight_only_appears_without_alerts(): void
    {
        $calm = $this->build([
            $this->card(['id' => 1, 'change_pct' => 0.5]),
            $this->card(['id' => 2, 'change_pct' => -1.0]),
        ]);
        $withSpike = $this->build([
            $this->card(['id' => 1, 'change_pct' => 0.5]),
            $this->card(['id' => 2, 'change_pct' => 9.0]),
        ]);

        $this->assertNotNull($calm->firstWhere('theme', 'stabil'));
        $this->assertNull($withSpike->firstWhere('theme', 'stabil'));
    }

    public function test_missing_comparisons_produce_data_notice(): void
    {
        $insights = $this->build([
            $this->card(['id' => 1, 'change_pct' => null]),
            $this->card(['id' => 2, 'change_pct' => null]),
            $this->card(['id' => 3, 'change_pct' => 1.0]),
        ]);

        $this->assertSame('Data pembanding belum lengkap', $insights->firstWhere('theme', 'data_terbatas')['title']);
    }

    public function test_insights_are_ordered_by_severity_and_limited(): void
    {
        $insights = app(PublicPriceInsight::class)->build(collect([
            $this->card(['id' => 1, 'name' => 'Beras', 'change_pct' => -7.0]),
            $this->card(['id' => 2, 'name' => 'Cabai', 'change_pct' => 6.0]),
            $this->card(['id' => 3, 'name' => 'Bawang', 'change_pct' => 12.0]),
        ]), [], 2);

        $this->assertSame(['Bawang', 'Cabai'], $insights->pluck('commodity')->all());
    }

    public function test_no_cards_produce_no_insights(): void
    {
        $this->assertTrue($this->build([])->isEmpty());
    }

    public function test_trend_page_renders_insights(): void
    {
        $response = $this->get('/tren');

        $response->assertOk();
        $response->assertViewHas('insights', fn (Collection $insights): bool => $insights->isNotEmpty());
        $response->assertSee('Insight Harga');
    }

    public function test_old_insight_url_redirects_to_trend_page(): void
    {
        $this->get('/insight')->assertRedirect('/tren')->assertStatus(301);
    }

    /**
     * @param  list<array<string, mixed>>  $cards
     * @param  array<int, array<string, float>>  $dailyPrices
     * @return Collection<int, array<string, mixed>>
     */
    private function build(array $cards, array $dailyPrices = []): Collection
    {
        return app(PublicPriceInsight::class)->build(collect($cards), $dailyPrices, 20);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function card(array $overrides = []): array
    {
        return array_merge([
            'id' => 5,
            'name' => 'Cabai Merah',
            'base_unit' => 'kg',
            'avg_price' => 45000.0,
            'min_price' => 44000.0,
            'max_price' => 46000.0,
            'market_count' => 3,
            'change_pct' => 0.0,
            'reference_price' => 45000.0,
            'vs_reference_pct' => 0.0,
            'citizen_report_count' => 0,
        ], $overrides);
    }
}
