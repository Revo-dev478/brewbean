<?php
session_start();
require_once 'config.php';

// Pastikan respon selalu JSON
header('Content-Type: application/json');

// 1. Cek Login
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi habis, silakan login kembali']);
    exit();
}

// 2. Cek apakah ada data yang dikirim via POST
if (isset($_POST['id_keranjang']) && isset($_POST['qty'])) {

    $id_keranjang = (int)$_POST['id_keranjang'];
    $qty = (int)$_POST['qty'];
    $id_user = $_SESSION['id_user'];

    // Validasi input tidak boleh 0 atau minus
    if ($qty < 1) {
        echo json_encode(['success' => false, 'message' => 'Jumlah minimal 1']);
        exit();
    }

    // 3. Ambil Harga Lama dari tabel_keranjang untuk hitung Subtotal Baru
    // Berdasarkan screenshot Anda, tabel_keranjang punya kolom 'harga'
    // 3. Ambil Harga Lama dari tabel_keranjang untuk hitung Subtotal Baru
    // Berdasarkan screenshot Anda, tabel_keranjang punya kolom 'harga'
    $queryCek = "SELECT harga FROM tabel_keranjang WHERE id_keranjang = $id_keranjang AND id_user = $id_user";
    $resultCek = false;
    if (isset($koneksi) && $koneksi) {
        $resultCek = mysqli_query($koneksi, $queryCek);
    } else {
        echo json_encode(['success' => false, 'message' => 'Koneksi database error']);
        exit();
    }

    $data = mysqli_fetch_assoc($resultCek);

    if ($data) {
        $harga = (int)$data['harga'];
        $subtotal_baru = $harga * $qty; // Hitung subtotal baru

        // 4. Update Qty dan Subtotal di Database
        $queryUpdate = "UPDATE tabel_keranjang 
                        SET qty = $qty, subtotal = $subtotal_baru 
                        WHERE id_keranjang = $id_keranjang AND id_user = $id_user";

        if (mysqli_query($koneksi, $queryUpdate)) {
            // Berhasil
            echo json_encode([
                'success' => true,
                'new_subtotal' => $subtotal_baru // Kirim balik subtotal baru ke JS
            ]);
        } else {
            // Gagal Query
            echo json_encode(['success' => false, 'message' => 'Gagal update database: ' . mysqli_error($koneksi)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data keranjang tidak ditemukan atau bukan milik Anda']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
}
