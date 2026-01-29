<?php
session_start();
include 'config.php';

// Check if admin is logged in (simplified check based on existing files)
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch reviews
$reviews = [];
if ($koneksi) {
    // Ensure table exists (Auto-fix)
    $checkTable = mysqli_query($koneksi, "SHOW TABLES LIKE 'tabel_review'");
    if (mysqli_num_rows($checkTable) == 0) {
        mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS tabel_review (
            id_review INT AUTO_INCREMENT PRIMARY KEY,
            id_transaksi INT NOT NULL,
            order_id VARCHAR(50) NOT NULL,
            id_product INT NOT NULL,
            id_user INT NOT NULL,
            rating INT NOT NULL,
            review_text TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (order_id),
            INDEX (id_product),
            INDEX (id_user)
        )");
    }

    $query = "SELECT r.*, p.nama_product, p.gambar, u.username, u.email 
              FROM tabel_review r
              LEFT JOIN tabel_product p ON r.id_product = p.id_product
              LEFT JOIN tabel_user u ON r.id_user = u.id_user
              ORDER BY r.created_at DESC";
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reviews[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sistem Admin - Ulasan Produk</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        .star-rating {
            color: #ffc107;
        }

        .review-text {
            font-style: italic;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #4e73df;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'topbar.php'; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Ulasan Produk</h1>
                    <p class="mb-4">Daftar ulasan dan rating yang diberikan oleh customer untuk produk yang telah dibeli.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Ulasan Customer</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Utama</th>
                                            <th>Produk</th>
                                            <th>Rating</th>
                                            <th>Ulasan</th>
                                            <th>Order ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($reviews)): ?>
                                            <?php foreach ($reviews as $r): ?>
                                                <tr>
                                                    <td style="white-space:nowrap;"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                                                    <td>
                                                        <div class="font-weight-bold"><?= htmlspecialchars($r['username']) ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($r['email']) ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if (!empty($r['gambar'])): ?>
                                                                <img src="../images/<?= htmlspecialchars($r['gambar']) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                                            <?php else: ?>
                                                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 4px; margin-right: 10px; display:flex; align-items:center; justify-content:center;">
                                                                    <i class="fas fa-box text-gray-400"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?= htmlspecialchars($r['nama_product']) ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="star-rating">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="<?= ($i <= $r['rating']) ? 'fas' : 'far' ?> fa-star"></i>
                                                            <?php endfor; ?>
                                                            <span class="ml-1 text-gray-600 extra-small">(<?= $r['rating'] ?>)</span>
                                                        </div>
                                                    </td>
                                                    <td width="35%">
                                                        <?php if (!empty($r['review_text'])): ?>
                                                            <div class="review-text">
                                                                "<?= htmlspecialchars($r['review_text']) ?>"
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted font-italic">- Tidak ada ulasan tertulis -</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><code><?= htmlspecialchars($r['order_id']) ?></code></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada ulasan yang masuk.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'footer.php'; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>