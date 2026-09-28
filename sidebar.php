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

$nav_items = [
    ['key' => 'dashboard',  'label' => 'Dashboard',      'icon' => 'dashboard',         'url' => $sidebar_base . 'index.php'],
    ['key' => 'mitra',      'label' => 'Mitra',          'icon' => 'handshake',         'url' => $sidebar_base . 'mitra/index.php'],
    ['key' => 'user',       'label' => 'Pengguna',       'icon' => 'person',            'url' => $sidebar_base . 'user/index.php'],
    ['key' => 'penerima',   'label' => 'Penerima',       'icon' => 'groups',            'url' => $sidebar_base . 'penerima/index.php'],
    ['key' => 'paket',      'label' => 'Paket Bantuan',  'icon' => 'inventory_2',       'url' => $sidebar_base . 'paketbantuan/index.php'],
    ['key' => 'distribusi', 'label' => 'Distribusi',     'icon' => 'local_shipping',    'url' => $sidebar_base . 'distribusi/index.php'],
    ['key' => 'laporan',    'label' => 'Laporan Data',   'icon' => 'analytics',         'url' => $sidebar_base . 'laporandata/index.php'],
    ['key' => 'item',       'label' => 'Gudang Item',    'icon' => 'warehouse',         'url' => $sidebar_base . 'item/index.php'],
];
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

    <!-- Profil Admin -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
        <div class="size-10 rounded-full ring-2 ring-primary-100 bg-cover bg-center shadow-sm"
             style="background-image: url('https://ui-avatars.com/api/?name=Admin+MBG&background=0ea5e9&color=fff&bold=true');">
        </div>
        <div class="flex flex-col leading-tight min-w-0">
            <h2 class="text-slate-900 text-sm font-bold truncate">Administrator</h2>
            <p class="text-slate-500 text-xs mt-0.5 truncate">admin@portal.com</p>
        </div>
    </div>

    <!-- Navigasi -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Menu Utama</p>
        <div class="flex flex-col gap-1">
            <?php foreach ($nav_items as $item): 
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
        </div>
    </nav>

    <!-- Logout -->
    <div class="p-3 border-t border-slate-100">
        <button class="w-full flex items-center justify-center gap-2 rounded-xl h-11 px-4 bg-slate-50 text-slate-600 hover:bg-red-50 hover:text-red-600 text-sm font-semibold transition-colors border border-slate-100 hover:border-red-100">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            Keluar
        </button>
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
            <?php foreach ($nav_items as $item): 
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
    </div>
</div>