<?php

/**
 * KONFIRMASI PESANAN - Endpoint untuk konfirmasi penerimaan pesanan
 */
session_start();
header('Content-Type: application/json');

// Cek login
if (empty($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_user = $_SESSION['id_user'];

// Validasi input
if (empty($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID required']);
    exit;
}

$order_id = $_POST['order_id'];

// Koneksi database
require_once 'config.php';
if (!$koneksi) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Cek ownership dan status
$stmtCheck = $koneksi->prepare("SELECT id_transaksi, delivery_status, transaction_status FROM transaksi_midtrans WHERE order_id = ? AND id_user = ?");
if (!$stmtCheck) {
    echo json_encode(['success' => false, 'message' => 'Prepare check failed: ' . $koneksi->error]);
    exit;
}

$stmtCheck->bind_param("si", $order_id, $id_user);
$stmtCheck->execute();
$result = $stmtCheck->get_result();
$transaksi = $result->fetch_assoc();
$stmtCheck->close();

if (!$transaksi) {
    echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan atau Anda tidak berhak mengaksesnya.']);
    exit;
}

// Treat NULL or empty status as 'processing' (matching riwayat_pemesanan.php display logic)
$current_status = !empty($transaksi['delivery_status']) ? strtolower($transaksi['delivery_status']) : 'processing';

// Cek apakah status valid untuk dikonfirmasi
if (!in_array($current_status, ['processing', 'shipped', 'delivered'])) {
    echo json_encode(['success' => false, 'message' => 'Status pesanan saat ini (' . $current_status . ') belum bisa dikonfirmasi.']);
    exit;
}

// Update status ke confirmed
$update = $koneksi->prepare("UPDATE transaksi_midtrans SET delivery_status = 'confirmed', delivery_confirmed_at = NOW() WHERE order_id = ? AND id_user = ?");
if (!$update) {
    echo json_encode(['success' => false, 'message' => 'Prepare update failed: ' . $koneksi->error]);
    exit;
}

$update->bind_param("si", $order_id, $id_user);

if ($update->execute()) {
    $update->close();
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dikonfirmasi. Terima kasih!']);
} else {
    $update->close();
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status: ' . $koneksi->error]);
}
