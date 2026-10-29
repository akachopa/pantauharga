<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\MonitoringPoint;
use App\Models\ReportItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Sp2kpPriceImportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_prices_page_shows_sp2kp_crawl_action(): void
    {
        $response = $this->withSession(['simulated_user_id' => 1])->get('/prices');

        $response->assertOk();
        $response->assertSee('Ambil SP2KP');
        $response->assertSee('Ambil data SP2KP Kemendag');
        $response->assertSee('Provinsi Bengkulu');
        $response->assertSee('>Bengkulu</div>', false);
        $response->assertSee('>Bengkulu Tengah</div>', false);
    }

    public function test_operator_does_not_see_sp2kp_crawl_action(): void
    {
        $response = $this->withSession(['simulated_user_id' => 2])->get('/prices');

        $response->assertOk();
        $response->assertDontSee('Ambil SP2KP');
    }

    public function test_admin_preview_renders_mapped_sp2kp_rows(): void
    {
        $this->fakeSp2kpSuccess();

        $response = $this->withSession(['simulated_user_id' => 1])->post(route('prices.sp2kp.preview'), $this->validPayload());

        $response->assertRedirect(route('prices.index', ['tab' => 'tabulasi', 'date' => '2026-10-27']));
        $this->get('/prices')->assertSee('Pratinjau crawling SP2KP')->assertSee('Beras Medium');

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json'
                && $request['kode_provinsi'] === '17'
                && $request['kode_kab_kota'] === '1709'
                && $request['level'] === '2';
        });
    }

    public function test_admin_import_persists_mapped_sp2kp_prices(): void
    {
        $this->fakeSp2kpSuccess();
        $commodity = Commodity::query()->where('code', 'BERAS_MEDIUM')->firstOrFail();

        $response = $this->withSession(['simulated_user_id' => 1])->post(route('prices.sp2kp.import'), $this->validPayload());

        $response->assertRedirect(route('prices.index', ['tab' => 'tabulasi', 'date' => '2026-10-27']));
        $this->assertDatabaseHas('report_items', [
            'commodity_id' => $commodity->id,
            'price' => 14500,
            'notes' => 'Impor SP2KP Kemendag — Beras Medium',
        ]);
    }

    public function test_operator_cannot_import_sp2kp_data(): void
    {
        $this->fakeSp2kpSuccess();
        $before = ReportItem::query()->count();

        $response = $this->withSession(['simulated_user_id' => 2])->post(route('prices.sp2kp.import'), $this->validPayload());

        $response->assertRedirect(route('prices.index', ['tab' => 'tabulasi']));
        $response->assertSessionHas('error');
        $this->assertSame($before, ReportItem::query()->count());
    }

    public function test_sp2kp_preview_rejects_invalid_dates(): void
    {
        Http::preventStrayRequests();

        $response = $this->withSession(['simulated_user_id' => 1])->from('/prices')->post(route('prices.sp2kp.preview'), [
            'start_date' => '27-10-2026',
            'end_date' => '2026-10-27',
            'point_id' => MonitoringPoint::query()->value('id'),
        ]);

        $response->assertRedirect('/prices');
        $response->assertSessionHasErrors(['start_date'], errorBag: 'sp2kp');
    }

    public function test_sp2kp_preview_shows_error_when_api_fails(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'api-sp2kp.kemendag.go.id/*' => Http::failedConnection(),
        ]);

        $response = $this->withSession(['simulated_user_id' => 1])->post(route('prices.sp2kp.preview'), $this->validPayload());

        $response->assertRedirect(route('prices.index', ['tab' => 'tabulasi']));
        $response->assertSessionHas('error');
    }

    /**
     * @return array{start_date: string, end_date: string, point_id: int, kode_provinsi: string, kode_kab_kota: string}
     */
    private function validPayload(): array
    {
        return [
            'start_date' => '2026-10-21',
            'end_date' => '2026-10-27',
            'point_id' => (int) MonitoringPoint::query()->value('id'),
            'kode_provinsi' => '17',
            'kode_kab_kota' => '1709',
        ];
    }

    private function fakeSp2kpSuccess(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'api-sp2kp.kemendag.go.id/*' => Http::response([
                'status' => 'success',
                'data' => [
                    [
                        'variant_id' => 52,
                        'variant' => 'Beras Medium',
                        'satuan' => 'kg',
                        'daftarHarga' => [
                            ['date' => '2026-10-20', 'harga' => 14000],
                            ['date' => '2026-10-27', 'harga' => 14500],
                        ],
                    ],
                    [
                        'variant_id' => 1,
                        'variant' => 'Beras SPHP Bulog',
                        'satuan' => 'kg',
                        'daftarHarga' => [
                            ['date' => '2026-10-27', 'harga' => 12000],
                        ],
                    ],
                ],
            ]),
        ]);
    }
}
