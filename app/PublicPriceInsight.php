<?php

namespace App;

use Illuminate\Support\Collection;

class PublicPriceInsight
{
    private const TONE_WEIGHTS = [
        'danger' => 4,
        'warning' => 3,
        'info' => 2,
        'success' => 1,
        'neutral' => 0,
    ];

    /**
     * Build rule-based insights from the public commodity cards.
     *
     * @param  Collection<int, array<string, mixed>>  $cards
     * @param  array<int, array<string, float>>  $dailyPrices  commodity id => [date => average price], oldest first
     * @return Collection<int, array{theme: string, label: string, tone: string, title: string, message: string, commodity: ?string, weight: float}>
     */
    public function build(Collection $cards, array $dailyPrices = [], ?int $limit = null): Collection
    {
        if ($cards->isEmpty()) {
            return collect();
        }

        $insights = collect();

        foreach ($cards as $card) {
            $insights->push(...$this->priceMovementInsights($card));
            $insights->push(...$this->risingStreakInsights($card, $dailyPrices[$card['id']] ?? []));
            $insights->push(...$this->referenceInsights($card));
            $insights->push(...$this->marketSpreadInsights($card));
            $insights->push(...$this->citizenReportInsights($card));
        }

        $hasAlert = $insights->contains(fn (array $insight): bool => in_array($insight['tone'], ['danger', 'warning'], true));
        if (! $hasAlert) {
            $insights->push(...$this->stableMarketInsights($cards));
        }
        $insights->push(...$this->missingComparisonInsights($cards));

        return $insights
            ->sortByDesc(fn (array $insight): array => [self::TONE_WEIGHTS[$insight['tone']], $insight['weight']])
            ->take($limit ?? (int) config('price_insights.max_items', 6))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $card
     * @return list<array<string, mixed>>
     */
    private function priceMovementInsights(array $card): array
    {
        $changePct = $card['change_pct'];
        if ($changePct === null) {
            return [];
        }

        $previousPrice = $card['avg_price'] / (1 + $changePct / 100);
        $difference = abs($card['avg_price'] - $previousPrice);

        if ($changePct >= (float) config('price_insights.spike_pct')) {
            return [$this->insight(
                'lonjakan',
                'Lonjakan harga',
                'danger',
                "{$card['name']} naik {$this->percent($changePct)}",
                "Naik {$this->rupiah($difference)} per {$card['base_unit']} dibanding hari pantau sebelumnya, kini rata-rata {$this->rupiah($card['avg_price'])}. Beli secukupnya dan bandingkan harga di pasar lain.",
                $card['name'],
                $changePct,
            )];
        }

        if ($changePct <= -(float) config('price_insights.drop_pct')) {
            return [$this->insight(
                'penurunan',
                'Harga turun',
                'success',
                "{$card['name']} turun {$this->percent(abs($changePct))}",
                "Turun {$this->rupiah($difference)} per {$card['base_unit']} dibanding hari pantau sebelumnya, kini rata-rata {$this->rupiah($card['avg_price'])}. Waktu yang baik untuk berbelanja.",
                $card['name'],
                abs($changePct),
            )];
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $card
     * @param  array<string, float>  $dailyPrices
     * @return list<array<string, mixed>>
     */
    private function risingStreakInsights(array $card, array $dailyPrices): array
    {
        $prices = array_values($dailyPrices);
        $streak = 0;
        for ($index = count($prices) - 1; $index > 0; $index--) {
            if ($prices[$index] <= $prices[$index - 1]) {
                break;
            }
            $streak++;
        }

        if ($streak < (int) config('price_insights.rising_streak')) {
            return [];
        }

        $startPrice = $prices[count($prices) - 1 - $streak];
        $endPrice = $prices[count($prices) - 1];
        $risePct = $startPrice > 0 ? (($endPrice - $startPrice) / $startPrice) * 100 : 0.0;

        return [$this->insight(
            'tren_naik',
            'Tren naik beruntun',
            'warning',
            "{$card['name']} naik {$streak} hari pantau berturut-turut",
            "Dari {$this->rupiah($startPrice)} menjadi {$this->rupiah($endPrice)} per {$card['base_unit']} ({$this->percent($risePct)}). Perlu dipantau sebelum kenaikan berlanjut.",
            $card['name'],
            $risePct,
        )];
    }

    /**
     * @param  array<string, mixed>  $card
     * @return list<array<string, mixed>>
     */
    private function referenceInsights(array $card): array
    {
        $vsReferencePct = $card['vs_reference_pct'];
        if ($vsReferencePct === null || $card['reference_price'] === null) {
            return [];
        }

        if ($vsReferencePct >= (float) config('price_insights.above_reference_pct')) {
            return [$this->insight(
                'di_atas_acuan',
                'Di atas harga acuan',
                'warning',
                "{$card['name']} {$this->percent($vsReferencePct)} di atas acuan",
                "Rata-rata {$this->rupiah($card['avg_price'])}, sedangkan harga acuan {$this->rupiah($card['reference_price'])} per {$card['base_unit']}.",
                $card['name'],
                $vsReferencePct,
            )];
        }

        if ($vsReferencePct <= -(float) config('price_insights.below_reference_pct')) {
            return [$this->insight(
                'di_bawah_acuan',
                'Di bawah harga acuan',
                'info',
                "{$card['name']} {$this->percent(abs($vsReferencePct))} di bawah acuan",
                "Rata-rata {$this->rupiah($card['avg_price'])}, sedangkan harga acuan {$this->rupiah($card['reference_price'])} per {$card['base_unit']}. Menguntungkan pembeli, namun perlu dicermati dampaknya bagi petani dan pedagang.",
                $card['name'],
                abs($vsReferencePct),
            )];
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $card
     * @return list<array<string, mixed>>
     */
    private function marketSpreadInsights(array $card): array
    {
        if ($card['market_count'] < (int) config('price_insights.market_spread_min_markets') || $card['avg_price'] <= 0) {
            return [];
        }

        $spread = $card['max_price'] - $card['min_price'];
        $spreadPct = ($spread / $card['avg_price']) * 100;
        if ($spreadPct < (float) config('price_insights.market_spread_pct')) {
            return [];
        }

        return [$this->insight(
            'selisih_pasar',
            'Selisih antar pasar',
            'info',
            "Harga {$card['name']} berbeda jauh antar pasar",
            "Selisih {$this->rupiah($spread)} ({$this->percent($spreadPct)}) antara harga terendah {$this->rupiah($card['min_price'])} dan tertinggi {$this->rupiah($card['max_price'])} dari {$card['market_count']} pasar. Bandingkan sebelum berbelanja.",
            $card['name'],
            $spreadPct,
        )];
    }

    /**
     * @param  array<string, mixed>  $card
     * @return list<array<string, mixed>>
     */
    private function citizenReportInsights(array $card): array
    {
        $reportCount = (int) ($card['citizen_report_count'] ?? 0);
        if ($reportCount < (int) config('price_insights.citizen_report_min')) {
            return [];
        }

        return [$this->insight(
            'laporan_warga',
            'Laporan warga',
            'warning',
            "{$reportCount} laporan warga tentang {$card['name']} hari ini",
            'Warga melaporkan harga yang berbeda dari data resmi. Petugas sedang meninjau laporan tersebut.',
            $card['name'],
            (float) $reportCount,
        )];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $cards
     * @return list<array<string, mixed>>
     */
    private function stableMarketInsights(Collection $cards): array
    {
        $compared = $cards->filter(fn (array $card): bool => $card['change_pct'] !== null);
        if ($compared->isEmpty()) {
            return [];
        }

        $stablePct = (float) config('price_insights.stable_pct');
        $stableCount = $compared->filter(fn (array $card): bool => abs($card['change_pct']) < $stablePct)->count();
        if ($stableCount / $compared->count() < (float) config('price_insights.stable_share')) {
            return [];
        }

        return [$this->insight(
            'stabil',
            'Kondisi stabil',
            'success',
            'Harga pangan relatif stabil',
            "{$stableCount} dari {$compared->count()} komoditi bergerak kurang dari {$this->percent($stablePct)} dibanding hari pantau sebelumnya.",
            null,
            (float) $stableCount,
        )];
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $cards
     * @return list<array<string, mixed>>
     */
    private function missingComparisonInsights(Collection $cards): array
    {
        $missingCount = $cards->filter(fn (array $card): bool => $card['change_pct'] === null)->count();
        if ($missingCount === 0 || $missingCount / $cards->count() < (float) config('price_insights.missing_comparison_share')) {
            return [];
        }

        return [$this->insight(
            'data_terbatas',
            'Data belum lengkap',
            'neutral',
            'Data pembanding belum lengkap',
            "{$missingCount} dari {$cards->count()} komoditi belum memiliki data hari pantau sebelumnya, sehingga perubahan harganya belum dapat dihitung.",
            null,
            (float) $missingCount,
        )];
    }

    /**
     * @return array{theme: string, label: string, tone: string, title: string, message: string, commodity: ?string, weight: float}
     */
    private function insight(string $theme, string $label, string $tone, string $title, string $message, ?string $commodity, float $weight): array
    {
        return [
            'theme' => $theme,
            'label' => $label,
            'tone' => $tone,
            'title' => $title,
            'message' => $message,
            'commodity' => $commodity,
            'weight' => round($weight, 2),
        ];
    }

    private function rupiah(float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }

    private function percent(float $value): string
    {
        return number_format($value, 1, ',', '.').'%';
    }
}
