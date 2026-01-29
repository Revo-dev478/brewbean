<?php

/**
 * RIWAYAT TRANSAKSI USER
 * Halaman untuk menampilkan semua invoice transaksi user
 */
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php?redirect=riwayat_transaksi_user.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Fetch all transactions for this user
$transactions = [];
if ($koneksi) {
    $query = "SELECT t.*, 
                     u.username, u.email,
                     (SELECT COUNT(*) FROM checkout_item ci WHERE ci.order_id = t.order_id) as item_count
              FROM transaksi_midtrans t
              LEFT JOIN tabel_user u ON t.id_user = u.id_user
              WHERE t.id_user = ?
              ORDER BY t.transaction_time DESC";

    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $transactions[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <title>Riwayat Transaksi - CoffeeBlend</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

    <!-- CSS -->
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
        .transaction-page {
            padding-top: 100px;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a0f0a 0%, #3e2723 50%, #5d4037 100%);
        }

        .transaction-page::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('images/bg_1.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            z-index: 0;
            pointer-events: none;
        }

        .transaction-container {
            position: relative;
            z-index: 1;
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px 60px;
        }

        .page-title {
            color: #fff;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }

        .page-subtitle {
            color: #c49b63;
            font-size: 16px;
            text-align: center;
            margin-bottom: 40px;
        }

        .transaction-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .transaction-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-info h3 {
            font-size: 16px;
            color: #3e2723;
            margin: 0;
            font-weight: 700;
        }

        .order-date {
            font-size: 12px;
            color: #8d6e63;
            margin-top: 3px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-success {
            background: #d4edda;
            color: #28a745;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background: #f8d7da;
            color: #dc3545;
        }

        .transaction-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed #d7ccc8;
            padding-top: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .transaction-details {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .detail-item {
            text-align: left;
        }

        .detail-label {
            font-size: 11px;
            color: #8d6e63;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            color: #3e2723;
            font-weight: 600;
        }

        .detail-value.amount {
            color: #c49b63;
            font-size: 18px;
        }

        .btn-invoice {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 100%);
            color: white;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-invoice:hover {
            background: linear-gradient(135deg, #5d4037 0%, #6d4c41 100%);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
        }

        .empty-state .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #3e2723;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #8d6e63;
            margin-bottom: 20px;
        }

        .btn-shop {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #c49b63 0%, #a37c45 100%);
            color: white;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-shop:hover {
            background: linear-gradient(135deg, #d4a970 0%, #b58b50 100%);
            color: white;
            text-decoration: none;
        }

        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px 30px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #c49b63;
        }

        .stat-label {
            font-size: 12px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* Navbar fix */
        .ftco-navbar-light.scrolled {
            background: #000 !important;
        }

        @media (max-width: 768px) {
            .transaction-header {
                flex-direction: column;
            }

            .transaction-body {
                flex-direction: column;
                align-items: flex-start;
            }

            .transaction-details {
                width: 100%;
            }

            .btn-invoice {
                width: 100%;
                justify-content: center;
            }

            .stats-bar {
                gap: 15px;
            }

            .stat-item {
                padding: 15px 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'partials/navbar.php'; ?>

    <div class="transaction-page">
        <div class="transaction-container">
            <h1 class="page-title">📋 Riwayat Transaksi</h1>
            <p class="page-subtitle">Semua invoice pembelian Anda di CoffeeBlend</p>

            <?php if (count($transactions) > 0): ?>
                <!-- Stats Bar -->
                <?php
                $total_transactions = count($transactions);
                $total_spent = 0;
                $success_count = 0;
                foreach ($transactions as $t) {
                    $status = strtolower($t['transaction_status']);
                    if (in_array($status, ['settlement', 'capture', 'success'])) {
                        $total_spent += $t['gross_amount'];
                        $success_count++;
                    }
                }
                ?>
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-number"><?= $total_transactions ?></div>
                        <div class="stat-label">Total Transaksi</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= $success_count ?></div>
                        <div class="stat-label">Berhasil</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">Rp <?= number_format($total_spent, 0, ',', '.') ?></div>
                        <div class="stat-label">Total Belanja</div>
                    </div>
                </div>

                <!-- Transaction List -->
                <?php foreach ($transactions as $trans):
                    $status = strtolower($trans['transaction_status']);

                    if (in_array($status, ['settlement', 'capture', 'success'])) {
                        $status_class = 'status-success';
                        $status_text = 'LUNAS';
                    } elseif ($status == 'pending') {
                        $status_class = 'status-pending';
                        $status_text = 'PENDING';
                    } else {
                        $status_class = 'status-failed';
                        $status_text = strtoupper($status);
                    }
                ?>
                    <div class="transaction-card">
                        <div class="transaction-header">
                            <div class="order-info">
                                <h3>INV-<?= htmlspecialchars($trans['order_id']) ?></h3>
                                <div class="order-date"><?= date('d F Y, H:i', strtotime($trans['transaction_time'])) ?> WIB</div>
                            </div>
                            <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                        </div>

                        <div class="transaction-body">
                            <div class="transaction-details">
                                <div class="detail-item">
                                    <div class="detail-label">Item</div>
                                    <div class="detail-value"><?= $trans['item_count'] ?> Produk</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Metode</div>
                                    <div class="detail-value"><?= ucfirst($trans['payment_type'] ?? 'Midtrans') ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Total</div>
                                    <div class="detail-value amount">Rp <?= number_format($trans['gross_amount'], 0, ',', '.') ?></div>
                                </div>
                            </div>

                            <a href="generate_invoice.php?order_id=<?= urlencode($trans['order_id']) ?>" target="_blank" class="btn-invoice">
                                📄 Lihat Invoice
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php else: ?>
                <div class="empty-state">
                    <div class="icon">☕</div>
                    <h3>Belum Ada Transaksi</h3>
                    <p>Anda belum melakukan pembelian apapun di CoffeeBlend</p>
                    <a href="menu.php" class="btn-shop">Mulai Belanja</a>
                </div>
            <?php endif; ?>
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
    <script src="js/scrollax.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>