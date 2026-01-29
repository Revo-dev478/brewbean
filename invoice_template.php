<?php

/**
 * INVOICE TEMPLATE - Browser Print Version WITH NAVBAR
 * Template HTML untuk invoice - langsung di-print dari browser
 * File ini di-include oleh generate_invoice.php
 */

// Data yang diharapkan dari parent script:
// $invoice_data - array dengan data transaksi
// $items - array dengan detail items
// $customer - array dengan data customer
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #<?= htmlspecialchars($invoice_data['order_id']) ?> - CoffeeBlend</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">

    <!-- CSS for Navbar -->
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background: #f5f5f5;
            line-height: 1.6;
        }

        .invoice-page {
            padding-top: 100px;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a0f0a 0%, #3e2723 50%, #5d4037 100%);
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            margin-bottom: 50px;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #c49b63;
            padding-bottom: 20px;
        }

        .brand h1 {
            font-size: 28px;
            color: #3e2723;
            margin: 0;
            font-weight: 800;
            text-transform: uppercase;
        }

        .brand small {
            color: #c49b63;
            font-size: 14px;
            letter-spacing: 2px;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-title {
            font-size: 24px;
            color: #c49b63;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-number {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* Info Section */
        .info-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
            padding: 15px;
            background: #f8f5f1;
            border-radius: 8px;
        }

        .info-box.right {
            text-align: right;
        }

        .info-label {
            font-size: 11px;
            color: #8d6e63;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 13px;
            color: #3e2723;
            font-weight: 600;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-success {
            background: #d4edda;
            color: #28a745;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead th {
            background: #3e2723;
            color: #fff;
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }

        .items-table thead th:last-child {
            border-radius: 0 8px 0 0;
            text-align: right;
        }

        .items-table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid #efebe9;
            font-size: 12px;
        }

        .items-table tbody td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        /* Summary */
        .summary-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-note {
            flex: 1;
            padding-right: 20px;
        }

        .summary-box {
            flex: 1;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table tr td {
            padding: 8px 15px;
            font-size: 13px;
        }

        .summary-table tr td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .summary-table tr.total {
            background: #c49b63;
            color: #fff;
        }

        .summary-table tr.total td {
            padding: 12px 15px;
            font-size: 15px;
            font-weight: 700;
        }

        .summary-table tr.total td:first-child {
            border-radius: 8px 0 0 8px;
        }

        .summary-table tr.total td:last-child {
            border-radius: 0 8px 8px 0;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #efebe9;
            color: #8d6e63;
            font-size: 11px;
        }

        .invoice-footer .thank-you {
            font-size: 16px;
            color: #c49b63;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Print Button - Hidden on Print */
        .print-actions {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f5f1;
            border-radius: 12px;
        }

        .btn-print {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #3e2723 0%, #5d4037 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            margin: 0 5px;
        }

        .btn-print:hover {
            background: linear-gradient(135deg, #5d4037 0%, #6d4c41 100%);
            color: white;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 30px;
            background: transparent;
            color: #5d4037;
            border: 2px solid #c49b63;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            margin: 0 5px;
        }

        .btn-back:hover {
            background: #c49b63;
            color: white;
        }

        .print-hint {
            margin-top: 10px;
            color: #8d6e63;
            font-size: 12px;
        }

        /* Navbar override */
        .ftco-navbar-light.scrolled {
            background: #000 !important;
        }

        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: #fff;
            }

            .ftco-navbar-light,
            nav {
                display: none !important;
            }

            .invoice-page {
                padding-top: 0;
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            .print-actions {
                display: none !important;
            }
        }

        @media (max-width: 768px) {

            .info-section,
            .summary-section {
                flex-direction: column;
            }

            .invoice-header {
                flex-direction: column;
                text-align: center;
            }

            .invoice-info {
                text-align: center;
                margin-top: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'partials/navbar.php'; ?>

    <div class="invoice-page">
        <div class="invoice-container">
            <!-- Print Actions (Hidden on Print) -->
            <div class="print-actions">
                <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
                <a href="riwayat_pemesanan.php" class="btn-back">📋 Kembali ke Pesanan</a>
                <p class="print-hint">Klik Print lalu pilih "Save as PDF" untuk menyimpan sebagai file PDF</p>
            </div>

            <!-- Header -->
            <div class="invoice-header">
                <div class="brand">
                    <h1>Coffee<small>Blend</small></h1>
                </div>
                <div class="invoice-info">
                    <div class="invoice-title">Invoice</div>
                    <div class="invoice-number">INV-<?= htmlspecialchars($invoice_data['order_id']) ?></div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="info-section">
                <div class="info-box">
                    <div class="info-label">Tagihan Kepada</div>
                    <div class="info-value"><?= htmlspecialchars($customer['username']) ?></div>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        <?= htmlspecialchars($customer['email']) ?><br>
                        <?= htmlspecialchars($customer['phone']) ?>
                    </div>
                </div>
                <div class="info-box right">
                    <div class="info-label">Tanggal Transaksi</div>
                    <div class="info-value"><?= date('d F Y', strtotime($invoice_data['transaction_time'])) ?></div>
                    <div style="margin-top: 10px;">
                        <div class="info-label">Status</div>
                        <?php
                        $status = strtolower($invoice_data['transaction_status']);
                        // FIXED: Include 'success' in the status check
                        $status_class = in_array($status, ['settlement', 'capture', 'success']) ? 'status-success' : 'status-pending';
                        $status_text = in_array($status, ['settlement', 'capture', 'success']) ? 'LUNAS' : 'PENDING';
                        ?>
                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Produk</th>
                        <th style="width: 15%;">Harga</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 20%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subtotal_items = 0;
                    if (!empty($items)):
                        foreach ($items as $item):
                            $subtotal_items += $item['subtotal'];
                    ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                            </tr>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Summary Section -->
            <div class="summary-section">
                <div class="summary-note">
                    <div class="info-label">Catatan</div>
                    <p style="font-size: 12px; color: #666;">
                        Terima kasih telah berbelanja di CoffeeBlend.
                        Invoice ini merupakan bukti pembayaran yang sah.
                    </p>
                </div>
                <div class="summary-box">
                    <table class="summary-table">
                        <tr>
                            <td>Subtotal</td>
                            <td>Rp <?= number_format($subtotal_items, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td>Ongkos Kirim</td>
                            <td>Rp <?= number_format($invoice_data['ongkir'], 0, ',', '.') ?></td>
                        </tr>
                        <tr class="total">
                            <td>Total</td>
                            <td>Rp <?= number_format($invoice_data['gross_amount'], 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <div class="thank-you">☕ Terima Kasih!</div>
                <p>CoffeeBlend - Premium Coffee Experience</p>
                <p style="margin-top: 5px;">Jika ada pertanyaan, hubungi kami di support@coffeeblend.id</p>
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
    <script src="js/scrollax.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>