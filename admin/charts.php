<?php
// admin/charts.php
session_start();
require_once '../config.php';

// Proteksi login
if (empty($_SESSION['username'])) {
    // header("Location: login.php"); // Uncomment jika perlu login
}

// ---------------------------------------------------------
// 1. DATA PENDAPATAN BULANAN (Area Chart)
// ---------------------------------------------------------
// Ambil data 12 bulan terakhir
$revenueLabels = [];
$revenueData = [];

$queryRevenue = "SELECT 
                    DATE_FORMAT(transaction_time, '%M %Y') as month_label, 
                    DATE_FORMAT(transaction_time, '%Y-%m') as month_sort,
                    SUM(gross_amount) as total 
                 FROM transaksi_midtrans 
                 WHERE transaction_status IN ('settlement', 'capture') 
                 GROUP BY month_sort 
                 ORDER BY month_sort ASC 
                 LIMIT 12";
$resultRevenue = mysqli_query($koneksi, $queryRevenue);

while ($row = mysqli_fetch_assoc($resultRevenue)) {
    $revenueLabels[] = $row['month_label'];
    $revenueData[] = (int)$row['total'];
}

// ---------------------------------------------------------
// 2. DATA DISTRIBUSI STATUS (Pie Chart)
// ---------------------------------------------------------
$statusLabels = [];
$statusData = [];
$statusColors = [];

$queryStatus = "SELECT transaction_status, COUNT(*) as count FROM transaksi_midtrans GROUP BY transaction_status";
$resultStatus = mysqli_query($koneksi, $queryStatus);

while ($row = mysqli_fetch_assoc($resultStatus)) {
    $statusLabels[] = ucfirst($row['transaction_status']);
    $statusData[] = (int)$row['count'];
    
    // Warna custom sesuai status
    switch($row['transaction_status']) {
        case 'settlement': $statusColors[] = '#1cc88a'; break; // Green
        case 'capture': $statusColors[] = '#1cc88a'; break;
        case 'pending': $statusColors[] = '#f6c23e'; break; // Yellow
        case 'expire': $statusColors[] = '#858796'; break; // Grey
        case 'cancel': $statusColors[] = '#e74a3b'; break; // Red
        case 'failure': $statusColors[] = '#e74a3b'; break;
        default: $statusColors[] = '#4e73df'; // Blue
    }
}

// ---------------------------------------------------------
// 3. JUMLAH TRANSAKSI PER BULAN (Bar Chart)
// ---------------------------------------------------------
$trxCountLabels = [];
$trxCountData = [];

$queryCount = "SELECT 
                    DATE_FORMAT(transaction_time, '%M') as month_label, 
                    DATE_FORMAT(transaction_time, '%Y-%m') as month_sort,
                    COUNT(*) as total 
               FROM transaksi_midtrans 
               GROUP BY month_sort 
               ORDER BY month_sort ASC 
               LIMIT 6";
$resultCount = mysqli_query($koneksi, $queryCount);

while ($row = mysqli_fetch_assoc($resultCount)) {
    $trxCountLabels[] = $row['month_label'];
    $trxCountData[] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Dynamic Charts</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        
        <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                <!-- Topbar -->
                <?php include('topbar.php'); ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Laporan Grafik & Statistik</h1>
                    <p class="mb-4">Menampilkan ringkasan data transaksi secara real-time.</p>

                    <div class="row">
                        <!-- AREA CHART -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Pendapatan (Revenue)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                    <hr>
                                    Total pendapatan bulanan dari transaksi sukses.
                                </div>
                            </div>

                            <!-- BAR CHART -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Jumlah Transaksi Bulanan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="myBarChart"></canvas>
                                    </div>
                                    <hr>
                                    Jumlah order masuk per bulan (Semua Status).
                                </div>
                            </div>
                        </div>

                        <!-- PIE CHART -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Status Transaksi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <?php foreach($statusLabels as $i => $label): ?>
                                            <span class="mr-2">
                                                <i class="fas fa-circle" style="color: <?= $statusColors[$i] ?>"></i> <?= $label ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <hr>
                                    Proporsi status pesanan keseluruhan.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            
            <!-- Footer -->
            <?php include('footer.php'); ?>

        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- INJECT DYNAMIC DATA -->
    <script>
    // Set default config
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
      number = (number + '').replace(',', '').replace(' ', '');
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
          var k = Math.pow(10, prec);
          return '' + Math.round(n * k) / k;
        };
      s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
      }
      return s.join(dec);
    }

    // 1. AREA CHART (REVENUE)
    var ctxArea = document.getElementById("myAreaChart");
    var myAreaChart = new Chart(ctxArea, {
      type: 'line',
      data: {
        labels: <?= json_encode($revenueLabels) ?>,
        datasets: [{
          label: "Pendapatan",
          lineTension: 0.3,
          backgroundColor: "rgba(78, 115, 223, 0.05)",
          borderColor: "rgba(78, 115, 223, 1)",
          pointRadius: 3,
          pointBackgroundColor: "rgba(78, 115, 223, 1)",
          pointBorderColor: "rgba(78, 115, 223, 1)",
          pointHoverRadius: 3,
          pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
          pointHoverBorderColor: "rgba(78, 115, 223, 1)",
          pointHitRadius: 10,
          pointBorderWidth: 2,
          data: <?= json_encode($revenueData) ?>,
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
        scales: {
          xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
          yAxes: [{
            ticks: { maxTicksLimit: 5, padding: 10, callback: function(value, index, values) { return 'Rp ' + number_format(value); } },
            gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
          }],
        },
        legend: { display: false },
        tooltips: {
          backgroundColor: "rgb(255,255,255)", bodyFontColor: "#858796", titleMarginBottom: 10, titleFontColor: '#6e707e', titleFontSize: 14, borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, intersect: false, mode: 'index', caretPadding: 10,
          callbacks: { label: function(tooltipItem, chart) { var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || ''; return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel); } }
        }
      }
    });

    // 2. PIE CHART (STATUS)
    var ctxPie = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($statusLabels) ?>,
        datasets: [{
          data: <?= json_encode($statusData) ?>,
          backgroundColor: <?= json_encode($statusColors) ?>,
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: { backgroundColor: "rgb(255,255,255)", bodyFontColor: "#858796", borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, caretPadding: 10 },
        legend: { display: false },
        cutoutPercentage: 80,
      },
    });

    // 3. BAR CHART (TRANSACTION COUNT)
    var ctxBar = document.getElementById("myBarChart");
    var myBarChart = new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: <?= json_encode($trxCountLabels) ?>,
        datasets: [{
          label: "Jumlah Transaksi",
          backgroundColor: "#4e73df",
          hoverBackgroundColor: "#2e59d9",
          borderColor: "#4e73df",
          data: <?= json_encode($trxCountData) ?>,
        }],
      },
      options: {
        maintainAspectRatio: false,
        layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
        scales: {
          xAxes: [{ time: { unit: 'month' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 6 }, maxBarThickness: 25 }],
          yAxes: [{ ticks: { min: 0, maxTicksLimit: 5, padding: 10 }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
        },
        legend: { display: false },
        tooltips: {
          titleMarginBottom: 10, titleFontColor: '#6e707e', titleFontSize: 14, backgroundColor: "rgb(255,255,255)", bodyFontColor: "#858796", borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, caretPadding: 10,
          callbacks: { label: function(tooltipItem, chart) { var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || ''; return datasetLabel + ': ' + number_format(tooltipItem.yLabel); } }
        },
      }
    });
    </script>
</body>
</html>
