<?php
session_start();
require_once '../config.php';

// if (!isset($_SESSION['is_admin'])) {
//     header('Location: login.php');
//     exit;
// }

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $stmt = $koneksi->prepare("DELETE FROM transaksi_midtrans WHERE id_transaksi = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Transaksi berhasil dihapus.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus transaksi: " . $koneksi->error;
        $_SESSION['flash_type'] = "danger";
    }

    $stmt->close();
} else {
    $_SESSION['flash_message'] = "ID tidak ditemukan.";
    $_SESSION['flash_type'] = "warning";
}

header('Location: riwayat_transaksi.php');
exit;
