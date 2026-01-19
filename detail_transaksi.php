<?php
session_start();

if (empty($_SESSION['id_user'])) {
    header("Location: login.php?redirect=detail_transaksi.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$order_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($order_id)) {
    header("Location: riwayat_pemesanan.php");
    exit;
}

// Koneksi database
$koneksi= new mysqli("localhost", "root", "", "db_brewbeans");
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

// Fetch transaksi detail
$query = "SELECT t.id_transaksi, t.order_id, t.transaction_status, t.payment_type, 
                 t.gross_amount, t.transaction_time, 
                 u.username, u.email as user_email,
                 c.customer_email, c.customer_phone, c.customer_address
          FROM transaksi_midtrans t
          LEFT JOIN tabel_user u ON t.id_user = u.id_user
          LEFT JOIN checkout c ON t.order_id = c.order_id
          WHERE t.order_id = ? AND t.id_user = ?";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("si", $order_id, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();
$stmt->close();

if (!$transaksi) {
    header("Location: riwayat_pemesanan.php");
    exit;
}

// Fetch item checkout
$queryItem = "SELECT * FROM checkout_item WHERE order_id = ?";
$stmtItem = $koneksi->prepare($queryItem);
$stmtItem->bind_param("s", $order_id);
$stmtItem->execute();
$resultItem = $stmtItem->get_result();
$items = $resultItem->fetch_all(MYSQLI_ASSOC);
$stmtItem->close();

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Detail Transaksi - CoffeeBlend</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .detail-card { background: #f9f9f9; padding: 30px; border-radius: 8px; margin-bottom: 20px; }
        .status-badge { font-size: 16px; padding: 8px 15px; }
        .item-table { margin-top: 20px; }
        .back-btn { margin-bottom: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light">
        <div class="container">
            <a class="navbar-brand" href="utama.php">Coffee<small>Blend</small></a>
        </div>
    </nav>

    <section class="ftco-section">
        <div class="container">
            <div class="back-btn">
                <a href="riwayat_pemesanan.php" class="btn btn-secondary">&larr; Kembali ke Pesanan Saya</a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h2>Detail Transaksi</h2>
                    
                    <div class="detail-card">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama:</strong><br><?= htmlspecialchars($transaksi['username']) ?></p>
                                <p><strong>Order ID:</strong><br><?= htmlspecialchars($transaksi['order_id']) ?></p>
                                <p><strong>Tanggal:</strong><br><?= date('d/m/Y H:i', strtotime($transaksi['transaction_time'])) ?></p>
                                <p><strong>Email:</strong><br><?= htmlspecialchars($transaksi['customer_email']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Alamat:</strong><br><?= htmlspecialchars($transaksi['customer_address']) ?></p>
                                <p><strong>Telepon:</strong><br><?= htmlspecialchars($transaksi['customer_phone']) ?></p>
                                <p><strong>Metode Pembayaran:</strong><br><?= htmlspecialchars($transaksi['payment_type']) ?></p>
                                <p><strong>Status:</strong><br>
                                    <?php
                                    $status = strtolower($transaksi['transaction_status']);
                                    $badgeClass = 'badge-warning';
                                    $statusText = ucfirst($status);
                                    
                                    if ($status === 'settlement') {
                                        $badgeClass = 'badge-success';
                                        $statusText = 'Berhasil';
                                    } elseif ($status === 'deny' || $status === 'cancel') {
                                        $badgeClass = 'badge-danger';
                                        $statusText = 'Gagal';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> status-badge"><?= $statusText ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <h3 class="mt-4">Detail Item</h3>
                    <div class="table-responsive item-table">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="detail-card">
                        <h4>Ringkasan Pembayaran</h4>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Pembayaran:</strong>
                            <span class="h5">Rp <?= number_format($transaksi['gross_amount'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
