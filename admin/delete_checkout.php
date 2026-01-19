<?php
session_start();
require_once '../config.php';

// Admin auth check disabled for testing
// if (!isset($_SESSION['is_admin'])) {
//     header('Location: login.php');
//     exit;
// }

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die('Missing checkout ID');
}

// Delete related checkout items first to maintain referential integrity
$delItems = $koneksi->prepare('DELETE FROM checkout_item WHERE order_id = (SELECT order_id FROM checkout WHERE id_checkout = ?)');
$delItems->bind_param('i', $id);
$delItems->execute();
$delItems->close();

// Delete the checkout record
$delCheckout = $koneksi->prepare('DELETE FROM checkout WHERE id_checkout = ?');
$delCheckout->bind_param('i', $id);
if ($delCheckout->execute()) {
    header('Location: tabel_checkout.php');
    exit;
} else {
    die('Error deleting checkout: ' . $koneksi->error);
}
$delCheckout->close();
