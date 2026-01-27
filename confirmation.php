<?php

/**
 * confirmation.php - Status Handler
 * FIXED: Prioritaskan status dari URL untuk cancelled
 * DESIGN: Sesuai tema CoffeeBlend
 */

// Start session and load config for navbar
session_start();
require_once 'config.php';

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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .confirmation-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a0f0a 0%, #3e2723 50%, #5d4037 100%);
            position: relative;
            overflow-x: hidden;
        }

        .confirmation-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('images/bg_1.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: 0;
        }

        .confirmation-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 120px 20px 60px 20px;
        }

        .container-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            max-width: 480px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #c49b63, #8d6e63);
        }

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
            margin-bottom: 10px;
            font-weight: 600;
        }

        .sub-message {
            color: #6d4c41;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .order-info {
            background: #f8f5f1;
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
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(62, 39, 35, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(62, 39, 35, 0.4);
            background: linear-gradient(135deg, #5d4037 0%, #6d4c41 100%);
            color: white;
            text-decoration: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #c49b63 0%, #a37c45 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(196, 155, 99, 0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #d4a970 0%, #b58b50 100%);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #d7ccc8;
            color: #6d4c41;
        }

        .btn-outline:hover {
            border-color: #8d6e63;
            color: #3e2723;
            text-decoration: none;
        }

        .coffee-icon {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 40px;
            opacity: 0.1;
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

        /* Navbar styling */
        .ftco-navbar-light.scrolled {
            background: #000 !important;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .container-card {
                padding: 35px 22px;
                margin: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'partials/navbar.php'; ?>

    <div class="confirmation-page">
        <div class="confirmation-content">
            <div class="container-card">
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/jquery.timepicker.min.js"></script>
    <script src="js/scrollax.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>