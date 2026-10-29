<?php

namespace Tests\Feature;

use App\Models\ActionPlan;
use App\Models\Commodity;
use App\Models\Market;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PantauPanganTest extends TestCase
{
    public function test_dashboard_page_loads_successfully()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('PANTAU HARGA');
        $response->assertSee('Bengkulu Tengah');
        $response->assertSee('Beras');
    }

    public function test_prices_page_and_crud()
    {
        $response = $this->get('/prices');
        $response->assertStatus(200);
        $response->assertSee('Komoditi');
        $response->assertSee('Klik status untuk verifikasi cepat');
        $response->assertSee('Data lapangan yang rapi dan terverifikasi.');
        $response->assertSee('Input Data');

        // Test POST new price
        $point = MonitoringPoint::first();
        $commodity = Commodity::first();

        $postResponse = $this->post('/prices', [
            'point_id' => $point->id,
            'observed_at' => '2026-10-27',
            'commodity_id' => $commodity->id,
            'price' => 15200,
            'stock' => 150,
            'notes' => 'Pengujian input data',
        ]);

        $postResponse->assertRedirect('/prices?tab=tabulasi');
        $this->assertDatabaseHas('report_items', [
            'commodity_id' => $commodity->id,
            'price' => 15200,
        ]);
    }

    public function test_master_data_page_and_market_creation()
    {
        $response = $this->get('/master');
        $response->assertStatus(200);
        $response->assertSee('Master Data');
        $response->assertSee('Satu referensi untuk seluruh pemantauan.');
        $response->assertSee('Tambah Data');
        $response->assertSee('Penugasan');

        // Test create market
        $postResponse = $this->post('/master/markets', [
            'name' => 'Pasar Uji Coba Bang Haji',
            'region_id' => 8,
            'address' => 'Bang Haji, Bengkulu Tengah',
            'market_days' => 'Rabu',
        ]);

        $postResponse->assertRedirect('/master?tab=pasar');
        $this->assertDatabaseHas('markets', [
            'name' => 'Pasar Uji Coba Bang Haji',
        ]);
    }

    public function test_policies_page()
    {
        $response = $this->get('/policies');
        $response->assertStatus(200);
        $response->assertSee('Paket Kebijakan');
        $response->assertSee('Stabilisasi harga');
        $response->assertSee('Ubah temuan menjadi rekomendasi yang terarah.');
        $response->assertSee('Kondisi pemicu');
        $response->assertSee('Simpan draft');
    }

    public function test_actions_page_and_creation()
    {
        $response = $this->get('/actions');
        $response->assertStatus(200);
        $response->assertSee('Tindak Lanjut');

        $postResponse = $this->post('/actions', [
            'title' => 'Distribusi Sembako Bersubsidi',
            'priority' => 'tinggi',
            'status' => 'dalam_proses',
            'progress_pct' => 40,
            'description' => 'Penyaluran beras dan gula murah',
        ]);

        $postResponse->assertRedirect('/actions');
        $this->assertDatabaseHas('action_plans', [
            'title' => 'Distribusi Sembako Bersubsidi',
        ]);
    }

    public function test_reports_page_and_exports()
    {
        $response = $this->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Laporan Pangan');

        // Export PDF
        $pdfResponse = $this->get('/reports/export/pdf');
        $pdfResponse->assertStatus(200);

        // Export Excel
        $excelResponse = $this->get('/reports/export/excel');
        $excelResponse->assertStatus(200);
    }

    public function test_settings_and_role_switcher()
    {
        $response = $this->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan');

        // Test switch user to Operator (id: 2)
        $switchResponse = $this->get('/switch-user/2');
        $switchResponse->assertStatus(302);
        $this->assertEquals(2, auth()->id());
    }
}
