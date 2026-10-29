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
# sesuaikan DB di .env
mysql -u root -e "CREATE DATABASE IF NOT EXISTS pantau_pangan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root pantau_pangan < database/dumps/pantau_pangan.sql
php artisan serve
```

Dump database lengkap ada di `database/dumps/pantau_pangan.sql`.

Jangan commit file `.env`. Gunakan `.env.example` sebagai acuan.
