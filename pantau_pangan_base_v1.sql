-- PANTAU PANGAN Base Schema v1 for Kabupaten Bengkulu Tengah
SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS verification_events;
DROP TABLE IF EXISTS report_items;
DROP TABLE IF EXISTS reports;
DROP TABLE IF EXISTS report_series;
DROP TABLE IF EXISTS data_sources;
DROP TABLE IF EXISTS action_plans;
DROP TABLE IF EXISTS point_assignments;
DROP TABLE IF EXISTS monitoring_points;
DROP TABLE IF EXISTS markets;
DROP TABLE IF EXISTS businesses;
DROP TABLE IF EXISTS commodities;
DROP TABLE IF EXISTS organizations;
DROP TABLE IF EXISTS regions;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS users;

-- 1. Regions (Wilayah Kabupaten & Kecamatan di Bengkulu Tengah)
CREATE TABLE regions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    level ENUM('provinsi','kabupaten','kecamatan','desa') NOT NULL DEFAULT 'kecamatan',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES regions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Organizations (Dinas / Instansi Pemkab Bengkulu Tengah)
CREATE TABLE organizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    region_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    address TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Users
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    organization_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    nip VARCHAR(50) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_demo BOOLEAN NOT NULL DEFAULT FALSE,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Roles & Permissions (V1)
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(60) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_roles (
    user_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE role_permissions (
    role_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Markets (Pasar)
CREATE TABLE markets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    region_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    address TEXT NULL,
    market_days VARCHAR(100) NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Monitoring Points (Titik Pantau Pasar / Toko / Agen)
CREATE TABLE monitoring_points (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    market_id BIGINT UNSIGNED NULL,
    region_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    point_type ENUM('pasar_tradisional','toko_kelontong','agen_distributor') NOT NULL DEFAULT 'pasar_tradisional',
    is_demo BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (market_id) REFERENCES markets(id) ON DELETE SET NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Businesses (Mitra / Pelaku Usaha Pangan)
CREATE TABLE businesses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    region_id BIGINT UNSIGNED NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    owner_name VARCHAR(150) NOT NULL,
    business_type VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    is_verified BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Point Assignments (Penugasan Petugas / Mitra ke Titik Pantau)
CREATE TABLE point_assignments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    point_id BIGINT UNSIGNED NOT NULL,
    starts_on DATE NOT NULL,
    ends_on DATE NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (point_id) REFERENCES monitoring_points(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Commodities (Komoditi Pangan Pokok & Strategis)
CREATE TABLE commodities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    base_unit VARCHAR(20) NOT NULL DEFAULT 'kg',
    icon VARCHAR(20) NOT NULL DEFAULT '🌾',
    heir_category VARCHAR(100) NULL,
    reference_price DECIMAL(16,2) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Data Sources
CREATE TABLE data_sources (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    source_type ENUM('petugas_pasar','mitra','instansi','simulation') NOT NULL DEFAULT 'petugas_pasar',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Report Series & Reports (Pencatatan & Siklus Laporan Harga)
CREATE TABLE report_series (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    point_id BIGINT UNSIGNED NOT NULL,
    observed_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (point_id) REFERENCES monitoring_points(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    series_id BIGINT UNSIGNED NOT NULL,
    source_id BIGINT UNSIGNED NOT NULL,
    status ENUM('draft','submitted','approved','returned','rejected','superseded') NOT NULL DEFAULT 'draft',
    submitted_by BIGINT UNSIGNED NOT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    previous_report_id BIGINT UNSIGNED NULL,
    version_no INT NOT NULL DEFAULT 1,
    is_demo BOOLEAN NOT NULL DEFAULT FALSE,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (series_id) REFERENCES report_series(id),
    FOREIGN KEY (source_id) REFERENCES data_sources(id),
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (previous_report_id) REFERENCES reports(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE report_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_id BIGINT UNSIGNED NOT NULL,
    commodity_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(16,2) NOT NULL,
    stock DECIMAL(16,2) NOT NULL DEFAULT 0,
    unit VARCHAR(20) NOT NULL DEFAULT 'kg',
    price_change_pct DECIMAL(8,2) NOT NULL DEFAULT 0,
    price_change_amount DECIMAL(16,2) NOT NULL DEFAULT 0,
    notes VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (commodity_id) REFERENCES commodities(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Action Plans (Tindak Lanjut)
CREATE TABLE action_plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    priority ENUM('tinggi','sedang','rendah') NOT NULL DEFAULT 'sedang',
    status ENUM('antrean','dalam_proses','selesai','terlambat') NOT NULL DEFAULT 'antrean',
    assigned_to BIGINT UNSIGNED NULL,
    deadline DATE NULL,
    progress_pct INT NOT NULL DEFAULT 0,
    target_date DATE NULL,
    notes TEXT NULL,
    is_demo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Verification Events & Audit Logs
CREATE TABLE verification_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    report_id BIGINT UNSIGNED NOT NULL,
    actor_id BIGINT UNSIGNED NOT NULL,
    decision VARCHAR(20) NOT NULL,
    note TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES reports(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_id BIGINT UNSIGNED NOT NULL,
    entity_type VARCHAR(60) NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(60) NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (actor_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Base Data (Kabupaten Bengkulu Tengah, Wilayah, Dinas, Akun)
-- 1. Regions
INSERT INTO regions (id, parent_id, code, name, level) VALUES
(1, NULL, 'BENGKULU_TENGAH', 'Kabupaten Bengkulu Tengah', 'kabupaten'),
(2, 1, 'KARANG_TINGGI', 'Karang Tinggi', 'kecamatan'),
(3, 1, 'TALANG_EMPAT', 'Talang Empat', 'kecamatan'),
(4, 1, 'PONDOK_KELAPA', 'Pondok Kelapa', 'kecamatan'),
(5, 1, 'TABA_PENANJUNG', 'Taba Penanjung', 'kecamatan'),
(6, 1, 'PONDOK_KUBANG', 'Pondok Kubang', 'kecamatan'),
(7, 1, 'PEMATANG_TIGA', 'Pematang Tiga', 'kecamatan'),
(8, 1, 'BANG_HAJI', 'Bang Haji', 'kecamatan'),
(9, 1, 'SEMIDANG_LAGAN', 'Semidang Lagan', 'kecamatan');

-- 2. Organizations
INSERT INTO organizations (id, region_id, code, name, address) VALUES
(1, 1, 'DISPERINDAG', 'Dinas Perdagangan, Perindustrian dan Koperasi Kab. Bengkulu Tengah', 'Komplek Perkantoran Renah Semanek, Karang Tinggi'),
(2, 1, 'DKP', 'Dinas Ketahanan Pangan dan Perikanan Bengkulu Tengah', 'Karang Tinggi, Bengkulu Tengah');

-- 3. Roles V1
INSERT INTO roles (id, code, name) VALUES
(1, 'admin', 'Administrator'),
(2, 'operator', 'Operator Lapangan'),
(3, 'verifikator', 'Verifikator Dinas'),
(4, 'pimpinan', 'Pimpinan / Kadis'),
(5, 'mitra', 'Mitra Usaha');

-- 4. Initial Permissions V1 (only those not inserted in v2)
INSERT INTO permissions (id, code, description) VALUES
(1, 'report.create', 'Input laporan harga'),
(2, 'report.verify', 'Verifikasi laporan pangan'),
(3, 'correction.review', 'Tinjau koreksi masyarakat'),
(4, 'point.manage', 'Kelola titik pantau'),
(5, 'supply.manage', 'Kelola pasokan'),
(6, 'analytics.read', 'Baca analitik'),
(7, 'report.export', 'Ekspor laporan'),
(8, 'action.assign', 'Tugaskan tindak lanjut'),
(9, 'action.update', 'Perbarui tindak lanjut'),
(10, 'alerts.validate', 'Validasi peringatan dini');

-- 5. Users (Password: 'password' hashed with bcrypt $2y$12$D24p... / standard laravel Hash::make('password'))
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi is 'password'
INSERT INTO users (id, organization_id, name, email, password, phone, nip, is_active, is_demo) VALUES
(1, 1, 'Administrator Dinas', 'admin@pantaupangan.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', '198501012010011001', TRUE, FALSE),
(2, 1, 'Operator Lapangan A (Karang Tinggi)', 'operator@pantaupangan.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', '199203152015021002', TRUE, FALSE),
(3, 1, 'Operator Lapangan B (Taba Penanjung)', 'operator2@pantaupangan.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', '199407202018011003', TRUE, FALSE),
(4, 1, 'Verifikator Disperindag', 'verifikator@pantaupangan.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567893', '198005122005011004', TRUE, FALSE),
(5, 1, 'Kepala Dinas / Pimpinan', 'pimpinan@pantaupangan.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567894', '197509181999031005', TRUE, FALSE),
(6, NULL, 'Mitra Usaha Sembako Berkah', 'mitra@pantaupangan.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567895', NULL, TRUE, FALSE);

-- Assign User Roles
INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1), -- Admin
(2, 2), -- Operator A
(3, 2), -- Operator B
(4, 3), -- Verifikator
(5, 4), -- Pimpinan
(6, 5); -- Mitra

-- 6. Markets
INSERT INTO markets (id, region_id, code, name, address, market_days, latitude, longitude, is_active) VALUES
(1, 2, 'MKT-KT-01', 'Pasar Karang Tinggi', 'Jl. Lintas Bengkulu-Kepahiang Km. 28, Karang Tinggi', 'Senin, Kamis', -3.73245000, 102.43120000, TRUE),
(2, 5, 'MKT-TP-01', 'Pasar Taba Penanjung', 'Jl. Raya Taba Penanjung, Bengkulu Tengah', 'Selasa, Sabtu', -3.69820000, 102.47890000, TRUE),
(3, 3, 'MKT-TE-01', 'Pasar Talang Empat', 'Kembang Seri, Talang Empat', 'Rabu, Minggu', -3.78450000, 102.39210000, TRUE),
(4, 4, 'MKT-PK-01', 'Pasar Pondok Kelapa', 'Jl. Lintas Barat Sumatera, Pondok Kelapa', 'Setiap Hari', -3.71230000, 102.28900000, TRUE),
(5, 2, 'MKT-SIM-A', 'Pasar Simulasi A', 'Kawasan Sentra Pangan Simulasi Renah Semanek', 'Setiap Hari', -3.73500000, 102.43500000, TRUE),
(6, 3, 'MKT-SIM-B', 'Pasar Simulasi B', 'Kawasan Distribusi Simulasi Ujung Karang', 'Setiap Hari', -3.78000000, 102.39900000, TRUE);

-- 7. Monitoring Points
INSERT INTO monitoring_points (id, market_id, region_id, code, name, point_type, is_demo, is_active) VALUES
(1, 1, 2, 'PT-01', 'Pasar Karang Tinggi (Titik Utama)', 'pasar_tradisional', FALSE, TRUE),
(2, 1, 2, 'PT-02', 'Kios Sembako Bu Siti - Pasar Karang Tinggi', 'toko_kelontong', FALSE, TRUE),
(3, 2, 5, 'PT-03', 'Pasar Taba Penanjung (Titik Utama)', 'pasar_tradisional', FALSE, TRUE),
(4, 2, 5, 'PT-04', 'Toko Berkah Mandiri - Taba Penanjung', 'toko_kelontong', FALSE, TRUE),
(5, 3, 3, 'PT-05', 'Pasar Talang Empat (Titik Utama)', 'pasar_tradisional', FALSE, TRUE),
(6, 4, 4, 'PT-06', 'Pasar Pondok Kelapa (Titik Utama)', 'pasar_tradisional', FALSE, TRUE),
(7, 5, 2, 'PT-07', 'Pasar Simulasi A (Titik Pantau Harian)', 'pasar_tradisional', TRUE, TRUE),
(8, 6, 3, 'PT-08', 'Pasar Simulasi B (Titik Pantau Harian)', 'pasar_tradisional', TRUE, TRUE),
(9, NULL, 2, 'PT-09', 'Gudang Distributor Pangan Karang Tinggi', 'agen_distributor', FALSE, TRUE),
(10, NULL, 4, 'PT-10', 'Agen Minyak Goreng & Sembako Pondok Kelapa', 'agen_distributor', FALSE, TRUE),
(11, NULL, 6, 'PT-11', 'Sentra Pengumpul Hasil Bumi Pondok Kubang', 'agen_distributor', FALSE, TRUE);

-- 8. Point Assignments
INSERT INTO point_assignments (user_id, point_id, starts_on, ends_on) VALUES
(2, 1, '2026-01-01', NULL),
(2, 2, '2026-01-01', NULL),
(2, 7, '2026-01-01', NULL),
(3, 3, '2026-01-01', NULL),
(3, 4, '2026-01-01', NULL),
(3, 8, '2026-01-01', NULL),
(6, 2, '2026-01-01', NULL);

-- 9. Businesses
INSERT INTO businesses (id, region_id, code, name, owner_name, business_type, phone, address, is_verified) VALUES
(1, 2, 'BIZ-01', 'UD Sembako Berkah Utama', 'H. Ahmad Fauzi', 'Grosir dan Eceran Pangan', '081399887766', 'Pasar Karang Tinggi Kios No. 12', TRUE),
(2, 5, 'BIZ-02', 'Toko Sumber Rezeki Taba Penanjung', 'Hj. Rohana', 'Kelontong dan Bahan Pokok', '081277665544', 'Jl. Pasar Taba Penanjung No. 4', TRUE),
(3, 3, 'BIZ-03', 'Distributor Minyak Curah Talang Empat', 'Budi Santoso', 'Agen / Distributor', '081188223344', 'Kembang Seri, Talang Empat', TRUE),
(4, 4, 'BIZ-04', 'Kios Cabai & Sayur Segar Bu Lina', 'Lina Marlina', 'Pedagang Sayur & Cabai', '085266778899', 'Pasar Pondok Kelapa Blok C', TRUE),
(5, 2, 'BIZ-05', 'Agen Telur Ayam Sejahtera Sentosa', 'Hendra Gunawan', 'Agen Petelur & Distribusi', '081344556677', 'Karang Tinggi', TRUE);

-- 10. Commodities
INSERT INTO commodities (id, code, name, category, base_unit, icon, reference_price, is_active) VALUES
(1, 'BERAS_MEDIUM', 'Beras Medium', 'Pangan Pokok', 'kg', '🍚', 14500.00, TRUE),
(2, 'BERAS_PREMIUM', 'Beras Premium', 'Pangan Pokok', 'kg', '🌾', 16000.00, TRUE),
(3, 'GULA_PASIR', 'Gula Pasir', 'Sembako', 'kg', '🧂', 18000.00, TRUE),
(4, 'MINYAK_GORENG', 'Minyak Goreng Curah', 'Minyak Curah', 'liter', '🍾', 18500.00, TRUE),
(5, 'CABAI_MERAH', 'Cabai Merah Keriting', 'Hortikultura', 'kg', '🌶️', 48000.00, TRUE),
(6, 'CABAI_RAWIT', 'Cabai Rawit Hijau', 'Hortikultura', 'kg', '🌶️', 42000.00, TRUE),
(7, 'TELUR_AYAM', 'Telur Ayam Ras', 'Peternakan', 'kg', '🥚', 28000.00, TRUE),
(8, 'BAWANG_MERAH', 'Bawang Merah Lokal', 'Hortikultura', 'kg', '🧅', 36000.00, TRUE),
(9, 'BAWANG_PUTIH', 'Bawang Putih Honan', 'Hortikultura', 'kg', '🧄', 38000.00, TRUE),
(10, 'DAGING_SAPI', 'Daging Sapi Murni', 'Peternakan', 'kg', '🥩', 135000.00, TRUE),
(11, 'DAGING_AYAM', 'Daging Ayam Broiler', 'Peternakan', 'kg', '🍗', 34000.00, TRUE),
(12, 'TEPUNG_TERIGU', 'Tepung Terigu Segitiga', 'Pangan Olahan', 'kg', '📦', 12500.00, TRUE);

-- 11. Data Sources
INSERT INTO data_sources (id, code, name, source_type) VALUES
(1, 'SRC_PETUGAS', 'Petugas Pengawas Pasar Disperindag', 'petugas_pasar'),
(2, 'SRC_MITRA', 'Pelaku Usaha / Mitra Pasar', 'mitra'),
(3, 'SRC_DKP', 'Dinas Ketahanan Pangan Bengkulu Tengah', 'instansi'),
(4, 'SRC_SIMULASI', 'Sistem Evaluasi Simulasi Kebijakan', 'simulation');

-- 12. Action Plans Base
INSERT INTO action_plans (id, title, description, priority, status, assigned_to, deadline, progress_pct, is_demo) VALUES
(1, 'Operasi Pasar Murah Beras SPHP di Karang Tinggi', 'Koordinasi dengan Bulog Kanwil Bengkulu untuk intervensi harga beras medium.', 'tinggi', 'antrean', 2, '2026-10-31', 20, TRUE),
(2, 'Penyaluran Stok Minyak Goreng Curah Talang Empat', 'Fasilitasi pasokan langsung dari distributor ke pedagang pasar rakyat.', 'sedang', 'dalam_proses', 2, '2026-10-30', 50, TRUE),
(3, 'Inspeksi Ketersediaan Pasokan Cabai Merah Taba Penanjung', 'Cek rantai distribusi pasokan cabai dari petani Rejang Lebong dan Kepahiang.', 'tinggi', 'dalam_proses', 3, '2026-10-29', 70, TRUE),
(4, 'Verifikasi Akurasi Timbangan dan Harga Telur Ayam Pondok Kelapa', 'Pemeriksaan rutin berkala kesesuaian harga dan tera timbangan.', 'rendah', 'selesai', 4, '2026-10-25', 100, TRUE),
(5, 'Evaluasi Keterlambatan Laporan Pangan Kecamatan Semidang Lagan', 'Pemberian pendampingan teknis pelaporan input harga kepada petugas kecamatan.', 'sedang', 'terlambat', 1, '2026-10-24', 35, TRUE);

-- 13. Reports and Report Series
INSERT INTO report_series (id, point_id, observed_at) VALUES
(1, 1, '2026-10-27 08:30:00'),
(2, 3, '2026-10-27 09:00:00'),
(3, 5, '2026-10-27 09:30:00'),
(4, 7, '2026-10-27 10:00:00');

INSERT INTO reports (id, series_id, source_id, status, submitted_by, verified_by, verified_at, previous_report_id, version_no, is_demo, notes) VALUES
(1, 1, 1, 'approved', 2, 4, '2026-10-27 10:24:00', NULL, 1, FALSE, 'Data terpantau stabil di Pasar Karang Tinggi'),
(2, 2, 1, 'approved', 3, 4, '2026-10-27 10:41:00', NULL, 1, FALSE, 'Pasokan cabai agak menurun karena hujan di sentra produksi'),
(3, 3, 1, 'approved', 2, 4, '2026-10-27 11:00:00', NULL, 1, FALSE, 'Stok beras dan minyak aman terkendali'),
(4, 4, 4, 'submitted', 2, NULL, NULL, NULL, 1, TRUE, 'Data pemantauan simulasi harian');

INSERT INTO report_items (report_id, commodity_id, price, stock, unit, price_change_pct, price_change_amount, notes) VALUES
-- Report 1 (Karang Tinggi)
(1, 1, 14500.00, 250.00, 'kg', -2.03, -300.00, 'Harga normal, stok memadai'),
(1, 3, 18000.00, 120.00, 'kg', 1.69, 300.00, 'Sedikit naik dari distributor'),
(1, 4, 18500.00, 90.00, 'liter', -1.60, -300.00, 'Minyak curah lancar'),
(1, 5, 48000.00, 12.00, 'kg', 4.35, 2000.00, 'Stok terbatas'),
(1, 7, 28000.00, 80.00, 'kg', 0.00, 0.00, 'Stabil'),
(1, 8, 36000.00, 45.00, 'kg', -1.50, -500.00, 'Stabil'),
-- Report 2 (Taba Penanjung)
(2, 1, 14800.00, 180.00, 'kg', 0.00, 0.00, 'Beras medium stabil'),
(2, 3, 18200.00, 95.00, 'kg', 1.10, 200.00, 'Gula pasir'),
(2, 5, 52000.00, 8.00, 'kg', 12.50, 5000.00, 'Lonjakan harga cabai'),
(2, 7, 28500.00, 60.00, 'kg', 1.79, 500.00, 'Telur ayam'),
-- Report 3 (Talang Empat)
(3, 1, 14400.00, 300.00, 'kg', -1.37, -200.00, 'Beras cukup'),
(3, 4, 18200.00, 110.00, 'liter', -2.15, -400.00, 'Minyak lancar'),
(3, 5, 46000.00, 25.00, 'kg', 2.22, 1000.00, 'Cabai merah'),
-- Report 4 (Simulasi)
(4, 1, 14500.00, 200.00, 'kg', 0.00, 0.00, 'Simulasi'),
(4, 5, 50000.00, 5.00, 'kg', 15.00, 6500.00, 'Simulasi kenaikan ekstrim cabai');

SET FOREIGN_KEY_CHECKS = 1;
