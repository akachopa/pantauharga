<?php

namespace App;

use App\Models\Commodity;
use App\Models\DataSource;
use App\Models\Report;
use App\Models\ReportItem;
use App\Models\ReportSeries;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Sp2kpPriceImporter
{
    /**
     * @var array<int, string>
     */
    public const VARIANT_CODES = [
        52 => 'BERAS_MEDIUM',
        51 => 'BERAS_PREMIUM',
        1 => 'BERAS_SPHP',
        48 => 'KEDELAI_IMPOR',
        2 => 'CABAI_MERAH',
        9 => 'CABAI_MERAH_BESAR',
        10 => 'CABAI_RAWIT_MERAH',
        11 => 'CABAI_RAWIT',
        13 => 'BAWANG_MERAH',
        14 => 'GULA_PASIR',
        15 => 'GULA_PASIR_KEMASAN',
        16 => 'MINYAK_GORENG',
        17 => 'MINYAK_GORENG_PREMIUM',
        18 => 'MINYAKITA',
        26 => 'TEPUNG_TERIGU',
        27 => 'DAGING_AYAM',
        25 => 'TELUR_AYAM',
        19 => 'DAGING_SAPI',
        53 => 'DAGING_SAPI_PAHA_DEPAN',
        50 => 'DAGING_SAPI_BEKU',
        20 => 'IKAN_TONGKOL',
        42 => 'MIE_INSTAN',
        38 => 'BAWANG_PUTIH',
        41 => 'GARAM_HALUS',
        31 => 'SUSU_KENTAL_MANIS',
        32 => 'SUSU_BUBUK',
        33 => 'SUSU_BUBUK_BALITA',
        36 => 'TEMPE_BUNGKUS',
        35 => 'TAHU_PUTIH',
        40 => 'UDANG_BASAH',
        54 => 'PISANG_LOKAL',
        55 => 'JERUK_LOKAL',
        56 => 'TOMAT',
        57 => 'KENTANG_SEDANG',
        5 => 'SAWI_HIJAU',
        58 => 'KANGKUNG',
        3 => 'KETIMUN_SEDANG',
        4 => 'KACANG_PANJANG',
        47 => 'KETELA_POHON',
        43 => 'DAGING_AYAM_KAMPUNG',
        44 => 'TELUR_AYAM_KAMPUNG',
        45 => 'KACANG_TANAH',
        46 => 'KACANG_HIJAU',
    ];

    public function __construct(
        private Sp2kpClient $client,
        private PriceReportRecorder $priceReportRecorder,
    ) {}

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array{mapped: list<array<string, mixed>>, unmapped: list<string>}
     */
    public function classify(array $rows): array
    {
        $commodities = Commodity::query()
            ->where('is_active', true)
            ->get(['id', 'code', 'name', 'base_unit'])
            ->keyBy('code');

        $mapped = [];
        $unmapped = [];

        foreach ($rows as $row) {
            $code = self::VARIANT_CODES[(int) $row['variant_id']] ?? null;
            $commodity = $code ? $commodities->get($code) : null;

            if (! $commodity) {
                $unmapped[] = (string) $row['commodity_name'];

                continue;
            }

            $mapped[] = array_merge($row, [
                'commodity_id' => $commodity->id,
                'local_name' => $commodity->name,
                'local_unit' => $commodity->base_unit,
            ]);
        }

        return [
            'mapped' => $mapped,
            'unmapped' => array_values(array_unique($unmapped)),
        ];
    }

    /**
     * @return array{imported: int, updated: int, verified: int, skipped: int, unmapped: list<string>}
     */
    public function import(
        string $startDate,
        string $endDate,
        int $pointId,
        int $userId,
        string $provinceCode = '17',
        string $regencyCode = '',
    ): array {
        $classified = $this->classify(
            $this->client->fetch($startDate, $endDate, $provinceCode, $regencyCode)
        );

        $source = DataSource::query()->firstOrCreate(
            ['code' => 'SRC_SP2KP'],
            [
                'name' => 'SP2KP Kemendag (Kab. Bengkulu Tengah)',
                'source_type' => 'instansi',
            ]
        );

        $imported = 0;
        $updated = 0;
        $verified = 0;
        $author = User::query()->find($userId);

        DB::transaction(function () use ($classified, $pointId, $userId, $author, $source, &$imported, &$updated, &$verified): void {
            $byDate = collect($classified['mapped'])->groupBy('date');

            foreach ($byDate as $date => $items) {
                $series = $this->seriesForSource((int) $pointId, (string) $date, $source->id);
                $report = $this->reportForSeries($series, $source->id, $userId, (string) $date);
                if ($author && $this->priceReportRecorder->approveOnEntry($report, $author)) {
                    $verified++;
                }

                foreach ($items as $item) {
                    $existing = ReportItem::query()
                        ->where('report_id', $report->id)
                        ->where('commodity_id', $item['commodity_id'])
                        ->first();

                    $payload = [
                        'price' => $item['price'],
                        'stock' => 0,
                        'unit' => $item['local_unit'] ?: $item['unit'],
                        'price_change_pct' => $item['change_pct'],
                        'price_change_amount' => $item['change_amount'],
                        'notes' => 'Impor SP2KP Kemendag — '.$item['commodity_name'],
                    ];

                    if ($existing) {
                        $existing->update($payload);
                        $updated++;

                        continue;
                    }

                    ReportItem::create(array_merge($payload, [
                        'report_id' => $report->id,
                        'commodity_id' => $item['commodity_id'],
                    ]));
                    $imported++;
                }
            }
        });

        return [
            'imported' => $imported,
            'updated' => $updated,
            'verified' => $verified,
            'skipped' => count($classified['unmapped']),
            'unmapped' => $classified['unmapped'],
        ];
    }

    private function seriesForSource(int $pointId, string $date, int $sourceId): ReportSeries
    {
        $existing = ReportSeries::query()
            ->where('point_id', $pointId)
            ->whereDate('observed_at', $date)
            ->whereHas('reports', fn ($query) => $query->where('source_id', $sourceId))
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return ReportSeries::create([
            'point_id' => $pointId,
            'observed_at' => $date.' 08:00:00',
        ]);
    }

    private function reportForSeries(ReportSeries $series, int $sourceId, int $userId, string $date): Report
    {
        $existing = Report::query()
            ->where('series_id', $series->id)
            ->where('source_id', $sourceId)
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return Report::create([
            'series_id' => $series->id,
            'source_id' => $sourceId,
            'status' => 'submitted',
            'submitted_by' => $userId,
            'version_no' => 1,
            'is_demo' => false,
            'notes' => 'Hasil crawling SP2KP Kemendag tanggal '.$date,
        ]);
    }
}
