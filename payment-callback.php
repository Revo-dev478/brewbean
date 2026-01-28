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

// Koneksi database diambil dari config.php
// Pastikan $koneksi tersedia
if (!isset($koneksi) || !$koneksi) {
    http_response_code(500);
    error_log("Database connection failed via config.php");
    exit;
}

// Hanya terima POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

try {
    // Dapatkan notifikasi dari Midtrans
    $notif = new \Midtrans\Notification();

    $order_id = $notif->order_id;
    $transaction_status = $notif->transaction_status;
    $payment_type = isset($notif->payment_type) ? $notif->payment_type : 'unknown';
    $gross_amount = isset($notif->gross_amount) ? (float)$notif->gross_amount : 0;

    error_log("Callback received for order: " . $order_id . " | Status: " . $transaction_status . " | Gross: " . $gross_amount);

    // ===========================
    // 1. AMBIL id_user dari checkout
    // ===========================
    $checkoutQuery = "SELECT id_user FROM checkout WHERE order_id = ?";
    $checkoutStmt = $koneksi->prepare($checkoutQuery);

    if (!$checkoutStmt) {
        throw new Exception("Prepare statement failed: " . $koneksi->error);
    }

    $checkoutStmt->bind_param("s", $order_id);
    $checkoutStmt->execute();
    $checkoutResult = $checkoutStmt->get_result();
    $checkoutData = $checkoutResult->fetch_assoc();
    $checkoutStmt->close();

    if (!$checkoutData) {
        throw new Exception("Order ID tidak ditemukan di tabel checkout: " . $order_id);
    }

    $id_user = (int)$checkoutData['id_user'];

    // ===========================
    // 2. CEK TRANSAKSI SUDAH ADA
    // ===========================
    $checkTransQuery = "SELECT id_transaksi FROM transaksi_midtrans WHERE order_id = ?";
    $checkTransStmt = $koneksi->prepare($checkTransQuery);

    if (!$checkTransStmt) {
        throw new Exception("Prepare statement failed: " . $koneksi->error);
    }

    $checkTransStmt->bind_param("s", $order_id);
    $checkTransStmt->execute();
    $checkTransResult = $checkTransStmt->get_result();
    $transactionExists = $checkTransResult->num_rows > 0;
    $checkTransStmt->close();

    // ===========================
    // 3. INSERT atau UPDATE transaksi_midtrans
    // ===========================
    if ($transactionExists) {
        // UPDATE jika sudah ada (✅ sekarang ikut update gross_amount)
        $updateQuery = "UPDATE transaksi_midtrans 
                        SET transaction_status = ?, 
                            payment_type = ?, 
                            gross_amount = ?,
                            transaction_time = NOW()
                        WHERE order_id = ?";
        $updateStmt = $koneksi->prepare($updateQuery);

        if (!$updateStmt) {
            throw new Exception("Prepare statement failed: " . $koneksi->error);
        }

        // s = status, s = payment_type, d = gross_amount, s = order_id
        $updateStmt->bind_param("ssds", $transaction_status, $payment_type, $gross_amount, $order_id);

        if (!$updateStmt->execute()) {
            throw new Exception("Update failed: " . $updateStmt->error);
        }
        $updateStmt->close();
        error_log("Transaction updated for order: " . $order_id);
    } else {
        // INSERT jika belum ada
        $insertQuery = "INSERT INTO transaksi_midtrans 
                        (id_user, order_id, transaction_status, payment_type, gross_amount, transaction_time)
                        VALUES (?, ?, ?, ?, ?, NOW())";
        $insertStmt = $koneksi->prepare($insertQuery);

        if (!$insertStmt) {
            throw new Exception("Prepare statement failed: " . $koneksi->error);
        }

        // i = id_user, s = order_id, s = status, s = payment_type, d = gross_amount
        $insertStmt->bind_param("isssd", $id_user, $order_id, $transaction_status, $payment_type, $gross_amount);

        if (!$insertStmt->execute()) {
            throw new Exception("Insert failed: " . $insertStmt->error);
        }
        $insertStmt->close();
        error_log("Transaction inserted for order: " . $order_id);
    }

    // ===========================
    // 4. UPDATE STATUS DI CHECKOUT
    // ===========================
    if ($transaction_status === 'settlement' || $transaction_status === 'capture') {
        $checkout_status = 'success';
    } elseif ($transaction_status === 'pending') {
        $checkout_status = 'pending';
    } else {
        $checkout_status = 'failed';
    }

    $updateCheckoutQuery = "UPDATE checkout 
                            SET status_checkout = ? 
                            WHERE order_id = ?";
    $updateCheckoutStmt = $koneksi->prepare($updateCheckoutQuery);

    if (!$updateCheckoutStmt) {
        throw new Exception("Prepare statement failed: " . $koneksi->error);
    }

    $updateCheckoutStmt->bind_param("ss", $checkout_status, $order_id);

    if (!$updateCheckoutStmt->execute()) {
        throw new Exception("Update checkout failed: " . $updateCheckoutStmt->error);
    }
    $updateCheckoutStmt->close();
    error_log("Checkout status updated: " . $order_id . " -> " . $checkout_status);

    // Response sukses ke Midtrans
    http_response_code(200);
    echo json_encode(['status' => 'success', 'order_id' => $order_id]);
} catch (Exception $e) {
    error_log("Error in payment callback: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} finally {
    $koneksi->close();
}
