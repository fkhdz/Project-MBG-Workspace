<?php
/**
 * Komponen Sidebar MBG Workspace
 * 
 * Variabel yang harus sudah di-set sebelum include:
 *   - $current_page (string) : key halaman aktif (lihat $nav_items)
 *   - $page_title   (string) : judul halaman (untuk tag <title> di header.php)
 * 
 * Gunakan dengan header.php:
 *   include 'includes/header.php'; // pasang <head> & body class
 *   include 'sidebar.php';         // render <aside>
 *   echo '<main class="flex-1 ...">'; // konten
 *   echo '</main></div></body></html>';
 */

// $mbg_current_user sudah di-set oleh includes/header.php sebelum file ini di-include.
// Fallback kalau sidebar di-include tanpa header (mis. halaman publik tertentu).
if (!isset($mbg_current_user)) {
    $mbg_current_user = [
        'id'    => $_SESSION['user_id'] ?? null,
        'nama'  => $_SESSION['nama']    ?? 'Pengguna',
        'email' => $_SESSION['email']   ?? '',
        'role'  => $_SESSION['role']    ?? 'karyawan',
    ];
}

$mbg_user_nama  = $mbg_current_user['nama']  ?: 'Pengguna';
$mbg_user_email = $mbg_current_user['email'] ?: '';
$mbg_user_role  = $mbg_current_user['role']  ?: 'karyawan';

// Inisial avatar dari nama user (untuk avatar di sidebar)
$mbg_avatar_initials = strtoupper(substr(preg_replace('/\s+/', ' ', trim($mbg_user_nama)), 0, 1));
$mbg_avatar_url = 'https://ui-avatars.com/api/?name=' . urlencode($mbg_user_nama)
                . '&background=0ea5e9&color=fff&bold=true';

if (!isset($current_page)) $current_page = '';

/**
 * Deteksi level direktori otomatis:
 * - Disertakan dari root    (mis. /index.php)        → $base = ''
 * - Disertakan dari modul   (mis. /mitra/index.php)   → $base = '../'
 * - Bisa di-override manual dengan set $sidebar_base sebelum include
 */
if (!isset($sidebar_base)) {
    // __FILE__ dari sidebar.php; bandingkan dengan path skrip yang memanggilnya
    $caller = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__;
    $sidebar_dir = str_replace('\\', '/', dirname(__FILE__));
    $caller_dir  = str_replace('\\', '/', dirname($caller));
    $sidebar_base = ($sidebar_dir === $caller_dir) ? '' : '../';
}

// URL logout adaptif terhadap base direktori
$mbg_logout_url = $sidebar_base . 'logout.php';

/**
 * Daftar menu navigasi + key modul untuk filtering akses per role.
 * 'module' dipakai oleh can_access() di auth.php.
 */
$nav_items = [
    ['key' => 'dashboard',  'module' => 'dashboard',  'label' => 'Dashboard',      'icon' => 'dashboard',         'url' => $sidebar_base . 'index.php'],
    ['key' => 'mitra',      'module' => 'mitra',      'label' => 'Mitra',          'icon' => 'handshake',         'url' => $sidebar_base . 'mitra/index.php'],
    ['key' => 'user',       'module' => 'user',       'label' => 'Pengguna',       'icon' => 'person',            'url' => $sidebar_base . 'user/index.php'],
    ['key' => 'penerima',   'module' => 'penerima',   'label' => 'Penerima',       'icon' => 'groups',            'url' => $sidebar_base . 'penerima/index.php'],
    ['key' => 'paket',      'module' => 'paket',      'label' => 'Paket Bantuan',  'icon' => 'inventory_2',       'url' => $sidebar_base . 'paketbantuan/index.php'],
    ['key' => 'distribusi', 'module' => 'distribusi', 'label' => 'Distribusi',     'icon' => 'local_shipping',    'url' => $sidebar_base . 'distribusi/index.php'],
    ['key' => 'laporan',    'module' => 'laporan',    'label' => 'Laporan Data',   'icon' => 'analytics',         'url' => $sidebar_base . 'laporandata/index.php'],
    ['key' => 'item',       'module' => 'item',       'label' => 'Gudang Item',    'icon' => 'warehouse',         'url' => $sidebar_base . 'item/index.php'],
];

// Filter menu: hanya tampilkan yang boleh diakses role user saat ini.
$nav_items_visible = array_values(array_filter($nav_items, function($it) {
    return function_exists('can_access') ? can_access($it['module']) : true;
}));

// Style badge untuk role user yang sedang login.
$mbg_role_style = function_exists('mbg_role_badge_style')
    ? mbg_role_badge_style($mbg_user_role)
    : ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'ring' => 'ring-slate-500/20', 'icon' => 'person'];
?>
<aside class="hidden lg:flex flex-col w-64 bg-white border-r border-slate-200 shrink-0 z-20 sticky top-0 h-screen">
    <!-- Logo / Brand -->
    <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-100">
        <div class="size-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center shadow-md shadow-primary-500/20">
            <span class="material-symbols-outlined text-xl">nutrition</span>
        </div>
        <div class="flex flex-col leading-tight">
            <h1 class="text-slate-900 text-base font-bold">MBG</h1>
            <p class="text-slate-500 text-[11px] font-semibold tracking-wider">WORKSPACE</p>
        </div>
    </div>

    <!-- Profil User (dari session login) -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
        <div class="size-10 rounded-full ring-2 ring-primary-100 bg-cover bg-center shadow-sm"
             style="background-image: url('<?= htmlspecialchars($mbg_avatar_url) ?>');">
        </div>
        <div class="flex flex-col leading-tight min-w-0 flex-1">
            <h2 class="text-slate-900 text-sm font-bold truncate"><?= htmlspecialchars($mbg_user_nama) ?></h2>
            <p class="text-slate-500 text-xs mt-0.5 truncate"><?= htmlspecialchars($mbg_user_email) ?></p>
            <span class="mt-1.5 inline-flex items-center gap-1 self-start px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $mbg_role_style['bg'] ?> <?= $mbg_role_style['text'] ?> <?= $mbg_role_style['ring'] ?>">
                <span class="material-symbols-outlined text-[12px]"><?= $mbg_role_style['icon'] ?></span>
                <?= htmlspecialchars(mbg_role_label($mbg_user_role)) ?>
            </span>
        </div>
    </div>

    <!-- Navigasi -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Menu Utama</p>
        <div class="flex flex-col gap-1">
            <?php foreach ($nav_items_visible as $item): 
                $is_active = ($current_page === $item['key']);
                $cls = $is_active 
                    ? 'bg-primary-50 text-primary-700 font-semibold shadow-sm shadow-primary-500/5' 
                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium';
                $fillStyle = $is_active ? 'style="font-variation-settings:\'FILL\' 1, \'wght\' 500;"' : '';
            ?>
                <a href="<?= $item['url'] ?>" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all <?= $cls ?>">
                    <span class="material-symbols-outlined text-[22px]" <?= $fillStyle ?>>
                        <?= $item['icon'] ?>
                    </span>
                    <span class="truncate"><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>

            <?php if (empty($nav_items_visible)): ?>
                <p class="px-3 py-4 text-xs text-slate-400 italic">Tidak ada modul yang dapat diakses.</p>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Logout -->
    <div class="p-3 border-t border-slate-100">
        <a href="<?= htmlspecialchars($mbg_logout_url) ?>"
           onclick="return confirm('Yakin ingin keluar dari sesi ini?');"
           class="w-full flex items-center justify-center gap-2 rounded-xl h-11 px-4 bg-slate-50 text-slate-600 hover:bg-red-50 hover:text-red-600 text-sm font-semibold transition-colors border border-slate-100 hover:border-red-100">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            Keluar
        </a>
    </div>
</aside>

<!-- Topbar untuk mobile -->
<header class="lg:hidden sticky top-0 z-30 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <div class="size-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center">
            <span class="material-symbols-outlined text-lg">nutrition</span>
        </div>
        <h1 class="text-base font-bold text-slate-900">MBG Workspace</h1>
    </div>
    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" 
            class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
        <span class="material-symbols-outlined">menu</span>
    </button>
</header>

<!-- Mobile Menu Drawer -->
<div id="mobile-menu" class="hidden lg:hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm" onclick="this.classList.add('hidden')">
    <div class="absolute left-0 top-0 h-full w-72 bg-white shadow-2xl p-4 overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="size-9 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">nutrition</span>
                </div>
                <h1 class="text-base font-bold text-slate-900">MBG Workspace</h1>
            </div>
            <button onclick="document.getElementById('mobile-menu').classList.add('hidden')" 
                    class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="flex flex-col gap-1">
            <?php foreach ($nav_items_visible as $item): 
                $is_active = ($current_page === $item['key']);
                $cls = $is_active 
                    ? 'bg-primary-50 text-primary-700 font-semibold' 
                    : 'text-slate-600 hover:bg-slate-50 font-medium';
            ?>
                <a href="<?= $item['url'] ?>" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-colors <?= $cls ?>">
                    <span class="material-symbols-outlined text-[22px]"><?= $item['icon'] ?></span>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Logout (mobile) -->
        <div class="mt-4 pt-4 border-t border-slate-100">
            <a href="<?= htmlspecialchars($mbg_logout_url) ?>"
               onclick="return confirm('Yakin ingin keluar dari sesi ini?');"
               class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors">
                <span class="material-symbols-outlined text-[22px]">logout</span>
                <span>Keluar</span>
            </a>
        </div>
    </div>
</div>