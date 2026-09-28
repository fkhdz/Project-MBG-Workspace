<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!isset($_GET['id'])) die("ID tidak ditemukan");
$id = $_GET['id'];

$q = mysqli_query($koneksi_db, "SELECT * FROM PAKETBANTUAN WHERE paket_id = '$id'");
$data = mysqli_fetch_assoc($q);
if (!$data) die("Data paket tidak ditemukan");

if (isset($_POST['update'])) {
    $nama_paket    = $_POST['nama_paket'];
    $deskripsi     = $_POST['deskripsi'];
    $jenis_bantuan = $_POST['jenis_bantuan'];
    $kalori_total  = $_POST['kalori_total'] ?: 0;
    $berat_total   = $_POST['berat_total'] ?: 0;
    $kadaluarsa    = $_POST['kadaluarsa'] ?: null;
    $kuantitas     = $_POST['kuantitas'];

    $kadaluarsa_sql = $kadaluarsa ? "'$kadaluarsa'" : "NULL";

    $upd = "UPDATE PAKETBANTUAN SET
            nama_paket='$nama_paket',
            deskripsi='$deskripsi',
            jenis_bantuan='$jenis_bantuan',
            kalori_total='$kalori_total',
            berat_total='$berat_total',
            kadaluarsa=$kadaluarsa_sql,
            kuantitas='$kuantitas'
            WHERE paket_id='$id'";

    if (mysqli_query($koneksi_db, $upd)) {
        header("Location: index.php?msg=updated");
        exit;
    } else {
        $error_msg = "Gagal update: " . mysqli_error($koneksi_db);
    }
}

$current_page = 'paket';
$page_title   = 'Edit Paket Bantuan';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-4xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Paket Bantuan</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Edit Data</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Edit Paket Bantuan</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Perbarui informasi paket bantuan.</p>
        </div>

        <?php if (isset($error_msg)): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($error_msg) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 sm:p-8 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Paket <span class="text-red-500">*</span></label>
                <input type="text" name="nama_paket" required value="<?= htmlspecialchars($data['nama_paket']) ?>" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" rows="3" 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all resize-none"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Bantuan</label>
                    <div class="relative">
                        <select name="jenis_bantuan" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                            <?php foreach(['Makanan','Sembako','Kesehatan','Pendidikan','Lainnya'] as $j): ?>
                                <option value="<?= $j ?>" <?= ($data['jenis_bantuan']==$j)?'selected':'' ?>><?= $j ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kuantitas <span class="text-red-500">*</span></label>
                    <input type="number" name="kuantitas" required value="<?= $data['kuantitas'] ?>" min="0" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kalori Total (kkal)</label>
                    <input type="number" name="kalori_total" value="<?= $data['kalori_total'] ?>" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Berat Total (Gram)</label>
                    <input type="number" name="berat_total" value="<?= $data['berat_total'] ?>" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Kadaluarsa</label>
                    <input type="date" name="kadaluarsa" value="<?= $data['kadaluarsa'] ?>" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
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