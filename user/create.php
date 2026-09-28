<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (isset($_POST['simpan'])) {
    $nama          = $_POST['nama'];
    $email         = $_POST['email'];
    $password      = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role          = $_POST['role'];
    $no_hp         = $_POST['no_hp'] ?: null;
    $tanggal_daftar= date('Y-m-d');
    $status_akun   = $_POST['status_akun'];

    $no_hp_sql = $no_hp ? "'$no_hp'" : "NULL";

    $q = "INSERT INTO USER (nama, email, password, role, no_hp, tanggal_daftar, last_login, status_akun)
          VALUES ('$nama', '$email', '$password', '$role', $no_hp_sql, '$tanggal_daftar', NULL, '$status_akun')";

    if (mysqli_query($koneksi_db, $q)) {
        header("Location: index.php?msg=created");
        exit;
    } else {
        $error_msg = "Gagal menambahkan: " . mysqli_error($koneksi_db);
    }
}

$current_page = 'user';
$page_title   = 'Tambah Pengguna';
include "../includes/header.php";
?>
<div class="flex min-h-screen w-full">
<?php include "../sidebar.php"; ?>

<main class="flex-1 min-w-0">
    <div class="px-5 sm:px-8 lg:px-10 py-6 lg:py-8 max-w-4xl mx-auto w-full">

        <nav class="flex items-center gap-2 text-sm mb-2">
            <a href="index.php" class="text-slate-500 hover:text-primary-600 font-medium transition-colors">Pengguna</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold">Tambah Baru</span>
        </nav>

        <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tambah Pengguna Baru</h2>
            <p class="text-slate-500 mt-1.5 text-sm">Daftarkan akun pengguna baru untuk akses sistem.</p>
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
                    <h3 class="text-base font-bold text-slate-900">Informasi Akun</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Masukkan nama lengkap" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required placeholder="email@contoh.com" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required placeholder="Masukkan password aman" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                        <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            Password akan di-hash otomatis
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                        <div class="relative">
                            <select name="role" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="user">User</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">No HP</label>
                        <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" 
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Status Akun</label>
                        <div class="relative">
                            <select name="status_akun" class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all appearance-none cursor-pointer">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="index.php" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                    Batal
                </a>
                <button type="submit" name="simpan" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-primary-600 rounded-xl hover:bg-primary-700 transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</main>
</div>
</body>
</html>