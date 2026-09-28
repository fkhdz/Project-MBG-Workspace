<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) {
    die("Error: Variabel koneksi database tidak ditemukan.");
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];
$q  = mysqli_query($koneksi_db, "SELECT * FROM DISTRIBUSI WHERE distribusi_id='$id'");
$row = mysqli_fetch_assoc($q);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paket_id       = $_POST['paket_id'];
    $penerima_id    = $_POST['penerima_id'];
    $mitra_id       = $_POST['mitra_id'];
    $tanggal_kirim  = $_POST['tanggal_kirim'];
    $tanggal_terima = $_POST['tanggal_terima'] ?: null;
    $lokasi         = $_POST['lokasi_pengiriman'];
    $status         = $_POST['status_pengiriman'];
    $catatan        = $_POST['catatan_petugas'] ?? '';

    $tanggal_terima_sql = $tanggal_terima ? "'$tanggal_terima'" : "NULL";

    $upd = "UPDATE DISTRIBUSI SET
                paket_id        = '$paket_id',
                penerima_id     = '$penerima_id',
                mitra_id        = '$mitra_id',
                tanggal_kirim   = '$tanggal_kirim',
                tanggal_terima  = $tanggal_terima_sql,
                lokasi_pengiriman = '$lokasi',
                status_pengiriman = '$status',
                catatan_petugas = '$catatan'
            WHERE distribusi_id = '$id'";

    if (mysqli_query($koneksi_db, $upd)) {
        header("Location: index.php?msg=updated");
        exit;
    } else {
        $error_msg = "Gagal update: " . mysqli_error($koneksi_db);
    }
}

$current_page = 'distribusi';
$page_title   = 'Edit Distribusi';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-4xl mx-auto w-full">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Distribusi</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Edit Distribusi #<?= $row['distribusi_id'] ?></span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Edit Data Distribusi</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Perbarui informasi distribusi bantuan.</p>
        </div>

        <!-- Error -->
        <?php if (isset($error_msg)): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($error_msg) ?></p>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-soft border border-slate-100 p-6 sm:p-8">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="space-y-6">

                    <!-- Paket -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Paket Bantuan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="paket_id" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="">-- Pilih Paket --</option>
                                <?php
                                $paket_q = mysqli_query($koneksi_db, "SELECT * FROM PAKETBANTUAN ORDER BY nama_paket ASC");
                                while ($p = mysqli_fetch_assoc($paket_q)):
                                    $sel = ($p['paket_id'] == $row['paket_id']) ? 'selected' : '';
                                    echo "<option value='{$p['paket_id']}' $sel>" . htmlspecialchars($p['nama_paket']) . " - " . htmlspecialchars($p['jenis_bantuan']) . "</option>";
                                endwhile;
                                ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Penerima & Mitra -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Penerima <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="penerima_id" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                    <option value="">-- Pilih Penerima --</option>
                                    <?php
                                    $penerima_q = mysqli_query($koneksi_db, "SELECT * FROM PENERIMA ORDER BY nama_lengkap ASC");
                                    while ($r = mysqli_fetch_assoc($penerima_q)):
                                        $sel = ($r['penerima_id'] == $row['penerima_id']) ? 'selected' : '';
                                        echo "<option value='{$r['penerima_id']}' $sel>" . htmlspecialchars($r['nama_lengkap']) . "</option>";
                                    endwhile;
                                    ?>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Mitra Penyalur <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="mitra_id" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                    <option value="">-- Pilih Mitra --</option>
                                    <?php
                                    $mitra_q = mysqli_query($koneksi_db, "SELECT * FROM MITRA ORDER BY nama_mitra ASC");
                                    while ($m = mysqli_fetch_assoc($mitra_q)):
                                        $sel = ($m['mitra_id'] == $row['mitra_id']) ? 'selected' : '';
                                        echo "<option value='{$m['mitra_id']}' $sel>" . htmlspecialchars($m['nama_mitra']) . "</option>";
                                    endwhile;
                                    ?>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Kirim <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_kirim" required value="<?= $row['tanggal_kirim'] ?>"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Terima <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="date" name="tanggal_terima" value="<?= $row['tanggal_terima'] ?>"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Lokasi Pengiriman <span class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_pengiriman" required value="<?= htmlspecialchars($row['lokasi_pengiriman']) ?>"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Status Pengiriman <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="status_pengiriman" required class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <?php
                                $status_opsi = ["Dikemas","Pending","Proses","Dikirim","Terkirim","Selesai","Gagal"];
                                foreach ($status_opsi as $s):
                                    $sel = ($row['status_pengiriman'] == $s) ? 'selected' : '';
                                    echo "<option value='$s' $sel>$s</option>";
                                endforeach;
                                ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan Petugas <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="catatan_petugas" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all resize-none"><?= htmlspecialchars($row['catatan_petugas']) ?></textarea>
                    </div>

                </div>

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-glow">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</div>
</body>
</html>