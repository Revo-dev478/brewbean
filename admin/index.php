<?php
session_start();
include 'config.php';

// ===================== STATISTICS QUERIES =====================

// Total Revenue (settlement only)
$queryRevenue = "SELECT COALESCE(SUM(gross_amount), 0) as total_revenue 
                 FROM transaksi_midtrans 
                 WHERE transaction_status = 'settlement'";
$resultRevenue = mysqli_query($koneksi, $queryRevenue);
$totalRevenue = mysqli_fetch_assoc($resultRevenue)['total_revenue'];

// Total Transactions
$queryTotalTrans = "SELECT COUNT(*) as total FROM transaksi_midtrans";
$resultTotalTrans = mysqli_query($koneksi, $queryTotalTrans);
$totalTransactions = mysqli_fetch_assoc($resultTotalTrans)['total'];

// Pending Transactions
$queryPending = "SELECT COUNT(*) as pending FROM transaksi_midtrans WHERE transaction_status = 'pending'";
$resultPending = mysqli_query($koneksi, $queryPending);
$pendingTransactions = mysqli_fetch_assoc($resultPending)['pending'];

// Total Users
$queryUsers = "SELECT COUNT(*) as total FROM tabel_user";
$resultUsers = mysqli_query($koneksi, $queryUsers);
$totalUsers = mysqli_fetch_assoc($resultUsers)['total'];

// Monthly Revenue for Chart (Last 6 months)
$monthlyData = [];
$monthLabels = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthLabels[] = date('M Y', strtotime("-$i months"));

    $queryMonthly = "SELECT COALESCE(SUM(gross_amount), 0) as revenue 
                     FROM transaksi_midtrans 
                     WHERE transaction_status = 'settlement' 
                     AND DATE_FORMAT(transaction_time, '%Y-%m') = '$month'";
    $resultMonthly = mysqli_query($koneksi, $queryMonthly);
    $monthlyData[] = (float) mysqli_fetch_assoc($resultMonthly)['revenue'];
}

// Transaction Status Distribution
$statusLabels = [];
$statusData = [];
$statusColors = [];
$colorMap = [
    'settlement' => '#28a745',
    'pending' => '#ffc107',
    'cancel' => '#dc3545',
    'expire' => '#6c757d',
    'deny' => '#e74c3c',
    'failure' => '#c0392b'
];

$queryStatus = "SELECT transaction_status, COUNT(*) as count FROM transaksi_midtrans GROUP BY transaction_status";
$resultStatus = mysqli_query($koneksi, $queryStatus);
while ($row = mysqli_fetch_assoc($resultStatus)) {
    $statusLabels[] = ucfirst($row['transaction_status']);
    $statusData[] = (int) $row['count'];
    $statusColors[] = isset($colorMap[$row['transaction_status']]) ? $colorMap[$row['transaction_status']] : '#999999';
}

// Recent Transactions
$queryRecent = "SELECT t.order_id, t.gross_amount, t.transaction_status, t.transaction_time, u.username 
                FROM transaksi_midtrans t 
                LEFT JOIN tabel_user u ON t.id_user = u.id_user 
                ORDER BY t.transaction_time DESC LIMIT 5";
$resultRecent = mysqli_query($koneksi, $queryRecent);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <!-- CSS SB Admin -->
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="css/sb-admin-2.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.revenue {
            border-left-color: #28a745;
        }

        .stat-card.transactions {
            border-left-color: #007bff;
        }

        .stat-card.pending {
            border-left-color: #ffc107;
        }

        .stat-card.users {
            border-left-color: #17a2b8;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-settlement {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancel,
        .status-expire,
        .status-deny,
        .status-failure {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- End Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar -->
                <?php include 'topbar.php'; ?>
                <!-- End Topbar -->

                <!-- MAIN PAGE -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard Keuangan</h1>
                    </div>

                    <!-- Stats Cards Row -->
                    <div class="row">

                        <!-- Total Revenue Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card revenue shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Pendapatan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rp <?= number_format($totalRevenue, 0, ',', '.') ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Transactions Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card transactions shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Transaksi</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= number_format($totalTransactions) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Transactions Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card pending shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Transaksi Pending</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= number_format($pendingTransactions) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Users Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card users shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total User</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= number_format($totalUsers) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <!-- Revenue Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafik Pendapatan (6 Bulan Terakhir)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="revenueChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Distribution Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Status Transaksi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="statusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                    <th>Waktu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (mysqli_num_rows($resultRecent) > 0): ?>
                                                    <?php while ($row = mysqli_fetch_assoc($resultRecent)): ?>
                                                        <tr>
                                                            <td><code><?= htmlspecialchars($row['order_id']) ?></code></td>
                                                            <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
                                                            <td>Rp <?= number_format($row['gross_amount'], 0, ',', '.') ?></td>
                                                            <td>
                                                                <span class="status-badge status-<?= $row['transaction_status'] ?>">
                                                                    <?= ucfirst($row['transaction_status']) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= date('d M Y H:i', strtotime($row['transaction_time'])) ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">Belum ada transaksi</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- END MAIN PAGE -->

            </div>

            <?php include 'footer.php'; ?>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthLabels) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($monthlyData) ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($statusLabels) ?>,
                datasets: [{
                    data: <?= json_encode($statusData) ?>,
                    backgroundColor: <?= json_encode($statusColors) ?>,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
    </script>

</body>

</html>