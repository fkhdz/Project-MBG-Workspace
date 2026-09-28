<?php
include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Hapus data (DB tidak pakai FK cascade di sini, jadi aman)
if (mysqli_query($conn, "DELETE FROM DISTRIBUSI WHERE distribusi_id='$id'")) {
    header("Location: index.php?msg=deleted");
} else {
    header("Location: index.php?msg=error");
}
exit;
?>