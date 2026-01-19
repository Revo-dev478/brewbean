<?php

/**
 * MIDTRANS PAYMENT CALLBACK
 * UPDATE CHECKOUT + LOG TRANSAKSI_MIDTRANS dengan id_user + gross_amount
 */

require_once 'config.php';

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY_ALT', '');
\Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Koneksi database
$koneksi = new mysqli('localhost', 'root', '', 'db_brewbeans');
if ($koneksi->connect_error) {
    http_response_code(500);
    error_log("Database connection failed: " . $koneksi->connect_error);
    exit;
}

// Hanya terima POST request dari Midtrans
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    // Ambil notifikasi dari Midtrans
    $notif = new \Midtrans\Notification();

    $order_id = $notif->order_id;
    $transaction_status = $notif->transaction_status;
    $payment_type = $notif->payment_type ?? 'unknown';
    $fraud_status = $notif->fraud_status ?? '';
    $gross_amount = (float) $notif->gross_amount;
    $settlement_time = $notif->settlement_time ?? null;

    error_log("Callback: Order {$order_id} | Status {$transaction_status} | Amount {$gross_amount}");

    // ===========================
    // 1️⃣ Ambil id_user dari checkout
    // ===========================
    $stmt = $koneksi->prepare("SELECT id_user FROM checkout WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $checkout = $result->fetch_assoc();
    $stmt->close();

    if (!$checkout) {
        throw new Exception("Order ID tidak ditemukan di tabel checkout: {$order_id}");
    }

    $id_user = (int)$checkout['id_user'];

    // ===========================
    // 2️⃣ Cek apakah transaksi sudah ada
    // ===========================
    $check = $koneksi->prepare("SELECT id_transaksi FROM transaksi_midtrans WHERE order_id = ?");
    $check->bind_param("s", $order_id);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();

    // ===========================
    // 3️⃣ Insert / Update ke transaksi_midtrans
    // ===========================
    if ($exists) {
        $update = $koneksi->prepare("
            UPDATE transaksi_midtrans 
            SET transaction_status=?, payment_type=?, fraud_status=?, 
                gross_amount=?, transaction_time=NOW(), settlement_time=? 
            WHERE order_id=?");
        $update->bind_param(
            "sssds",
            $transaction_status,
            $payment_type,
            $fraud_status,
            $gross_amount,
            $settlement_time,
            $order_id
        );
        $update->execute();
        $update->close();
        error_log("Updated transaksi_midtrans: {$order_id}");
    } else {
        $insert = $koneksi->prepare("
            INSERT INTO transaksi_midtrans 
            (id_user, order_id, transaction_status, payment_type, fraud_status, gross_amount, transaction_time, settlement_time)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
        $insert->bind_param(
            "issssds",
            $id_user,
            $order_id,
            $transaction_status,
            $payment_type,
            $fraud_status,
            $gross_amount,
            $settlement_time
        );
        $insert->execute();
        $insert->close();
        error_log("Inserted transaksi_midtrans: {$order_id}");
    }

    // ===========================
    // 4️⃣ Update status_checkout di tabel checkout
    // ===========================
    if ($transaction_status === 'settlement' || $transaction_status === 'capture') {
        $checkout_status = 'success';
    } elseif ($transaction_status === 'pending') {
        $checkout_status = 'pending';
    } elseif ($transaction_status === 'deny' || $transaction_status === 'cancel' || $transaction_status === 'expire' || $transaction_status === 'failure') {
        $checkout_status = 'failed';
    } else {
        $checkout_status = 'pending';
    }

    $updateCheckout = $koneksi->prepare("UPDATE checkout SET status_checkout=? WHERE order_id=?");
    $updateCheckout->bind_param("ss", $checkout_status, $order_id);
    $updateCheckout->execute();
    $updateCheckout->close();

    error_log("Updated checkout: {$order_id} -> {$checkout_status}");

    // ===========================
    // 5️⃣ Response sukses ke Midtrans
    // ===========================
    http_response_code(200);
    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
} catch (Exception $e) {
    error_log("Callback Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    $koneksi->close();
}
