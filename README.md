# Pantau Harga

Sistem informasi pemantauan harga dan stok pangan Kabupaten Bengkulu Tengah (Laravel).

## Fitur utama

- Portal publik harga terverifikasi dan laporan koreksi warga
- Input harga/stok, draft, verifikasi, impor CSV, crawl SP2KP
- Master data pasar, titik pantau, mitra, komoditi, penugasan
- Radar peta sebaran harga/stok (Leaflet)
- Rantai pasok: relasi mitra–titik dan jadwal kiriman
- Paket kebijakan, tindak lanjut, laporan, pengaturan peran

## Stack

- Laravel (PHP 8.3)
- MySQL
- Blade + Tailwind CDN

## Setup singkat

```bash
composer install
cp .env.example .env
php artisan key:generate
# sesuaikan DB di .env, lalu impor skema/SQL proyek atau jalankan migrasi terkait
php artisan serve
```

Jangan commit file `.env`. Gunakan `.env.example` sebagai acuan.
