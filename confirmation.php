<?php

/**
 * confirmation.php - Status Handler
 * FIXED: Prioritaskan status dari URL untuk cancelled
 * DESIGN: Sesuai tema CoffeeBlend
 */

error_reporting(0);
ini_set('display_errors', 0);

// Load environment variables
require_once __DIR__ . '/env_loader.php';

// ================= KONFIGURASI =================
$serverKey = env('MIDTRANS_SERVER_KEY', '');
$clientKey = env('MIDTRANS_CLIENT_KEY', '');
$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

// Ambil parameter dari URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$url_status = isset($_GET['status']) ? strtolower($_GET['status']) : '';

// Default UI Variables
$transaction_status = 'unknown';
$status_type = 'unknown';
$color = '#999999';
$message = 'Menunggu Konfirmasi Status...';
$icon = '❓';
$sub_message = '';

// ================= LOGIK VERIFIKASI =================
if (!empty($order_id)) {

    // PRIORITAS 1: Jika URL status adalah cancelled, failed, atau error
    // Langsung gunakan status tersebut tanpa cek API
    if ($url_status == 'cancelled' || $url_status == 'cancel' || $url_status == 'failed' || $url_status == 'error' || $url_status == 'success') {
        $transaction_status = $url_status;
    } else {
        // Coba verifikasi ke API Midtrans
        $libPath = dirname(__FILE__) . '/midtrans-php-master/Midtrans.php';

        if (file_exists($libPath)) {
            try {
                require_once $libPath;
                \Midtrans\Config::$serverKey = $serverKey;
                \Midtrans\Config::$isProduction = $isProduction;

                // Cek status ke API Midtrans
                $transaction = \Midtrans\Transaction::status($order_id);
                $transaction_status = is_object($transaction) ? $transaction->transaction_status : $transaction['transaction_status'];
            } catch (Exception $e) {
                // Fallback ke URL status
                $transaction_status = !empty($url_status) ? $url_status : 'pending';
            }
        } else {
            // Fallback jika library tidak ada
            $transaction_status = !empty($url_status) ? $url_status : 'pending';
        }
    }

    // ================= TENTUKAN STATUS DISPLAY =================
    $transaction_status = strtolower($transaction_status);

    // SUCCESS
    if ($transaction_status == 'capture' || $transaction_status == 'settlement' || $transaction_status == 'success') {
        $status_type = 'success';
        $color = '#28a745';
        $message = 'Pembayaran Berhasil!';
        $sub_message = 'Terima kasih telah berbelanja di CoffeeBlend';
        $icon = '✓';
    }
    // PENDING
    elseif ($transaction_status == 'pending') {
        $status_type = 'pending';
        $color = '#F96D00';
        $message = 'Menunggu Pembayaran';
        $sub_message = 'Segera selesaikan pembayaran Anda';
        $icon = '⏳';
    }
    // CANCELLED (user menutup popup)
    elseif ($transaction_status == 'cancel' || $transaction_status == 'cancelled') {
        $status_type = 'cancelled';
        $color = '#dc3545';
        $message = 'Transaksi Dibatalkan';
        $sub_message = 'Anda membatalkan proses pembayaran';
        $icon = '✕';
    }
    // EXPIRED
    elseif ($transaction_status == 'expire' || $transaction_status == 'expired') {
        $status_type = 'expired';
        $color = '#6c757d';
        $message = 'Transaksi Kadaluarsa';
        $sub_message = 'Waktu pembayaran telah habis';
        $icon = '⏰';
    }
    // DENIED
    elseif ($transaction_status == 'deny' || $transaction_status == 'denied') {
        $status_type = 'denied';
        $color = '#dc3545';
        $message = 'Pembayaran Ditolak';
        $sub_message = 'Silakan hubungi bank Anda atau coba metode lain';
        $icon = '⚠';
    }
    // FAILED / ERROR
    elseif ($transaction_status == 'failure' || $transaction_status == 'error' || $transaction_status == 'failed') {
        $status_type = 'failed';
        $color = '#dc3545';
        $message = 'Pembayaran Gagal';
        $sub_message = 'Terjadi kesalahan saat memproses pembayaran';
        $icon = '✕';
    }
    // UNKNOWN
    else {
        $status_type = 'unknown';
        $color = '#6c757d';
        $message = 'Status Tidak Diketahui';
        $sub_message = 'Silakan cek kembali nanti';
        $icon = '?';
    }
} else {
    $message = "Akses tidak valid";
    $sub_message = "Tidak ada Order ID";
    $icon = '⚠';
    $color = '#dc3545';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Konfirmasi Pembayaran - CoffeeBlend</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #232526 0%, #414345 100%);
            /* Fallback Black/Grey */
            background: linear-gradient(135deg, #1b1b1b 0%, #3e2723 100%);
            /* Black to Dark Coffee */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.98);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            max-width: 480px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #c49b63;
            /* Coffee Gold */
        }

        /* Dynamic Color Overrides based on status if needed, but keeping primary theme brown */
        .status-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: <?php echo $color; ?>20;
            border: 4px solid <?php echo $color; ?>;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 25px;
            font-size: 42px;
            color: <?php echo $color; ?>;
            font-weight: bold;
        }

        h1 {
            font-size: 26px;
            color: #3e2723;
            /* Dark Brown */
            margin-bottom: 10px;
            font-weight: 600;
        }

        .sub-message {
            color: #6d4c41;
            /* Brownish Grey */
            font-size: 14px;
            margin-bottom: 30px;
        }

        .order-info {
            background: #f8f5f1;
            /* Light Cream */
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid #efebe9;
        }

        .order-id {
            font-size: 12px;
            color: #8d6e63;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .order-value {
            font-size: 16px;
            color: #3e2723;
            font-weight: 600;
            word-break: break-all;
        }

        .details {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: left;
            margin-bottom: 30px;
            border: 1px solid #efebe9;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #d7ccc8;
            font-size: 14px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #8d6e63;
        }

        .detail-value {
            font-weight: 600;
            color: #3e2723;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: <?php echo $color; ?>20;
            color: <?php echo $color; ?>;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            display: block;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #c49b63 0%, #a37c45 100%);
            /* Coffee Gold */
            color: white;
            box-shadow: 0 4px 15px rgba(196, 155, 99, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(196, 155, 99, 0.5);
            background: linear-gradient(135deg, #d4a970 0%, #b58b50 100%);
        }

        .btn-secondary {
            background: #3e2723;
            /* Dark Coffee */
            color: white;
        }

        .btn-secondary:hover {
            background: #5d4037;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #d7ccc8;
            color: #6d4c41;
        }

        .btn-outline:hover {
            border-color: #c49b63;
            color: #c49b63;
        }

        .coffee-icon {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 40px;
            opacity: 0.1;
            filter: grayscale(100%) sepia(100%) hue-rotate(0deg) brightness(0.8) saturate(1.5);
            /* Brownish tint */
        }

        /* Animation Definitions */
        @keyframes checkmark {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Access based animations */
        .status-icon.success {
            animation: checkmark 0.5s ease-out;
        }

        .status-icon.pending {
            animation: pulse 2s ease-in-out infinite;
        }

        .status-icon.failed,
        .status-icon.cancelled,
        .status-icon.denied,
        .status-icon.expired {
            animation: shake 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="status-icon <?php echo $status_type; ?>">
            <?php echo $icon; ?>
        </div>

        <h1><?php echo $message; ?></h1>
        <p class="sub-message"><?php echo $sub_message; ?></p>

        <?php if (!empty($order_id)): ?>
            <div class="order-info">
                <div class="order-id">Order ID</div>
                <div class="order-value"><?php echo htmlspecialchars($order_id); ?></div>
            </div>

            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Tanggal</span>
                    <span class="detail-value"><?php echo date('d M Y, H:i'); ?> WIB</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode</span>
                    <span class="detail-value">Midtrans</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="status-badge"><?php echo strtoupper($status_type); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="btn-group">
            <?php if ($status_type == 'success'): ?>
                <a href="riwayat_pemesanan.php" class="btn btn-primary">Lihat Pesanan Saya</a>
                <a href="menu.php" class="btn btn-secondary">Belanja Lagi</a>
            <?php elseif ($status_type == 'pending'): ?>
                <a href="javascript:location.reload();" class="btn btn-primary">Cek Status Terbaru</a>
                <a href="riwayat_pemesanan.php" class="btn btn-outline">Lihat Pesanan</a>
            <?php else: ?>
                <a href="checkout.php" class="btn btn-primary">Coba Lagi</a>
                <a href="menu.php" class="btn btn-secondary">Kembali ke Menu</a>
            <?php endif; ?>
            <a href="utama.php" class="btn btn-outline">Ke Beranda</a>
        </div>

        <span class="coffee-icon">☕</span>
    </div>
</body>

</html>