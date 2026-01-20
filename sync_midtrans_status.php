<?php

/**
 * SYNC MIDTRANS STATUS
 * Mengambil status transaksi terbaru dari Midtrans API
 * dan menyinkronkan dengan database lokal
 * 
 * Kompatibel dengan PHP 5.6+
 */

// Load environment variables
require_once __DIR__ . '/env_loader.php';

// Konfigurasi Midtrans
$serverKey = env('MIDTRANS_SERVER_KEY_ALT', '');
$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

$apiUrl = $isProduction
    ? 'https://api.midtrans.com/v2'
    : 'https://api.sandbox.midtrans.com/v2';

/**
 * Fungsi untuk mendapatkan status transaksi dari Midtrans API
 * @param string $orderId Order ID yang ingin dicek
 * @param string $serverKey Server Key Midtrans
 * @param string $apiUrl Base URL API Midtrans
 * @return array|null Response dari Midtrans atau null jika gagal
 */
function getMidtransStatus($orderId, $serverKey, $apiUrl)
{
    $url = $apiUrl . '/' . $orderId . '/status';

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($serverKey . ':')
        ),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_TIMEOUT => 30
    ));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);


    if ($curlError) {
        error_log("Curl Error for order $orderId: $curlError");
        return null;
    }

    if ($httpCode == 200 || $httpCode == 201) {
        return json_decode($response, true);
    }

    return null;
}

/**
 * Fungsi untuk menentukan status checkout berdasarkan status transaksi Midtrans
 * @param string $transactionStatus Status dari Midtrans
 * @return string Status untuk checkout
 */
function getCheckoutStatus($transactionStatus)
{
    if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
        return 'success';
    } elseif ($transactionStatus === 'pending') {
        return 'pending';
    } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire' || $transactionStatus === 'failure') {
        return 'failed';
    } else {
        return 'pending';
    }
}

/**
 * Fungsi untuk menyinkronkan status transaksi dari Midtrans ke database
 * @param mysqli $koneksi Koneksi database
 * @param int $id_user ID User yang ingin disinkronkan
 * @return array Hasil sinkronisasi
 */
function syncUserTransactions($koneksi, $id_user)
{
    global $serverKey, $apiUrl;

    $result = array(
        'synced' => 0,
        'errors' => 0,
        'details' => array()
    );

    // Ambil semua order dari checkout milik user
    $query = "SELECT order_id, total_harga, status_checkout, created_at 
              FROM checkout 
              WHERE id_user = ? 
              ORDER BY created_at DESC";

    $stmt = $koneksi->prepare($query);
    if (!$stmt) {
        $result['errors']++;
        return $result;
    }

    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $ordersResult = $stmt->get_result();
    $orders = array();
    while ($row = $ordersResult->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();

    foreach ($orders as $order) {
        $orderId = $order['order_id'];

        // Cek status dari Midtrans API
        $midtransData = getMidtransStatus($orderId, $serverKey, $apiUrl);

        if ($midtransData && isset($midtransData['transaction_status'])) {
            $transactionStatus = $midtransData['transaction_status'];
            $paymentType = isset($midtransData['payment_type']) ? $midtransData['payment_type'] : 'unknown';
            $grossAmount = isset($midtransData['gross_amount']) ? (float)$midtransData['gross_amount'] : (float)$order['total_harga'];
            $fraudStatus = isset($midtransData['fraud_status']) ? $midtransData['fraud_status'] : null;
            $transactionTime = isset($midtransData['transaction_time']) ? $midtransData['transaction_time'] : $order['created_at'];
            $settlementTime = isset($midtransData['settlement_time']) ? $midtransData['settlement_time'] : null;

            // Update status checkout
            $checkoutStatus = getCheckoutStatus($transactionStatus);
            $updateCheckout = $koneksi->prepare("UPDATE checkout SET status_checkout = ? WHERE order_id = ?");
            if ($updateCheckout) {
                $updateCheckout->bind_param("ss", $checkoutStatus, $orderId);
                $updateCheckout->execute();
                $updateCheckout->close();
            }

            // Cek apakah sudah ada di transaksi_midtrans
            $checkQuery = "SELECT id_transaksi, transaction_status FROM transaksi_midtrans WHERE order_id = ?";
            $checkStmt = $koneksi->prepare($checkQuery);
            if (!$checkStmt) {
                $result['errors']++;
                continue;
            }

            $checkStmt->bind_param("s", $orderId);
            $checkStmt->execute();
            $existingResult = $checkStmt->get_result();
            $existing = $existingResult->fetch_assoc();
            $checkStmt->close();

            if ($existing) {
                // SELALU update - tidak perlu cek apakah status berbeda
                $updateQuery = "UPDATE transaksi_midtrans 
                                SET transaction_status = ?, 
                                    payment_type = ?, 
                                    gross_amount = ?,
                                    fraud_status = ?,
                                    transaction_time = ?,
                                    settlement_time = ?
                                WHERE order_id = ?";
                $updateStmt = $koneksi->prepare($updateQuery);
                if ($updateStmt) {
                    $updateStmt->bind_param(
                        "ssdssss",
                        $transactionStatus,
                        $paymentType,
                        $grossAmount,
                        $fraudStatus,
                        $transactionTime,
                        $settlementTime,
                        $orderId
                    );
                    $updateStmt->execute();
                    $updateStmt->close();

                    $result['synced']++;
                    $result['details'][] = "Updated: $orderId -> $transactionStatus";
                }
            } else {
                // Insert baru
                $insertQuery = "INSERT INTO transaksi_midtrans 
                                (id_user, order_id, transaction_status, payment_type, gross_amount, fraud_status, transaction_time, settlement_time)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $koneksi->prepare($insertQuery);
                if ($insertStmt) {
                    $insertStmt->bind_param(
                        "isssdsss",
                        $id_user,
                        $orderId,
                        $transactionStatus,
                        $paymentType,
                        $grossAmount,
                        $fraudStatus,
                        $transactionTime,
                        $settlementTime
                    );
                    $insertStmt->execute();
                    $insertStmt->close();

                    $result['synced']++;
                    $result['details'][] = "Inserted: $orderId -> $transactionStatus";
                }
            }
        } else {
            // Jika Midtrans tidak menemukan transaksi (pending yang belum dibayar)
            // Masukkan sebagai pending jika belum ada di transaksi_midtrans
            $checkQuery = "SELECT id_transaksi FROM transaksi_midtrans WHERE order_id = ?";
            $checkStmt = $koneksi->prepare($checkQuery);
            if (!$checkStmt) {
                continue;
            }

            $checkStmt->bind_param("s", $orderId);
            $checkStmt->execute();
            $existingResult = $checkStmt->get_result();
            $existingCheck = $existingResult->fetch_assoc();
            $checkStmt->close();

            if (!$existingCheck) {
                $insertQuery = "INSERT INTO transaksi_midtrans 
                                (id_user, order_id, transaction_status, payment_type, gross_amount, transaction_time)
                                VALUES (?, ?, 'pending', 'midtrans', ?, ?)";
                $insertStmt = $koneksi->prepare($insertQuery);
                if ($insertStmt) {
                    $totalHarga = (float)$order['total_harga'];
                    $createdAt = $order['created_at'];
                    $insertStmt->bind_param("isds", $id_user, $orderId, $totalHarga, $createdAt);
                    $insertStmt->execute();
                    $insertStmt->close();

                    $result['synced']++;
                    $result['details'][] = "Inserted pending: $orderId";
                }
            }
        }
    }

    return $result;
}
