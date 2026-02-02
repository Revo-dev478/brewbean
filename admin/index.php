<?php
session_start();
include 'config.php';
require_once '../auth.php'; // Helper auth

// Pastikan yang akses adalah admin
requireAdmin();

// ===================== STATISTICS QUERIES =====================

// Initialize default values
$totalRevenue = 0;
$totalTransactions = 0;
$pendingTransactions = 0;
$totalUsers = 0;
$monthlyData = [];
$monthLabels = [];
$statusLabels = [];
$statusData = [];
$statusColors = [];
$resultRecent = false;

if ($koneksi) {
    // Total Revenue (settlement only)
    $queryRevenue = "SELECT COALESCE(SUM(gross_amount), 0) as total_revenue
FROM transaksi_midtrans
WHERE transaction_status = 'settlement'";
    $resultRevenue = mysqli_query($koneksi, $queryRevenue);
    if ($resultRevenue) {
        $totalRevenue = mysqli_fetch_assoc($resultRevenue)['total_revenue'];
    }

    // Total Transactions
    $queryTotalTrans = "SELECT COUNT(*) as total FROM transaksi_midtrans";
    $resultTotalTrans = mysqli_query($koneksi, $queryTotalTrans);
    if ($resultTotalTrans) {
        $totalTransactions = mysqli_fetch_assoc($resultTotalTrans)['total'];
    }

    // Pending Transactions
    $queryPending = "SELECT COUNT(*) as pending FROM transaksi_midtrans WHERE transaction_status = 'pending'";
    $resultPending = mysqli_query($koneksi, $queryPending);
    if ($resultPending) {
        $pendingTransactions = mysqli_fetch_assoc($resultPending)['pending'];
    }

    // Total Users
    $queryUsers = "SELECT COUNT(*) as total FROM tabel_user";
    $resultUsers = mysqli_query($koneksi, $queryUsers);
    if ($resultUsers) {
        $totalUsers = mysqli_fetch_assoc($resultUsers)['total'];
    }

    // Monthly Revenue for Chart (Last 6 months)
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthLabels[] = date('M Y', strtotime("-$i months"));

        $queryMonthly = "SELECT COALESCE(SUM(gross_amount), 0) as revenue
FROM transaksi_midtrans
WHERE transaction_status = 'settlement'
AND DATE_FORMAT(transaction_time, '%Y-%m') = '$month'";
        $resultMonthly = mysqli_query($koneksi, $queryMonthly);
        if ($resultMonthly) {
            $monthlyData[] = (float) mysqli_fetch_assoc($resultMonthly)['revenue'];
        } else {
            $monthlyData[] = 0;
        }
    }

    // Transaction Status Distribution
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
    if ($resultStatus) {
        while ($row = mysqli_fetch_assoc($resultStatus)) {
            $statusLabels[] = ucfirst($row['transaction_status']);
            $statusData[] = (int) $row['count'];
            $statusColors[] = isset($colorMap[$row['transaction_status']]) ? $colorMap[$row['transaction_status']] : '#999999';
        }
    }

    // Recent Transactions
    $queryRecent = "SELECT t.order_id, t.gross_amount, t.transaction_status, t.transaction_time, u.username
FROM transaksi_midtrans t
LEFT JOIN tabel_user u ON t.id_user = u.id_user
ORDER BY t.transaction_time DESC LIMIT 5";
    $resultRecent = mysqli_query($koneksi, $queryRecent);

    // ===================== DAILY, WEEKLY, MONTHLY EARNINGS =====================

    // Today's Earnings
    $today = date('Y-m-d');
    $queryDaily = "SELECT COALESCE(SUM(gross_amount), 0) as daily_revenue, COUNT(*) as daily_count
FROM transaksi_midtrans
WHERE transaction_status IN ('settlement', 'capture', 'success')
AND DATE(transaction_time) = '$today'";
    $resultDaily = mysqli_query($koneksi, $queryDaily);
    $dailyData = $resultDaily ? mysqli_fetch_assoc($resultDaily) : ['daily_revenue' => 0, 'daily_count' => 0];
    $dailyRevenue = $dailyData['daily_revenue'];
    $dailyCount = $dailyData['daily_count'];

    // This Week's Earnings (Monday to Sunday)
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $weekEnd = date('Y-m-d', strtotime('sunday this week'));
    $queryWeekly = "SELECT COALESCE(SUM(gross_amount), 0) as weekly_revenue, COUNT(*) as weekly_count
FROM transaksi_midtrans
WHERE transaction_status IN ('settlement', 'capture', 'success')
AND DATE(transaction_time) BETWEEN '$weekStart' AND '$weekEnd'";
    $resultWeekly = mysqli_query($koneksi, $queryWeekly);
    $weeklyData = $resultWeekly ? mysqli_fetch_assoc($resultWeekly) : ['weekly_revenue' => 0, 'weekly_count' => 0];
    $weeklyRevenue = $weeklyData['weekly_revenue'];
    $weeklyCount = $weeklyData['weekly_count'];

    // This Month's Earnings
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');
    $queryMonthlyEarn = "SELECT COALESCE(SUM(gross_amount), 0) as monthly_revenue, COUNT(*) as monthly_count
FROM transaksi_midtrans
WHERE transaction_status IN ('settlement', 'capture', 'success')
AND DATE(transaction_time) BETWEEN '$monthStart' AND '$monthEnd'";
    $resultMonthlyEarn = mysqli_query($koneksi, $queryMonthlyEarn);
    $monthlyEarnData = $resultMonthlyEarn ? mysqli_fetch_assoc($resultMonthlyEarn) : ['monthly_revenue' => 0, 'monthly_count' => 0];
    $monthlyRevenue = $monthlyEarnData['monthly_revenue'];
    $monthlyCount = $monthlyEarnData['monthly_count'];

    // Daily breakdown for the last 7 days (for chart)
    $dailyLabels = [];
    $dailyChartData = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime("-$i days"));
        $dailyLabels[] = date('d M', strtotime("-$i days"));

        $qDay = "SELECT COALESCE(SUM(gross_amount), 0) as rev FROM transaksi_midtrans
WHERE transaction_status IN ('settlement', 'capture', 'success') AND DATE(transaction_time) = '$day'";
        $rDay = mysqli_query($koneksi, $qDay);
        $dailyChartData[] = $rDay ? (float)mysqli_fetch_assoc($rDay)['rev'] : 0;
    }
} else {
    // If DB is offline, defaults are already set to 0/empty.
    // We could add an error message variable to display in the dashboard if desired.
    $db_dashboard_error = "Database Connection Failed";
    $dailyRevenue = 0;
    $dailyCount = 0;
    $weeklyRevenue = 0;
    $weeklyCount = 0;
    $monthlyRevenue = 0;
    $monthlyCount = 0;
    $dailyLabels = [];
    $dailyChartData = [];
}
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

        .earning-card {
            border-radius: 12px;
            overflow: hidden;
        }

        .earning-card .card-body {
            position: relative;
        }

        .earning-card .earning-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.2;
        }

        .earning-card.daily {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .earning-card.weekly {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .earning-card.monthly {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .earning-card .text-white-75 {
            color: rgba(255, 255, 255, 0.75);
        }

        .report-table th {
            background: #f8f9fc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
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

                    <!-- Earnings Cards (Daily, Weekly, Monthly) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-gray-800 mb-3"><i class="fas fa-chart-line mr-2"></i>Laporan Penghasilan</h5>
                        </div>

                        <!-- Daily Earnings -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card earning-card daily shadow h-100">
                                <div class="card-body text-white py-4">
                                    <div class="earning-icon"><i class="fas fa-sun"></i></div>
                                    <div class="text-white-75 text-uppercase mb-1" style="font-size: 11px; letter-spacing: 1px;">Hari Ini</div>
                                    <div class="h4 mb-1 font-weight-bold">Rp <?= number_format($dailyRevenue, 0, ',', '.') ?></div>
                                    <div class="text-white-75" style="font-size: 13px;">
                                        <i class="fas fa-shopping-bag mr-1"></i><?= $dailyCount ?> Transaksi
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Earnings -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card earning-card weekly shadow h-100">
                                <div class="card-body text-white py-4">
                                    <div class="earning-icon"><i class="fas fa-calendar-week"></i></div>
                                    <div class="text-white-75 text-uppercase mb-1" style="font-size: 11px; letter-spacing: 1px;">Minggu Ini</div>
                                    <div class="h4 mb-1 font-weight-bold">Rp <?= number_format($weeklyRevenue, 0, ',', '.') ?></div>
                                    <div class="text-white-75" style="font-size: 13px;">
                                        <i class="fas fa-shopping-bag mr-1"></i><?= $weeklyCount ?> Transaksi
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Earnings -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card earning-card monthly shadow h-100">
                                <div class="card-body text-white py-4">
                                    <div class="earning-icon"><i class="fas fa-calendar-alt"></i></div>
                                    <div class="text-white-75 text-uppercase mb-1" style="font-size: 11px; letter-spacing: 1px;">Bulan Ini (<?= date('F Y') ?>)</div>
                                    <div class="h4 mb-1 font-weight-bold">Rp <?= number_format($monthlyRevenue, 0, ',', '.') ?></div>
                                    <div class="text-white-75" style="font-size: 13px;">
                                        <i class="fas fa-shopping-bag mr-1"></i><?= $monthlyCount ?> Transaksi
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

                    <!-- Daily Revenue Chart (Last 7 Days) -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-2"></i>Pendapatan 7 Hari Terakhir</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="dailyRevenueChart"></canvas>
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
                                                <?php if ($resultRecent && mysqli_num_rows($resultRecent) > 0): ?>
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

        // Daily Revenue Chart (Last 7 Days)
        const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(dailyRevenueCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($dailyLabels) ?>,
                datasets: [{
                    label: 'Pendapatan Harian (Rp)',
                    data: <?= json_encode($dailyChartData) ?>,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(118, 75, 162, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(245, 87, 108, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(0, 242, 254, 0.8)',
                        'rgba(40, 167, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgb(102, 126, 234)',
                        'rgb(118, 75, 162)',
                        'rgb(240, 147, 251)',
                        'rgb(245, 87, 108)',
                        'rgb(79, 172, 254)',
                        'rgb(0, 242, 254)',
                        'rgb(40, 167, 69)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8
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
                                if (value >= 1000000) {
                                    return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                } else if (value >= 1000) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                }
                                return 'Rp ' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>