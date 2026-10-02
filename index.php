<?php 
include "config/koneksi.php"; 

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("<div style='padding:2rem; font-family:sans-serif;'><h3 style='color:red;'>Koneksi Gagal!</h3><p>Pastikan file <code>config/koneksi.php</code> ada.</p></div>");
}


$stats_penerima       = (int)(mysqli_fetch_assoc(mysqli_query($koneksi_db, "SELECT COUNT(*) as total FROM PENERIMA"))['total'] ?? 0);
$jumlah_selesai       = (int)(mysqli_fetch_assoc(mysqli_query($koneksi_db, "SELECT COUNT(*) as total FROM DISTRIBUSI WHERE status_pengiriman IN ('Selesai','Terkirim','Diterima')"))['total'] ?? 0);
$stats_pending        = (int)(mysqli_fetch_assoc(mysqli_query($koneksi_db, "SELECT COUNT(*) as total FROM DISTRIBUSI WHERE status_pengiriman IN ('Pending','Diproses','Gagal','Retur')"))['total'] ?? 0);
$total_mitra          = (int)(mysqli_fetch_assoc(mysqli_query($koneksi_db, "SELECT COUNT(*) as total FROM MITRA"))['total'] ?? 0);

date_default_timezone_set('Asia/Jakarta');
$hour = (int)date('H');
if      ($hour < 11) $greeting = "Selamat Pagi";
elseif  ($hour < 15) $greeting = "Selamat Siang";
elseif  ($hour < 18) $greeting = "Selamat Sore";
else                 $greeting = "Selamat Malam";

// Sapaan personal sesuai user yang sedang login.
$mbg_dashboard_nama = $mbg_current_user['nama'] ?: 'Admin';
$mbg_dashboard_role = $mbg_current_user['role'] ?: 'admin';
$mbg_dashboard_sapa = $mbg_dashboard_role === 'admin' ? 'Admin' : 'Staf';

$current_page = 'dashboard';
$page_title   = 'Dashboard';
include "includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-[1400px] mx-auto">

        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-2">Dashboard Utama</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= $greeting ?>, <?= htmlspecialchars($mbg_dashboard_sapa) ?>! 👋</h2>
                <p class="text-slate-500 mt-1.5 text-sm sm:text-base">Berikut adalah ringkasan operasional distribusi bantuan hari ini.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="distribusi/create.php" 
                   class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    Buat Distribusi Baru
                </a>
                <a href="seed.php" 
                   class="inline-flex items-center justify-center gap-2 bg-white text-slate-600 hover:text-primary-600 hover:bg-primary-50 border border-slate-200 px-5 py-2.5 rounded-xl font-semibold transition-all"
                   title="Isi data dummy ke database untuk demo">
                    <span class="material-symbols-outlined text-[20px]">database</span>
                    Seed Data
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100 hover:border-primary-200 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">groups</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penerima</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($stats_penerima) ?></p>
                <p class="text-xs text-slate-500 mt-1">Total penerima aktif terdaftar</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100 hover:border-emerald-200 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sukses</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($jumlah_selesai) ?></p>
                <p class="text-xs text-slate-500 mt-1">Distribusi telah diterima</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100 hover:border-amber-200 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">hourglass_top</span>
                    </div>
                    <?php if($stats_pending > 0): ?>
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    </span>
                    <?php endif; ?>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($stats_pending) ?></p>
                <p class="text-xs text-slate-500 mt-1">Menunggu tindakan</p>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-soft border border-slate-100 hover:border-purple-200 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">handshake</span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mitra</span>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= number_format($total_mitra) ?></p>
                <p class="text-xs text-slate-500 mt-1">Mitra aktif terdaftar</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-900">Modul Sistem</h3>
                        <p class="text-xs text-slate-500 hidden sm:block">Akses cepat ke seluruh fitur</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php
                        $menus = [
                            ['title' => 'Manajemen Distribusi', 'desc' => 'Atur rute & jadwal pengiriman',  'icon' => 'local_shipping',     'link' => 'distribusi/index.php',   'color' => 'blue'],
                            ['title' => 'Data Penerima',         'desc' => 'Validasi KPM & dokumen',        'icon' => 'contact_page',       'link' => 'penerima/index.php',     'color' => 'indigo'],
                            ['title' => 'Stok Paket Bantuan',    'desc' => 'Katalog & ketersediaan paket',  'icon' => 'inventory_2',        'link' => 'paketbantuan/index.php', 'color' => 'violet'],
                            ['title' => 'Gudang Item',           'desc' => 'Inventaris fisik barang',       'icon' => 'warehouse',          'link' => 'item/index.php',         'color' => 'amber'],
                            ['title' => 'Jejaring Mitra',        'desc' => 'Vendor dan pihak ketiga',       'icon' => 'handshake',          'link' => 'mitra/index.php',        'color' => 'rose'],
                            ['title' => 'Pengaturan Admin',      'desc' => 'Otorisasi dan akses sistem',    'icon' => 'admin_panel_settings','link' => 'user/index.php',         'color' => 'slate'],
                        ];
                        $colorMap = [
                            'blue'   => ['bg' => 'bg-blue-50',    'text' => 'text-blue-600'],
                            'indigo' => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600'],
                            'violet' => ['bg' => 'bg-violet-50',  'text' => 'text-violet-600'],
                            'amber'  => ['bg' => 'bg-amber-50',   'text' => 'text-amber-600'],
                            'rose'   => ['bg' => 'bg-rose-50',    'text' => 'text-rose-600'],
                            'slate'  => ['bg' => 'bg-slate-50',   'text' => 'text-slate-600'],
                        ];
                        foreach ($menus as $menu):
                            $c = $colorMap[$menu['color']];
                        ?>
                        <a href="<?= $menu['link'] ?>" 
                           class="group bg-white p-4 rounded-2xl border border-slate-100 shadow-soft hover:shadow-md hover:border-primary-200 hover:-translate-y-0.5 transition-all duration-200 flex items-start gap-4">
                            <div class="flex items-center justify-center size-12 rounded-xl <?= $c['bg'] ?> <?= $c['text'] ?> group-hover:scale-105 transition-transform shrink-0">
                                <span class="material-symbols-outlined text-2xl"><?= $menu['icon'] ?></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-primary-600 transition-colors truncate"><?= $menu['title'] ?></h4>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed"><?= $menu['desc'] ?></p>
                            </div>
                            <span class="material-symbols-outlined text-slate-300 group-hover:text-primary-500 group-hover:translate-x-0.5 transition-all self-center">arrow_forward</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 lg:sticky lg:top-6">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Aktivitas Terakhir</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Riwayat sistem terbaru</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">history</span>
                    </div>

                    <div class="relative border-l-2 border-slate-100 ml-3 space-y-5">
                        <div class="relative pl-5">
                            <div class="absolute -left-[9px] top-1 size-4 bg-emerald-500 rounded-full border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-semibold text-slate-900">Distribusi Selesai</p>
                            <p class="text-xs text-slate-500 mt-0.5">Paket bantuan telah diterima penerima.</p>
                            <p class="text-[11px] text-slate-400 mt-1">Baru saja</p>
                        </div>
                        <div class="relative pl-5">
                            <div class="absolute -left-[9px] top-1 size-4 bg-blue-500 rounded-full border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-semibold text-slate-900">Data Penerima Baru</p>
                            <p class="text-xs text-slate-500 mt-0.5">Penerima baru telah ditambahkan.</p>
                            <p class="text-[11px] text-slate-400 mt-1">1 jam yang lalu</p>
                        </div>
                        <div class="relative pl-5">
                            <div class="absolute -left-[9px] top-1 size-4 bg-amber-500 rounded-full border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-semibold text-slate-900">Stok Menipis</p>
                            <p class="text-xs text-slate-500 mt-0.5">Beberapa item hampir habis di gudang.</p>
                            <p class="text-[11px] text-slate-400 mt-1">3 jam yang lalu</p>
                        </div>
                        <div class="relative pl-5">
                            <div class="absolute -left-[9px] top-1 size-4 bg-slate-300 rounded-full border-4 border-white shadow-sm"></div>
                            <p class="text-sm font-semibold text-slate-900">Login Sistem</p>
                            <p class="text-xs text-slate-500 mt-0.5">Admin MBG masuk ke sistem.</p>
                            <p class="text-[11px] text-slate-400 mt-1">Hari ini</p>
                        </div>
                    </div>

                    <a href="laporandata/index.php" 
                       class="mt-6 block w-full py-2.5 px-4 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 text-slate-600 text-sm font-semibold text-center rounded-xl transition-colors">
                        Lihat Semua Log Aktivitas
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>