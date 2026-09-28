<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (isset($_POST['simpan'])) {
    $user_id             = $_POST['user_id'] ?: null;
    $nama_mitra          = $_POST['nama_mitra'];
    $jenis_mitra         = $_POST['jenis_mitra'];
    $kontak_person       = $_POST['kontak_person'];
    $no_hp               = $_POST['no_hp'];
    $alamat_mitra        = $_POST['alamat_mitra'];
    $wilayah_operasional = $_POST['wilayah_operasional'];

    $user_id_sql = $user_id ? "'$user_id'" : "NULL";

    $q = "INSERT INTO MITRA (user_id, nama_mitra, jenis_mitra, kontak_person, no_hp, alamat_mitra, wilayah_operasional)
          VALUES ($user_id_sql, '$nama_mitra', '$jenis_mitra', '$kontak_person', '$no_hp', '$alamat_mitra', '$wilayah_operasional')";

    if (mysqli_query($koneksi_db, $q)) {
        header("Location: index.php?msg=created");
        exit;
    } else {
        $error_msg = "Gagal menambahkan: " . mysqli_error($koneksi_db);
    }
}

$current_page = 'mitra';
$page_title   = 'Tambah Mitra';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-4xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Mitra</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Tambah Baru</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tambah Mitra Baru</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Daftarkan mitra atau partner baru untuk program bantuan.</p>
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
                    <span class="material-symbols-outlined text-primary-600">handshake</span>
                    <h3 class="text-base font-bold text-slate-900">Detail Mitra</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Akun User Penanggung Jawab <span class="text-slate-400 font-normal">(Opsional)</span></label>
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
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Mitra <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mitra" required placeholder="Nama Perusahaan/Instansi" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Mitra</label>
                        <input type="text" name="jenis_mitra" placeholder="Contoh: Supplier, NGO, Pemerintah" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kontak Person (PIC)</label>
                        <input type="text" name="kontak_person" placeholder="Nama PIC" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">No HP</label>
                        <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Mitra</label>
                        <textarea name="alamat_mitra" rows="3" placeholder="Alamat lengkap mitra" 
                                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 resize-none"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Wilayah Operasional</label>
                        <input type="text" name="wilayah_operasional" placeholder="Contoh: Jawa Tengah, Nasional" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" name="simpan" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Mitra
                </button>
            </div>
        </form>
    </div>
</main>
</div>
</body>
</html>