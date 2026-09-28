<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (isset($_POST['simpan'])) {
    $nama   = $_POST['nama_item'];
    $satuan = $_POST['satuan'];
    $stok   = $_POST['stok_gudang'];

    $q = "INSERT INTO ITEM (nama_item, satuan, stok_gudang) VALUES ('$nama', '$satuan', '$stok')";

    if (mysqli_query($koneksi_db, $q)) {
        header("Location: index.php?msg=created");
        exit;
    } else {
        $error_msg = "Gagal menambah item: " . mysqli_error($koneksi_db);
    }
}

$current_page = 'item';
$page_title   = 'Tambah Item';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-3xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Gudang Item</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Tambah Baru</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tambah Item Baru</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Tambahkan data barang mentah atau satuan ke inventaris gudang.</p>
        </div>

        <?php if (isset($error_msg)): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($error_msg) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 sm:p-8 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Item <span class="text-red-500">*</span></label>
                <input type="text" name="nama_item" required placeholder="Contoh: Beras Premium, Minyak Goreng" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="satuan" required placeholder="Contoh: Kg, Liter, Pcs" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Stok Awal <span class="text-red-500">*</span></label>
                    <input type="number" name="stok_gudang" required placeholder="0" min="0" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" name="simpan" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Item
                </button>
            </div>
        </form>
    </div>
</main>
</div>
</body>
</html>