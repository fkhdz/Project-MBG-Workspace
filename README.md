# 🍱 MBG Workspace — Logistics & Distribution System

> Sistem informasi berbasis web untuk mengelola rantai pasok (supply chain) Program Makan Bergizi Gratis Nasional: mulai dari manajemen mitra, penerima bantuan, inventaris gudang, komposisi paket, hingga pelacakan distribusi dan laporan analitik.

[![Stack](https://img.shields.io/badge/Stack-PHP%208%20%7C%20MySQL%20%7C%20Tailwind-0ea5e9)](#-tech-stack)
[![Schema](https://img.shields.io/badge/Schema-3rd%20Normal%20Form-22c55e)](#-database-schema)
[![Modules](https://img.shields.io/badge/Modules-7%20CRUD%20Modules-8b5cf6)](#-module-structure)

---

## 📋 Daftar Isi

- [Tech Stack](#-tech-stack)
- [Quick Start](#-quick-start)
- [Login & Autentikasi](#-login--autentikasi)
- [Struktur Proyek](#-struktur-proyek)
- [Database Schema](#-database-schema)
- [Design System](#-design-system)
- [Arsitektur Aplikasi](#-arsitektur-aplikasi)
- [Konvensi Kode](#-konvensi-kode)
- [Module Reference](#-module-reference)
- [API/Action Endpoints](#-apiaction-endpoints)
- [Seed Data](#-seed-data)
- [Troubleshooting](#-troubleshooting)

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend** | PHP 8.x (procedural, mysqli) |
| **Database** | MySQL 8.0 / MariaDB 10.x |
| **Frontend** | Tailwind CSS 3 (via CDN `cdn.tailwindcss.com`) |
| **Typography** | [Inter](https://fonts.google.com/specimen/Inter) (300–800) via Google Fonts |
| **Icons** | [Material Symbols Outlined](https://fonts.google.com/icons) via Google Fonts |
| **Charts** | [Chart.js 4.x](https://www.chartjs.org/) via jsDelivr |
| **Web Server** | Apache (XAMPP/LAMP) atau PHP built-in server |

Tidak ada build step. Tidak ada Composer dependency. Tidak ada framework. Pure PHP + Tailwind CDN — clone, import SQL, langsung jalan.

---

## 🚀 Quick Start

### Prasyarat
- PHP ≥ 8.0 dengan ekstensi `mysqli`
- MySQL/MariaDB ≥ 5.7
- Web server (Apache/Nginx) atau gunakan PHP built-in server untuk development

### Langkah 1 — Import Schema
```bash
mysql -u root -p < db_mbg.sql
```
Atau lewat phpMyAdmin → tab **Import** → pilih `db_mbg.sql`.

Skema akan membuat database `db_mbg` dan 8 tabel.

### Langkah 2 — Konfigurasi Koneksi
Edit `config/koneksi.php`:
```php
$host = "localhost";
$user = "root";
$pass = "";      // sesuaikan dengan password MySQL kamu
$db   = "db_mbg";
```

### Langkah 3 — Jalankan
Letakkan folder di `htdocs/` (XAMPP) atau `www/` (Laragon), lalu akses:
```
http://localhost/Project-web-DB_MBG/
```

Atau pakai PHP built-in server:
```bash
cd Project-web-DB_MBG
php -S 127.0.0.1:8000 -t .
```
Lalu buka `http://127.0.0.1:8000/`.

### Langkah 4 — Seed Data (Opsional, untuk Demo)
Buka `http://localhost/Project-web-DB_MBG/seed.php` lalu klik **"Jalankan Seed Data"**. Halaman ini akan:
- Auto-create tabel `ITEM` & `DETAIL_PAKET` jika belum ada
- TRUNCATE semua tabel dan generate data dummy realistis
- Menampilkan ringkasan jumlah baris per tabel

> ⚠️ **PERINGATAN:** Seed akan **menghapus semua data**. Hanya untuk development/demo.

---

## 🔐 Login & Autentikasi

Sistem memiliki halaman login dengan **2 peran (role)**:

| Role | Email | Password | Akses |
|---|---|---|---|
| **Admin** | `admin@gmail.com` | `1234` | Full akses ke seluruh modul |
| **Karyawan** | `karyawan@gmail.com` | `1234` | Akses terbatas (modul-modul tertentu) |

### Endpoint Autentikasi

| Method | URL | Keterangan |
|---|---|---|
| GET | `/login.php` | Halaman form login |
| POST | `/login.php` | Submit kredensial (field: `email`, `password`) |
| GET | `/logout.php` | Hancurkan sesi & kembali ke halaman login |

### Cara Kerja

1. Akses `http://localhost/Project-web-DB_MBG/login.php` — semua halaman lain akan otomatis redirect ke sini kalau belum login
2. Masukkan email & password (lihat tabel di atas)
3. Setelah berhasil, user diarahkan ke **dashboard** (`index.php`)
4. Session disimpan di `$_SESSION` dengan key: `user_id`, `nama`, `email`, `role`, `login_at`
5. Klik tombol **Keluar** di sidebar → kembali ke halaman login

> ✅ **Proteksi otomatis:** `includes/header.php` sudah memanggil `require_login()` secara global, jadi **semua halaman CRUD (mitra, penerima, paket, distribusi, laporan, item, user) otomatis aman** tanpa harus mengubah file modul-nya satu per satu.
> 
> Untuk menjadikan sebuah halaman **publik** (tidak wajib login), set variabel ini sebelum include header:
> ```php
> $public_page = true;
> include 'includes/header.php';
> ```
> Contoh penggunaan: `login.php`, `logout.php`.

### Melindungi Halaman dengan Auth

Tambahkan 2 baris di paling atas setiap halaman yang ingin diproteksi:

```php
require_once 'includes/auth.php';
require_login();              // wajib login (admin / karyawan)
// require_role('admin');     // uncomment jika hanya admin yang boleh akses
```

Fungsi helper yang tersedia di `includes/auth.php`:

| Fungsi | Keterangan |
|---|---|
| `is_logged_in()` | Cek apakah user sedang login |
| `require_login()` | Redirect ke `login.php` kalau belum login |
| `require_role('admin')` | Redirect kalau role tidak sesuai |
| `mbg_authenticate($email, $pass)` | Validasi kredensial, return array data user / `null` |

### Catatan Keamanan

> ⚠️ Kredensial di atas saat ini masih **hardcode statis** di `includes/auth.php` (fungsi `mbg_get_static_credentials()`). Untuk produksi, ganti blok tersebut dengan **query ke tabel `USER`** dan gunakan hashing `password_hash()` / `password_verify()` (saat ini seed masih memakai `MD5`).

---

## 📁 Struktur Proyek

```
Project-web-DB_MBG/
│
├── index.php                      # Dashboard utama (entry point)
├── sidebar.php                    # Shared sidebar (desktop + mobile drawer)
├── seed.php                       # Seed data dummy (utility page)
│
├── config/
│   └── koneksi.php                # Koneksi MySQL — include di semua halaman
│
├── includes/
│   └── header.php                 # Shared <head> + Tailwind config + body class
│
├── distribusi/                    # Modul Distribusi (transaksi utama)
│   ├── index.php                  #   List + stats + grafik status
│   ├── create.php                 #   Form buat distribusi baru
│   ├── update.php                 #   Form edit distribusi
│   └── delete.php                 #   Handler hapus (redirect-only)
│
├── penerima/                      # Modul Penerima Bantuan
│   ├── index.php                  #   List 30+ penerima + filter kategori
│   ├── create.php
│   ├── update.php
│   └── delete.php
│
├── mitra/                         # Modul Mitra (penyedia/distributor)
│   ├── index.php
│   ├── create.php
│   ├── update.php
│   └── delete.php
│
├── user/                          # Modul User (akun sistem)
│   ├── index.php
│   ├── create.php
│   ├── update.php
│   └── delete.php
│
├── item/                          # Modul Gudang Item (bahan pangan)
│   ├── index.php
│   ├── create.php
│   ├── update.php
│   └── delete.php
│
├── paketbantuan/                  # Modul Paket Bantuan + Komposisi
│   ├── index.php                  #   List paket + link komposisi
│   ├── create.php                 #   Setelah buat → redirect ke komposisi
│   ├── update.php
│   ├── delete.php
│   ├── komposisi.php              #   Editor komposisi (Junction UI)
│   ├── tambah_komposisi.php       #   Handler: tambah item ke komposisi
│   ├── update_komposisi.php       #   Handler + form edit jumlah
│   └── delete_komposisi.php       #   Handler hapus item dari komposisi
│
├── laporandata/                   # Modul Laporan & Analitik
│   └── index.php                  #   Dashboard analitik + 2 Chart.js
│
├── db_mbg.sql                     # Schema database (8 tabel)
├── README.md                      # File ini
└── bootstrap/                     # ⚠️ Di-skip (legacy, tidak dipakai)
```

---

## 🗄️ Database Schema

Database: **`db_mbg`** (InnoDB, charset utf8mb4). 8 tabel dengan relasi sebagai berikut:

```
                    ┌──────────┐
                    │   USER   │ (parent — akun & autentikasi)
                    └─────┬────┘
              ┌────────────┼────────────┐
              │            │            │
        ┌─────▼─────┐ ┌────▼─────┐      │
        │  PENERIMA │ │  MITRA   │      │
        └─────┬─────┘ └────┬─────┘      │
              │            │            │
              │  ┌─────────┴─────────┐  │
              │  │                   │  │
              │  │       ┌───────────┴──▼────────┐
              │  │       │     PAKETBANTUAN      │
              │  │       └──────┬──────────┬────┘
              │  │              │          │
              │  │       ┌──────▼──────┐   │ ┌────────┐
              │  │       │ DETAIL_PAKET│◄──┼─┤  ITEM  │
              │  │       │ (junction) │   │ └────────┘
              │  │       └─────────────┘   │
              │  │                          │
              └──┴──────────┐              │
                            │              │
                      ┌─────▼──────────────▼─────┐
                      │       DISTRIBUSI          │
                      └──────────────┬────────────┘
                                     │
                              ┌──────▼──────────┐
                              │   LAPORANDATA   │
                              └─────────────────┘
```

### Tabel & Kolom Penting

| Tabel | PK | FK | Keterangan |
|---|---|---|---|
| `USER` | `user_id` | — | Akun sistem (admin/koordinator/petugas). Password MD5. |
| `PENERIMA` | `penerima_id` | `user_id` | Profil penerima bantuan (8 kategori sosial) |
| `MITRA` | `mitra_id` | `user_id` | Mitra penyedia/distributor |
| `ITEM` | `item_id` | — | Bahan pangan di gudang |
| `PAKETBANTUAN` | `paket_id` | — | Paket bantuan (produk jadi) |
| `DETAIL_PAKET` | `detail_id` | `paket_id`, `item_id` | Junction: komposisi bahan per paket |
| `DISTRIBUSI` | `distribusi_id` | `paket_id`, `penerima_id`, `mitra_id` | Transaksi pengiriman |
| `LAPORANDATA` | `laporan_id` | `distribusi_id`, `dibuat_oleh` | Laporan per distribusi |

### Relasi Kunci

- **USER → PENERIMA / MITRA**: 1-to-many. Satu user bisa mengelola banyak profil.
- **PAKETBANTUAN ↔ ITEM (M:N via DETAIL_PAKET)**: Satu paket terdiri dari banyak item, satu item bisa dipakai di banyak paket.
- **DISTRIBUSI = Fact Table**: Menghubungkan PENERIMA × MITRA × PAKETBANTUAN + timestamp.
- **LAPORANDATA → DISTRIBUSI**: 1-to-many. Audit trail terpisah untuk menjaga DISTRIBUSI tetap ramping.

### Query Pattern Penting

**Mengambil jumlah paket selesai (untuk dashboard):**
```sql
SELECT COUNT(*) FROM DISTRIBUSI
WHERE status_pengiriman IN ('Selesai','Terkirim','Diterima');
```

**Mengambil jumlah paket dalam proses:**
```sql
SELECT COUNT(*) FROM DISTRIBUSI
WHERE status_pengiriman NOT IN ('Selesai','Terkirim','Diterima');
```

**Distribusi per bulan (untuk chart di laporandata):**
```sql
SELECT DATE_FORMAT(tanggal_kirim, '%Y-%m') AS bulan, COUNT(*) AS total
FROM DISTRIBUSI
GROUP BY bulan
ORDER BY bulan ASC
LIMIT 12;
```

**Distribusi penerima per kategori (untuk pie chart):**
```sql
SELECT kategori_penerima, COUNT(*) AS total
FROM PENERIMA
GROUP BY kategori_penerima;
```

---

## 🎨 Design System

Semua UI menggunakan **Tailwind CSS via CDN** dengan konfigurasi terpusat di `includes/header.php`.

### Color Palette

| Token | Hex | Penggunaan |
|---|---|---|
| `primary-50` … `primary-900` | `#f0f9ff` … `#0c4a6e` | Sky Blue — warna utama (Sky-500 = `#0ea5e9`) |
| `surface` | `#f8fafc` | Background halaman |
| `slate-50` … `slate-900` | (default Tailwind) | Teks & border |
| `emerald/amber/red-*` | (default) | Alert & status badge |

### Custom Shadows

- **`shadow-soft`**: `0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03)` — kartu standar
- **`shadow-glow`**: `0 4px 14px -2px rgba(14, 165, 233, 0.25)` — hover pada primary button

### Typography: Inter
Loaded via Google Fonts dengan bobot 300, 400, 500, 600, 700, 800.

### Iconography: Material Symbols Outlined
Loaded via Google Fonts. Untuk ikon **active/filled**, gunakan:
```html
<span class="material-symbols-outlined"
      style="font-variation-settings:'FILL' 1, 'wght' 500;">dashboard</span>
```

### Komponen Standar

```html
<!-- Kartu -->
<div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-5">

<!-- Tombol Primary -->
<button class="bg-primary-600 hover:bg-primary-700 text-white rounded-xl
               shadow-sm hover:shadow-glow font-semibold">

<!-- Tombol Cancel/Secondary -->
<button class="bg-white border border-slate-200 text-slate-600
               hover:bg-slate-50 rounded-xl">

<!-- Input -->
<input class="bg-slate-50 border border-slate-200 rounded-xl
              focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">

<!-- Status Badge (auto-warna) -->
<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs
             font-semibold bg-emerald-50 text-emerald-700">
```

---

## 🏛️ Arsitektur Aplikasi

### Layout Pattern
Semua halaman UI menggunakan pola layout konsisten:

```
┌──────────────────────────────────────────────────────────┐
│ <head> + Tailwind config (dari includes/header.php)      │
├──────────┬───────────────────────────────────────────────┤
│          │                                               │
│ sidebar  │   <main class="flex-1 min-w-0">               │
│ (lg)     │     <div class="px-... max-w-... mx-auto">    │
│          │       Breadcrumb                              │
│          │       Page Header                             │
│          │       Content                                 │
│          │     </div>                                    │
│          │   </main>                                     │
└──────────┴───────────────────────────────────────────────┘
```

### Include Order (wajib)
Setuju variabel dulu, baru include:
```php
<?php
$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

$current_page = 'mitra';   // ← WAJIB diset sebelum include sidebar
$page_title   = 'Mitra';   // ← Untuk tag <title>
include "../includes/header.php";   // path berbeda untuk root vs module
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>  <!-- path berbeda untuk root vs module -->
<main class="flex-1 min-w-0">
  <!-- konten di sini -->
</main>
</div>
```

### Path Resolution
| Lokasi file | Header include | Sidebar include |
|---|---|---|
| Root (`/index.php`, `/seed.php`) | `includes/header.php` | `sidebar.php` |
| Module (`/mitra/index.php`, dll) | `../includes/header.php` | `../sidebar.php` |

Sidebar otomatis mendeteksi level direktori melalui `$_SERVER['SCRIPT_FILENAME']`, jadi link di sidebar akan jadi:
- Dari root: `mitra/index.php`
- Dari module: `../mitra/index.php`

### `$current_page` Keys (untuk highlight sidebar)
| Key | Modul |
|---|---|
| `dashboard` | Root dashboard |
| `mitra` | Mitra |
| `user` | Pengguna |
| `penerima` | Penerima |
| `paket` | Paket Bantuan (termasuk komposisi) |
| `distribusi` | Distribusi |
| `laporan` | Laporan Data |
| `item` | Gudang Item |

### Sidebar
- **Desktop** (`lg:` ke atas): sticky aside, lebar 256px, selalu visible
- **Mobile**: hidden by default, muncul drawer overlay dengan tombol hamburger di topbar
- Logo + profil admin + 8 menu nav + tombol logout (visual only, belum ada handler)

---

## 📝 Konvensi Kode

### Naming
- **File**: lowercase, snake_case untuk multi-kata (`tambah_komposisi.php`)
- **Variabel PHP**: `$snake_case`
- **Class CSS**: Tailwind utility, tidak ada BEM
- **Tabel DB**: UPPER_SNAKE_CASE (`PAKETBANTUAN`, `DETAIL_PAKET`)
- **Kolom DB**: `snake_case` (`nama_lengkap`, `tanggal_kirim`)

### Pattern Wajib di Setiap Halaman Module
1. Include `config/koneksi.php`
2. Resolve `$koneksi_db = $conn ?? $koneksi`
3. Set `$current_page` dan `$page_title`
4. Include `../includes/header.php` (atau `includes/header.php` di root)
5. Buka `<div class="flex min-h-screen w-full">`
6. Include sidebar
8. Buka `<main class="flex-1 min-w-0"><div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-[1400px] mx-auto">`
7. Konten
8. Tutup `</div></main></div>` di akhir

### Pattern DELETE
File `delete.php` adalah **redirect-only handler** — tidak render UI:
```php
<?php
include "../config/koneksi.php";
$koneksi_db = $conn ?? $koneksi;

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

if (mysqli_query($koneksi_db, "DELETE FROM TABEL WHERE pk='$id'")) {
    header("Location: index.php?msg=deleted");
} else {
    header("Location: index.php?msg=error");
}
exit;
```

### Flash Message
Halaman `index.php` menampilkan alert berdasarkan query string `?msg=...` atau `?msg_type=...&msg=...`:
- `?msg=created` → "Data berhasil ditambahkan"
- `?msg=updated` → "Data berhasil diperbarui"
- `?msg=deleted` → "Data berhasil dihapus"
- `?msg=error` → "Terjadi kesalahan"
- `?msg_type=success|error&msg=<text>` → Custom message (dipakai di komposisi)

### Empty State
Semua tabel menampilkan empty state ketika data kosong:
```html
<div class="py-12 text-center">
    <span class="material-symbols-outlined text-slate-300 text-5xl mb-3">inbox</span>
    <p class="text-slate-500 font-semibold">Belum ada data</p>
    <p class="text-slate-400 text-sm mt-1">Tambahkan data pertama Anda.</p>
</div>
```

### Security (Current Limitations)
⚠️ Project ini menggunakan **plain mysqli** dengan string interpolation — **rentan SQL injection** untuk input user. Saat ini hanya untuk demo/internal. Untuk production:
1. Migrasi ke PDO + prepared statements (`mysqli_prepare`)
2. Hash password dengan `password_hash()` / `password_verify()` (saat ini pakai MD5 — **tidak aman**)
4. Escape semua output dengan `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')`
3. Tambahkan CSRF token di setiap form

---

## 📚 Module Reference

Setiap modul mengikuti pola **CRUD 4-file**: `index.php` (list), `create.php` (form+insert), `update.php` (form+update), `delete.php` (handler).

### 1. Dashboard (`/`)
**File:** `index.php`

Menampilkan:
- 4 stat cards: Total Penerima, Distribusi Selesai, Distribusi Pending, Total Mitra
- Daftar distribusi terbaru (limit 5)
- Trend modul

### 2. Distribusi (`/distribusi/`)
**Tables:** `DISTRIBUSI`, `PAKETBANTUAN`, `PENERIMA`, `MITRA`

Halaman `index.php`:
- Stats badges: Selesai, Pending, Gagal (auto-colored)
- Tabel dengan kolom: No, Tanggal, Paket, Penerima, Mitra, Status, Bukti, Catatan
- Tombol Aksi: Edit (→ update.php), Hapus (→ delete.php via confirm)
- FAB "+ Buat Distribusi" di header

Halaman `create.php`:
- Dropdown pilih Paket (dari `PAKETBANTUAN`)
- Dropdown pilih Penerima (dari `PENERIMA`)
- Dropdown pilih Mitra (dari `MITRA`)
- Input tanggal_kirim, tanggal_terima, lokasi_pengiriman, status, catatan_petugas

### 3. Penerima (`/penerima/`)
**Tables:** `PENERIMA`

30 field untuk profil penerima + data bantuan (nama, NIK/TTL, alamat lengkap, kategori, penghasilan, tanggungan, status validasi, tanggal validasi).

### 4. Mitra (`/mitra/`)
**Tables:** `MITRA`

Fields: nama_mitra, jenis_mitra, kontak_person, no_hp, alamat_mitra, wilayah_operasional.

### 5. User (`/user/`)
**Tables:** `USER`

Fields: nama, email, password (MD5), role (admin/petugas/koordinator), no_hp, tanggal_daftar, last_login, status_akun.

### 6. Gudang Item (`/item/`)
**Tables:** `ITEM`

Halaman `index.php`:
- Stats: Total Item, Total Stok, Stok Rendah (< 100)
- Tabel: nama_item, satuan, stok_gudang (dengan badge warna: rendah/medium/cukup)
- Tombol Tambah Item

> ⚠️ Catatan: Tabel `ITEM` **tidak ada di `db_mbg.sql` original** — sudah ditambahkan di versi terbaru dan akan auto-create oleh `seed.php` jika belum ada.

### 7. Paket Bantuan (`/paketbantuan/`) + Komposisi
**Tables:** `PAKETBANTUAN`, `DETAIL_PAKET`, `ITEM`

**Alur kerja:**
1. User klik "+ Buat Paket" → `create.php` insert ke `PAKETBANTUAN` → redirect ke `komposisi.php?id=<new_id>`
2. Di halaman komposisi, user pilih ITEM dari dropdown + jumlah per paket → submit ke `tambah_komposisi.php` → insert ke `DETAIL_PAKET`
3. Dari `index.php`, setiap paket punya tombol "Kelola Komposisi" → `komposisi.php?id=<paket_id>`
4. Di komposisi, tiap item punya tombol edit/hapus yang mengarah ke `update_komposisi.php` / `delete_komposisi.php`

`komposisi.php` menampilkan:
- Header paket (nama, deskripsi, jenis, kalori, berat, kuantitas)
- Form tambah item (dropdown ITEM + input jumlah)
- Tabel komposisi saat ini (join `DETAIL_PAKET` × `ITEM`)

### 8. Laporan Data (`/laporandata/`)
**Tables:** Semua

Halaman `index.php`:
- 5 stat cards: Total Penerima, Total Mitra, Total Kuantitas Paket, Distribusi Selesai, Distribusi Proses
- **Line Chart** (Chart.js): Jumlah distribusi per bulan (12 bulan terakhir)
- **Doughnut Chart** (Chart.js): Distribusi penerima per kategori sosial
- Tabel distribusi terbaru dengan badge status

---

## 🔌 API/Action Endpoints

Semua endpoint menerima request via **GET** (untuk navigasi) atau **POST** (untuk submit form):

### DELETE Endpoints (redirect-only)
| Method | Endpoint | Aksi |
|---|---|---|
| GET | `/distribusi/delete.php?id=<id>` | Hapus distribusi |
| GET | `/penerima/delete.php?id=<id>` | Hapus penerima |
| GET | `/mitra/delete.php?id=<id>` | Hapus mitra |
| GET | `/user/delete.php?id=<id>` | Hapus user |
| GET | `/item/delete.php?id=<id>` | Hapus item |
| GET | `/paketbantuan/delete.php?id=<id>` | Hapus paket |
| GET | `/paketbantuan/delete_komposisi.php?id=<paket_id>&detail_id=<id>` | Hapus item dari komposisi |

### Komposisi Endpoints
| Method | Endpoint | Aksi |
|---|---|---|
| GET | `/paketbantuan/komposisi.php?id=<paket_id>` | Lihat/edit komposisi |
| POST | `/paketbantuan/tambah_komposisi.php?id=<paket_id>` | Tambah item ke komposisi |
| GET | `/paketbantuan/update_komposisi.php?id=<paket_id>&detail_id=<id>` | Form edit jumlah |
| POST | `/paketbantuan/update_komposisi.php?id=<paket_id>&detail_id=<id>` | Submit update jumlah |

### Seed Endpoint
| Method | Endpoint | Aksi |
|---|---|---|
| GET | `/seed.php` | Lihat statistik & form seed |
| POST | `/seed.php` (body: `run_seed=yes`) | Generate data dummy |

---

## 🌱 Seed Data

Halaman `seed.php` menghasilkan dataset dev/demo lengkap:

| Tabel | Rows | Konten |
|---|---|---|
| `USER` | 15 | 1 admin + 2 koordinator + 12 petugas |
| `MITRA` | 8 | Yayasan, Supplier, Produsen, Logistik, Panti, Toko |
| `PENERIMA` | 30 | 8 kategori sosial ekonomi |
| `ITEM` | 20 | Bahan pangan pokok + sayuran |
| `PAKETBANTUAN` | 10 | Balita, Ibu Hamil, Lansia, Anak, Keluarga, dll |
| `DETAIL_PAKET` | 30–50 | 3–5 item random per paket |
| `DISTRIBUSI` | 40 | 6 bulan terakhir, 10 variasi status |
| `LAPORANDATA` | 40 | Per distribusi, 3 variasi status laporan |

**Karakteristik data:**
- Password default semua user: `password123` (MD5 hash)
- Foreign keys terjaga (semua relasional valid)
- Realistis: nama Indonesia, alamat Jakarta/Jabodetabek, supplier di berbagai provinsi
- Distribusi disebar 1–180 hari ke belakang → grafik Chart.js akan menampilkan 6 bulan data

---

## 🐛 Troubleshooting

### ❌ "Koneksi database gagal"
- Pastikan MySQL/MariaDB sudah running
- Cek kredensial di `config/koneksi.php` (host/user/pass/db)
- Pastikan database `db_mbg` sudah ada (`CREATE DATABASE db_mbg;` lalu import `db_mbg.sql`)

### ❌ Sidebar link 404
- Pastikan `sidebar.php` ada di root dan `$_SERVER['SCRIPT_FILENAME']` bisa diakses
- Cek di browser DevTools → Network: path harus benar (root: `mitra/index.php`, module: `../mitra/index.php`)

### ❌ "Tabel 'db_mbg.ITEM' doesn't exist"
- Tabel `ITEM` & `DETAIL_PAKET` belum ada di DB
- Jalankan `seed.php` (akan auto-create), atau import ulang `db_mbg.sql` versi terbaru

### ❌ Chart tidak muncul di laporandata
- Pastikan ada data di tabel `DISTRIBUSI` dan `PENERIMA`
- Cek console browser untuk error JS (mungkin CDN Chart.js terblokir)

### ❌ Halaman putih / 500 error
- Aktifkan display_errors di PHP: `php.ini` → `display_errors = On`
- Cek log error Apache: `xampp/apache/logs/error.log`

### ❌ Font Inter / Material Symbols tidak muncul
- Butuh akses internet untuk Google Fonts CDN
- Untuk local-only, download file font dan serve dari folder `/assets/fonts/`

---

## 🔮 Roadmap / TODO

- [ ] Migrasi ke PDO + prepared statements (security)
- [ ] Hash password dengan `password_hash()` (ganti MD5)
- [ ] Autentikasi & session management (saat ini semua halaman publik)
- [ ] Handler logout button di sidebar (saat ini visual only)
- [ ] Pagination untuk tabel dengan rows banyak
- [ ] Search & filter di setiap modul
- [ ] Export laporan ke PDF / Excel
- [ ] Upload bukti foto pengiriman (saat ini hanya field string)
- [ ] Notifikasi real-time untuk distribusi pending
- [ ] Dark mode toggle

---

## 👥 Kredit

- **DB Schema & Backend Logic**: Original project
- **UI/UX Redesign**: Tailwind + Sky Blue design system
- **Charts**: Chart.js
- **Icons**: Material Symbols Outlined (Google)
- **Font**: Inter (Google Fonts)

---

## 📄 Lisensi

Internal / Educational use only.