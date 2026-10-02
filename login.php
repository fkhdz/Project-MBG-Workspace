<?php
/**
 * ============================================================================
 *  HALAMAN LOGIN - MBG Workspace
 * ============================================================================
 *  Halaman login dengan 2 peran:
 *    - admin     -> email: admin@gmail.com    password: 1234
 *    - karyawan  -> email: karyawan@gmail.com password: 1234
 *
 *  Catatan:
 *    - Kredensial saat ini di-handle oleh helper statis di includes/auth.php.
 *    - Pada halaman sukses, user akan diarahkan ke index.php (dashboard).
 *    - Setiap halaman lain yang ingin diproteksi cukup memanggil
 *      require_login() dari includes/auth.php.
 * ============================================================================
 */
require_once 'includes/auth.php';

// Tandai halaman ini sebagai publik agar includes/header.php
// tidak melakukan redirect ke login.php (loop).
$public_page = true;

// Kalau sudah login, langsung lempar ke dashboard.
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error      = '';
$email_val  = '';
$page_title = 'Login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_val = trim($_POST['email'] ?? '');
    $password   = (string)($_POST['password'] ?? '');

    if ($email_val === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $user = mbg_authenticate($email_val, $password);
        if ($user) {
            // Regenerasi session ID untuk mencegah session fixation.
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['email']; // sementara pakai email sbg id statis
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['login_at'] = time();

            header('Location: index.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= htmlspecialchars($page_title) ?> - MBG Workspace</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        primary: {
                            50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',
                            500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e',
                        },
                        surface: '#f8fafc',
                    },
                    boxShadow: {
                        'soft': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.03)',
                        'glow': '0 4px 14px -2px rgba(14, 165, 233, 0.25)',
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }
        body { font-feature-settings: "cv11", "ss01"; }
    </style>
</head>
<body class="font-sans bg-surface text-slate-800 antialiased min-h-screen">

<div class="min-h-screen flex flex-col lg:flex-row">

    <!-- ============ PANEL KIRI: BRANDING ============ -->
    <aside class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-primary-500 text-white p-12 flex-col justify-between">
        <!-- Decorative blobs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[28rem] h-[28rem] bg-primary-300/30 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <div class="size-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center ring-1 ring-white/20">
                    <span class="material-symbols-outlined text-3xl">lunch_dining</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">MBG Workspace</h1>
                    <p class="text-primary-100 text-sm">Logistics & Distribution System</p>
                </div>
            </div>

            <h2 class="text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight">
                Kelola distribusi<br>
                <span class="text-primary-100">Makan Bergizi Gratis</span><br>
                jadi lebih mudah.
            </h2>
            <p class="mt-6 text-primary-50 text-base max-w-md leading-relaxed">
                Sistem informasi terpadu untuk manajemen mitra, penerima bantuan,
                inventaris gudang, hingga pelacakan distribusi dan laporan analitik
                Program MBG Nasional.
            </p>

            <div class="mt-10 grid grid-cols-3 gap-4 max-w-md">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 ring-1 ring-white/15">
                    <p class="text-2xl font-bold">7+</p>
                    <p class="text-[11px] uppercase tracking-wider text-primary-100 mt-1">Modul CRUD</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 ring-1 ring-white/15">
                    <p class="text-2xl font-bold">3NF</p>
                    <p class="text-[11px] uppercase tracking-wider text-primary-100 mt-1">Schema</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 ring-1 ring-white/15">
                    <p class="text-2xl font-bold">2</p>
                    <p class="text-[11px] uppercase tracking-wider text-primary-100 mt-1">Role Akses</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 text-xs text-primary-100/80">
            &copy; <?= date('Y') ?> MBG Workspace. All rights reserved.
        </div>
    </aside>

    <!-- ============ PANEL KANAN: FORM LOGIN ============ -->
    <main class="flex-1 flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-md">

            <!-- Brand mobile -->
            <div class="lg:hidden flex items-center gap-3 mb-8 justify-center">
                <div class="size-11 rounded-xl bg-primary-600 text-white flex items-center justify-center shadow-glow">
                    <span class="material-symbols-outlined text-2xl">lunch_dining</span>
                </div>
                <h1 class="text-lg font-bold text-slate-900">MBG Workspace</h1>
            </div>

            <div class="mb-8">
                <p class="text-xs font-bold text-primary-600 uppercase tracking-widest mb-2">Selamat Datang Kembali</p>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Masuk ke akun Anda</h2>
                <p class="text-slate-500 mt-2 text-sm">Gunakan kredensial yang telah diberikan untuk melanjutkan ke dashboard.</p>
            </div>

            <!-- Alert Error -->
            <?php if ($error !== ''): ?>
                <div class="mb-5 flex items-start gap-3 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 shadow-sm" role="alert">
                    <span class="material-symbols-outlined text-red-500 mt-0.5">error</span>
                    <div>
                        <p class="text-sm font-semibold">Login gagal</p>
                        <p class="text-xs text-red-600 mt-0.5"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="space-y-5" novalidate>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">mail</span>
                        <input id="email" name="email" type="email" required autocomplete="username"
                               value="<?= htmlspecialchars($email_val) ?>"
                               placeholder="nama@mbg.id"
                               class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 shadow-sm" />
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock</span>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-11 pr-12 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder:text-slate-400 shadow-sm" />
                        <button type="button" id="togglePass"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                aria-label="Tampilkan password">
                            <span class="material-symbols-outlined text-[20px]" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2 text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember"
                               class="size-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="font-medium">Ingat saya</span>
                    </label>
                    <a href="#" class="text-primary-600 hover:text-primary-700 font-semibold">Lupa password?</a>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-3 rounded-xl font-semibold transition-all shadow-sm hover:shadow-glow">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                    Masuk
                </button>
            </form>

            <!-- Info kredensial default -->
            <div class="mt-8 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-primary-600 text-[18px]">info</span>
                    <h3 class="font-bold text-slate-900 text-sm">Akun Demo</h3>
                </div>
                <div class="space-y-2 text-xs text-slate-600 font-mono">
                    <div class="flex items-center justify-between gap-2 bg-white px-3 py-2 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-700">Admin</span>
                        <span>admin@gmail.com / 1234</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 bg-white px-3 py-2 rounded-lg border border-slate-200">
                        <span class="font-bold text-slate-700">Karyawan</span>
                        <span>karyawan@gmail.com / 1234</span>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-8">
                &copy; <?= date('Y') ?> MBG Workspace
            </p>
        </div>
    </main>
</div>

<script>
    // Toggle show/hide password
    (function () {
        const btn = document.getElementById('togglePass');
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (!btn || !input || !icon) return;
        btn.addEventListener('click', function () {
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            icon.textContent = showing ? 'visibility' : 'visibility_off';
        });
    })();
</script>
</body>
</html>
