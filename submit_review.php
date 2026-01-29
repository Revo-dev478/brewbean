<?php

/**
 * SUBMIT REVIEW
 * Endpoint untuk menyimpan ulasan user
 */
session_start();
header('Content-Type: application/json');
require_once 'config.php';

if (empty($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_user = $_SESSION['id_user'];
$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['order_id']) || empty($input['reviews']) || !is_array($input['reviews'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$order_id = $input['order_id'];
$reviews = $input['reviews'];
$success_count = 0;

// Verify order ownership
$verify = mysqli_query($koneksi, "SELECT id_transaksi FROM transaksi_midtrans WHERE order_id = '$order_id' AND id_user = '$id_user'");
if (!$verify || mysqli_num_rows($verify) === 0) {
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
    exit;
}
$transaksi_data = mysqli_fetch_assoc($verify);
$id_transaksi = $transaksi_data['id_transaksi'];

foreach ($reviews as $item) {
    if (empty($item['id_product']) || empty($item['rating'])) continue;

    $id_product = (int)$item['id_product'];
    $rating = (int)$item['rating'];
    $review_text = isset($item['review']) ? mysqli_real_escape_string($koneksi, $item['review']) : '';

    // Check if already reviewed
    $check = mysqli_query($koneksi, "SELECT id_review FROM tabel_review WHERE order_id = '$order_id' AND id_product = '$id_product' AND id_user = '$id_user'");

    if (mysqli_num_rows($check) == 0) {
        $query = "INSERT INTO tabel_review (id_transaksi, order_id, id_product, id_user, rating, review_text) 
                  VALUES ('$id_transaksi', '$order_id', '$id_product', '$id_user', '$rating', '$review_text')";
        if (mysqli_query($koneksi, $query)) {
            $success_count++;
        }
    }
}

echo json_encode(['success' => true, 'message' => "$success_count ulasan berhasil disimpan"]);
