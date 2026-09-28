<?php
/**
 * ============================================================================
 *  SEED DATA DUMMY - MBG Workspace
 * ============================================================================
 *  Skrip untuk mengisi data dummy ke seluruh tabel database agar
 *  modul Laporan Data, Gudang Item, dan modul lainnya memiliki data.
 *
 *  Cara pakai:
 *    1. Buka halaman ini di browser:  http://localhost/.../seed.php
 *    2. Klik tombol "Jalankan Seed Data"
 *    3. Data dummy akan di-generate (idempotent — TRUNCATE dulu).
 *
 *  CATATAN:
 *    - Skrip ini akan MENGHAPUS semua data lama di tabel terkait.
 *    - Hanya untuk development / demo, JANGAN jalankan di production.
 * ============================================================================
 */
include "config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

$db_ready = ($koneksi_db !== null);

// ----- Eksekusi seed bila diminta -----
$run_seed = isset($_POST['run_seed']) && $_POST['run_seed'] === 'yes' && $db_ready;

// ----- Auto-create tabel yang hilang (ITEM & DETAIL_PAKET) -----
$table_create_results = [];
if ($db_ready) {
    $create_sql = [
        'ITEM' => "CREATE TABLE IF NOT EXISTS ITEM (
            item_id INT AUTO_INCREMENT PRIMARY KEY,
            nama_item VARCHAR(100) NOT NULL,
            satuan VARCHAR(50),
            stok_gudang INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'DETAIL_PAKET' => "CREATE TABLE IF NOT EXISTS DETAIL_PAKET (
            detail_id INT AUTO_INCREMENT PRIMARY KEY,
            paket_id INT,
            item_id INT,
            jumlah_per_paket INT,
            FOREIGN KEY (paket_id) REFERENCES PAKETBANTUAN(paket_id),
            FOREIGN KEY (item_id) REFERENCES ITEM(item_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];
    foreach ($create_sql as $name => $sql) {
        $table_create_results[$name] = @mysqli_query($koneksi_db, $sql);
    }
    // re-detect tabel
    $existing_tables = [];
    $tr = @mysqli_query($koneksi_db, "SHOW TABLES");
    if ($tr) while ($r = mysqli_fetch_row($tr)) $existing_tables[] = strtolower($r[0]);
}

if ($run_seed) {
    // Matikan cek foreign key sementara agar TRUNCATE berurutan bisa jalan
    mysqli_query($koneksi_db, "SET FOREIGN_KEY_CHECKS = 0");

    // Bersihkan semua tabel (hanya yang ada, urutan tidak penting karena FK di-off)
    $tables = ['LAPORANDATA', 'DISTRIBUSI', 'DETAIL_PAKET', 'PAKETBANTUAN', 'PENERIMA', 'MITRA', 'USER', 'ITEM'];
    foreach ($tables as $t) {
        if (in_array(strtolower($t), $existing_tables)) {
            @mysqli_query($koneksi_db, "TRUNCATE TABLE $t");
        }
    }
    mysqli_query($koneksi_db, "SET FOREIGN_KEY_CHECKS = 1");

    $ok = [];

    // ============================================================
    // 1) USER (15 user)
    // ============================================================
    $user_names = [
        ['Administrator',     'admin@mbg.id',        'admin'],
        ['Budi Santoso',      'budi.santoso@mbg.id', 'petugas'],
        ['Siti Aminah',       'siti.aminah@mbg.id',  'petugas'],
        ['Andi Wijaya',       'andi.wijaya@mbg.id',  'petugas'],
        ['Dewi Lestari',      'dewi.lestari@mbg.id', 'koordinator'],
        ['Rina Hartati',      'rina.hartati@mbg.id', 'petugas'],
        ['Agus Prabowo',      'agus.prabowo@mbg.id', 'petugas'],
        ['Lina Marlina',      'lina.marlina@mbg.id', 'koordinator'],
        ['Hendra Gunawan',    'hendra.g@mbg.id',     'petugas'],
        ['Maya Sari',         'maya.sari@mbg.id',    'petugas'],
        ['Rizky Ramadhan',    'rizky.r@mbg.id',      'petugas'],
        ['Putri Anggraini',   'putri.a@mbg.id',      'koordinator'],
        ['Fajar Nugroho',     'fajar.n@mbg.id',      'petugas'],
        ['Indah Permata',     'indah.p@mbg.id',      'petugas'],
        ['Yusuf Hidayat',     'yusuf.h@mbg.id',      'admin'],
    ];
    $user_ids = [];
    foreach ($user_names as $i => $u) {
        $nama = $u[0]; $email = $u[1]; $role = $u[2];
        $hp = '0812' . str_pad((string)random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $tgl_daftar = date('Y-m-d', strtotime('-' . random_int(30, 365) . ' days'));
        $last_login = date('Y-m-d', strtotime('-' . random_int(0, 14) . ' days'));
        $status = ($i < 13) ? 'Aktif' : 'Nonaktif';

        $sql = "INSERT INTO USER (nama, email, password, role, no_hp, tanggal_daftar, last_login, status_akun)
                VALUES ('$nama', '$email', MD5('password123'), '$role', '$hp', '$tgl_daftar', '$last_login', '$status')";
        if (mysqli_query($koneksi_db, $sql)) {
            $user_ids[] = mysqli_insert_id($koneksi_db);
        }
    }
    $ok['USER'] = count($user_ids);

    // ============================================================
    // 2) MITRA (8 mitra, masing-masing terkait 1 user)
    // ============================================================
    $mitra_data = [
        ['Yayasan Peduli Anak',      'Lembaga Sosial',    'Hj. Maryati',     'Jl. Merdeka No.12, Jakarta Pusat',  'DKI Jakarta'],
        ['CV Nutrisi Bangsa',         'Supplier Bahan',    'Irwan Setiawan',  'Jl. Industri Raya No.45, Bekasi',   'Jawa Barat'],
        ['Koperasi Tani Makmur',     'Produsen Pangan',   'Pak Darmawan',    'Jl. Raya Bogor Km.30, Cibinong',    'Jawa Barat'],
        ['Yayasan Sehat Sentosa',    'Lembaga Sosial',    'Dr. Anissa',      'Jl. Diponegoro No.88, Surabaya',    'Jawa Timur'],
        ['PT Distribusi Mandiri',    'Logistik',          'Bambang Eko',     'Jl. Gatot Subroto Kav.21, Jakarta', 'DKI Jakarta'],
        ['Panti Asuhan Kasih Bunda', 'Panti',             'Suster Maria',    'Jl. Kaliurang Km.5, Yogyakarta',    'DI Yogyakarta'],
        ['UD Sumber Rezeki',         'Toko Sembako',      'H. Mansur',       'Jl. Pasar Induk No.7, Bandung',     'Jawa Barat'],
        ['Yayasan Tunas Bangsa',     'Lembaga Sosial',    'Ratna Megawati',  'Jl. Pahlawan No.33, Semarang',      'Jawa Tengah'],
    ];
    $mitra_ids = [];
    foreach ($mitra_data as $i => $m) {
        $uid = $user_ids[$i % count($user_ids)];
        $hp = '0813' . str_pad((string)random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO MITRA (user_id, nama_mitra, jenis_mitra, kontak_person, no_hp, alamat_mitra, wilayah_operasional)
                VALUES ('$uid', '$m[0]', '$m[1]', '$m[2]', '$hp', '$m[3]', '$m[4]')";
        if (mysqli_query($koneksi_db, $sql)) {
            $mitra_ids[] = mysqli_insert_id($koneksi_db);
        }
    }
    $ok['MITRA'] = count($mitra_ids);

    // ============================================================
    // 3) PENERIMA (30 penerima terkait user)
    // ============================================================
    $nama_depan = ['Ahmad','Bintang','Citra','Dian','Eka','Fikri','Galih','Hani','Irfan','Jaka','Kartika','Lutfi','Maulana','Nanda','Oki','Putra','Qori','Rangga','Salsa','Tegar','Umi','Vina','Wahyu','Yanto','Zara','Aisyah','Bayu','Dewi','Erlang','Fani'];
    $nama_belakang = ['Pratama','Wijaya','Lestari','Saputra','Putri','Nugroho','Anggraini','Maulana','Permata','Hidayat','Ramadhan','Sari','Maharani','Setiawan','Fadilah','Kusuma'];
    $kecamatan = ['Cempaka Putih','Kemayoran','Senen','Gambir','Tanah Abang','Menteng','Setiabudi','Tebet','Pasar Minggu','Jagakarsa','Kebayoran','Cilandak','Mampang Prapatan','Pancoran'];
    $kelurahan = ['Cempaka Putih Timur','Utan Kayu','Paseban','Karet Tengsin','Kuningan Timur','Bukit Duri','Pejaten','Cililitan','Tanjung Barat','Lebak Bulus','Pondok Labu','Bangka'];
    $kategori = ['Keluarga Miskin','Lansia','Yatim Piatu','Ibu Hamil','Balita Stunting','Penyandang Disabilitas','Korban PHK','Petani Kecil'];

    $penerima_ids = [];
    for ($i = 0; $i < 30; $i++) {
        $uid = $user_ids[($i + 2) % count($user_ids)];
        $nama = $nama_depan[$i % count($nama_depan)] . ' ' . $nama_belakang[($i * 3) % count($nama_belakang)];
        $tgl_lahir = date('Y-m-d', strtotime('-' . random_int(5, 75) . ' years'));
        $jk = ($i % 2 === 0) ? 'Laki-laki' : 'Perempuan';
        $alamat = 'Jl. ' . $kecamatan[$i % count($kecamatan)] . ' No.' . ($i + 1) . ', Jakarta';
        $kec = $kecamatan[$i % count($kecamatan)];
        $kel = $kelurahan[$i % count($kelurahan)];
        $kat = $kategori[$i % count($kategori)];
        $gaji = random_int(500000, 3500000);
        $tanggungan = random_int(0, 5);
        $status_val = ['Tervalidasi', 'Tervalidasi', 'Tervalidasi', 'Pending', 'Ditolak'][$i % 5];
        $tgl_val = ($status_val === 'Tervalidasi') ? date('Y-m-d', strtotime('-' . random_int(1, 60) . ' days')) : null;
        $tgl_val_sql = $tgl_val ? "'$tgl_val'" : "NULL";

        $sql = "INSERT INTO PENERIMA (user_id, nama_lengkap, tanggal_lahir, jenis_kelamin, alamat, kecamatan, kelurahan, kategori_penerima, penghasilan_bulanan, jumlah_tanggungan, status_validasi, tanggal_validasi)
                VALUES ('$uid', '$nama', '$tgl_lahir', '$jk', '$alamat', '$kec', '$kel', '$kat', '$gaji', '$tanggungan', '$status_val', $tgl_val_sql)";
        if (mysqli_query($koneksi_db, $sql)) {
            $penerima_ids[] = mysqli_insert_id($koneksi_db);
        }
    }
    $ok['PENERIMA'] = count($penerima_ids);

    // ============================================================
    // 4) ITEM (20 item bahan pangan)
    // ============================================================
    $items = [
        ['Beras Premium 5kg',         'kg',    1500],
        ['Telur Ayam Negeri 1kg',     'butir', 18000],
        ['Minyak Goreng 1L',          'liter', 900],
        ['Gula Pasir 1kg',            'kg',    1200],
        ['Tepung Terigu 1kg',         'kg',    850],
        ['Susu UHT Fullcream 1L',     'kotak', 2400],
        ['Kacang Hijau 500g',         'kg',    400],
        ['Daging Ayam Fillet 1kg',    'kg',    600],
        ['Daging Sapi Giling 1kg',    'kg',    350],
        ['Ikan Tongkol 1kg',          'kg',    500],
        ['Tempe Segar 500g',          'pack',  800],
        ['Tahu Putih 500g',           'pack',  750],
        ['Wortel 1kg',                'kg',    420],
        ['Bayam Segar 500g',          'ikat',  380],
        ['Brokoli 1kg',               'kg',    280],
        ['Bawang Merah 3mm 1kg',      'kg',    950],
        ['Bawang Putih 1kg',          'kg',    820],
        ['Cabai Merah 500g',          'kg',    210],
        ['Tomat Sayur 1kg',           'kg',    540],
        ['Kentang 1kg',               'kg',    670],
    ];
    $item_ids = [];
    foreach ($items as $it) {
        $stok = random_int(50, 500);
        $sql = "INSERT INTO ITEM (nama_item, satuan, stok_gudang) VALUES ('$it[0]', '$it[1]', '$stok')";
        if (mysqli_query($koneksi_db, $sql)) {
            $item_ids[] = mysqli_insert_id($koneksi_db);
        }
    }
    $ok['ITEM'] = count($item_ids);

    // ============================================================
    // 5) PAKETBANTUAN (10 paket)
    // ============================================================
    $paket_data = [
        ['Paket Balita Sehat',           'Paket gizi untuk balita usia 1-5 tahun',     'Paket Balita',     650,   3, 200],
        ['Paket Ibu Hamil',              'Paket nutrisi untuk ibu hamil & menyusui',     'Paket Ibu Hamil',  800,   4, 150],
        ['Paket Lansia',                'Paket makanan ringan untuk lansia',           'Paket Lansia',     500,   2, 180],
        ['Paket Anak Sekolah',          'Paket snack sehat untuk anak usia sekolah',   'Paket Anak',       550,   2, 300],
        ['Paket Keluarga Pra-Sejahtera','Paket lengkap untuk keluarga miskin',          'Paket Keluarga',   1800,  8, 100],
        ['Paket Disabilitas',           'Paket diet khusus untuk penyandang disabilitas','Paket Disabilitas',700, 3, 80],
        ['Paket Yatim Piatu',           'Paket gizi untuk anak yatim piatu',           'Paket Anak',       600,   3, 120],
        ['Paket Petani Kecil',          'Paket bahan pokok untuk petani kecil',        'Paket Keluarga',   1200,  6, 90],
        ['Paket Korban PHK',            'Paket darurat untuk korban PHK',              'Paket Darurat',    1500,  7, 110],
        ['Paket Tambahan Stunting',     'Paket anti stunting dengan protein tinggi',    'Paket Balita',     900,   4, 70],
    ];
    $paket_ids = [];
    foreach ($paket_data as $p) {
        $kadaluarsa = date('Y-m-d', strtotime('+' . random_int(60, 365) . ' days'));
        $sql = "INSERT INTO PAKETBANTUAN (nama_paket, deskripsi, jenis_bantuan, kalori_total, berat_total, kadaluarsa, kuantitas)
                VALUES ('$p[0]', '$p[1]', '$p[2]', '$p[3]', '$p[4]', '$kadaluarsa', '$p[5]')";
        if (mysqli_query($koneksi_db, $sql)) {
            $paket_ids[] = mysqli_insert_id($koneksi_db);
        }
    }
    $ok['PAKETBANTUAN'] = count($paket_ids);

    // ============================================================
    // 6) DETAIL_PAKET (komposisi paket, 3-5 item per paket)
    // ============================================================
    $detail_count = 0;
    foreach ($paket_ids as $pi => $paket_id) {
        // pilih 3-5 item acak
        $num_items = random_int(3, 5);
        $shuffled = $item_ids;
        shuffle($shuffled);
        $selected = array_slice($shuffled, 0, $num_items);
        foreach ($selected as $item_id) {
            $jumlah = random_int(1, 5);
            $sql = "INSERT INTO DETAIL_PAKET (paket_id, item_id, jumlah_per_paket)
                    VALUES ('$paket_id', '$item_id', '$jumlah')";
            if (mysqli_query($koneksi_db, $sql)) {
                $detail_count++;
            }
        }
    }
    $ok['DETAIL_PAKET'] = $detail_count;

    // ============================================================
    // 7) DISTRIBUSI (40 distribusi dalam 6 bulan terakhir)
    // ============================================================
    $lokasi = [
        'Posyandu Anggrek, Cempaka Putih',
        'Panti Asuhan Kasih Bunda, Yogyakarta',
        'Balai RW 05, Tebet',
        'Kantor Kelurahan Bukit Duri',
        'Gedung Serbaguna Pancoran',
        'Masjid Al-Ikhlas, Kemayoran',
        'Sekolah Dasar Negeri 03, Setiabudi',
        'Puskesmas Pembantu, Pasar Minggu',
    ];
    $status_pengiriman = ['Selesai', 'Selesai', 'Selesai', 'Selesai', 'Diterima', 'Diterima', 'Dalam Perjalanan', 'Diproses', 'Pending', 'Gagal'];
    $catatan_petugas = [
        'Penerima hadir dan menandatangani bukti terima.',
        'Paket diterima dalam kondisi baik.',
        'Pengiriman lancar tanpa kendala.',
        'Alamat sesuai, penerima diwakili keluarga.',
        'Paket diterima sebagian, sisanya dikirim ulang.',
        'Cuaca hujan, pengiriman terlambat 1 jam.',
        'Penerima berhalangan, dijadwalkan ulang.',
        'Kendaraan mogok di perjalanan.',
        '',
        '',
    ];

    $distribusi_ids = [];
    for ($i = 0; $i < 40; $i++) {
        $paket_id = $paket_ids[array_rand($paket_ids)];
        $penerima_id = $penerima_ids[array_rand($penerima_ids)];
        $mitra_id = $mitra_ids[array_rand($mitra_ids)];

        $days_ago = random_int(1, 180);
        $tgl_kirim = date('Y-m-d', strtotime("-$days_ago days"));
        $status = $status_pengiriman[array_rand($status_pengiriman)];
        $tgl_terima = in_array($status, ['Selesai', 'Diterima'])
            ? date('Y-m-d', strtotime($tgl_kirim . ' +' . random_int(0, 3) . ' days'))
            : null;
        $tgl_terima_sql = $tgl_terima ? "'$tgl_terima'" : "NULL";
        $lokasi_kirim = $lokasi[array_rand($lokasi)];
        $bukti = 'bukti_' . ($i + 1) . '.jpg';
        $catatan = $catatan_petugas[array_rand($catatan_petugas)];

        $sql = "INSERT INTO DISTRIBUSI (paket_id, penerima_id, mitra_id, tanggal_kirim, tanggal_terima, lokasi_pengiriman, status_pengiriman, bukti_pengiriman, catatan_petugas)
                VALUES ('$paket_id', '$penerima_id', '$mitra_id', '$tgl_kirim', $tgl_terima_sql, '$lokasi_kirim', '$status', '$bukti', '$catatan')";
        if (mysqli_query($koneksi_db, $sql)) {
            $distribusi_ids[] = mysqli_insert_id($koneksi_db);
        }
    }
    $ok['DISTRIBUSI'] = count($distribusi_ids);

    // ============================================================
    // 8) LAPORANDATA (1 laporan per distribusi yang selesai)
    // ============================================================
    $status_laporan = ['Disetujui', 'Disetujui', 'Disetujui', 'Menunggu Review', 'Ditolak'];
    $laporan_count = 0;
    foreach ($distribusi_ids as $did) {
        $d_q = mysqli_query($koneksi_db, "SELECT tanggal_kirim, paket_id, status_pengiriman FROM DISTRIBUSI WHERE distribusi_id = '$did'");
        $d = mysqli_fetch_assoc($d_q);
        if (!$d) continue;

        $paket_q = mysqli_query($koneksi_db, "SELECT kuantitas FROM PAKETBANTUAN WHERE paket_id = '" . $d['paket_id'] . "'");
        $p = mysqli_fetch_assoc($paket_q);
        $kuantitas = (int)($p['kuantitas'] ?? 1);
        $dikirim = $kuantitas;
        $diterima = in_array($d['status_pengiriman'], ['Selesai', 'Diterima']) ? $kuantitas : random_int(0, $kuantitas);

        $tgl_lap = date('Y-m-d', strtotime($d['tanggal_kirim'] . ' +7 days'));
        $status_l = $status_laporan[array_rand($status_laporan)];
        $catatan = ($status_l === 'Disetujui') ? 'Laporan sesuai dengan bukti di lapangan.' : (($status_l === 'Ditolak') ? 'Bukti pengiriman tidak jelas.' : 'Sedang diverifikasi.');
        $dibuat = $user_ids[array_rand($user_ids)];

        $sql = "INSERT INTO LAPORANDATA (distribusi_id, tanggal_laporan, status_laporan, jumlah_paket_dikirim, jumlah_paket_diterima, catatan, dibuat_oleh)
                VALUES ('$did', '$tgl_lap', '$status_l', '$dikirim', '$diterima', '$catatan', '$dibuat')";
        if (mysqli_query($koneksi_db, $sql)) {
            $laporan_count++;
        }
    }
    $ok['LAPORANDATA'] = $laporan_count;
}

// ----- Hitung statistik saat ini untuk ditampilkan (defensif) -----
$stats = [];
$existing_tables = [];
$missing_tables  = [];
if ($db_ready) {
    // deteksi tabel yang ada di database
    $tr = @mysqli_query($koneksi_db, "SHOW TABLES");
    if ($tr) {
        while ($r = mysqli_fetch_row($tr)) $existing_tables[] = strtolower($r[0]);
    }
    $all_tables = ['USER','MITRA','PENERIMA','ITEM','PAKETBANTUAN','DETAIL_PAKET','DISTRIBUSI','LAPORANDATA'];
    foreach ($all_tables as $t) {
        if (!in_array(strtolower($t), $existing_tables)) $missing_tables[] = $t;
    }

    $queries = [
        'USER'         => "SELECT COUNT(*) c FROM USER",
        'MITRA'        => "SELECT COUNT(*) c FROM MITRA",
        'PENERIMA'     => "SELECT COUNT(*) c FROM PENERIMA",
        'ITEM'         => "SELECT COUNT(*) c FROM ITEM",
        'PAKETBANTUAN' => "SELECT COUNT(*) c FROM PAKETBANTUAN",
        'DETAIL_PAKET' => "SELECT COUNT(*) c FROM DETAIL_PAKET",
        'DISTRIBUSI'   => "SELECT COUNT(*) c FROM DISTRIBUSI",
        'LAPORANDATA'  => "SELECT COUNT(*) c FROM LAPORANDATA",
    ];
    foreach ($queries as $label => $q) {
        if (in_array(strtolower($label), $existing_tables)) {
            $r = @mysqli_query($koneksi_db, $q);
            $stats[$label] = $r ? (int)mysqli_fetch_assoc($r)['c'] : 0;
        } else {
            $stats[$label] = '—';  // tabel belum ada
        }
    }
}

$current_page = 'dashboard';
$page_title   = 'Seed Data Dummy';
include "includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-[1100px] mx-auto">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Seed Data Dummy</span>
        </nav>

        <!-- Header -->
        <div class="mb-8">
            <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-2">Alat Pengembangan</p>
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Seed Data Dummy</h2>
            <p class="text-slate-500 mt-1.5 text-sm sm:text-base">
                Hasilkan data dummy realistis untuk mengisi modul Laporan Data, Gudang Item, dan modul lainnya.
            </p>
        </div>

        <!-- Banner jika koneksi DB gagal -->
        <?php if (!$db_ready): ?>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <div>
                        <h3 class="font-bold text-red-900">Koneksi database gagal</h3>
                        <p class="text-red-700 text-sm mt-1">Pastikan MySQL berjalan dan database <code class="bg-red-100 px-1 rounded">db_mbg</code> sudah di-import.</p>
                    </div>
                </div>
            </div>
        <?php elseif (!empty($missing_tables)): ?>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600">build</span>
                    <div>
                        <h3 class="font-bold text-amber-900">Tabel tambahan akan dibuat otomatis</h3>
                        <p class="text-amber-700 text-sm mt-1">
                            Tabel berikut belum ada dan akan dibuat saat Anda menjalankan seed:
                            <span class="font-mono bg-amber-100 px-1.5 py-0.5 rounded"><?= implode('</span>, <span class="font-mono bg-amber-100 px-1.5 py-0.5 rounded">', $missing_tables) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hasil eksekusi -->
        <?php if (isset($ok) && $run_seed): ?>
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 mb-6">
                <div class="flex items-start gap-3 mb-4">
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                    <div>
                        <h3 class="font-bold text-emerald-900">Seed berhasil!</h3>
                        <p class="text-emerald-700 text-sm mt-1">Data dummy telah diisi ke seluruh tabel. Tabel lama di-<b>TRUNCATE</b> dan diganti dengan data baru.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <?php foreach ($ok as $tbl => $cnt): ?>
                        <div class="bg-white border border-emerald-100 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-emerald-700"><?= $cnt ?></div>
                            <div class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider mt-0.5"><?= $tbl ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistik saat ini -->
        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Statistik Database Saat Ini</h3>
                    <p class="text-slate-500 text-sm mt-0.5">Jumlah baris di setiap tabel.</p>
                </div>
                <span class="material-symbols-outlined text-primary-600">database</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <?php
                $icons = [
                    'USER' => 'person', 'MITRA' => 'handshake', 'PENERIMA' => 'groups',
                    'ITEM' => 'warehouse', 'PAKETBANTUAN' => 'inventory_2',
                    'DETAIL_PAKET' => 'list_alt', 'DISTRIBUSI' => 'local_shipping',
                    'LAPORANDATA' => 'analytics',
                ];
                foreach ($stats as $tbl => $cnt): ?>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="size-8 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[18px]"><?= $icons[$tbl] ?? 'storage' ?></span>
                            </div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider"><?= $tbl ?></div>
                        </div>
                        <div class="text-2xl font-bold text-slate-900"><?= $cnt ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Form seed -->
        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 mb-6">
            <div class="flex items-start gap-3 mb-4">
                <div class="size-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Perhatian!</h3>
                    <p class="text-slate-600 text-sm mt-1">
                        Menjalankan seed akan <b>menghapus semua data</b> di 8 tabel dan menggantinya dengan data dummy baru.
                        Skrip ini aman dipakai berulang kali (idempotent) dan hanya untuk <b>lingkungan development</b>.
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 mb-5">
                <h4 class="font-bold text-slate-900 text-sm mb-2">Data yang akan dihasilkan:</h4>
                <ul class="text-sm text-slate-600 space-y-1.5">
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 15 User (admin, koordinator, petugas)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 8 Mitra (Lembaga Sosial, Supplier, Logistik, dll)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 30 Penerima (8 kategori sosial ekonomi)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 20 Item Gudang (bahan pangan & sayuran)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 10 Paket Bantuan (Balita, Lansia, Keluarga, dll)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 30-50 Komposisi Paket (DETAIL_PAKET)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> 40 Distribusi (6 bulan terakhir, berbagai status)</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-600 text-[18px]">check</span> ~35 Laporan Data (review & approval)</li>
                </ul>
            </div>

            <form method="POST" onsubmit="return confirm('Yakin ingin generate data dummy? Semua data lama akan DIHAPUS dan diganti.');">
                <input type="hidden" name="run_seed" value="yes">
                <button type="submit" <?= $db_ready ? '' : 'disabled' ?>
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all shadow-sm
                        <?= $db_ready 
                                ? 'bg-primary-600 hover:bg-primary-700 text-white hover:shadow-glow' 
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed' ?>">
                    <span class="material-symbols-outlined text-[20px]">play_arrow</span>
                    Jalankan Seed Data
                </button>
                <a href="index.php" class="ml-3 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold transition-colors bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali ke Dashboard
                </a>
            </form>
        </div>

        <!-- Tabel referensi login -->
        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="material-symbols-outlined text-primary-600">info</span>
                <h3 class="font-bold text-slate-900">Informasi Login Default</h3>
            </div>
            <p class="text-sm text-slate-600 mb-4">Semua user yang di-generate menggunakan password default:</p>
            <div class="bg-slate-900 text-slate-100 rounded-xl p-4 font-mono text-sm">
                <div class="flex justify-between"><span>Email:</span><span class="text-primary-400">&lt;lihat tabel USER&gt;</span></div>
                <div class="flex justify-between mt-1"><span>Password:</span><span class="text-primary-400">password123</span></div>
                <div class="text-xs text-slate-500 mt-2">(Password di-hash dengan MD5)</div>
            </div>
        </div>

    </div>
</main>
</div>
</body>
</html>