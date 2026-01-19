<?php
session_start();
include 'config.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteQuery = "DELETE FROM tabel_keranjang WHERE id_keranjang = '$id'";

    if (mysqli_query($koneksi, $deleteQuery)) {
        header("Location: tabel_keranjang.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($koneksi);
    }
}

// Query JOIN ke tabel user dan product
$sql = "
    SELECT 
        k.id_keranjang,
        k.id_user,
        k.id_product,
        k.qty,
        k.harga,
        k.subtotal,
        k.Tanggal AS tanggal_tambah,
        u.username,
        p.nama_product,
        p.gambar
    FROM tabel_keranjang k
    LEFT JOIN tabel_user u ON k.id_user = u.id_user
    LEFT JOIN tabel_product p ON k.id_product = p.id_product
    ORDER BY k.Tanggal DESC
";

$result = mysqli_query($koneksi, $sql);
if (!$result) {
    die("Query Error: " . mysqli_error($koneksi) . " | SQL: " . $sql);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - Data Keranjang</title>

    <!-- Custom fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        .sidebar {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            min-height: 100vh;
        }

        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            z-index: 1;
        }

        .sidebar-brand-icon {
            font-size: 2rem;
        }

        .sidebar-brand-text {
            margin-left: 0.5rem;
        }

        .sidebar .nav-item {
            position: relative;
        }

        .sidebar .nav-item .nav-link {
            text-align: left;
            padding: 1rem;
            width: 100%;
            color: rgba(255, 255, 255, 0.8);
        }

        .sidebar .nav-item .nav-link:hover,
        .sidebar .nav-item .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-item .nav-link i {
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 0 1rem 1rem;
        }

        .sidebar-heading {
            text-align: left;
            padding: 0 1rem;
            font-weight: 800;
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- End of Sidebar -->
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" role="button">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" role="button">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle" src="https://via.placeholder.com/60" style="width: 40px; height: 40px;">
                            </a>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Tabel Keranjang</h1>
                    <p class="mb-4">Data keranjang belanja dari seluruh user yang terdaftar di sistem.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Keranjang User</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama user</th>
                                            <th>Gambar</th>
                                            <th>Nama product</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                                <tr>
                                                    <td><?= $row['id_keranjang'] ?></td>
                                                    <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>

                                                    <td class="text-center">
                                                        <?php if (!empty($row['gambar'])): ?>
                                                            <img src="../images/<?= htmlspecialchars($row['gambar']) ?>"
                                                                alt="<?= htmlspecialchars($row['nama_product']) ?>"
                                                                width="60"
                                                                height="60"
                                                                style="object-fit: cover;"
                                                                class="img-thumbnail"
                                                                onerror="this.src='https://via.placeholder.com/60?text=No+Image'">
                                                        <?php else: ?>
                                                            <img src="https://via.placeholder.com/60?text=No+Image"
                                                                width="60"
                                                                height="60"
                                                                class="img-thumbnail">
                                                        <?php endif; ?>
                                                    </td>

                                                    <td><?= htmlspecialchars($row['nama_product']) ?></td>
                                                    <td><?= $row['qty'] ?></td>
                                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                                    <td><strong>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></strong></td>
                                                    <td>
                                                        <?php
                                                        $t = $row['tanggal_tambah'];
                                                        echo ($t && $t != '0000-00-00 00:00:00')
                                                            ? date('d/m/Y H:i', strtotime($t))
                                                            : '-';
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="edit_keranjang.php?id=<?= $row['id_keranjang'] ?>"
                                                            class="btn btn-warning btn-action"
                                                            title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="tabel_keranjang.php?action=delete&id=<?= $row['id_keranjang'] ?>"
                                                            class="btn btn-danger btn-action"
                                                            title="Hapus"
                                                            onclick="return confirm('Yakin ingin menghapus data keranjang ini?')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                        } else { ?>
                                            <tr>
                                                <td colspan="9" class="text-center">Belum ada data keranjang</td>
                                            </tr>
                                        <?php } ?>
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
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
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
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>

</body>

</html>