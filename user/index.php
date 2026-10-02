<?php 
include "../config/koneksi.php"; 
require_once "../includes/auth.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("<div style='padding:2rem; font-family:sans-serif;'><h3 style='color:red;'>Koneksi Gagal!</h3></div>");
}

$current_page = 'user';
$page_title   = 'Manajemen Pengguna';

// Modul 'user' hanya untuk admin. require_access() akan render
// halaman Akses Ditolak kalau koordinator/petugas mencoba masuk.
require_access('user');

include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-7xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="../index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Kelola Pengguna</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Manajemen Pengguna</h2>
                <p class="text-slate-500 mt-1.5 text-sm">Atur dan kelola hak akses setiap staf dalam sistem.</p>
            </div>
            <a href="create.php" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
                <span class="material-symbols-outlined text-[20px]">add</span> Tambah Pengguna
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
        <div class="mb-6">
            <?php if ($_GET['msg']==='deleted'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Data pengguna berhasil dihapus.</p>
            </div>
            <?php elseif ($_GET['msg']==='updated'): ?>
            <div class="flex items-center gap-3 p-4 bg-primary-50 text-primary-700 rounded-xl border border-primary-100 shadow-sm">
                <span class="material-symbols-outlined text-primary-500">info</span>
                <p class="text-sm font-semibold">Berhasil! Data pengguna berhasil diperbarui.</p>
            </div>
            <?php elseif ($_GET['msg']==='created'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Pengguna baru telah ditambahkan.</p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">

            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="relative w-full max-w-md">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 shadow-sm" placeholder="Cari nama atau email pengguna..." />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Informasi Pengguna</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Peran</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php
                    $q = mysqli_query($koneksi_db, "SELECT * FROM USER ORDER BY user_id DESC");
                    if (mysqli_num_rows($q) > 0):
                        while ($row = mysqli_fetch_assoc($q)):
                            $status_lower = strtolower($row['status_akun']);
                            if ($status_lower === 'aktif') {
                                $badge = 'bg-emerald-50 text-emerald-700 ring-emerald-500/20';
                            } else {
                                $badge = 'bg-slate-50 text-slate-600 ring-slate-500/20';
                            }
                            $role_lower = strtolower($row['role']);
                            if ($role_lower === 'admin') {
                                $role_badge = 'bg-primary-50 text-primary-700 ring-primary-500/20';
                                $role_icon  = 'admin_panel_settings';
                            } elseif ($role_lower === 'koordinator') {
                                $role_badge = 'bg-indigo-50 text-indigo-700 ring-indigo-500/20';
                                $role_icon  = 'supervisor_account';
                            } elseif ($role_lower === 'petugas') {
                                $role_badge = 'bg-emerald-50 text-emerald-700 ring-emerald-500/20';
                                $role_icon  = 'engineering';
                            } else {
                                $role_badge = 'bg-slate-100 text-slate-700';
                                $role_icon  = 'person';
                            }
                    ?>
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <td class="px-6 py-4 max-w-xs">
                                <div class="flex items-center gap-3">
                                    <div class="size-9 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center text-xs font-bold ring-2 ring-white">
                                        <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($row['nama']) ?></p>
                                        <p class="text-xs text-slate-500 mt-0.5 truncate"><?= htmlspecialchars($row['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $role_badge ?>">
                                    <span class="material-symbols-outlined text-[12px]"><?= $role_icon ?></span>
                                    <?= htmlspecialchars($row['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-700 font-mono"><?= htmlspecialchars($row['no_hp'] ?? '-') ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $badge ?>">
                                    <span class="size-1.5 rounded-full <?= $status_lower==='aktif'?'bg-emerald-500':'bg-slate-400' ?>"></span>
                                    <?= htmlspecialchars($row['status_akun']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <a href="update.php?id=<?= $row['user_id'] ?>" 
                                       class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-600 hover:border-primary-200 hover:bg-primary-50 transition-all shadow-sm" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <a href="delete.php?id=<?= $row['user_id'] ?>" 
                                       onclick="return confirm('Hapus pengguna <?= htmlspecialchars($row['nama']) ?>?')"
                                       class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm" title="Hapus">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <tr>
                            <td colspan="5" class="text-center py-16">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="size-14 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl">person_off</span>
                                    </div>
                                    <p class="text-slate-500 text-sm font-semibold">Belum ada pengguna</p>
                                    <a href="create.php" class="mt-4 inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Tambah pengguna pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                <p class="text-xs text-slate-500 font-medium">Total pengguna terdaftar</p>
                <p class="text-xs text-slate-400"><?= (int)mysqli_num_rows($q) ?> data</p>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>