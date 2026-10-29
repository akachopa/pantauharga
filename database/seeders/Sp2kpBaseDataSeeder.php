<?php

namespace Database\Seeders;

use App\Models\Commodity;
use App\Models\DataSource;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class Sp2kpBaseDataSeeder extends Seeder
{
    /**
     * Definisi lengkap 42 komoditas pangan SP2KP Kemendag untuk Bengkulu Tengah.
     *
     * @var array<int, array{code: string, name: string, category: string, unit: string, icon: string, reference_price: float}>
     */
    public const COMMODITY_DEFINITIONS = [
        52 => ['code' => 'BERAS_MEDIUM', 'name' => 'Beras Medium', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🍚', 'reference_price' => 14000],
        51 => ['code' => 'BERAS_PREMIUM', 'name' => 'Beras Premium', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🍚', 'reference_price' => 16500],
        1 => ['code' => 'BERAS_SPHP', 'name' => 'Beras SPHP Bulog', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🍚', 'reference_price' => 12000],
        48 => ['code' => 'KEDELAI_IMPOR', 'name' => 'Kedelai Impor', 'category' => 'Kacang-kacangan', 'unit' => 'kg', 'icon' => '🌱', 'reference_price' => 13000],
        2 => ['code' => 'CABAI_MERAH', 'name' => 'Cabai Merah Keriting', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🌶️', 'reference_price' => 48000],
        9 => ['code' => 'CABAI_MERAH_BESAR', 'name' => 'Cabai Merah Besar', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🌶️', 'reference_price' => 45000],
        10 => ['code' => 'CABAI_RAWIT_MERAH', 'name' => 'Cabai Rawit Merah', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🌶️', 'reference_price' => 62000],
        11 => ['code' => 'CABAI_RAWIT', 'name' => 'Cabai Rawit Hijau', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🌶️', 'reference_price' => 42000],
        13 => ['code' => 'BAWANG_MERAH', 'name' => 'Bawang Merah Lokal', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🧅', 'reference_price' => 36000],
        14 => ['code' => 'GULA_PASIR', 'name' => 'Gula Pasir Curah', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🍬', 'reference_price' => 18000],
        15 => ['code' => 'GULA_PASIR_KEMASAN', 'name' => 'Gula Pasir Kemasan', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🍬', 'reference_price' => 18000],
        17 => ['code' => 'MINYAK_GORENG_PREMIUM', 'name' => 'Minyak Goreng Sawit Kemasan Premium', 'category' => 'Pangan Pokok', 'unit' => 'liter', 'icon' => '🫗', 'reference_price' => 22000],
        18 => ['code' => 'MINYAKITA', 'name' => 'Minyakita', 'category' => 'Pangan Pokok', 'unit' => 'liter', 'icon' => '🫗', 'reference_price' => 15700],
        26 => ['code' => 'TEPUNG_TERIGU', 'name' => 'Tepung Terigu Segitiga', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🌾', 'reference_price' => 12500],
        27 => ['code' => 'DAGING_AYAM', 'name' => 'Daging Ayam Broiler', 'category' => 'Daging & Unggas', 'unit' => 'kg', 'icon' => '🍗', 'reference_price' => 34000],
        25 => ['code' => 'TELUR_AYAM', 'name' => 'Telur Ayam Ras', 'category' => 'Daging & Unggas', 'unit' => 'kg', 'icon' => '🥚', 'reference_price' => 28000],
        19 => ['code' => 'DAGING_SAPI', 'name' => 'Daging Sapi Paha Belakang', 'category' => 'Daging & Unggas', 'unit' => 'kg', 'icon' => '🥩', 'reference_price' => 150000],
        53 => ['code' => 'DAGING_SAPI_PAHA_DEPAN', 'name' => 'Daging Sapi Paha Depan', 'category' => 'Daging & Unggas', 'unit' => 'kg', 'icon' => '🥩', 'reference_price' => 150000],
        50 => ['code' => 'DAGING_SAPI_BEKU', 'name' => 'Daging Sapi Impor Beku', 'category' => 'Daging & Unggas', 'unit' => 'kg', 'icon' => '🥩', 'reference_price' => 90000],
        20 => ['code' => 'IKAN_TONGKOL', 'name' => 'Ikan Tongkol', 'category' => 'Perikanan', 'unit' => 'kg', 'icon' => '🐟', 'reference_price' => 27000],
        42 => ['code' => 'MIE_INSTAN', 'name' => 'Mie Instan', 'category' => 'Pangan Pokok', 'unit' => 'bungkus', 'icon' => '🍜', 'reference_price' => 3500],
        38 => ['code' => 'BAWANG_PUTIH', 'name' => 'Bawang Putih Honan', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🧄', 'reference_price' => 35000],
        41 => ['code' => 'GARAM_HALUS', 'name' => 'Garam Halus', 'category' => 'Bumbu & Rempah', 'unit' => 'kg', 'icon' => '🧂', 'reference_price' => 16000],
        31 => ['code' => 'SUSU_KENTAL_MANIS', 'name' => 'Susu Kental Manis', 'category' => 'Susu & Olahan', 'unit' => 'kaleng', 'icon' => '🥛', 'reference_price' => 13000],
        32 => ['code' => 'SUSU_BUBUK', 'name' => 'Susu Bubuk', 'category' => 'Susu & Olahan', 'unit' => 'kotak', 'icon' => '🥛', 'reference_price' => 52000],
        33 => ['code' => 'SUSU_BUBUK_BALITA', 'name' => 'Susu Bubuk Balita', 'category' => 'Susu & Olahan', 'unit' => 'kotak', 'icon' => '🥛', 'reference_price' => 43000],
        36 => ['code' => 'TEMPE_BUNGKUS', 'name' => 'Tempe Bungkus', 'category' => 'Kacang-kacangan', 'unit' => 'kg', 'icon' => '🧈', 'reference_price' => 15000],
        35 => ['code' => 'TAHU_PUTIH', 'name' => 'Tahu Putih', 'category' => 'Kacang-kacangan', 'unit' => 'kg', 'icon' => '🧈', 'reference_price' => 10000],
        40 => ['code' => 'UDANG_BASAH', 'name' => 'Udang Basah', 'category' => 'Perikanan', 'unit' => 'kg', 'icon' => '🦐', 'reference_price' => 80000],
        54 => ['code' => 'PISANG_LOKAL', 'name' => 'Pisang Lokal', 'category' => 'Buah-buahan', 'unit' => 'kg', 'icon' => '🍌', 'reference_price' => 10000],
        55 => ['code' => 'JERUK_LOKAL', 'name' => 'Jeruk Lokal', 'category' => 'Buah-buahan', 'unit' => 'kg', 'icon' => '🍊', 'reference_price' => 17000],
        56 => ['code' => 'TOMAT', 'name' => 'Tomat', 'category' => 'Sayuran', 'unit' => 'kg', 'icon' => '🍅', 'reference_price' => 9500],
        57 => ['code' => 'KENTANG_SEDANG', 'name' => 'Kentang Sedang', 'category' => 'Sayuran', 'unit' => 'kg', 'icon' => '🥔', 'reference_price' => 11500],
        5 => ['code' => 'SAWI_HIJAU', 'name' => 'Sawi Hijau', 'category' => 'Sayuran', 'unit' => 'kg', 'icon' => '🥬', 'reference_price' => 9000],
        58 => ['code' => 'KANGKUNG', 'name' => 'Kangkung', 'category' => 'Sayuran', 'unit' => 'kg', 'icon' => '🥬', 'reference_price' => 9000],
        3 => ['code' => 'KETIMUN_SEDANG', 'name' => 'Ketimun Sedang', 'category' => 'Sayuran', 'unit' => 'kg', 'icon' => '🥒', 'reference_price' => 10500],
        4 => ['code' => 'KACANG_PANJANG', 'name' => 'Kacang Panjang', 'category' => 'Sayuran', 'unit' => 'kg', 'icon' => '🫘', 'reference_price' => 10000],
        47 => ['code' => 'KETELA_POHON', 'name' => 'Ketela Pohon', 'category' => 'Pangan Pokok', 'unit' => 'kg', 'icon' => '🍠', 'reference_price' => 4500],
        43 => ['code' => 'DAGING_AYAM_KAMPUNG', 'name' => 'Daging Ayam Kampung', 'category' => 'Daging & Unggas', 'unit' => 'ekor', 'icon' => '🍗', 'reference_price' => 59000],
        44 => ['code' => 'TELUR_AYAM_KAMPUNG', 'name' => 'Telur Ayam Kampung', 'category' => 'Daging & Unggas', 'unit' => 'kg', 'icon' => '🥚', 'reference_price' => 54000],
        45 => ['code' => 'KACANG_TANAH', 'name' => 'Kacang Tanah', 'category' => 'Kacang-kacangan', 'unit' => 'kg', 'icon' => '🥜', 'reference_price' => 41500],
        46 => ['code' => 'KACANG_HIJAU', 'name' => 'Kacang Hijau', 'category' => 'Kacang-kacangan', 'unit' => 'kg', 'icon' => '🫘', 'reference_price' => 32000],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('1. Memperbarui Master Komoditas SP2KP...');
        $commodityMap = [];

        foreach (self::COMMODITY_DEFINITIONS as $variantId => $def) {
            $existing = Commodity::query()->where('code', $def['code'])->first();

            if ($existing) {
                $existing->update([
                    'name' => $def['name'],
                    'category' => $def['category'],
                    'base_unit' => $def['unit'],
                    'icon' => $def['icon'],
                    'reference_price' => $def['reference_price'],
                    'is_active' => true,
                ]);
                $commodityMap[$variantId] = $existing->id;
            } else {
                $newComm = Commodity::create([
                    'code' => $def['code'],
                    'name' => $def['name'],
                    'category' => $def['category'],
                    'base_unit' => $def['unit'],
                    'icon' => $def['icon'],
                    'reference_price' => $def['reference_price'],
                    'is_active' => true,
                ]);
                $commodityMap[$variantId] = $newComm->id;
            }
        }
        $this->command->line('   ✓ '.count($commodityMap).' komoditas SP2KP siap di database.');

        $this->command->info('2. Memastikan Sumber Data SP2KP Kemendag...');
        $source = DataSource::query()->updateOrCreate(
            ['code' => 'SRC_SP2KP'],
            [
                'name' => 'SP2KP Kemendag (Kabupaten Bengkulu Tengah)',
                'source_type' => 'instansi',
            ]
        );
        $this->command->line("   ✓ Sumber data ID: {$source->id} ({$source->name})");

        $csvPath = base_path('data/sp2kp/bengkulu_tengah_2026_daily.csv');
        if (! File::exists($csvPath)) {
            $this->command->error("File {$csvPath} tidak ditemukan. Jalankan php artisan sp2kp:collect terlebih dahulu.");

            return;
        }

        $this->command->info('3. Mengimpor data harga harian 2026 ke database...');
        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows);

        // Group rows by date
        $byDate = [];
        foreach ($rows as $r) {
            if (count($r) < 9) {
                continue;
            }
            $tgl = $r[0];
            $byDate[$tgl][] = [
                'tanggal' => $r[0],
                'variant_id' => (int) $r[1],
                'komoditas' => $r[2],
                'satuan' => $r[3],
                'harga' => (float) $r[4],
                'harga_kemarin' => (float) $r[5],
                'perubahan_rp' => (float) $r[6],
                'perubahan_persen' => (float) $r[7],
                'status' => $r[8],
            ];
        }

        $pointId = 1; // Titik Pantau Utama: Pasar Karang Tinggi
        $totalItems = 0;
        $totalDates = count($byDate);

        DB::transaction(function () use ($byDate, $pointId, $source, $commodityMap, &$totalItems): void {
            foreach ($byDate as $date => $items) {
                // Find or create series for point 1 on this date
                $series = ReportSeries::query()
                    ->where('point_id', $pointId)
                    ->whereDate('observed_at', $date)
                    ->whereHas('reports', fn ($q) => $q->where('source_id', $source->id))
                    ->first();

                if (! $series) {
                    $series = ReportSeries::create([
                        'point_id' => $pointId,
                        'observed_at' => $date.' 08:00:00',
                    ]);
                }

                // Find or create approved report for this series
                $report = Report::query()
                    ->where('series_id', $series->id)
                    ->where('source_id', $source->id)
                    ->first();

                if (! $report) {
                    $report = Report::create([
                        'series_id' => $series->id,
                        'source_id' => $source->id,
                        'status' => 'approved',
                        'submitted_by' => 1,
                        'verified_by' => 1,
                        'verified_at' => $date.' 09:00:00',
                        'version_no' => 1,
                        'is_demo' => false,
                        'notes' => 'Data resmi SP2KP Kemendag Kab. Bengkulu Tengah tanggal '.$date,
                    ]);
                } else {
                    $report->update([
                        'status' => 'approved',
                        'verified_by' => 1,
                        'verified_at' => $date.' 09:00:00',
                        'notes' => 'Data resmi SP2KP Kemendag Kab. Bengkulu Tengah tanggal '.$date,
                    ]);
                }

                foreach ($items as $item) {
                    $commId = $commodityMap[$item['variant_id']] ?? null;
                    if (! $commId) {
                        continue;
                    }

                    ReportItem::query()->updateOrCreate(
                        [
                            'report_id' => $report->id,
                            'commodity_id' => $commId,
                        ],
                        [
                            'price' => $item['harga'],
                            'stock' => 0,
                            'unit' => $item['satuan'],
                            'price_change_amount' => $item['perubahan_rp'],
                            'price_change_pct' => $item['perubahan_persen'],
                            'notes' => 'SP2KP Kemendag — '.$item['komoditas'],
                        ]
                    );

                    $totalItems++;
                }
            }
        });

        $this->command->info("   ✓ Selesai: {$totalDates} hari pemantauan dan {$totalItems} baris harga telah dijadikan base data aktif di database.");
    }
}
