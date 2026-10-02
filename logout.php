<?php
/**
 * ============================================================================
 *  LOGOUT - MBG Workspace
 * ============================================================================
 *  Hancurkan sesi dan kembalikan user ke halaman login.
 * ============================================================================
 */
require_once 'includes/auth.php';

// Tandai halaman ini publik — session_destroy() di bawah akan
// menghapus data login sehingga halaman logout sendiri tidak
// boleh memanggil require_login() (akan terjadi loop).
$public_page = true;

// Bersihkan semua variabel session
$_SESSION = [];

// Hapus cookie session kalau ada
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit;
