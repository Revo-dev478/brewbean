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
$koneksi = new mysqli("localhost", "root", "", "db_brewbeans");
if ($koneksi->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Cek ownership dan status
$check = $koneksi->prepare("SELECT id_transaksi, delivery_status FROM transaksi_midtrans WHERE order_id = ? AND id_user = ?");
$check->bind_param("si", $order_id, $id_user);
$check->execute();
$result = $check->get_result();
$transaksi = $result->fetch_assoc();
$check->close();

if (!$transaksi) {
    $koneksi->close();
    echo json_encode(['success' => false, 'message' => 'Order not found or unauthorized']);
    exit;
}

// Cek apakah status delivered
if ($transaksi['delivery_status'] != 'delivered') {
    $koneksi->close();
    echo json_encode(['success' => false, 'message' => 'Order status is not delivered yet']);
    exit;
}

// Update status ke confirmed
$update = $koneksi->prepare("UPDATE transaksi_midtrans SET delivery_status = 'confirmed', delivery_confirmed_at = NOW() WHERE order_id = ? AND id_user = ?");
$update->bind_param("si", $order_id, $id_user);

if ($update->execute()) {
    $update->close();
    $koneksi->close();
    echo json_encode(['success' => true, 'message' => 'Order confirmed successfully']);
} else {
    $update->close();
    $koneksi->close();
    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
}
?>
