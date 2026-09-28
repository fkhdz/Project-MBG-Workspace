<?php
include "../config/koneksi.php";

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

if (mysqli_query($conn, "DELETE FROM MITRA WHERE mitra_id='$id'")) {
    header("Location: index.php?msg=deleted");
} else {
    header("Location: index.php?msg=error");
}
exit;
?>