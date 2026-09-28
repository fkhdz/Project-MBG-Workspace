<?php 
include "../config/koneksi.php"; 

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("<div style='padding:2rem; font-family:sans-serif;'><h3 style='color:red;'>Koneksi Gagal!</h3></div>");
}

$current_page = 'mitra';
$page_title   = 'Data Mitra';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-7xl mx-auto w-full">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="../index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Kelola Mitra</span>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Data Mitra Kerjasama</h2>
                <p class="text-slate-500 mt-1.5 text-sm">Kelola data partner, donatur, dan penyalur bantuan.</p>
            </div>
            <a href="create.php" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
                <span class="material-symbols-outlined text-[20px]">add</span> Tambah Mitra
            </a>
        </div>

        <!-- Alerts -->
        <?php if (isset($_GET['msg'])): ?>
        <div class="mb-6">
            <?php if ($_GET['msg']==='deleted'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Data mitra berhasil dihapus.</p>
            </div>
            <?php elseif ($_GET['msg']==='updated'): ?>
            <div class="flex items-center gap-3 p-4 bg-primary-50 text-primary-700 rounded-xl border border-primary-100 shadow-sm">
                <span class="material-symbols-outlined text-primary-500">info</span>
                <p class="text-sm font-semibold">Berhasil! Data mitra berhasil diperbarui.</p>
            </div>
            <?php elseif ($_GET['msg']==='created'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Mitra baru telah ditambahkan.</p>
            </div>
            <?php elseif ($_GET['msg']==='error'): ?>
            <div class="flex items-center gap-3 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm">
                <span class="material-symbols-outlined text-red-500">error</span>
                <p class="text-sm font-semibold">Gagal! Data tidak dapat dihapus karena masih digunakan di tabel lain.</p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">

            <!-- Toolbar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="relative w-full max-w-md">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 shadow-sm" placeholder="Cari nama mitra atau narahubung..." />
                </div>
            </div>

            <!-- Tabel -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas Mitra</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Kontak Person</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Wilayah</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php
                    $no = 1;
                    $q = mysqli_query($koneksi_db, "SELECT * FROM MITRA ORDER BY mitra_id DESC");
                    if (mysqli_num_rows($q) > 0):
                        while ($row = mysqli_fetch_assoc($q)):
                            $j = strtolower($row['jenis_mitra']);
                            if (strpos($j,'pemerintah')!==false || strpos($j,'dinas')!==false) {
                                $badge = 'bg-blue-50 text-blue-700 ring-blue-500/20';
                            } elseif (strpos($j,'swasta')!==false || strpos($j,'pt')!==false || strpos($j,'cv')!==false) {
                                $badge = 'bg-purple-50 text-purple-700 ring-purple-500/20';
                            } elseif (strpos($j,'ngo')!==false || strpos($j,'yayasan')!==false || strpos($j,'sosial')!==false) {
                                $badge = 'bg-emerald-50 text-emerald-700 ring-emerald-500/20';
                            } else {
                                $badge = 'bg-slate-50 text-slate-700 ring-slate-500/20';
                            }
                    ?>
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400"><?= $no++ ?></td>
                            <td class="px-6 py-4 max-w-[260px]">
                                <p class="text-sm font-bold text-slate-900 truncate" title="<?= htmlspecialchars($row['nama_mitra']) ?>"><?= htmlspecialchars($row['nama_mitra']) ?></p>
                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1"><?= htmlspecialchars($row['alamat_mitra']) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $badge ?>">
                                    <?= htmlspecialchars($row['jenis_mitra']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($row['kontak_person'] ?? '-') ?></p>
                                <?php if (!empty($row['no_hp'])): ?>
                                <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">call</span>
                                    <span class="font-mono"><?= htmlspecialchars($row['no_hp']) ?></span>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-sm text-slate-700">
                                    <span class="material-symbols-outlined text-[16px] text-primary-500">location_on</span>
                                    <?= htmlspecialchars($row['wilayah_operasional'] ?? '-') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <a href="update.php?id=<?= $row['mitra_id'] ?>" 
                                       class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-600 hover:border-primary-200 hover:bg-primary-50 transition-all shadow-sm" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <a href="delete.php?id=<?= $row['mitra_id'] ?>" 
                                       onclick="return confirm('Yakin hapus mitra ini?')"
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
                            <td colspan="6" class="text-center py-16">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="size-14 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mb-3">
                                        <span class="material-symbols-outlined text-3xl">handshake</span>
                                    </div>
                                    <p class="text-slate-500 text-sm font-semibold">Belum ada mitra kerjasama</p>
                                    <a href="create.php" class="mt-4 inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Tambah mitra pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                <p class="text-xs text-slate-500 font-medium">Total mitra terdaftar</p>
                <p class="text-xs text-slate-400"><?= (int)mysqli_num_rows($q) ?> data</p>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>