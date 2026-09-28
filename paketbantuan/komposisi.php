<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) die("Error: Koneksi database.");

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$paket_id = $_GET['id'];

$paket_q = mysqli_query($koneksi_db, "SELECT * FROM PAKETBANTUAN WHERE paket_id = '$paket_id'");
$paket = mysqli_fetch_assoc($paket_q);
if (!$paket) { header("Location: index.php"); exit; }

$items_available = mysqli_query($koneksi_db, "SELECT * FROM ITEM ORDER BY nama_item ASC");

$details = mysqli_query($koneksi_db, "
    SELECT dp.detail_id, i.nama_item, i.satuan, dp.jumlah_per_paket, i.stok_gudang
    FROM DETAIL_PAKET dp
    JOIN ITEM i ON dp.item_id = i.item_id
    WHERE dp.paket_id = '$paket_id'
    ORDER BY dp.detail_id DESC
");

$current_page = 'paket';
$page_title   = 'Komposisi Paket';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-6xl mx-auto w-full">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Paket Bantuan</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Atur Komposisi</span>
        </nav>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-8">
            <div>
                <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-1">Komposisi Item</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight"><?= htmlspecialchars($paket['nama_paket']) ?></h2>
                <p class="text-slate-500 mt-1 text-sm">Tentukan item-item yang menyusun paket bantuan.</p>
            </div>
            <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali
            </a>
        </div>

        <!-- Alert -->
        <?php if (isset($_GET['msg_type'])): 
            $msg_type = $_GET['msg_type'];
            $msg = urldecode($_GET['msg']);
            $cls = ($msg_type == 'success') ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-red-50 border-red-100 text-red-700';
            $icon = ($msg_type == 'success') ? 'check_circle' : 'error';
        ?>
        <div class="mb-6 flex items-center gap-3 p-4 <?= $cls ?> rounded-xl border shadow-sm" role="alert">
            <span class="material-symbols-outlined"><?= $icon ?></span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($msg) ?></p>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Tambah -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden lg:sticky lg:top-6">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary-600">add_box</span>
                        <h3 class="text-base font-bold text-slate-900">Tambah Komponen</h3>
                    </div>
                    <form method="POST" action="tambah_komposisi.php?id=<?= $paket_id ?>" class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Item Gudang</label>
                            <div class="relative">
                                <select name="item_id" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                    <option value="">-- Pilih Item --</option>
                                    <?php
                                    mysqli_data_seek($items_available, 0);
                                    while ($i = mysqli_fetch_assoc($items_available)):
                                        echo "<option value='{$i['item_id']}'>" . htmlspecialchars($i['nama_item']) . " (" . htmlspecialchars($i['satuan']) . ")</option>";
                                    endwhile;
                                    ?>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah per Paket</label>
                            <input type="number" name="jumlah" step="0.01" min="0.01" required placeholder="0" 
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                        </div>
                        <button type="submit" name="tambah_item" class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-all shadow-sm hover:shadow-glow">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Tambahkan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Komposisi -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Daftar Komposisi</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Item-item yang menyusun paket</p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-primary-50 text-primary-700 text-xs font-bold rounded-full">
                            <?= mysqli_num_rows($details) ?> item
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Barang</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Kebutuhan</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Stok Gudang</th>
                                    <th class="px-6 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                            <?php if (mysqli_num_rows($details) > 0): 
                                mysqli_data_seek($details, 0);
                                while ($d = mysqli_fetch_assoc($details)):
                                    $stok_status = ($d['stok_gudang'] < $d['jumlah_per_paket']) ? 'text-rose-600 font-bold' : 'text-slate-700';
                            ?>
                                <tr class="hover:bg-slate-50/60 transition-colors group">
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-900"><?= htmlspecialchars($d['nama_item']) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-primary-600"><?= number_format($d['jumlah_per_paket'], 2) ?></span>
                                        <span class="text-xs text-slate-500 ml-1"><?= htmlspecialchars($d['satuan']) ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm <?= $stok_status ?>"><?= number_format($d['stok_gudang']) ?></span>
                                        <span class="text-xs text-slate-500 ml-1"><?= htmlspecialchars($d['satuan']) ?></span>
                                        <?php if ($d['stok_gudang'] < $d['jumlah_per_paket']): ?>
                                            <span class="ml-1 inline-flex items-center gap-0.5 text-rose-600 text-[10px] font-bold uppercase tracking-wider">
                                                <span class="material-symbols-outlined text-[12px]">warning</span>
                                                Kurang
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="update_komposisi.php?id=<?= $paket_id ?>&detail_id=<?= $d['detail_id'] ?>" 
                                               class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-primary-600 hover:border-primary-200 hover:bg-primary-50 transition-all shadow-sm" 
                                               title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                            <a href="delete_komposisi.php?id=<?= $paket_id ?>&detail_id=<?= $d['detail_id'] ?>" 
                                               onclick="return confirm('Hapus barang ini dari komposisi?')"
                                               class="flex items-center justify-center size-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm" 
                                               title="Hapus">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-16">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="size-14 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mb-3">
                                                <span class="material-symbols-outlined text-3xl">inventory_2</span>
                                            </div>
                                            <p class="text-slate-500 text-sm font-semibold">Belum ada komposisi</p>
                                            <p class="text-slate-400 text-xs mt-1">Tambahkan item dari form di samping</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
</body>
</html>