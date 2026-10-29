<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Resmi Pemantauan Harga Pangan - Pemkab Bengkulu Tengah</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #111; margin: 30px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; margin: 0; text-transform: uppercase; font-weight: bold; }
        .header h2 { font-size: 16pt; margin: 4px 0; text-transform: uppercase; font-weight: bold; }
        .header p { font-size: 9.5pt; margin: 2px 0; color: #444; }
        .title { text-align: center; margin: 15px 0 20px 0; }
        .title h3 { margin: 0; text-decoration: underline; font-size: 12pt; text-transform: uppercase; }
        .meta-info { margin-bottom: 15px; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        th, td { border: 1px solid #333; padding: 7px 10px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signatures { margin-top: 40px; display: flex; justify-content: space-between; page-break-inside: avoid; }
        .sign-box { width: 45%; text-align: center; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <span style="color: #065f46; font-size: 10pt; font-weight: bold;">Dokumen Siap Cetak / Ekspor PDF</span>
        <button onclick="window.print()" style="background: #047857; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">Cetak / Simpan ke PDF</button>
    </div>

    <div class="header">
        <h1>Pemerintah Kabupaten Bengkulu Tengah</h1>
        <h2>Dinas Perdagangan, Perindustrian dan Koperasi</h2>
        <p>Komplek Perkantoran Renah Semanek, Karang Tinggi, Bengkulu Tengah, Bengkulu</p>
        <p>Portal Sistem Informasi Pemantauan Harga dan Ketersediaan Pangan (PANTAU HARGA)</p>
    </div>

    <div class="title">
        <h3>Laporan Rekapitulasi Harga dan Pasokan Komoditas Pangan Pokok</h3>
        <span style="font-size: 10pt; color: #555;">Periode Pengamatan: 27 Oktober 2026</span>
    </div>

    <div class="meta-info">
        <strong>Wilayah Cakupan:</strong> Seluruh Pasar Rakyat Kabupaten Bengkulu Tengah<br>
        <strong>Status Data:</strong> Terverifikasi Tim Pengawas &amp; Verifikator Disperindag
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Komoditi Pangan</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga Min (Rp)</th>
                <th>Harga Rata-rata (Rp)</th>
                <th>Harga Maks (Rp)</th>
                <th>Total Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $idx => $it)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td><strong>{{ $it->name }}</strong></td>
                    <td>{{ $it->category }}</td>
                    <td class="text-center">{{ $it->base_unit }}</td>
                    <td class="text-right">Rp{{ number_format($it->min_price, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold; background-color: #fafafa;">Rp{{ number_format($it->avg_price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($it->max_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($it->total_stock, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 40px; width: 100%; display: table;">
        <div style="display: table-row;">
            <div style="display: table-cell; width: 50%; text-align: center;">
                <p>Mengetahui,<br>Kepala Dinas Perdagangan, Perindustrian<br>dan Koperasi Bengkulu Tengah</p>
                <br><br><br>
                <p><strong><u>Drs. H. Pimpinan Daerah, M.Si</u></strong><br>NIP. 19750918 199903 1 005</p>
            </div>
            <div style="display: table-cell; width: 50%; text-align: center;">
                <p>Karang Tinggi, 27 Oktober 2026<br>Verifikator Data Pangan Daerah</p>
                <br><br><br>
                <p><strong><u>Ahmad Verifikator, S.E.</u></strong><br>NIP. 19800512 200501 1 004</p>
            </div>
        </div>
    </div>
</body>
</html>
