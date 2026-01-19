<?php

/**
 * RIWAYAT PEMESANAN - Order History with Delivery Tracking
 * User dapat melihat pesanan mereka dan konfirmasi penerimaan
 */
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';



// Query pesanan user (hanya yang payment success)
// Note: delivery_status dan delivery_confirmed_at tidak ada di DB production
$query = "SELECT 
            t.id_transaksi,
            t.order_id,
            t.transaction_status,
            t.payment_type,
            t.gross_amount,
            t.transaction_time,
            t.settlement_time,
            c.status_checkout,
            u.email as user_email,
            u.username
          FROM transaksi_midtrans t
          LEFT JOIN checkout c ON t.order_id = c.order_id
          LEFT JOIN tabel_user u ON t.id_user = u.id_user
          WHERE t.id_user = ? 
          AND t.transaction_status IN ('settlement', 'capture', 'pending')
          ORDER BY t.transaction_time DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Add default delivery status for all orders (kolom tidak ada di DB)
foreach ($pesanan as &$p) {
    $p['delivery_status'] = 'processing'; // default
    $p['delivery_confirmed_at'] = null;
}
unset($p);
// $koneksi->close(); // Connection kept open for fetching items in the loop below
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Blend | Pesanan Saya</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #0f0f0f;
        }

        /* Glassmorphism Navbar */
        .ftco_navbar {
            background: rgba(18, 18, 18, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(196, 155, 99, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: 800;
            text-transform: uppercase;
        }

        .navbar-brand small {
            color: #c49b63 !important;
            display: block;
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: none;
            margin-top: -5px;
        }

        .ftco_navbar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 14px;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            padding-left: 20px;
            padding-right: 20px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .ftco_navbar .nav-item.active .nav-link {
            color: #c49b63 !important;
        }

        /* Order Card Styling */
        .order-card {
            background: rgba(30, 30, 30, 0.6) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(196, 155, 99, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s;
        }

        .order-card:hover {
            transform: translateY(-5px);
            border-color: rgba(196, 155, 99, 0.5);
        }

        .order-header {
            border-bottom: 1px solid rgba(196, 155, 99, 0.2);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .order-id {
            color: #c49b63;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .order-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 13px;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-processing {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-shipped {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            border: 1px solid rgba(0, 123, 255, 0.3);
        }

        .status-delivered {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-confirmed {
            background: rgba(196, 155, 99, 0.2);
            color: #c49b63;
            border: 1px solid #c49b63;
        }

        .item-row {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .item-name {
            color: #fff;
            font-weight: 600;
            font-size: 15px;
        }

        .item-meta {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .order-amount {
            color: #c49b63;
            font-size: 24px;
            font-weight: 700;
        }

        .payment-info {
            text-align: right;
        }

        .payment-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            text-transform: uppercase;
        }

        .payment-value {
            color: #fff;
            font-weight: 600;
        }

        /* Timeline Styling */
        .delivery-timeline {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .timeline-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 12px;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .timeline-icon.active {
            background: #c49b63;
            color: #000;
            box-shadow: 0 0 10px rgba(196, 155, 99, 0.5);
        }

        .timeline-icon.inactive {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.3);
        }

        .timeline-label {
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .timeline-time {
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
        }

        .btn-primary {
            background: #c49b63 !important;
            border: 1px solid #c49b63 !important;
            color: #000 !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 25px;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: transparent !important;
            color: #c49b63 !important;
        }

        .empty-state {
            background: rgba(30, 30, 30, 0.6);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 80px 40px;
            text-align: center;
            border: 1px solid rgba(196, 155, 99, 0.2);
        }

        .empty-state i {
            display: block;
            font-size: 80px;
            color: #c49b63;
            margin-bottom: 30px;
        }

        .empty-state h3 {
            color: #fff;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    <?php include 'partials/navbar.php'; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12 ftco-animate">
                    <?php if (count($pesanan) > 0): ?>
                        <?php foreach ($pesanan as $item): ?>
                            <?php
                            $delivery_status = strtolower($item['delivery_status']);
                            if ($delivery_status == 'confirmed') {
                                $badge_class = 'status-confirmed';
                                $status_text = 'Selesai';
                            } elseif ($delivery_status == 'delivered') {
                                $badge_class = 'status-delivered';
                                $status_text = 'Terkirim';
                            } elseif ($delivery_status == 'shipped') {
                                $badge_class = 'status-shipped';
                                $status_text = 'Dikirim';
                            } else {
                                $badge_class = 'status-processing';
                                $status_text = 'Diproses';
                            }
                            ?>

                            <div class="order-card">
                                <div class="order-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="order-id">#<?= htmlspecialchars($item['order_id']) ?></div>
                                        <div class="order-date">
                                            <span class="ion-ios-calendar mr-2"></span>
                                            <?= date('d M Y, H:i', strtotime($item['transaction_time'])) ?> WIB
                                        </div>
                                    </div>
                                    <span class="status-badge <?= $badge_class ?>"><?= $status_text ?></span>
                                </div>

                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="order-items">
                                            <?php
                                            // Note: checkout_item table tidak ada di DB production
                                            // Tampilkan placeholder saja
                                            ?>
                                                <div class="item-row d-flex align-items-center">
                                                    <div class="img mr-3" style="background: rgba(196,155,99,0.2); width: 60px; height: 60px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <span class="ion-ios-cube" style="font-size: 24px; color: #c49b63;"></span>
                                                    </div>
                                                    <div class="text">
                                                        <div class="item-name">Pesanan #<?= htmlspecialchars($item['order_id']) ?></div>
                                                        <div class="item-meta">Total: Rp <?= number_format($item['gross_amount'], 0, ',', '.') ?></div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="order-summary text-right">
                                            <div class="order-amount mb-2">
                                                Rp <?= number_format($item['gross_amount'], 0, ',', '.') ?>
                                            </div>
                                            <div class="payment-info">
                                                <div class="payment-label">Metode Pembayaran</div>
                                                <div class="payment-value"><?= htmlspecialchars(ucfirst($item['payment_type'])) ?></div>
                                            </div>
                                        </div>

                                        <div class="delivery-timeline">
                                            <div class="timeline-item">
                                                <div class="timeline-icon active"><span class="ion-ios-checkmark"></span></div>
                                                <div class="timeline-content">
                                                    <div class="timeline-label">Pembayaran Berhasil</div>
                                                    <div class="timeline-time"><?= $item['settlement_time'] ? date('d M Y, H:i', strtotime($item['settlement_time'])) : '-' ?></div>
                                                </div>
                                            </div>

                                            <div class="timeline-item">
                                                <div class="timeline-icon <?= in_array($delivery_status, ['processing', 'shipped', 'delivered', 'confirmed']) ? 'active' : 'inactive' ?>"><span class="ion-ios-cube"></span></div>
                                                <div class="timeline-content">
                                                    <div class="timeline-label">Pesanan Diproses</div>
                                                </div>
                                            </div>

                                            <div class="timeline-item">
                                                <div class="timeline-icon <?= in_array($delivery_status, ['shipped', 'delivered', 'confirmed']) ? 'active' : 'inactive' ?>"><span class="ion-ios-car"></span></div>
                                                <div class="timeline-content">
                                                    <div class="timeline-label">Dalam Pengiriman</div>
                                                </div>
                                            </div>

                                            <div class="timeline-item">
                                                <div class="timeline-icon <?= in_array($delivery_status, ['delivered', 'confirmed']) ? 'active' : 'inactive' ?>"><span class="ion-ios-home"></span></div>
                                                <div class="timeline-content">
                                                    <div class="timeline-label">Pesanan Sampai</div>
                                                    <?php if ($delivery_status == 'confirmed'): ?>
                                                        <div class="timeline-time"><?= date('d M Y, H:i', strtotime($item['delivery_confirmed_at'])) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($delivery_status == 'delivered'): ?>
                                            <div class="mt-4 text-right">
                                                <button class="btn btn-primary" onclick="konfirmasiPesanan('<?= $item['order_id'] ?>')">
                                                    Konfirmasi Terima Pesanan
                                                </button>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($delivery_status == 'confirmed'): ?>
                                            <div class="mt-4 text-right" style="color: #c49b63; font-weight: 600;">
                                                <span class="ion-ios-checkmark-circle mr-2"></span> Pesanan Selesai
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="ion-ios-paper-plane"></i>
                            <h3>Belum Ada Pesanan</h3>
                            <p>Sepertinya Anda belum melakukan pemesanan apapun.</p>
                            <a href="menu.php" class="btn btn-primary px-5 py-3">Mulai Belanja</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div id="ftco-loader" class="show fullscreen">
        <svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#F96D00" />
        </svg>
    </div>

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

    <script>
        function konfirmasiPesanan(orderId) {
            Swal.fire({
                title: 'Konfirmasi Penerimaan?',
                text: "Pastikan pesanan sudah Anda terima dengan baik.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c49b63',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Sudah Terima!',
                cancelButtonText: 'Batal',
                background: '#1a1a1a',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'konfirmasi_pesanan.php',
                        type: 'POST',
                        data: {
                            order_id: orderId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Terima kasih telah berbelanja di Coffee Blend.',
                                    background: '#1a1a1a',
                                    color: '#fff',
                                    confirmButtonColor: '#c49b63'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>
```