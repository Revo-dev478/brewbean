<?php
session_start();
require_once '../config.php';

// Admin auth check (uncomment if needed)
// if (!isset($_SESSION['is_admin'])) {
//     header('Location: login.php');
//     exit;
// }

// HANDLE POST REQUEST (UPDATE DATA)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update_checkout'])) {
    $id_checkout = $_POST['id_checkout'];
    $status      = $_POST['status'];
    $metode      = $_POST['metode'];
    $total       = $_POST['total'];

    $update = $koneksi->prepare('UPDATE checkout SET status_checkout = ?, metode_pembayaran = ?, total_harga = ? WHERE id_checkout = ?');
    $update->bind_param('ssii', $status, $metode, $total, $id_checkout);

    if ($update->execute()) {
        $_SESSION['flash_message'] = "Data checkout berhasil diperbarui!";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Gagal memperbarui data: " . $koneksi->error;
        $_SESSION['flash_type'] = "danger";
    }
    $update->close();

    header('Location: tabel_checkout.php');
    exit;
}

/**
 * QUERY CHECKOUT + USER
 */
$sql = "
    SELECT 
        c.id_checkout,
        c.order_id,
        c.id_user,
        c.total_harga,
        c.status_checkout,
        c.metode_pembayaran,
        c.created_at,
        u.username,
        u.email,
        GROUP_CONCAT(ci.product_name SEPARATOR ', ') as product_names
    FROM checkout c
    LEFT JOIN tabel_user u ON c.id_user = u.id_user
    LEFT JOIN checkout_item ci ON c.order_id = ci.order_id
    GROUP BY c.id_checkout
    ORDER BY c.created_at DESC
";

$result = mysqli_query($koneksi, $sql);
if (!$result) {
    die('Query error: ' . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin - Tabel Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- SB Admin 2 -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- End of Sidebar -->
        <!-- END SIDEBAR -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- TOPBAR -->
                <?php include 'topbar.php'; ?>

                <!-- CONTENT -->
                <div class="container-fluid">

                    <h1 class="h3 mb-2 text-gray-800">Tabel Checkout</h1>
                    <p class="mb-4">Data checkout user yang melakukan pemesanan.</p>

                    <!-- FLASH MESSAGE -->
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['flash_message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php
                        unset($_SESSION['flash_message']);
                        unset($_SESSION['flash_type']);
                        ?>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Checkout</h6>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Order ID</th>
                                            <th>Produk</th>
                                            <th>User</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Metode</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <?php
                                                $username = isset($row['username']) && $row['username'] !== '' ? $row['username'] : '-';
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['id_checkout']; ?></td>
                                                    <td><strong><?php echo htmlspecialchars($row['order_id']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars(isset($row['product_names']) ? $row['product_names'] : '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($username); ?></td>
                                                    <td><strong>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></strong></td>
                                                    <td>
                                                        <?php
                                                        $status = $row['status_checkout'];
                                                        if ($status === 'success') {
                                                            echo '<span class="badge badge-success">SUCCESS</span>';
                                                        } elseif ($status === 'pending') {
                                                            echo '<span class="badge badge-warning">PENDING</span>';
                                                        } else {
                                                            echo '<span class="badge badge-danger">FAILED</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info btn-edit"
                                                            data-toggle="modal"
                                                            data-target="#editModal"
                                                            data-id="<?php echo $row['id_checkout']; ?>"
                                                            data-order="<?php echo htmlspecialchars($row['order_id']); ?>"
                                                            data-status="<?php echo $row['status_checkout']; ?>"
                                                            data-metode="<?php echo htmlspecialchars($row['metode_pembayaran']); ?>"
                                                            data-total="<?php echo $row['total_harga']; ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>

                                                        <a href="delete_checkout.php?id=<?php echo $row['id_checkout']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this checkout?');">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center">Belum ada data checkout</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- END CONTENT -->

            </div>

            <!-- FOOTER -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; SB Admin 2026</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit Checkout</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="tabel_checkout.php">
                    <div class="modal-body">
                        <input type="hidden" name="id_checkout" id="edit_id">

                        <div class="form-group">
                            <label>Order ID</label>
                            <input type="text" class="form-control" id="edit_order_display" disabled>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="success">SUCCESS</option>
                                <option value="pending">PENDING</option>
                                <option value="failed">FAILED</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <input type="text" name="metode" id="edit_metode" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Total Harga</label>
                            <input type="number" name="total" id="edit_total" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="btn_update_checkout" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();

            // Handle Tombol Edit Click
            $('.btn-edit').on('click', function() {
                var id = $(this).data('id');
                var order = $(this).data('order');
                var status = $(this).data('status');
                var metode = $(this).data('metode');
                var total = $(this).data('total');

                $('#edit_id').val(id);
                $('#edit_order_display').val(order);
                $('#edit_status').val(status);
                $('#edit_metode').val(metode);
                $('#edit_total').val(total);
            });
        });
    </script>

</body>

</html>