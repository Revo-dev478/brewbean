<?php
session_start();

// Cek login
if (empty($_SESSION['id_user'])) {
    header("Location: login.php?redirect=riwayat_transaksi.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "db_brewbeans");
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

// ============================================
// SINKRONISASI STATUS TRANSAKSI DARI MIDTRANS
// ============================================
require_once 'sync_midtrans_status.php';

// Sync transaksi user dari Midtrans API
$syncResult = syncUserTransactions($koneksi, $id_user);

// Query dengan LEFT JOIN ke tabel_user - sekarang data sudah tersinkron
$query = "SELECT t.id_transaksi, t.order_id, t.transaction_status, t.payment_type, 
                 t.gross_amount, t.transaction_time,
                 u.email as nama_user
          FROM transaksi_midtrans t
          LEFT JOIN tabel_user u ON t.id_user = u.id_user
          WHERE t.id_user = ? 
          ORDER BY t.transaction_time DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$koneksi->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Riwayat Transaksi - CoffeeBlend</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .transaction-table { margin-top: 30px; }
        .badge-success { background-color: #28a745; }
        .badge-pending { background-color: #ffc107; color: black; }
        .badge-failed { background-color: #dc3545; }
        .btn-detail { padding: 5px 15px; font-size: 12px; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 60px; color: #ccc; margin-bottom: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="utama.php">Coffee<small>Blend</small></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav">
                <span class="oi oi-menu"></span> Menu
            </button>
            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="utama.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="menu.php" class="nav-link">Menu</a></li>
                    <li class="nav-item"><a href="riwayat_pemesanan.php" class="nav-link">Pesanan Saya</a></li>
                    <li class="nav-item"><a href="keranjang.php" class="nav-link">Cart</a></li>
                    <li class="nav-item"><span class="nav-link">Hi, <?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></span></li>
                    <li class="nav-item"><a href="logout.php?redirect=utama.php" class="nav-link">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-4">Riwayat Transaksi Anda</h2>

                    <?php if (count($transaksi) > 0): ?>
                        <div class="table-responsive transaction-table">
                            <table class="table table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Order ID</th>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transaksi as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><strong><?= htmlspecialchars($item['order_id']) ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($item['transaction_time'])) ?></td>
                                            <td>Rp <?= number_format($item['gross_amount'], 0, ',', '.') ?></td>
                                            <td><?= htmlspecialchars($item['payment_type']) ?></td>
                                            <td>
                                                <?php
                                                $status = strtolower($item['transaction_status']);
                                                $badgeClass = 'badge-pending';
                                                $statusText = ucfirst($status);
                                                
                                                if ($status === 'settlement' || $status === 'capture') {
                                                    $badgeClass = 'badge-success';
                                                    $statusText = 'Berhasil';
                                                } elseif ($status === 'deny' || $status === 'cancel' || $status === 'failure') {
                                                    $badgeClass = 'badge-failed';
                                                    $statusText = 'Gagal';
                                                } elseif ($status === 'expire') {
                                                    $badgeClass = 'badge-failed';
                                                    $statusText = 'Kadaluarsa';
                                                } elseif ($status === 'pending') {
                                                    $badgeClass = 'badge-pending';
                                                    $statusText = 'Menunggu Pembayaran';
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <a href="detail_transaksi.php?id=<?= urlencode($item['order_id']) ?>" 
                                                   class="btn btn-sm btn-info btn-detail">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p style="font-size: 18px; color: #666;">Belum ada riwayat transaksi</p>
                            <a href="menu.php" class="btn btn-primary mt-3">Mulai Belanja</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
