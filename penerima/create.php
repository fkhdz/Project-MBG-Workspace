<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (isset($_POST['simpan'])) {
    $user_id             = $_POST['user_id'] ?? null;
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

    $q = "INSERT INTO PENERIMA 
          (user_id, nama_lengkap, tanggal_lahir, jenis_kelamin, alamat, kecamatan, kelurahan, kategori_penerima, penghasilan_bulanan, jumlah_tanggungan, status_validasi, tanggal_validasi)
          VALUES 
          ($user_id_sql, '$nama_lengkap', $tgl_lahir_sql, '$jenis_kelamin', '$alamat', '$kecamatan', '$kelurahan', '$kategori_penerima', '$penghasilan_bulanan', '$jumlah_tanggungan', '$status_validasi', $tgl_val_sql)";

    if (mysqli_query($koneksi_db, $q)) {
        header("Location: index.php?msg=created");
        exit;
    } else {
        $error_msg = "Gagal menambahkan: " . mysqli_error($koneksi_db);
    }
}

$current_page = 'penerima';
$page_title   = 'Tambah Penerima';
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
            <span class="text-slate-700 font-semibold">Tambah Baru</span>
        </nav>

        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tambah Penerima Baru</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Lengkapi informasi penerima manfaat bantuan sosial.</p>
        </div>

        <?php if (isset($error_msg)): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm flex items-center gap-3">
            <span class="material-symbols-outlined text-red-500">error</span>
            <p class="text-sm font-semibold"><?= htmlspecialchars($error_msg) ?></p>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">

            <!-- Card: Informasi Pribadi -->
            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">person</span>
                    <h3 class="text-base font-bold text-slate-900">Informasi Pribadi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Akun User Terkait <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <select name="user_id" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="">-- Tidak terkait --</option>
                                <?php
                                $user_q = mysqli_query($koneksi_db, "SELECT * FROM USER ORDER BY nama ASC");
                                while ($u = mysqli_fetch_assoc($user_q)):
                                    echo "<option value='{$u['user_id']}'>" . htmlspecialchars($u['nama']) . " (" . htmlspecialchars($u['role']) . ")</option>";
                                endwhile;
                                ?>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_lengkap" required placeholder="Masukkan nama lengkap" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori Penerima</label>
                        <input type="text" name="kategori_penerima" placeholder="Contoh: Lansia, PKH, Balita" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kelamin</label>
                        <div class="relative">
                            <select name="jenis_kelamin" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Alamat -->
            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">location_on</span>
                    <h3 class="text-base font-bold text-slate-900">Informasi Alamat</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" placeholder="Nama jalan, RT/RW, No Rumah" 
                                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan</label>
                        <input type="text" name="kecamatan" placeholder="Nama kecamatan" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kelurahan/Desa</label>
                        <input type="text" name="kelurahan" placeholder="Nama kelurahan/desa" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                </div>
            </div>

            <!-- Card: Ekonomi & Validasi -->
            <div class="bg-white rounded-2xl shadow-soft border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-600">payments</span>
                    <h3 class="text-base font-bold text-slate-900">Data Ekonomi & Validasi</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Penghasilan Bulanan (Rp)</label>
                        <input type="number" name="penghasilan_bulanan" placeholder="0" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Tanggungan</label>
                        <input type="number" name="jumlah_tanggungan" placeholder="0" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Status Validasi</label>
                        <div class="relative">
                            <select name="status_validasi" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Valid">Valid</option>
                                <option value="Tidak Valid">Tidak Valid</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Validasi</label>
                        <input type="date" name="tanggal_validasi" value="<?= date('Y-m-d') ?>" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all" />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" name="simpan" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Data
                </button>
            </div>

        </form>
    </div>
</main>
</div>
</body>
</html>