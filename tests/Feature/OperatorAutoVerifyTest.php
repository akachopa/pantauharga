<?php

namespace Tests\Feature;

use App\Models\Commodity;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\ReportSeries;
use App\PriceReportRecorder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Feature\Concerns\ManagesRoleUsers;
use Tests\TestCase;

class OperatorAutoVerifyTest extends TestCase
{
    use ManagesRoleUsers;

    public function test_operator_input_is_verified_on_entry(): void
    {
        $operator = $this->userWithRole('operator');

        $response = $this->actingAs($operator)->post(route('prices.store'), $this->payload());

        $response->assertSessionHas('success', 'Data harga dan stok berhasil dicatat dan langsung terverifikasi.');
        $report = Report::query()->where('submitted_by', $operator->id)->sole();
        $this->assertSame('approved', $report->status);
        $this->assertSame($operator->id, (int) $report->verified_by);
        $this->assertNotNull($report->verified_at);
        $this->assertDatabaseHas('verification_events', [
            'report_id' => $report->id,
            'decision' => 'approved',
            'note' => PriceReportRecorder::AUTO_VERIFY_NOTE,
        ]);
    }

    public function test_operator_draft_stays_draft(): void
    {
        $operator = $this->userWithRole('operator');

        $this->actingAs($operator)->post(route('prices.store'), $this->payload(['status' => 'draft']));

        $report = Report::query()->where('submitted_by', $operator->id)->sole();
        $this->assertSame('draft', $report->status);
        $this->assertNull($report->verified_by);
        $this->assertFalse(DB::table('verification_events')->where('report_id', $report->id)->exists());
    }

    public function test_verifier_input_still_needs_another_verifier(): void
    {
        $verifier = $this->userWithRole('verifikator');

        $response = $this->actingAs($verifier)->post(route('prices.store'), $this->payload());

        $response->assertSessionHas('success', 'Data harga dan stok berhasil dicatat dan menunggu verifikasi.');
        $this->assertSame('submitted', Report::query()->where('submitted_by', $verifier->id)->sole()->status);
    }

    public function test_operator_csv_import_is_verified_on_entry(): void
    {
        $operator = $this->userWithRole('operator');

        $response = $this->actingAs($operator)->post(route('prices.import'), ['csv_file' => $this->priceCsv()]);

        $response->assertSessionHas('success', 'Impor CSV selesai: 1 baris masuk, 1 langsung terverifikasi.');
        $report = Report::query()->where('submitted_by', $operator->id)->sole();
        $this->assertSame('approved', $report->status);
        $this->assertSame($operator->id, (int) $report->verified_by);
    }

    public function test_verifier_csv_import_waits_for_verification(): void
    {
        $verifier = $this->userWithRole('verifikator');

        $response = $this->actingAs($verifier)->post(route('prices.import'), ['csv_file' => $this->priceCsv()]);

        $response->assertSessionHas('success', 'Impor CSV selesai: 1 baris masuk.');
        $this->assertSame('submitted', Report::query()->where('submitted_by', $verifier->id)->sole()->status);
    }

    public function test_admin_sp2kp_import_is_verified_on_entry(): void
    {
        $admin = $this->userWithRole('admin');
        $point = MonitoringPoint::query()->where('is_active', true)->whereNotIn('id', ReportSeries::query()->whereDate('observed_at', '2026-10-25')->select('point_id'))->firstOrFail();
        Http::preventStrayRequests();
        Http::fake([
            'api-sp2kp.kemendag.go.id/*' => Http::response([
                'status' => 'success',
                'data' => [[
                    'variant_id' => 52,
                    'variant' => 'Beras Medium',
                    'satuan' => 'kg',
                    'daftarHarga' => [['date' => '2026-10-25', 'harga' => 14650]],
                ]],
            ]),
        ]);

        $response = $this->actingAs($admin)->post(route('prices.sp2kp.import'), [
            'start_date' => '2026-10-25',
            'end_date' => '2026-10-25',
            'point_id' => $point->id,
            'kode_provinsi' => '17',
            'kode_kab_kota' => '1709',
        ]);

        $response->assertSessionHas('success', fn (string $message): bool => str_contains($message, '1 laporan langsung terverifikasi.'));
        $report = Report::query()->where('submitted_by', $admin->id)->sole();
        $this->assertSame('approved', $report->status);
        $this->assertSame($admin->id, (int) $report->verified_by);
    }

    private function priceCsv(): UploadedFile
    {
        $point = MonitoringPoint::query()->whereNotNull('code')->where('is_active', true)->firstOrFail();
        $commodity = Commodity::query()->whereNotNull('code')->where('is_active', true)->firstOrFail();

        return UploadedFile::fake()->createWithContent(
            'impor.csv',
            "point_code,observed_at,commodity_code,price,stock\n{$point->code},2026-10-28,{$commodity->code},15900,20\n",
        );
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return $overrides + [
            'point_id' => MonitoringPoint::query()->where('is_active', true)->value('id'),
            'observed_at' => '2026-10-28',
            'commodity_id' => Commodity::query()->where('is_active', true)->value('id'),
            'price' => 15300,
            'stock' => 25,
        ];
    }
}
