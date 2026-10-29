<?php

/**
 * Mengambil data perbandingan harga barang kebutuhan pokok SP2KP Kemendag.
 *
 * @param string $startDate    Tanggal awal format YYYY-MM-DD (misal: '2026-01-01')
 * @param string $endDate      Tanggal akhir format YYYY-MM-DD (misal: date('Y-m-d'))
 * @param string $kodeProvinsi Kode provinsi SP2KP ('17' = Bengkulu)
 * @return array               Array asosiatif berisi daftar perbandingan harga harian
 * @throws RuntimeException    Jika request cURL atau respons API gagal
 */
function getPerbandinganHargaSp2kp(
    string $startDate = '2026-01-01',
    ?string $endDate = null,
    string $kodeProvinsi = '17'
): array {
    $endDate = $endDate ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        throw new InvalidArgumentException('Format tanggal harus YYYY-MM-DD.');
    }

    // Ambil 7 hari sebelum startDate agar hari pertama punya harga pembanding hari kerja sebelumnya
    $fetchStart = date('Y-m-d', strtotime($startDate . ' -7 days'));
    $variantOrder = [52, 51, 1, 14, 17, 16, 18, 19, 27, 25, 26, 48, 2, 10, 9, 13, 38];

    $ch = curl_init('https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_POSTFIELDS     => [
            'start_date'     => $fetchStart,
            'end_date'       => $endDate,
            'level'          => '1',
            'variant_ids'    => implode(',', $variantOrder),
            'kode_provinsi'  => $kodeProvinsi,
            'kode_kab_kota'  => '',
            'pasar_id'       => '',
            'skip_sat_sun'   => 'false',
            'tipe_komoditas' => '1',
        ],
    ]);

    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false || $httpCode !== 200) {
        throw new RuntimeException("cURL gagal (HTTP $httpCode): $err");
    }

    $json = json_decode($raw, true);
    if (($json['status'] ?? '') !== 'success' || !isset($json['data']) || !is_array($json['data'])) {
        throw new RuntimeException('Respons API SP2KP tidak valid: ' . ($json['message'] ?? 'Unknown error'));
    }

    $byId = [];
    foreach ($json['data'] as $item) {
        $byId[(int) $item['variant_id']] = $item;
    }

    $result = [];
    foreach ($variantOrder as $vid) {
        if (!isset($byId[$vid])) {
            continue;
        }
        $v = $byId[$vid];
        $lastValidHarga = 0.0;

        foreach ($v['daftarHarga'] ?? [] as $entry) {
            $tgl = $entry['date'];
            $harga = (float) $entry['harga'];

            if ($harga <= 0) {
                continue; // Lewati hari Sabtu/Minggu/libur nasional (harga = 0)
            }

            if ($tgl >= $startDate) {
                $delta = $lastValidHarga > 0 ? ($harga - $lastValidHarga) : 0.0;
                $persen = $lastValidHarga > 0 ? (($delta / $lastValidHarga) * 100.0) : 0.0;
                $status = $delta > 0 ? 'Naik' : ($delta < 0 ? 'Turun' : 'Tidak Berubah');
                $deltaStr = $delta > 0
                    ? '+Rp ' . number_format($delta, 0, ',', '.')
                    : ($delta < 0 ? '-Rp ' . number_format(abs($delta), 0, ',', '.') : 'Rp 0');

                $result[] = [
                    'Komoditas'              => $v['variant'],
                    'Unit'                   => $v['satuan'],
                    'Tanggal(yyyy-mm-dd)'    => $tgl,
                    'Perubahan dari kemarin' => sprintf('%s %.2f %% (%s)', $status, $persen, $deltaStr),
                    'harga_kemarin'          => $lastValidHarga,
                    'harga_hari_ini'         => $harga,
                    'selisih_rp'             => $delta,
                    'persen_perubahan'       => round($persen, 2),
                    'status_perubahan'       => $status,
                ];
            }

            $lastValidHarga = $harga;
        }
    }

    return $result;
}

// Self-check saat dijalankan langsung dari CLI: php sp2kp_bengkulu.php
if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    $rows = getPerbandinganHargaSp2kp('2026-01-01', '2026-10-27', '17');
    assert(count($rows) > 0, 'Data tidak boleh kosong');
    echo "Total baris: " . count($rows) . PHP_EOL;
    print_r(array_slice($rows, -3));
}
