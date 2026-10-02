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

        /* Animasi float untuk ikon dekoratif */
        @keyframes mbg-float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-14px) rotate(2deg); }
        }
        @keyframes mbg-float-slow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50%      { transform: translateY(-20px) rotate(-3deg); }
        }
        .mbg-float-1 { animation: mbg-float     9s ease-in-out infinite; }
        .mbg-float-2 { animation: mbg-float-slow 11s ease-in-out infinite; }
        .mbg-float-3 { animation: mbg-float     13s ease-in-out infinite; }
        .mbg-float-4 { animation: mbg-float-slow 10s ease-in-out infinite; }

        /* Slow drifting untuk orbs */
        @keyframes mbg-drift {
            0%, 100% { transform: translate(0, 0); }
            50%      { transform: translate(20px, -20px); }
        }
        .mbg-drift { animation: mbg-drift 14s ease-in-out infinite; }
    </style>
</head>
<body class="font-sans bg-surface text-slate-800 antialiased min-h-screen">

<div class="min-h-screen flex flex-col lg:flex-row">

    <!-- ============ PANEL KIRI: BRANDING ============ -->
    <aside class="hidden lg:flex lg:w-1/2 relative overflow-hidden text-white p-12 flex-col justify-between"
           style="background:
                radial-gradient(circle at 20% 0%, rgba(56,189,248,0.45), transparent 55%),
                radial-gradient(circle at 85% 100%, rgba(2,132,199,0.55), transparent 60%),
                linear-gradient(135deg, #0c4a6e 0%, #0369a1 45%, #0284c7 100%);">

        <!-- Pattern grid halus -->
        <div class="absolute inset-0 opacity-[0.07]"
             style="background-image:
                linear-gradient(rgba(255,255,255,.6) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.6) 1px, transparent 1px);
                background-size: 32px 32px;"></div>

        <!-- Glow orb atas-kiri -->
        <div class="mbg-drift absolute -top-32 -left-32 w-[28rem] h-[28rem] bg-sky-300/30 rounded-full blur-3xl"></div>
        <!-- Glow orb bawah-kanan -->
        <div class="mbg-drift absolute -bottom-24 -right-24 w-[24rem] h-[24rem] bg-cyan-400/25 rounded-full blur-3xl" style="animation-delay: -4s;"></div>
        <!-- Glow orb tengah (halus) -->
        <div class="mbg-drift absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[32rem] h-[32rem] bg-white/5 rounded-full blur-3xl" style="animation-delay: -7s;"></div>

        <!-- Pattern ikon MBG melayang samar -->
        <div class="absolute inset-0 pointer-events-none select-none overflow-hidden" aria-hidden="true">
            <span class="material-symbols-outlined mbg-float-1 absolute text-white/[0.06]" style="top: 8%; left: 6%; font-size: 80px;">restaurant</span>
            <span class="material-symbols-outlined mbg-float-2 absolute text-white/[0.06]" style="top: 22%; right: 8%; font-size: 100px;">local_shipping</span>
            <span class="material-symbols-outlined mbg-float-3 absolute text-white/[0.06]" style="bottom: 28%; left: 10%; font-size: 90px;">inventory_2</span>
            <span class="material-symbols-outlined mbg-float-4 absolute text-white/[0.06]" style="bottom: 12%; right: 14%; font-size: 110px;">nutrition</span>
            <span class="material-symbols-outlined mbg-float-2 absolute text-white/[0.05]" style="top: 48%; left: 38%; font-size: 60px;">rice_bowl</span>
            <span class="material-symbols-outlined mbg-float-1 absolute text-white/[0.05]" style="top: 62%; right: 32%; font-size: 70px;">groups</span>
            <span class="material-symbols-outlined mbg-float-3 absolute text-white/[0.05]" style="top: 5%; left: 48%; font-size: 50px;">handshake</span>
            <span class="material-symbols-outlined mbg-float-4 absolute text-white/[0.05]" style="bottom: 45%; right: 5%; font-size: 55px;">analytics</span>
        </div>

        <!-- Garis dekoratif -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

        <!-- Konten (z-10 supaya di atas semua dekorasi) -->
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <div class="relative size-14 rounded-2xl bg-white/15 backdrop-blur-xl flex items-center justify-center ring-1 ring-white/25 shadow-lg shadow-black/10">
                    <span class="material-symbols-outlined text-3xl">lunch_dining</span>
                    <span class="absolute -top-1 -right-1 size-3 rounded-full bg-emerald-400 ring-2 ring-primary-700 animate-pulse"></span>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">MBG Workspace</h1>
                    <p class="text-sky-100 text-sm font-medium">Logistics & Distribution System</p>
                </div>
            </div>

            <h2 class="text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight">
                Kelola distribusi<br>
                <span class="bg-gradient-to-r from-sky-200 via-white to-sky-100 bg-clip-text text-transparent">Makan Bergizi Gratis</span><br>
                jadi lebih mudah.
            </h2>
            <p class="mt-6 text-sky-50/90 text-base max-w-md leading-relaxed">
                Sistem informasi terpadu untuk manajemen mitra, penerima bantuan,
                inventaris gudang, hingga pelacakan distribusi dan laporan analitik
                Program MBG Nasional.
            </p>

            <!-- Stats cards dengan ikon -->
            <div class="mt-10 grid grid-cols-3 gap-4 max-w-md">
                <div class="group bg-white/10 hover:bg-white/15 backdrop-blur-xl rounded-xl p-4 ring-1 ring-white/20 transition-all hover:-translate-y-0.5">
                    <div class="size-8 rounded-lg bg-sky-400/20 flex items-center justify-center mb-2 ring-1 ring-sky-300/30">
                        <span class="material-symbols-outlined text-sky-100 text-[18px]">dashboard_customize</span>
                    </div>
                    <p class="text-2xl font-extrabold">7+</p>
                    <p class="text-[10px] uppercase tracking-wider text-sky-100/80 mt-0.5 font-semibold">Modul CRUD</p>
                </div>
                <div class="group bg-white/10 hover:bg-white/15 backdrop-blur-xl rounded-xl p-4 ring-1 ring-white/20 transition-all hover:-translate-y-0.5">
                    <div class="size-8 rounded-lg bg-sky-400/20 flex items-center justify-center mb-2 ring-1 ring-sky-300/30">
                        <span class="material-symbols-outlined text-sky-100 text-[18px]">database</span>
                    </div>
                    <p class="text-2xl font-extrabold">3NF</p>
                    <p class="text-[10px] uppercase tracking-wider text-sky-100/80 mt-0.5 font-semibold">Schema</p>
                </div>
                <div class="group bg-white/10 hover:bg-white/15 backdrop-blur-xl rounded-xl p-4 ring-1 ring-white/20 transition-all hover:-translate-y-0.5">
                    <div class="size-8 rounded-lg bg-sky-400/20 flex items-center justify-center mb-2 ring-1 ring-sky-300/30">
                        <span class="material-symbols-outlined text-sky-100 text-[18px]">verified_user</span>
                    </div>
                    <p class="text-2xl font-extrabold">2</p>
                    <p class="text-[10px] uppercase tracking-wider text-sky-100/80 mt-0.5 font-semibold">Role Akses</p>
                </div>
            </div>

            <!-- Quote / tagline kecil -->
            <div class="mt-8 flex items-center gap-2 text-sky-100/70 text-xs">
                <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem online &mdash; siap melayani</span>
            </div>
        </div>

        <div class="relative z-10 flex items-center justify-between text-xs text-sky-100/70">
            <span>&copy; <?= date('Y') ?> MBG Workspace. All rights reserved.</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur ring-1 ring-white/15">
                <span class="material-symbols-outlined text-[14px]">bolt</span>
                v1.0
            </span>
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
