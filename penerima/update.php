<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!$koneksi_db) die("Error: Koneksi database.");

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

if (isset($_POST['update'])) {
    $user_id             = $_POST['user_id'] ?: null;
    $nama_lengkap        = $_POST['nama_lengkap'];
    $tanggal_lahir       = $_POST['tanggal_lahir'] ?: null;
    $jenis_kelamin       = $_POST['jenis_kelamin'];
    $alamat              = $_POST['alamat'];
    $kecamatan           = $_POST['kecamatan'];
    $kelurahan           = $_POST['kelurahan'];
    $kategori_penerima   = $_POST['kategori_penerima'];
    $penghasilan_bulanan = $_POST['penghasilan_bulanan'] ?: 0;
    $jumlah_tanggungan   = $_POST['jumlah_tanggungan'] ?: 0;
    $status_validasi     = $_POST['status_validasi'];
    $tanggal_validasi    = $_POST['tanggal_validasi'] ?: null;

    $user_id_sql = $user_id ? "'$user_id'" : "NULL";
    $tgl_lahir_sql = $tanggal_lahir ? "'$tanggal_lahir'" : "NULL";
    $tgl_val_sql = $tanggal_validasi ? "'$tanggal_validasi'" : "NULL";

    $q = "UPDATE PENERIMA SET
          user_id = $user_id_sql,
          nama_lengkap = '$nama_lengkap',
          tanggal_lahir = $tgl_lahir_sql,
          jenis_kelamin = '$jenis_kelamin',
          alamat = '$alamat',
          kecamatan = '$kecamatan',
          kelurahan = '$kelurahan',
          kategori_penerima = '$kategori_penerima',
          penghasilan_bulanan = '$penghasilan_bulanan',
          jumlah_tanggungan = '$jumlah_tanggungan',
          status_validasi = '$status_validasi',
          tanggal_validasi = $tgl_val_sql
          WHERE penerima_id = $id";

    if (mysqli_query($koneksi_db, $q)) {
        header("Location: index.php?msg=updated");
        exit;
    } else {
        $error_msg = "Gagal update: " . mysqli_error($koneksi_db);
    }
}

$data_q = mysqli_query($koneksi_db, "SELECT * FROM PENERIMA WHERE penerima_id = $id");
$data = mysqli_fetch_assoc($data_q);
if (!$data) die("Data tidak ditemukan.");

$current_page = 'penerima';
$page_title   = 'Edit Penerima';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-4xl mx-auto w-full">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Penerima</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Edit Data</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Edit Data Penerima</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Perbarui informasi detail untuk penerima yang dipilih.</p>
        </div>

        <?php if (isset($error_msg)): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($error_msg) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">

            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">person</span>
                    <h3 class="text-base font-bold text-slate-900">Informasi Pribadi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Akun User Terkait</label>
                        <div class="relative">
                            <select name="user_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="">-- Tidak terkait --</option>
                                <?php
                                $user_q = mysqli_query($koneksi_db, "SELECT * FROM USER ORDER BY nama ASC");
                                while ($u = mysqli_fetch_assoc($user_q)):
                                    $sel = ($u['user_id'] == $data['user_id']) ? 'selected' : '';
                                    echo "<option value='{$u['user_id']}' $sel>" . htmlspecialchars($u['nama']) . " (" . htmlspecialchars($u['role']) . ")</option>";
                                endwhile;
                                ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($data['nama_lengkap']) ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="<?= $data['tanggal_lahir'] ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kelamin</label>
                        <div class="relative">
                            <select name="jenis_kelamin" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="Laki-laki" <?= ($data['jenis_kelamin']=='Laki-laki')?'selected':'' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($data['jenis_kelamin']=='Perempuan')?'selected':'' ?>>Perempuan</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori Penerima</label>
                        <input type="text" name="kategori_penerima" value="<?= htmlspecialchars($data['kategori_penerima']) ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">location_on</span>
                    <h3 class="text-base font-bold text-slate-900">Informasi Alamat</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" 
                                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all resize-none"><?= htmlspecialchars($data['alamat']) ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" value="<?= htmlspecialchars($data['kecamatan']) ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" value="<?= htmlspecialchars($data['kelurahan']) ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">payments</span>
                    <h3 class="text-base font-bold text-slate-900">Data Ekonomi & Validasi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Penghasilan Bulanan (Rp)</label>
                        <input type="number" name="penghasilan_bulanan" value="<?= $data['penghasilan_bulanan'] ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Tanggungan</label>
                        <input type="number" name="jumlah_tanggungan" value="<?= $data['jumlah_tanggungan'] ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Status Validasi</label>
                        <div class="relative">
                            <select name="status_validasi" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="Menunggu" <?= ($data['status_validasi']=='Menunggu')?'selected':'' ?>>Menunggu</option>
                                <option value="Valid" <?= ($data['status_validasi']=='Valid')?'selected':'' ?>>Valid</option>
                                <option value="Tidak Valid" <?= ($data['status_validasi']=='Tidak Valid')?'selected':'' ?>>Tidak Valid</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Validasi</label>
                        <input type="date" name="tanggal_validasi" value="<?= $data['tanggal_validasi'] ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
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