<?php
include "../config/koneksi.php";
require_once "../includes/auth.php";

// Blokir akses langsung untuk role yang tidak boleh Delete.
if (!can_action('mitra', 'delete')) {
    http_response_code(403);
    mbg_render_forbidden('mitra');
    exit;
}

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

if (mysqli_query($conn, "DELETE FROM MITRA WHERE mitra_id='$id'")) {
    header("Location: index.php?msg=deleted");
} else {
    header("Location: index.php?msg=error");
}
exit;
?>