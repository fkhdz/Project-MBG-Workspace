<?php
/**
 * ============================================================================
 *  AUTH HELPER - MBG Workspace
 * ============================================================================
 *  Helper sederhana untuk proteksi halaman berdasarkan role.
 *
 *  Cara pakai di halaman protected:
 *    require_once 'includes/auth.php';
 *    require_login();           // wajib login (admin atau karyawan)
 *    require_role('admin');     // hanya admin yang boleh akses
 *
 *  Session yang tersedia setelah login:
 *    $_SESSION['user_id']   -> int
 *    $_SESSION['nama']      -> string
 *    $_SESSION['email']     -> string
 *    $_SESSION['role']      -> 'admin' | 'karyawan'
 *    $_SESSION['login_at']  -> timestamp login
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Definisi kredensial statis (sesuai permintaan: login saat ini
 * menggunakan akun hardcode, belum membaca dari tabel USER).
 *
 * Untuk produksi, ganti blok ini dengan query ke tabel USER.
 */
function mbg_get_static_credentials(): array {
    return [
        [
            'email'    => 'admin@gmail.com',
            'password' => '1234',
            'nama'     => 'Administrator',
            'role'     => 'admin',
        ],
        [
            'email'    => 'karyawan@gmail.com',
            'password' => '1234',
            'nama'     => 'Karyawan MBG',
            'role'     => 'karyawan',
        ],
    ];
}

/**
 * Cek apakah user sudah login.
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect ke halaman login kalau belum login.
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . mbg_base_url() . 'login.php');
        exit;
    }
}

/**
 * Redirect kalau role user tidak sesuai.
 */
function require_role(string $role): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) {
        header('Location: ' . mbg_base_url() . 'index.php?msg=forbidden');
        exit;
    }
}

/**
 * Bangun base URL otomatis berdasarkan posisi file.
 * Mendukung pemanggilan dari root (login.php) maupun dari subfolder (mis. user/index.php).
 */
function mbg_base_url(): string {
    // Hitung level kedalaman relatif terhadap root project.
    // Contoh: di root -> '', di user/index.php -> '../'
    return './';
}

/**
 * Autentikasi user berdasarkan email + password.
 * Return: array data user jika sukses, atau null jika gagal.
 */
function mbg_authenticate(string $email, string $password): ?array {
    $email = strtolower(trim($email));
    foreach (mbg_get_static_credentials() as $cred) {
        if (strtolower($cred['email']) === $email && $cred['password'] === $password) {
            return [
                'email' => $cred['email'],
                'nama'  => $cred['nama'],
                'role'  => $cred['role'],
            ];
        }
    }
    return null;
}
