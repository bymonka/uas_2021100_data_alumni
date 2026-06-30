# UAS Pemrograman Web 2 — Aplikasi Data Alumni

**Nama  :** Budi Santoso
**NIM   :** 2021100
**Kategori (digit terakhir NIM = 0):** Data Alumni
**Project:** uas_2021100_data_alumni

## 1. Deskripsi
Aplikasi web manajemen Data Alumni berbasis PHP Native dengan konsep OOP dan pola
MVC (Model-View-Controller). Aplikasi memungkinkan pengelolaan data alumni (CRUD),
pencarian, login/registrasi dengan session management, serta laporan data dalam
format PDF dan Excel.

## 2. Struktur Folder
```
uas_2021100_data_alumni/
├── config/
│   └── Database.php          # Koneksi database (PDO)
├── model/
│   ├── Model.php              # Abstract class (Abstraction)
│   ├── User.php                # Model untuk users (login/registrasi)
│   └── Alumni.php              # Model untuk data alumni (CRUD + search)
├── controller/
│   ├── AuthController.php      # Login, registrasi, session, logout
│   ├── AlumniController.php    # Logika CRUD, validasi, upload file
│   ├── ReportController.php    # Generate laporan PDF & Excel
│   └── export.php              # Endpoint export laporan
├── view/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── alumni/
│   │   ├── dashboard.php       # List data + search + delete
│   │   ├── tambah.php          # Create
│   │   ├── edit.php            # Update
│   │   └── laporan.php         # Report page
│   └── layout/
│       └── sidebar.php
├── public/
│   ├── css/style.css
│   └── uploads/                # Folder upload foto alumni
├── library/
│   └── fpdf.php                # Library FPDF untuk export PDF
├── database.sql                # Script database
├── index.php                   # Entry point (redirect)
└── README.md
```

## 3. Konsep OOP yang Diterapkan
- **Encapsulation**: Properti `Database` (host, username, password) bersifat `private`.
- **Abstraction**: `Model.php` adalah abstract class dengan method abstrak
  `getAll()`, `getById()`, `create()`, `update()`, `delete()` yang wajib
  diimplementasikan oleh class turunannya.
- **Inheritance**: `Alumni` dan `User` extends `Model`. `AlumniPDF` extends
  `FPDF` (library eksternal).
- **Polymorphism**: `AlumniPDF` meng-override method `Header()` dan `Footer()`
  milik class induk `FPDF` untuk menghasilkan tampilan laporan kustom.

## 4. Alur Kerja Aplikasi
1. **Login/Registrasi** — User mendaftar akun baru melalui `register.php`
   (password di-hash dengan `password_hash`). Setelah login berhasil,
   `AuthController` menyimpan data user ke `$_SESSION`.
2. **Session Management** — Setiap halaman alumni (`dashboard.php`, `tambah.php`,
   `edit.php`, `laporan.php`) memanggil `AuthController::checkSession()` di
   awal file untuk memastikan hanya user yang sudah login dapat mengakses.
3. **Dashboard** — Menampilkan statistik (total alumni, status pekerjaan) dan
   tabel data alumni dengan fitur pencarian (`search()` di model `Alumni`)
   berdasarkan NIM/nama/jurusan/email, serta filter status.
4. **Tambah Data (Create)** — Form input divalidasi di `AlumniController::validate()`
   (format NIM, email, no telepon, tahun lulus, NIM unik), lalu file foto
   divalidasi & diupload melalui `handleUpload()` (tipe file, ukuran maks 2MB).
5. **Edit Data (Update)** — Mirip dengan create, namun mempertahankan foto lama
   apabila user tidak mengunggah foto baru.
6. **Hapus Data (Delete)** — Menghapus baris data sekaligus file foto terkait
   dari folder `public/uploads/`.
7. **Laporan (Report)** — Halaman `laporan.php` menampilkan data dengan filter
   status, dan menyediakan tombol export ke PDF (menggunakan FPDF) dan Excel
   (format tabel HTML dengan header `application/vnd.ms-excel`).

## 5. Cara Menjalankan
1. Salin folder project ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Jalankan Apache dan MySQL melalui XAMPP/Laragon Control Panel.
3. Buka phpMyAdmin, import file `database.sql` (otomatis membuat database
   `uas_2021100_data_alumni` beserta data dummy).
4. Akses aplikasi melalui browser: `http://localhost/uas_2021100_data_alumni/`
5. Login menggunakan akun demo:
   - Username: `budisantoso`
   - Password: `admin123`

   atau lakukan registrasi akun baru melalui halaman Registrasi.

## 6. Validasi Data yang Diterapkan
| Field | Aturan Validasi |
|---|---|
| NIM | Wajib diisi, hanya angka 5-20 digit, harus unik |
| Nama Lengkap | Wajib diisi |
| Jurusan | Wajib diisi |
| Tahun Lulus | Angka, antara 2000 - tahun depan |
| Email | Format email valid (`FILTER_VALIDATE_EMAIL`) |
| No. Telepon | Angka/simbol +,-, spasi, 8-20 karakter |
| Foto | Opsional, format JPG/PNG, maksimal 2MB |
| Username (registrasi) | Minimal 4 karakter, unik |
| Password (registrasi) | Minimal 6 karakter, harus sama dengan konfirmasi |

## 7. Catatan
- Library FPDF (`library/fpdf.php`) digunakan untuk generate laporan PDF.
- Export Excel menggunakan format tabel HTML dengan ekstensi `.xls` yang
  kompatibel dibuka langsung oleh Microsoft Excel.
- Seluruh akses ke database menggunakan **PDO prepared statements** untuk
  mencegah SQL Injection.
"# uas_2021100_data_alumni" 
