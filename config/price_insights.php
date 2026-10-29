<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Insight Harga Halaman Publik
    |--------------------------------------------------------------------------
    |
    | Ambang batas yang dipakai App\PublicPriceInsight untuk memilih skenario
    | insight di halaman Home. Semua persentase dibandingkan terhadap harga
    | rata-rata komoditi pada tanggal yang dipilih.
    |
    */

    'max_items' => (int) env('PRICE_INSIGHT_MAX_ITEMS', 6),

    // Kenaikan dibanding hari pantau sebelumnya (persen) yang dianggap lonjakan.
    'spike_pct' => 5.0,

    // Penurunan dibanding hari pantau sebelumnya (persen) yang dianggap penurunan berarti.
    'drop_pct' => 5.0,

    // Jumlah kenaikan beruntun antar hari pantau untuk skenario tren naik.
    'rising_streak' => 3,

    // Selisih terhadap harga acuan (persen) untuk skenario di atas / di bawah acuan.
    'above_reference_pct' => 10.0,
    'below_reference_pct' => 10.0,

    // Selisih harga termurah-termahal antar pasar terhadap rata-rata (persen).
    'market_spread_pct' => 15.0,
    'market_spread_min_markets' => 2,

    // Jumlah laporan warga per komoditi hari ini yang dianggap sinyal.
    'citizen_report_min' => 3,

    // Perubahan maksimum (persen) agar komoditi dihitung stabil, dan porsi minimum komoditi stabil.
    'stable_pct' => 2.0,
    'stable_share' => 0.6,

    // Porsi komoditi tanpa data pembanding yang memicu skenario data belum lengkap.
    'missing_comparison_share' => 0.5,

];
