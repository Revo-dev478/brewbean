<?php

/**
 * GENERATE INVOICE - Browser Print Version
 * Lightweight solution - renders HTML for browser print (Ctrl+P / Save as PDF)
 * URL: generate_invoice.php?order_id=ORDER-XXXXX
 */

session_start();
require_once 'config.php';

// Validasi user login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

// Validasi order_id
if (empty($order_id)) {
    die("Order ID tidak valid");
}

// Cek koneksi database
if (!$koneksi) {
    die("Koneksi database gagal");
}

// Fetch transaksi data
$query = "SELECT t.*, u.username, u.email, u.phone
          FROM transaksi_midtrans t
          LEFT JOIN tabel_user u ON t.id_user = u.id_user
          WHERE t.order_id = ? AND t.id_user = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "si", $order_id, $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$transaksi = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$transaksi) {
    die("Transaksi tidak ditemukan atau Anda tidak memiliki akses");
}

// Fetch items
$query_items = "SELECT * FROM checkout_item WHERE order_id = ?";
$stmt_items = mysqli_prepare($koneksi, $query_items);
mysqli_stmt_bind_param($stmt_items, "s", $order_id);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
$items = [];
while ($row = mysqli_fetch_assoc($result_items)) {
    $items[] = $row;
}
mysqli_stmt_close($stmt_items);

// Prepare data untuk template
$invoice_data = [
    'order_id' => $transaksi['order_id'],
    'transaction_time' => $transaksi['transaction_time'],
    'transaction_status' => $transaksi['transaction_status'],
    'gross_amount' => $transaksi['gross_amount'],
    'ongkir' => $transaksi['ongkir'] ?? 0,
    'payment_type' => $transaksi['payment_type']
];

$customer = [
    'username' => $transaksi['username'],
    'email' => $transaksi['email'],
    'phone' => $transaksi['phone']
];

// Handle phone number formatting
if (!empty($customer['phone']) && substr($customer['phone'], 0, 1) != '0') {
    $customer['phone'] = '0' . $customer['phone'];
}

// Render HTML template directly
include 'invoice_template.php';
