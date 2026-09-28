<?php
include "../config/koneksi.php";

$koneksi_db = null;
if (isset($conn)) $koneksi_db = $conn;
elseif (isset($koneksi)) $koneksi_db = $koneksi;

if (!isset($_GET['id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$paket_id = $_GET['id'];
$item_id  = $_POST['item_id'];
$jumlah   = $_POST['jumlah'];

$cek = mysqli_query($koneksi_db, "SELECT * FROM DETAIL_PAKET WHERE paket_id='$paket_id' AND item_id='$item_id'");

if (mysqli_num_rows($cek) > 0) {
    header("Location: komposisi.php?id=$paket_id&msg_type=error&msg=" . urlencode("Item ini sudah ada di paket. Silakan edit jumlahnya."));
    exit;
}

$insert = mysqli_query($koneksi_db, "INSERT INTO DETAIL_PAKET (paket_id, item_id, jumlah_per_paket) VALUES ('$paket_id', '$item_id', '$jumlah')");

if ($insert) {
    header("Location: komposisi.php?id=$paket_id&msg_type=success&msg=" . urlencode("Item berhasil ditambahkan"));
} else {
    header("Location: komposisi.php?id=$paket_id&msg_type=error&msg=" . urlencode("Gagal menambahkan item"));
}
exit;