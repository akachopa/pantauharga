<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Sp2kpCollectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sp2kp:collect
                            {--year=2026 : Tahun data SP2KP yang akan dikumpulkan}
                            {--start-date= : Tanggal awal format YYYY-MM-DD}
                            {--end-date= : Tanggal akhir format YYYY-MM-DD}
                            {--province=17 : Kode provinsi SP2KP (17 = Bengkulu)}
                            {--regency=1709 : Kode kab/kota SP2KP (1709 = Bengkulu Tengah)}
                            {--out-dir=data/sp2kp : Direktori output penyimpanan file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengumpulkan data harga komoditas pangan SP2KP Kemendag untuk Kabupaten Bengkulu Tengah';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = (string) $this->option('year');
        $provinceCode = (string) $this->option('province');
        $regencyCode = (string) $this->option('regency');
        $outDir = base_path((string) $this->option('out-dir'));

        $startDate = $this->option('start-date') ?: "{$year}-01-01";
        $today = date('Y-m-d');
        $defaultEndDate = ($year === date('Y')) ? $today : "{$year}-12-31";
        $endDate = $this->option('end-date') ?: $defaultEndDate;

        $this->info('=== Pengumpulan Data SP2KP Kemendag ===');
        $this->info("Wilayah      : Provinsi {$provinceCode}, Kab/Kota {$regencyCode} (Bengkulu Tengah)");
        $this->info("Rentang Waktu: {$startDate} s/d {$endDate}");
        $this->info("Direktori    : {$outDir}");

        if (! File::isDirectory($outDir)) {
            File::makeDirectory($outDir, 0755, true);
        }

        // 1. Fetch Daily JSON
        $this->line('');
        $this->info('[1/4] Mengambil data harian JSON dari API SP2KP...');
        $fetchStart = Carbon::parse($startDate)->subDays(7)->toDateString();

        $dailyResponse = Http::asForm()
            ->connectTimeout(10)
            ->timeout(60)
            ->post('https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json', [
                'start_date' => $fetchStart,
                'end_date' => $endDate,
                'level' => $regencyCode !== '' ? '2' : '1',
                'kode_provinsi' => $provinceCode,
                'kode_kab_kota' => $regencyCode,
                'pasar_id' => '',
                'skip_sat_sun' => 'false',
                'tipe_komoditas' => '1',
            ]);

        if (! $dailyResponse->successful()) {
            $this->error("Gagal mengambil data harian JSON: HTTP {$dailyResponse->status()}");

            return self::FAILURE;
        }

        $rawJson = $dailyResponse->json();
        if (($rawJson['status'] ?? '') !== 'success' || ! isset($rawJson['data']) || ! is_array($rawJson['data'])) {
            $this->error('Respons API SP2KP tidak valid: '.($rawJson['message'] ?? 'Unknown error'));

            return self::FAILURE;
        }

        // Simpan raw JSON harian
        $rawDailyPath = "{$outDir}/bengkulu_tengah_{$year}_daily_raw.json";
        File::put($rawDailyPath, json_encode($rawJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->line("  ✓ Tersimpan: {$rawDailyPath} (".number_format(filesize($rawDailyPath) / 1024, 1).' KB)');

        // 2. Fetch Daily Excel
        $this->line('');
        $this->info('[2/4] Mengunduh berkas Excel resmi harian SP2KP...');
        $dailyExcelResponse = Http::asForm()
            ->connectTimeout(10)
            ->timeout(60)
            ->post('https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-excel', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'level' => $regencyCode !== '' ? '2' : '1',
                'kode_provinsi' => $provinceCode,
                'kode_kab_kota' => $regencyCode,
                'pasar_id' => '',
                'skip_sat_sun' => 'false',
                'tipe_komoditas' => '1',
            ]);

        if ($dailyExcelResponse->successful() && strlen($dailyExcelResponse->body()) > 1000) {
            $dailyExcelPath = "{$outDir}/bengkulu_tengah_{$year}_daily.xlsx";
            File::put($dailyExcelPath, $dailyExcelResponse->body());
            $this->line("  ✓ Tersimpan: {$dailyExcelPath} (".number_format(filesize($dailyExcelPath) / 1024, 1).' KB)');
        } else {
            $this->warn('  ! Pengunduhan Excel harian tidak berhasil (status: '.$dailyExcelResponse->status().')');
        }

        // 3. Fetch Monthly JSON & Excel
        $this->line('');
        $this->info('[3/4] Mengambil data rata-rata bulanan SP2KP...');
        $monthlyResponse = Http::asForm()
            ->connectTimeout(10)
            ->timeout(60)
            ->post('https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-monthly-json', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'level' => $regencyCode !== '' ? '2' : '1',
                'kode_provinsi' => $provinceCode,
                'kode_kab_kota' => $regencyCode,
                'tipe_komoditas' => '1',
            ]);

        if ($monthlyResponse->successful()) {
            $rawMonthly = $monthlyResponse->json();
            $monthlyJsonPath = "{$outDir}/bengkulu_tengah_{$year}_monthly.json";
            File::put($monthlyJsonPath, json_encode($rawMonthly, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->line("  ✓ Tersimpan: {$monthlyJsonPath}");

            // Process Monthly CSV
            $this->exportMonthlyCsv($rawMonthly['data'] ?? [], "{$outDir}/bengkulu_tengah_{$year}_monthly.csv");
            $this->line("  ✓ Tersimpan: {$outDir}/bengkulu_tengah_{$year}_monthly.csv");
        }

        $monthlyExcelResponse = Http::asForm()
            ->connectTimeout(10)
            ->timeout(60)
            ->post('https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-monthly-excel', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'level' => $regencyCode !== '' ? '2' : '1',
                'kode_provinsi' => $provinceCode,
                'kode_kab_kota' => $regencyCode,
                'tipe_komoditas' => '1',
            ]);

        if ($monthlyExcelResponse->successful() && strlen($monthlyExcelResponse->body()) > 1000) {
            $monthlyExcelPath = "{$outDir}/bengkulu_tengah_{$year}_monthly.xlsx";
            File::put($monthlyExcelPath, $monthlyExcelResponse->body());
            $this->line("  ✓ Tersimpan: {$monthlyExcelPath} (".number_format(filesize($monthlyExcelPath) / 1024, 1).' KB)');
        }

        // 4. Process and export clean Daily CSV & JSON
        $this->line('');
        $this->info('[4/4] Memproses dataset harian dan menyusun ringkasan...');
        $processed = $this->processDailyData($rawJson['data'], $startDate, $endDate);

        // Simpan clean daily CSV
        $dailyCsvPath = "{$outDir}/bengkulu_tengah_{$year}_daily.csv";
        $this->exportDailyCsv($processed['rows'], $dailyCsvPath);
        $this->line("  ✓ Tersimpan: {$dailyCsvPath} (".number_format(filesize($dailyCsvPath) / 1024, 1).' KB, '.number_format(count($processed['rows'])).' baris)');

        // Simpan clean daily JSON
        $dailyJsonPath = "{$outDir}/bengkulu_tengah_{$year}_daily.json";
        File::put($dailyJsonPath, json_encode([
            'metadata' => [
                'sumber' => 'SP2KP Kementerian Perdagangan RI (https://sp2kp.kemendag.go.id/)',
                'provinsi' => 'Bengkulu (Kode: '.$provinceCode.')',
                'kabupaten_kota' => 'Bengkulu Tengah (Kode: '.$regencyCode.')',
                'periode' => "{$startDate} s/d {$endDate}",
                'total_komoditas' => count($processed['commodities']),
                'total_baris_data' => count($processed['rows']),
                'dibuat_pada' => Carbon::now()->toIso8601String(),
            ],
            'ringkasan_komoditas' => $processed['summary'],
            'data_harian' => $processed['rows'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->line("  ✓ Tersimpan: {$dailyJsonPath} (".number_format(filesize($dailyJsonPath) / 1024, 1).' KB)');

        // Simpan Summary CSV
        $summaryCsvPath = "{$outDir}/bengkulu_tengah_{$year}_summary.csv";
        $this->exportSummaryCsv($processed['summary'], $summaryCsvPath);
        $this->line("  ✓ Tersimpan: {$summaryCsvPath}");

        // Tampilkan tabel ringkasan di terminal
        $this->line('');
        $this->info('=== Ringkasan Harga Komoditas Bengkulu Tengah (Tahun '.$year.') ===');
        $tableData = [];
        foreach ($processed['summary'] as $s) {
            $tableData[] = [
                $s['variant_id'],
                $s['komoditas'],
                $s['satuan'],
                $s['hari_pencatatan'],
                'Rp '.number_format($s['harga_terendah'], 0, ',', '.'),
                'Rp '.number_format($s['harga_tertinggi'], 0, ',', '.'),
                'Rp '.number_format($s['harga_rata_rata'], 0, ',', '.'),
                'Rp '.number_format($s['harga_terkini'], 0, ',', '.'),
                $s['perubahan_ytd'],
            ];
        }

        $this->table(
            ['ID', 'Komoditas', 'Satuan', 'Hari', 'Min', 'Max', 'Rata-rata', 'Terkini', 'Tren 2026'],
            $tableData
        );

        $this->info("Pengumpulan data selesai. Seluruh berkas tersimpan di: {$outDir}");

        return self::SUCCESS;
    }

    /**
     * @param  list<array<string, mixed>>  $rawVariants
     * @return array{
     *     rows: list<array<string, mixed>>,
     *     commodities: array<int, string>,
     *     summary: list<array<string, mixed>>
     * }
     */
    private function processDailyData(array $rawVariants, string $startDate, string $endDate): array
    {
        $rows = [];
        $commodities = [];
        $summary = [];

        foreach ($rawVariants as $variant) {
            $variantId = (int) ($variant['variant_id'] ?? 0);
            $variantName = (string) ($variant['variant'] ?? '');
            $unit = (string) ($variant['satuan'] ?? 'kg');
            $commodities[$variantId] = $variantName;

            $lastValidPrice = 0.0;
            $firstValidPrice = 0.0;
            $activePrices = [];

            foreach ($variant['daftarHarga'] ?? [] as $entry) {
                $date = (string) ($entry['date'] ?? '');
                $price = (float) ($entry['harga'] ?? 0);

                if ($price <= 0) {
                    continue; // Skip akhir pekan / hari libur tanpa pencatatan survei
                }

                if ($firstValidPrice === 0.0 && $date >= $startDate) {
                    $firstValidPrice = $price;
                }

                if ($date >= $startDate && $date <= $endDate) {
                    $delta = $lastValidPrice > 0 ? ($price - $lastValidPrice) : 0.0;
                    $pct = $lastValidPrice > 0 ? (($delta / $lastValidPrice) * 100.0) : 0.0;
                    $status = $delta > 0 ? 'Naik' : ($delta < 0 ? 'Turun' : 'Tetap');

                    $rows[] = [
                        'tanggal' => $date,
                        'variant_id' => $variantId,
                        'komoditas' => $variantName,
                        'satuan' => $unit,
                        'harga' => $price,
                        'harga_kemarin' => $lastValidPrice,
                        'perubahan_rp' => $delta,
                        'perubahan_persen' => round($pct, 2),
                        'status_perubahan' => $status,
                    ];

                    $activePrices[] = $price;
                }

                $lastValidPrice = $price;
            }

            if (count($activePrices) > 0) {
                $min = min($activePrices);
                $max = max($activePrices);
                $avg = array_sum($activePrices) / count($activePrices);
                $latest = end($activePrices);
                $ytdDelta = $latest - $firstValidPrice;
                $ytdPct = $firstValidPrice > 0 ? (($ytdDelta / $firstValidPrice) * 100.0) : 0.0;
                $ytdStr = sprintf(
                    '%s (%.1f%%)',
                    $ytdDelta > 0 ? '+Rp '.number_format($ytdDelta, 0, ',', '.') : ($ytdDelta < 0 ? '-Rp '.number_format(abs($ytdDelta), 0, ',', '.') : 'Rp 0'),
                    $ytdPct
                );

                $summary[] = [
                    'variant_id' => $variantId,
                    'komoditas' => $variantName,
                    'satuan' => $unit,
                    'hari_pencatatan' => count($activePrices),
                    'harga_terendah' => $min,
                    'harga_tertinggi' => $max,
                    'harga_rata_rata' => round($avg, 0),
                    'harga_terkini' => $latest,
                    'harga_awal_tahun' => $firstValidPrice,
                    'perubahan_ytd' => $ytdStr,
                ];
            }
        }

        // Urutkan rows secara kronologis berdasarkan tanggal, lalu variant_id
        usort($rows, function (array $a, array $b): int {
            if ($a['tanggal'] === $b['tanggal']) {
                return $a['variant_id'] <=> $b['variant_id'];
            }

            return strcmp($a['tanggal'], $b['tanggal']);
        });

        // Urutkan summary berdasarkan nama komoditas
        usort($summary, fn (array $a, array $b): int => strcmp($a['komoditas'], $b['komoditas']));

        return [
            'rows' => $rows,
            'commodities' => $commodities,
            'summary' => $summary,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function exportDailyCsv(array $rows, string $filePath): void
    {
        $fp = fopen($filePath, 'wb');
        if (! $fp) {
            throw new RuntimeException("Gagal membuka file {$filePath} untuk penulisan.");
        }

        fputcsv($fp, [
            'tanggal',
            'variant_id',
            'komoditas',
            'satuan',
            'harga',
            'harga_kemarin',
            'perubahan_rp',
            'perubahan_persen',
            'status_perubahan',
        ]);

        foreach ($rows as $row) {
            fputcsv($fp, [
                $row['tanggal'],
                $row['variant_id'],
                $row['komoditas'],
                $row['satuan'],
                $row['harga'],
                $row['harga_kemarin'],
                $row['perubahan_rp'],
                $row['perubahan_persen'],
                $row['status_perubahan'],
            ]);
        }

        fclose($fp);
    }

    /**
     * @param  list<array<string, mixed>>  $summary
     */
    private function exportSummaryCsv(array $summary, string $filePath): void
    {
        $fp = fopen($filePath, 'wb');
        if (! $fp) {
            throw new RuntimeException("Gagal membuka file {$filePath} untuk penulisan.");
        }

        fputcsv($fp, [
            'variant_id',
            'komoditas',
            'satuan',
            'hari_pencatatan',
            'harga_terendah',
            'harga_tertinggi',
            'harga_rata_rata',
            'harga_terkini',
            'harga_awal_tahun',
            'perubahan_ytd',
        ]);

        foreach ($summary as $s) {
            fputcsv($fp, [
                $s['variant_id'],
                $s['komoditas'],
                $s['satuan'],
                $s['hari_pencatatan'],
                $s['harga_terendah'],
                $s['harga_tertinggi'],
                $s['harga_rata_rata'],
                $s['harga_terkini'],
                $s['harga_awal_tahun'],
                $s['perubahan_ytd'],
            ]);
        }

        fclose($fp);
    }

    /**
     * @param  list<array<string, mixed>>  $monthlyData
     */
    private function exportMonthlyCsv(array $monthlyData, string $filePath): void
    {
        $fp = fopen($filePath, 'wb');
        if (! $fp) {
            return;
        }

        // Ambil semua bulan unik
        $months = [];
        foreach ($monthlyData as $item) {
            foreach ($item['daftarHarga'] ?? [] as $entry) {
                $m = (string) ($entry['date'] ?? '');
                if ($m !== '' && ! in_array($m, $months, true)) {
                    $months[] = $m;
                }
            }
        }
        sort($months);

        // Header CSV
        fputcsv($fp, array_merge(['variant_id', 'komoditas', 'satuan'], $months));

        foreach ($monthlyData as $item) {
            $byMonth = [];
            foreach ($item['daftarHarga'] ?? [] as $entry) {
                $byMonth[(string) $entry['date']] = $entry['harga'] ?? 0;
            }

            $line = [
                $item['variant_id'] ?? '',
                $item['variant'] ?? '',
                $item['satuan'] ?? '',
            ];

            foreach ($months as $m) {
                $line[] = $byMonth[$m] ?? 0;
            }

            fputcsv($fp, $line);
        }

        fclose($fp);
    }
}
