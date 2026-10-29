<?php

namespace App;

use App\Models\Commodity;
use App\Models\DataSource;
use App\Models\MonitoringPoint;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PriceCsvImporter
{
    public function __construct(private PriceReportRecorder $priceReportRecorder) {}

    /**
     * @return array{imported: int, verified: int, skipped: int, errors: list<string>}
     */
    public function import(UploadedFile $file, int $userId): array
    {
        $author = User::query()->find($userId);
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle === false) {
            throw new RuntimeException('File CSV tidak dapat dibaca.');
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            throw new RuntimeException('CSV kosong.');
        }

        $map = $this->headerMap($header);
        $imported = 0;
        $verified = 0;
        $skipped = 0;
        $errors = [];
        $sourceId = (int) (DataSource::query()->where('code', 'SRC_PETUGAS')->value('id') ?? 1);

        DB::transaction(function () use ($handle, $map, $userId, $author, $sourceId, &$imported, &$verified, &$skipped, &$errors): void {
            $line = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if ($this->rowEmpty($row)) {
                    continue;
                }

                $pointCode = trim((string) ($row[$map['point_code']] ?? ''));
                $date = trim((string) ($row[$map['observed_at']] ?? ''));
                $commodityCode = trim((string) ($row[$map['commodity_code']] ?? ''));
                $price = (float) str_replace([',', ' '], ['.', ''], (string) ($row[$map['price']] ?? 0));
                $stock = (float) str_replace([',', ' '], ['.', ''], (string) ($row[$map['stock']] ?? 0));

                $point = MonitoringPoint::query()->where('code', $pointCode)->first();
                $commodity = Commodity::query()->where('code', $commodityCode)->first();

                if (! $point || ! $commodity || $date === '' || $price <= 0) {
                    $skipped++;
                    $errors[] = "Baris {$line}: data tidak lengkap atau tidak dikenal.";

                    continue;
                }

                $series = ReportSeries::create([
                    'point_id' => $point->id,
                    'observed_at' => $date.' 08:00:00',
                ]);

                $report = Report::create([
                    'series_id' => $series->id,
                    'source_id' => $sourceId,
                    'status' => 'submitted',
                    'submitted_by' => $userId,
                    'version_no' => 1,
                    'is_demo' => false,
                    'notes' => 'Impor CSV template harga',
                ]);

                ReportItem::create([
                    'report_id' => $report->id,
                    'commodity_id' => $commodity->id,
                    'price' => $price,
                    'stock' => max(0, $stock),
                    'unit' => $commodity->base_unit ?? 'kg',
                    'price_change_pct' => 0,
                    'price_change_amount' => 0,
                    'notes' => 'Impor CSV',
                ]);

                if ($author && $this->priceReportRecorder->approveOnEntry($report, $author)) {
                    $verified++;
                }

                $imported++;
            }
        });

        fclose($handle);

        return compact('imported', 'verified', 'skipped', 'errors');
    }

    /**
     * @param  list<string|null>  $header
     * @return array{point_code: int, observed_at: int, commodity_code: int, price: int, stock: int}
     */
    private function headerMap(array $header): array
    {
        $normalized = [];
        foreach ($header as $index => $column) {
            $key = strtolower(trim((string) $column));
            $normalized[$key] = $index;
        }

        $aliases = [
            'point_code' => ['point_code', 'kode_titik', 'titik'],
            'observed_at' => ['observed_at', 'tanggal', 'date'],
            'commodity_code' => ['commodity_code', 'kode_komoditi', 'komoditi'],
            'price' => ['price', 'harga'],
            'stock' => ['stock', 'stok'],
        ];

        $map = [];
        foreach ($aliases as $field => $names) {
            foreach ($names as $name) {
                if (isset($normalized[$name])) {
                    $map[$field] = $normalized[$name];
                    break;
                }
            }
            if (! isset($map[$field])) {
                throw new RuntimeException("Kolom CSV wajib tidak ditemukan: {$field}");
            }
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $row
     */
    private function rowEmpty(array $row): bool
    {
        return collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty();
    }
}
