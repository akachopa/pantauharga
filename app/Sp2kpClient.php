<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class Sp2kpClient
{
    /**
     * @var list<int>
     */
    public const VARIANT_IDS = [
        52, 51, 1, 48, 2, 9, 10, 11, 13, 14, 15, 17, 18, 26, 27, 25,
        19, 53, 50, 20, 42, 38, 41, 31, 32, 33, 36, 35, 40, 54, 55,
        56, 57, 5, 58, 3, 4, 47, 43, 44, 45, 46,
    ];

    /**
     * @return list<array{
     *     variant_id: int,
     *     commodity_name: string,
     *     unit: string,
     *     date: string,
     *     price: float,
     *     previous_price: float,
     *     change_amount: float,
     *     change_pct: float,
     *     status: string
     * }>
     */
    public function fetch(
        string $startDate,
        string $endDate,
        ?string $provinceCode = null,
        ?string $regencyCode = null,
    ): array {
        $provinceCode ??= (string) config('services.sp2kp.province_code', '17');
        $regencyCode ??= (string) config('services.sp2kp.regency_code', '1709');
        $fetchStart = Carbon::parse($startDate)->subDays(7)->toDateString();

        $response = Http::asForm()
            ->connectTimeout(5)
            ->timeout((int) config('services.sp2kp.timeout', 30))
            ->retry([200, 500, 1000], 0, function (Throwable $exception): bool {
                return $exception instanceof ConnectionException
                    || ($exception instanceof RequestException
                        && ($exception->response->serverError() || $exception->response->status() === 429));
            })
            ->post((string) config('services.sp2kp.url'), [
                'start_date' => $fetchStart,
                'end_date' => $endDate,
                'level' => $regencyCode !== '' ? '2' : '1',
                'variant_ids' => implode(',', self::VARIANT_IDS),
                'kode_provinsi' => $provinceCode,
                'kode_kab_kota' => $regencyCode,
                'pasar_id' => '',
                'skip_sat_sun' => 'false',
                'tipe_komoditas' => '1',
            ])
            ->throw();

        $json = $response->json();
        if (($json['status'] ?? '') !== 'success' || ! isset($json['data']) || ! is_array($json['data'])) {
            throw new RuntimeException('Respons API SP2KP tidak valid: '.($json['message'] ?? 'Unknown error'));
        }

        $byId = [];
        foreach ($json['data'] as $item) {
            $byId[(int) $item['variant_id']] = $item;
        }

        $result = [];
        foreach (self::VARIANT_IDS as $variantId) {
            if (! isset($byId[$variantId])) {
                continue;
            }

            $variant = $byId[$variantId];
            $lastValidPrice = 0.0;

            foreach ($variant['daftarHarga'] ?? [] as $entry) {
                $date = (string) ($entry['date'] ?? '');
                $price = (float) ($entry['harga'] ?? 0);

                if ($price <= 0) {
                    continue;
                }

                if ($date >= $startDate && $date <= $endDate) {
                    $delta = $lastValidPrice > 0 ? ($price - $lastValidPrice) : 0.0;
                    $percent = $lastValidPrice > 0 ? (($delta / $lastValidPrice) * 100.0) : 0.0;

                    $result[] = [
                        'variant_id' => $variantId,
                        'commodity_name' => (string) $variant['variant'],
                        'unit' => (string) ($variant['satuan'] ?? 'kg'),
                        'date' => $date,
                        'price' => $price,
                        'previous_price' => $lastValidPrice,
                        'change_amount' => $delta,
                        'change_pct' => round($percent, 2),
                        'status' => $delta > 0 ? 'Naik' : ($delta < 0 ? 'Turun' : 'Tidak Berubah'),
                    ];
                }

                $lastValidPrice = $price;
            }
        }

        return $result;
    }
}
