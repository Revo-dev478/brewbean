<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Silahkan login']);
    exit();
}

// Menerima input POST dari AJAX
if (isset($_POST['id_keranjang'])) {
    $id_keranjang = (int)$_POST['id_keranjang'];
    $id_user = $_SESSION['id_user'];

    // Hapus data
    $query = "DELETE FROM tabel_keranjang WHERE id_keranjang = $id_keranjang AND id_user = $id_user";

    if (isset($koneksi) && $koneksi && mysqli_query($koneksi, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
}
