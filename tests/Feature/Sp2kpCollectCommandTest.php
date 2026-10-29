<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Sp2kpCollectCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('tests/fixtures/sp2kp_test_out'));
        parent::tearDown();
    }

    public function test_sp2kp_collect_command_creates_daily_and_monthly_artifacts(): void
    {
        $testDir = 'tests/fixtures/sp2kp_test_out';
        File::deleteDirectory(base_path($testDir));

        Http::fake([
            'https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json' => Http::response([
                'status' => 'success',
                'message' => 'Success retrieving record',
                'data' => [
                    [
                        'variant_id' => 52,
                        'variant' => 'Beras Medium',
                        'satuan' => 'kg',
                        'daftarHarga' => [
                            ['date' => '2026-01-01', 'harga' => 0],
                            ['date' => '2026-01-02', 'harga' => 13750],
                            ['date' => '2026-01-05', 'harga' => 14000],
                        ],
                    ],
                ],
            ]),
            'https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-excel' => Http::response(
                str_repeat('excel-binary-mock', 100),
                200,
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            ),
            'https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-monthly-json' => Http::response([
                'status' => 'success',
                'message' => 'Success',
                'data' => [
                    [
                        'variant_id' => 52,
                        'variant' => 'Beras Medium',
                        'satuan' => 'kg',
                        'daftarHarga' => [
                            ['date' => '2026-01', 'harga' => 13750],
                        ],
                    ],
                ],
            ]),
            'https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-monthly-excel' => Http::response(
                str_repeat('monthly-excel-mock', 100),
                200,
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            ),
        ]);

        $this->artisan('sp2kp:collect', [
            '--year' => '2026',
            '--start-date' => '2026-01-01',
            '--end-date' => '2026-01-05',
            '--out-dir' => $testDir,
        ])
            ->assertExitCode(0)
            ->expectsOutputToContain('=== Pengumpulan Data SP2KP Kemendag ===')
            ->expectsOutputToContain('Beras Medium');

        $this->assertFileExists(base_path("{$testDir}/bengkulu_tengah_2026_daily_raw.json"));
        $this->assertFileExists(base_path("{$testDir}/bengkulu_tengah_2026_daily.csv"));
        $this->assertFileExists(base_path("{$testDir}/bengkulu_tengah_2026_daily.json"));
        $this->assertFileExists(base_path("{$testDir}/bengkulu_tengah_2026_daily.xlsx"));
        $this->assertFileExists(base_path("{$testDir}/bengkulu_tengah_2026_monthly.csv"));
        $this->assertFileExists(base_path("{$testDir}/bengkulu_tengah_2026_summary.csv"));

        $csvContent = File::get(base_path("{$testDir}/bengkulu_tengah_2026_daily.csv"));
        $this->assertStringContainsString('2026-01-02,52,"Beras Medium",kg,13750', $csvContent);
        $this->assertStringContainsString('2026-01-05,52,"Beras Medium",kg,14000', $csvContent);
    }
}
