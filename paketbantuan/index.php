<?php 
include "../config/koneksi.php"; 

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("<div style='padding:2rem; font-family:sans-serif;'><h3 style='color:red;'>Koneksi Gagal!</h3></div>");
}

$current_page = 'paket';
$page_title   = 'Katalog Paket Bantuan';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-7xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="../index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Stok Paket</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Katalog Paket Bantuan</h2>
                <p class="text-slate-500 mt-1.5 text-sm">Kelola spesifikasi, ketersediaan, dan komposisi item bantuan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="../item/index.php" class="inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">warehouse</span> Gudang Item
                </a>
                <a href="create.php" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[20px]">add</span> Tambah Paket
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
        <div class="mb-6">
            <?php if ($_GET['msg']==='deleted'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Data paket berhasil dihapus.</p>
            </div>
            <?php elseif ($_GET['msg']==='updated'): ?>
            <div class="flex items-center gap-3 p-4 bg-primary-50 text-primary-700 rounded-xl border border-primary-100 shadow-sm">
                <span class="material-symbols-outlined text-primary-500">info</span>
                <p class="text-sm font-semibold">Berhasil! Data paket berhasil diperbarui.</p>
            </div>
            <?php elseif ($_GET['msg']==='created'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Paket baru telah ditambahkan.</p>
            </div>
            <?php elseif ($_GET['msg']==='error'): ?>
            <div class="flex items-center gap-3 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm">
                <span class="material-symbols-outlined text-red-500">error</span>
                <p class="text-sm font-semibold">Gagal! Data tidak dapat dihapus (mungkin masih terhubung dengan tabel lain).</p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">

            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="relative w-full max-w-md">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 shadow-sm" placeholder="Cari nama atau jenis paket..." />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas Paket</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Spesifikasi</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Stok</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php
                    $no = 1;
                    $q = mysqli_query($koneksi_db, "SELECT * FROM PAKETBANTUAN ORDER BY paket_id DESC");
                    if (mysqli_num_rows($q) > 0):
                        while ($row = mysqli_fetch_assoc($q)):
                            $j = strtolower($row['jenis_bantuan']);
                            if (strpos($j,'makanan')!==false || strpos($j,'sembako')!==false) {
                                $badge = 'bg-blue-50 text-blue-700 ring-blue-500/20';
                            } elseif (strpos($j,'kesehatan')!==false || strpos($j,'medis')!==false) {
                                $badge = 'bg-rose-50 text-rose-700 ring-rose-500/20';
                            } elseif (strpos($j,'kebersihan')!==false) {
                                $badge = 'bg-emerald-50 text-emerald-700 ring-emerald-500/20';
                            } elseif (strpos($j,'bayi')!==false || strpos($j,'anak')!==false) {
                                $badge = 'bg-purple-50 text-purple-700 ring-purple-500/20';
                            } else {
                                $badge = 'bg-slate-50 text-slate-700 ring-slate-500/20';
                            }
                    ?>
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400"><?= $no++ ?></td>
                            <td class="px-6 py-4 max-w-[260px]">
                                <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($row['nama_paket']) ?></p>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= htmlspecialchars($row['deskripsi']) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $badge ?>">
                                    <?= htmlspecialchars($row['jenis_bantuan']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 text-xs text-slate-600">
                                    <?php if (!empty($row['berat_total'])): ?>
                                    <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px] text-slate-400">scale</span> <?= htmlspecialchars($row['berat_total']) ?> g</div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['kalori_total'])): ?>
                                    <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px] text-slate-400">local_fire_department</span> <?= htmlspecialchars($row['kalori_total']) ?> Kalori</div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['kadaluarsa'])): ?>
                                    <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px] text-slate-400">event_busy</span> Exp: <?= date('d M Y', strtotime($row['kadaluarsa'])) ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 text-sm font-bold rounded-lg border border-slate-200">
                                    <?= (int)$row['kuantitas'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <a href="komposisi.php?id=<?= $row['paket_id'] ?>" 
                                       class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm" 
                                       title="Atur Komposisi">
                                        <span class="material-symbols-outlined text-[18px]">view_list</span>
                                    </a>
                                    <a href="update.php?id=<?= $row['paket_id'] ?>" 
                                       class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-600 hover:border-primary-200 hover:bg-primary-50 transition-all shadow-sm" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <a href="delete.php?id=<?= $row['paket_id'] ?>" 
                                       onclick="return confirm('Hapus data paket ini?')"
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
                                        <span class="material-symbols-outlined text-3xl">inventory_2</span>
                                    </div>
                                    <p class="text-slate-500 text-sm font-semibold">Belum ada paket bantuan</p>
                                    <a href="create.php" class="mt-4 inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Buat paket pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                <p class="text-xs text-slate-500 font-medium">Total paket dalam sistem</p>
                <p class="text-xs text-slate-400"><?= (int)mysqli_num_rows($q) ?> paket</p>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>