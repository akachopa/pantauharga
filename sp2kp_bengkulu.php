<?php

/**
 * Mengambil data perbandingan harga barang kebutuhan pokok SP2KP Kemendag.
 *
 * @param  string  $startDate  Tanggal awal format YYYY-MM-DD (misal: '2026-01-01')
 * @param  string|null  $endDate  Tanggal akhir format YYYY-MM-DD (misal: date('Y-m-d'))
 * @param  string  $kodeProvinsi  Kode provinsi SP2KP ('17' = Bengkulu)
 * @param  string  $kodeKabKota  Kode kab/kota SP2KP ('1709' = Bengkulu Tengah, kosong '' = Provinsi)
 * @param  array|null  $variantIds  Daftar ID varian (null untuk mengambil seluruh varian yang tersedia)
 * @return array Array asosiatif berisi daftar perbandingan harga harian
 *
 * @throws RuntimeException Jika request cURL atau respons API gagal
 */
function getPerbandinganHargaSp2kp(
    string $startDate = '2026-01-01',
    ?string $endDate = null,
    string $kodeProvinsi = '17',
    string $kodeKabKota = '1709',
    ?array $variantIds = null
): array {
    $endDate = $endDate ?? date('Y-m-d');
    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
        throw new InvalidArgumentException('Format tanggal harus YYYY-MM-DD.');
    }

    // Ambil 7 hari sebelum startDate agar hari pertama punya harga pembanding hari kerja sebelumnya
    $fetchStart = date('Y-m-d', strtotime($startDate.' -7 days'));

    $postData = [
        'start_date' => $fetchStart,
        'end_date' => $endDate,
        'level' => $kodeKabKota !== '' ? '2' : '1',
        'kode_provinsi' => $kodeProvinsi,
        'kode_kab_kota' => $kodeKabKota,
        'pasar_id' => '',
        'skip_sat_sun' => 'false',
        'tipe_komoditas' => '1',
    ];

    if ($variantIds !== null && count($variantIds) > 0) {
        $postData['variant_ids'] = implode(',', $variantIds);
    }

    $ch = curl_init('https://api-sp2kp.kemendag.go.id/report/api/average-price/export-area-daily-json');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POSTFIELDS => $postData,
    ]);

    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false || $httpCode !== 200) {
        throw new RuntimeException("cURL gagal (HTTP $httpCode): $err");
    }

    $json = json_decode($raw, true);
    if (($json['status'] ?? '') !== 'success' || ! isset($json['data']) || ! is_array($json['data'])) {
        throw new RuntimeException('Respons API SP2KP tidak valid: '.($json['message'] ?? 'Unknown error'));
    }

    $result = [];

    foreach ($json['data'] as $v) {
        $lastValidHarga = 0.0;

        foreach ($v['daftarHarga'] ?? [] as $entry) {
            $tgl = (string) ($entry['date'] ?? '');
            $harga = (float) ($entry['harga'] ?? 0);

            if ($harga <= 0) {
                continue; // Lewati hari Sabtu/Minggu/libur nasional (harga = 0)
            }

            if ($tgl >= $startDate && $tgl <= $endDate) {
                $delta = $lastValidHarga > 0 ? ($harga - $lastValidHarga) : 0.0;
                $persen = $lastValidHarga > 0 ? (($delta / $lastValidHarga) * 100.0) : 0.0;
                $status = $delta > 0 ? 'Naik' : ($delta < 0 ? 'Turun' : 'Tidak Berubah');
                $deltaStr = $delta > 0
                    ? '+Rp '.number_format($delta, 0, ',', '.')
                    : ($delta < 0 ? '-Rp '.number_format(abs($delta), 0, ',', '.') : 'Rp 0');

                $result[] = [
                    'variant_id' => (int) ($v['variant_id'] ?? 0),
                    'Komoditas' => $v['variant'] ?? '',
                    'Unit' => $v['satuan'] ?? '',
                    'Tanggal(yyyy-mm-dd)' => $tgl,
                    'Perubahan dari kemarin' => sprintf('%s %.2f %% (%s)', $status, $persen, $deltaStr),
                    'harga_kemarin' => $lastValidHarga,
                    'harga_hari_ini' => $harga,
                    'selisih_rp' => $delta,
                    'persen_perubahan' => round($persen, 2),
                    'status_perubahan' => $status,
                ];
            }

            $lastValidHarga = $harga;
        }
    }

    return $result;
}

// Self-check saat dijalankan langsung dari CLI: php sp2kp_bengkulu.php
if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    echo "Mengambil data SP2KP Kabupaten Bengkulu Tengah sepanjang 2026...\n";
    $rows = getPerbandinganHargaSp2kp('2026-01-01', '2026-10-29', '17', '1709');
    assert(count($rows) > 0, 'Data tidak boleh kosong');
    echo 'Total baris data terambil: '.count($rows).PHP_EOL;
    echo "Contoh 3 baris data terakhir:\n";
    print_r(array_slice($rows, -3));
}
