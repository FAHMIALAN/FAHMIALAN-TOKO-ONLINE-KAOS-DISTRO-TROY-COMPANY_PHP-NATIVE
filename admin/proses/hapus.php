<?php
include '../../koneksi/koneksi.php';

if (isset($_GET['inv'])) {
    $invoice = $_GET['inv'];

    // Hapus data berdasarkan invoice
    $query = "DELETE FROM produksi WHERE invoice = '$invoice'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pesanan berhasil dihapus!'); window.location='../produksi.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pesanan!'); window.location='../produksi.php';</script>";
    }
} else {
    header('location:../produksi.php');
}
?>
