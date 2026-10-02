<?php
/**
 * ============================================================================
 *  AUTH HELPER - MBG Workspace
 * ============================================================================
 *  Helper untuk proteksi halaman & pembedaan hak akses berdasarkan role.
 *
 *  Cara pakai di halaman protected:
 *    require_once 'includes/auth.php';
 *    require_login();           // wajib login (semua role)
 *    require_access('mitra');   // hanya role yang boleh akses modul 'mitra'
 *
 *  Session yang tersedia setelah login:
 *    $_SESSION['user_id']   -> int/string
 *    $_SESSION['nama']      -> string
 *    $_SESSION['email']     -> string
 *    $_SESSION['role']      -> 'admin' | 'petugas' | 'koordinator'
 *    $_SESSION['login_at']  -> timestamp login
 *
 *  Definisi 3 role di MBG Workspace:
 *    - admin       -> Full akses ke seluruh modul + utility dev (seed)
 *    - koordinator -> Akses modul operasional, REVIEW/VALIDASI laporan,
 *                    tidak bisa kelola pengguna & tidak bisa hapus master data
 *    - petugas     -> Akses input lapangan: buat distribusi, update stok,
 *                    buat laporan baru (status: Menunggu Review)
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Definisi kredensial statis untuk demo / development.
 * Sesuai permintaan: untuk saat ini login menggunakan akun hardcode.
 * Pada produksi, ganti blok ini dengan query ke tabel USER.
 */
function mbg_get_static_credentials(): array {
    return [
        [
            'email'    => 'admin@gmail.com',
            'password' => '1234',
            'nama'     => 'Administrator',
            'role'     => 'admin',
        ],
        [
            'email'    => 'koordinator@gmail.com',
            'password' => '1234',
            'nama'     => 'Koordinator MBG',
            'role'     => 'koordinator',
        ],
        [
            'email'    => 'petugas@gmail.com',
            'password' => '1234',
            'nama'     => 'Petugas MBG',
            'role'     => 'petugas',
        ],
    ];
}

/**
 * Definisi akses modul per role.
 * Key = nama modul, value = array role yang diizinkan masuk ke modul tsb.
 *
 * Catatan:
 * - 'dashboard' selalu terbuka untuk semua role yang sudah login.
 * - 'seed' hanya untuk admin (utility development).
 * - Pembedaan di level "boleh masuk atau tidak". Di dalam modul, role
 *   tertentu disembunyikan tombol/aksi CRUD via helper can_action().
 */
function mbg_module_access_map(): array {
    return [
        'dashboard'   => ['admin', 'koordinator', 'petugas'],
        'mitra'       => ['admin', 'koordinator', 'petugas'],   // petugas read-only via UI
        'user'        => ['admin'],                             // khusus admin
        'penerima'    => ['admin', 'koordinator', 'petugas'],   // petugas read-only
        'paket'       => ['admin', 'koordinator', 'petugas'],   // petugas read-only
        'distribusi'  => ['admin', 'koordinator', 'petugas'],   // semua bisa CRUD
        'laporan'     => ['admin', 'koordinator', 'petugas'],   // role beda = aksi beda
        'item'        => ['admin', 'koordinator', 'petugas'],   // petugas update stok
        'seed'        => ['admin'],                             // utility dev
    ];
}

/**
 * Definisi aksi CRUD per role per modul.
 * Key = "module.action" (action: 'create', 'update', 'delete').
 * Value = array role yang diizinkan.
 *
 * Gunakan via can_action($module, $action).
 */
function mbg_action_access_map(): array {
    return [
        // Mitra: hanya admin & koordinator yang boleh CRUD.
        // Petugas hanya boleh lihat (tidak ada create/update/delete).
        'mitra.create'  => ['admin', 'koordinator'],
        'mitra.update'  => ['admin', 'koordinator'],
        'mitra.delete'  => ['admin'],

        // Default rules untuk modul lain (bisa ditambah nanti sesuai kebutuhan)
        'penerima.create'  => ['admin', 'koordinator'],
        'penerima.update'  => ['admin', 'koordinator'],
        'penerima.delete'  => ['admin'],

        'paket.create'  => ['admin', 'koordinator'],
        'paket.update'  => ['admin', 'koordinator'],
        'paket.delete'  => ['admin'],

        'distribusi.create' => ['admin', 'koordinator', 'petugas'],
        'distribusi.update' => ['admin', 'koordinator', 'petugas'],
        'distribusi.delete' => ['admin'],

        'laporan.create'  => ['admin', 'koordinator', 'petugas'],
        'laporan.update'  => ['admin', 'koordinator'],
        'laporan.delete'  => ['admin'],

        'item.create' => ['admin', 'koordinator'],
        'item.update' => ['admin', 'koordinator', 'petugas'],
        'item.delete' => ['admin'],

        'user.create'  => ['admin'],
        'user.update'  => ['admin'],
        'user.delete'  => ['admin'],
    ];
}

/**
 * Cek apakah user sudah login.
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect ke halaman login kalau belum login.
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . mbg_base_url() . 'login.php');
        exit;
    }
}

/**
 * Redirect kalau role user tidak sesuai (untuk halaman dengan 1 role spesifik).
 */
function require_role(string $role): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        header('Location: ' . mbg_base_url() . 'index.php?msg=forbidden');
        exit;
    }
}

/**
 * Cek apakah role user saat ini boleh mengakses modul tertentu.
 * Return true jika boleh, false jika tidak.
 */
function can_access(string $module_key): bool {
    $role = $_SESSION['role'] ?? '';
    $map  = mbg_module_access_map();
    if (!isset($map[$module_key])) {
        // Modul tidak dikenali -> default izinkan (fail-open) supaya
        // halaman baru tidak langsung terkunci tanpa sadar.
        return true;
    }
    return in_array($role, $map[$module_key], true);
}

/**
 * Cek apakah role user saat ini boleh melakukan aksi CRUD tertentu
 * pada modul. Return true jika boleh, false jika tidak.
 *
 * Contoh:
 *   if (can_action('mitra', 'create')) { ... tampilkan tombol Tambah ... }
 *   if (can_action('mitra', 'update')) { ... tampilkan tombol Edit ... }
 */
function can_action(string $module_key, string $action): bool {
    $role = $_SESSION['role'] ?? '';
    $key  = $module_key . '.' . $action;
    $map  = mbg_action_access_map();
    if (!isset($map[$key])) {
        // Aksi tidak terdaftar -> default tolak (fail-closed) supaya
        // fitur baru tidak sengaja terbuka ke semua role.
        return false;
    }
    return in_array($role, $map[$key], true);
}

/**
 * Variabel global sederhana agar view bisa akses peran & kemampuan user.
 * Di-set oleh mbg_init_view() ketika header.php dipanggil.
 */
function mbg_init_view(): void {
    global $mbg_role, $mbg_is_petugas, $mbg_is_koordinator, $mbg_is_admin;
    $mbg_role          = $_SESSION['role'] ?? '';
    $mbg_is_admin      = ($mbg_role === 'admin');
    $mbg_is_koordinator= ($mbg_role === 'koordinator');
    $mbg_is_petugas    = ($mbg_role === 'petugas');
}

/**
 * Redirect kalau user tidak boleh akses modul tertentu.
 * Tampilkan halaman "Akses Ditolak" yang ramah jika tidak boleh.
 */
function require_access(string $module_key): void {
    require_login();
    if (!can_access($module_key)) {
        // Tampilkan halaman forbidden inline (tanpa include header publik)
        http_response_code(403);
        mbg_render_forbidden($module_key);
        exit;
    }
}

/**
 * Render halaman "Akses Ditolak" yang konsisten dengan desain MBG.
 * Dipakai oleh require_access() dan sidebar.
 */
function mbg_render_forbidden(string $module_key = ''): void {
    $role  = $_SESSION['role'] ?? 'Tidak Dikenal';
    $nama  = $_SESSION['nama'] ?? 'Pengguna';
    $home  = mbg_base_url() . 'index.php';

    // Escape untuk HTML
    $role_e  = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');
    $nama_e  = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
    $home_e  = htmlspecialchars($home, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - MBG Workspace</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] }, colors: { primary: { 50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e' } } } } }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; user-select: none; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-800 antialiased min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-slate-100 p-8 text-center">
        <div class="size-20 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-5 ring-1 ring-red-100">
            <span class="material-symbols-outlined text-5xl">block</span>
        </div>
        <p class="text-xs font-bold text-red-600 uppercase tracking-widest mb-2">Akses Ditolak</p>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Anda tidak punya akses</h1>
        <p class="text-slate-500 text-sm mb-6 leading-relaxed">
            Halo <b>{$nama_e}</b> (<span class="font-mono text-xs px-2 py-0.5 rounded bg-slate-100">{$role_e}</span>),
            role Anda saat ini tidak diizinkan membuka modul ini.
        </p>
        <a href="{$home_e}" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
HTML;
}

/**
 * Bangun base URL otomatis berdasarkan posisi file.
 */
function mbg_base_url(): string {
    return './';
}

/**
 * Autentikasi user berdasarkan email + password.
 * Return: array data user jika sukses, atau null jika gagal.
 */
function mbg_authenticate(string $email, string $password): ?array {
    $email = strtolower(trim($email));
    foreach (mbg_get_static_credentials() as $cred) {
        if (strtolower($cred['email']) === $email && $cred['password'] === $password) {
            return [
                'email' => $cred['email'],
                'nama'  => $cred['nama'],
                'role'  => $cred['role'],
            ];
        }
    }
    return null;
}

/**
 * Helper untuk menampilkan label role dalam Bahasa Indonesia.
 */
function mbg_role_label(string $role): string {
    $map = [
        'admin'       => 'Administrator',
        'koordinator' => 'Koordinator',
        'petugas'     => 'Petugas',
    ];
    return $map[$role] ?? ucfirst($role);
}

/**
 * Helper warna badge per role (untuk konsistensi UI).
 * Return array dengan key 'bg', 'text', 'ring' untuk Tailwind classes.
 */
function mbg_role_badge_style(string $role): array {
    switch ($role) {
        case 'admin':
            return ['bg' => 'bg-primary-50',  'text' => 'text-primary-700',  'ring' => 'ring-primary-500/20',  'icon' => 'admin_panel_settings'];
        case 'koordinator':
            return ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'ring' => 'ring-indigo-500/20',  'icon' => 'supervisor_account'];
        case 'petugas':
            return ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-500/20', 'icon' => 'engineering'];
        default:
            return ['bg' => 'bg-slate-50',   'text' => 'text-slate-700',   'ring' => 'ring-slate-500/20',   'icon' => 'person'];
    }
}
