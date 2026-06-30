-- =========================================================
-- Database: uas_2021100_data_alumni
-- Project UAS Pemrograman Web 2 - Data Alumni
-- Nama  : Muhammad Abi
-- NIM   : 251011700710
-- =========================================================

CREATE DATABASE IF NOT EXISTS uas_2021100_data_alumni;
USE uas_2021100_data_alumni;

-- Tabel users (untuk login & registrasi pengguna sistem)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel alumni (data utama aplikasi)
-- Baris pertama (id_alumni) diisi dengan NIM masing-masing mahasiswa
CREATE TABLE IF NOT EXISTS alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    tahun_lulus YEAR NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(20) NOT NULL,
    pekerjaan_saat_ini VARCHAR(100) DEFAULT '-',
    alamat TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('Bekerja','Belum Bekerja','Wirausaha','Studi Lanjut') DEFAULT 'Belum Bekerja',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Data dummy user login (password: admin123)
INSERT INTO users (nama_lengkap, username, password) VALUES
('Muhammad Abi', 'abyy', '$2y$10$92IXUNpkjO0rOQ5byMi.YeIonQk1Vh4D5kXobzNTHLFGwLwOI/cle');
-- hash di atas adalah bcrypt untuk password "admin123"

-- Data dummy alumni (minimal 5 baris sesuai ketentuan soal)
INSERT INTO alumni (nim, nama_lengkap, jurusan, tahun_lulus, email, no_telepon, pekerjaan_saat_ini, alamat, status) VALUES
('2021100', 'Budi Santoso', 'Teknik Informatika', 2025, 'budi.santoso@gmail.com', '081234567890', 'Software Engineer', 'Jl. Merdeka No. 10, Tangerang Selatan', 'Bekerja'),
('2021101', 'Siti Aminah', 'Sistem Informasi', 2024, 'siti.aminah@gmail.com', '081234567891', 'Data Analyst', 'Jl. Sudirman No. 25, Jakarta', 'Bekerja'),
('2021102', 'Andi Wijaya', 'Teknik Informatika', 2024, 'andi.wijaya@gmail.com', '081234567892', '-', 'Jl. Gatot Subroto No. 5, Tangerang', 'Belum Bekerja'),
('2021103', 'Dewi Lestari', 'Manajemen Informatika', 2023, 'dewi.lestari@gmail.com', '081234567893', 'Owner Toko Online', 'Jl. Ahmad Yani No. 12, Bekasi', 'Wirausaha'),
('2021104', 'Rizky Pratama', 'Sistem Informasi', 2023, 'rizky.pratama@gmail.com', '081234567894', 'Mahasiswa S2', 'Jl. Diponegoro No. 8, Depok', 'Studi Lanjut'),
('2021105', 'Putri Ramadhani', 'Teknik Informatika', 2025, 'putri.ramadhani@gmail.com', '081234567895', 'Web Developer', 'Jl. Kartini No. 3, Tangerang Selatan', 'Bekerja');
