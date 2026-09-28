<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!isset($_GET['id']) || !isset($_GET['detail_id'])) {
    header("Location: index.php");
    exit;
}

$paket_id   = $_GET['id'];
$detail_id  = $_GET['detail_id'];

$q = mysqli_query($koneksi_db, "
    SELECT dp.*, p.nama_paket, i.nama_item, i.satuan 
    FROM DETAIL_PAKET dp
    JOIN PAKETBANTUAN p ON dp.paket_id = p.paket_id
    JOIN ITEM i ON dp.item_id = i.item_id
    WHERE dp.detail_id = '$detail_id'
");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    header("Location: komposisi.php?id=$paket_id");
    exit;
}

if (isset($_POST['update'])) {
    $jumlah = $_POST['jumlah'];
    if (mysqli_query($koneksi_db, "UPDATE DETAIL_PAKET SET jumlah_per_paket='$jumlah' WHERE detail_id='$detail_id'")) {
        header("Location: komposisi.php?id=$paket_id&msg_type=success&msg=" . urlencode("Jumlah berhasil diperbarui"));
        exit;
    } else {
        $error_msg = "Gagal update data";
    }
}

$current_page = 'paket';
$page_title   = 'Edit Komposisi';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-3xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Paket Bantuan</a>
            <span class="text-slate-300">/</span>
            <a href="komposisi.php?id=<?= $paket_id ?>" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Komposisi</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Edit</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Edit Komposisi Paket</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Ubah jumlah item yang dibutuhkan untuk paket ini.</p>
        </div>

        <?php if (isset($error_msg)): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($error_msg) ?></p>
        </div>
        <?php endif; ?>

        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Nama Paket</p>
                <p class="text-base font-bold text-slate-900"><?= htmlspecialchars($data['nama_paket']) ?></p>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Item yang Diedit</p>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">deployed_code</span>
                    <p class="text-base font-bold text-slate-900">
                        <?= htmlspecialchars($data['nama_item']) ?> 
                        <span class="text-xs font-normal text-slate-500">(<?= htmlspecialchars($data['satuan']) ?>)</span>
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 sm:p-8 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Baru per Paket <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" step="0.01" min="0.01" required value="<?= htmlspecialchars($data['jumlah_per_paket']) ?>" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                <p class="text-xs text-slate-500 mt-1.5">Satuan pengukuran dalam <?= htmlspecialchars($data['satuan']) ?>.</p>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="komposisi.php?id=<?= $paket_id ?>" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" name="update" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>
</div>
</body>
</html>