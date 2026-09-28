<?php 
include "../config/koneksi.php"; 

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("<div style='padding:2rem; font-family:sans-serif;'><h3 style='color:red;'>Koneksi Gagal!</h3></div>");
}

$current_page = 'item';
$page_title   = 'Gudang Item';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-7xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="../index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Dashboard</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Gudang Item</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Manajemen Gudang Item</h2>
                <p class="text-slate-500 mt-1.5 text-sm">Kelola inventaris barang mentah dan ketersediaan stok fisik.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="../paketbantuan/index.php" class="inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span> Paket Bantuan
                </a>
                <a href="create.php" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[20px]">add</span> Tambah Item
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
        <div class="mb-6">
            <?php if ($_GET['msg']==='deleted'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Data item berhasil dihapus.</p>
            </div>
            <?php elseif ($_GET['msg']==='updated'): ?>
            <div class="flex items-center gap-3 p-4 bg-primary-50 text-primary-700 rounded-xl border border-primary-100 shadow-sm">
                <span class="material-symbols-outlined text-primary-500">info</span>
                <p class="text-sm font-semibold">Berhasil! Data stok item berhasil diperbarui.</p>
            </div>
            <?php elseif ($_GET['msg']==='created'): ?>
            <div class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100 shadow-sm">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold">Berhasil! Item baru telah ditambahkan.</p>
            </div>
            <?php elseif ($_GET['msg']==='error'): ?>
            <div class="flex items-center gap-3 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm">
                <span class="material-symbols-outlined text-red-500">error</span>
                <p class="text-sm font-semibold">Gagal! Item tidak dapat dihapus karena masih digunakan pada paket bantuan.</p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">

            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="relative w-full max-w-md">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 shadow-sm" placeholder="Cari nama barang atau satuan..." />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Detail Item</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Stok Gudang</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php
                    $no = 1;
                    $q = mysqli_query($koneksi_db, "SELECT * FROM ITEM ORDER BY item_id DESC");
                    if (mysqli_num_rows($q) > 0):
                        while ($row = mysqli_fetch_assoc($q)):
                            $stok = (int)$row['stok_gudang'];
                            if ($stok == 0) {
                                $badge = 'bg-rose-50 text-rose-700 ring-rose-500/20';
                                $label = 'Habis';    $icon = 'block';
                            } elseif ($stok < 50) {
                                $badge = 'bg-amber-50 text-amber-700 ring-amber-500/20';
                                $label = 'Menipis';  $icon = 'warning';
                            } else {
                                $badge = 'bg-emerald-50 text-emerald-700 ring-emerald-500/20';
                                $label = 'Aman';     $icon = 'check_circle';
                            }
                    ?>
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400"><?= $no++ ?></td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-900"><?= htmlspecialchars($row['nama_item']) ?></p>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">category</span>
                                    Satuan: <?= htmlspecialchars($row['satuan']) ?>
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="text-2xl font-black <?= $stok==0?'text-rose-600':'text-slate-800' ?>">
                                    <?= number_format($stok) ?>
                                </p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-0.5"><?= htmlspecialchars($row['satuan']) ?></p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider ring-1 ring-inset <?= $badge ?>">
                                    <span class="material-symbols-outlined text-[13px]"><?= $icon ?></span>
                                    <?= $label ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <a href="update.php?id=<?= $row['item_id'] ?>" 
                                       class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-600 hover:border-primary-200 hover:bg-primary-50 transition-all shadow-sm" title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <a href="delete.php?id=<?= $row['item_id'] ?>" 
                                       onclick="return confirm('Hapus item ini dari gudang?')"
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
                                        <span class="material-symbols-outlined text-3xl">category</span>
                                    </div>
                                    <p class="text-slate-500 text-sm font-semibold">Gudang kosong</p>
                                    <p class="text-slate-400 text-xs mt-1">Belum ada item yang ditambahkan</p>
                                    <a href="create.php" class="mt-4 inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 text-sm font-semibold">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                        Tambah item pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
                <p class="text-xs text-slate-500 font-medium">Total inventaris gudang</p>
                <p class="text-xs text-slate-400"><?= (int)mysqli_num_rows($q) ?> item</p>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>