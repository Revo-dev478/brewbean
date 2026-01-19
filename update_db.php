<?php
/**
 * UPDATE STATUS TRANSAKSI
 * Admin dapat mengupdate status sesuai dengan yang ada di Midtrans
 */
require_once 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'update_single') {
        $order_id = mysqli_real_escape_string($koneksi, $_POST['order_id']);
        $new_status = mysqli_real_escape_string($koneksi, $_POST['new_status']);
        
        // Update transaksi_midtrans
        mysqli_query($koneksi, "UPDATE transaksi_midtrans SET transaction_status = '$new_status' WHERE order_id = '$order_id'");
        
        // Update checkout status
        if ($new_status === 'settlement' || $new_status === 'capture') {
            $checkoutStatus = 'success';
        } elseif ($new_status === 'pending') {
            $checkoutStatus = 'pending';
        } else {
            $checkoutStatus = 'failed';
        }
        mysqli_query($koneksi, "UPDATE checkout SET status_checkout = '$checkoutStatus' WHERE order_id = '$order_id'");
        
        $message = "Order $order_id berhasil diupdate ke status: $new_status";
    }
    
    if ($action === 'update_all') {
        $new_status = mysqli_real_escape_string($koneksi, $_POST['new_status']);
        
        // Update semua transaksi_midtrans
        mysqli_query($koneksi, "UPDATE transaksi_midtrans SET transaction_status = '$new_status'");
        $affected1 = mysqli_affected_rows($koneksi);
        
        // Update checkout status
        if ($new_status === 'settlement' || $new_status === 'capture') {
            $checkoutStatus = 'success';
        } elseif ($new_status === 'pending') {
            $checkoutStatus = 'pending';
        } else {
            $checkoutStatus = 'failed';
        }
        mysqli_query($koneksi, "UPDATE checkout SET status_checkout = '$checkoutStatus'");
        $affected2 = mysqli_affected_rows($koneksi);
        
        $message = "Berhasil mengupdate $affected1 transaksi ke status: $new_status";
    }
    
    if ($action === 'insert_missing') {
        // Insert missing data
        $query = "SELECT c.order_id, c.id_user, c.total_harga, c.created_at 
                  FROM checkout c 
                  LEFT JOIN transaksi_midtrans t ON c.order_id = t.order_id 
                  WHERE t.id_transaksi IS NULL";
        $missingData = mysqli_query($koneksi, $query);
        $insertCount = 0;
        
        $default_status = mysqli_real_escape_string($koneksi, $_POST['default_status']);
        
        while ($checkout = mysqli_fetch_assoc($missingData)) {
            $id_user = (int)$checkout['id_user'];
            $order_id = mysqli_real_escape_string($koneksi, $checkout['order_id']);
            $gross_amount = (float)$checkout['total_harga'];
            $transaction_time = mysqli_real_escape_string($koneksi, $checkout['created_at']);
            
            mysqli_query($koneksi, "INSERT INTO transaksi_midtrans 
                (id_user, order_id, transaction_status, payment_type, gross_amount, transaction_time) 
                VALUES ($id_user, '$order_id', '$default_status', 'bank_transfer', $gross_amount, '$transaction_time')");
            $insertCount++;
        }
        
        $message = "Berhasil insert $insertCount transaksi dengan status: $default_status";
    }
}

// Ambil data untuk ditampilkan
$transaksiList = mysqli_query($koneksi, "
    SELECT t.order_id, t.transaction_status, t.gross_amount, t.transaction_time, c.status_checkout, u.username
    FROM transaksi_midtrans t
    LEFT JOIN checkout c ON t.order_id = c.order_id
    LEFT JOIN tabel_user u ON t.id_user = u.id_user
    ORDER BY t.transaction_time DESC
");

$checkoutList = mysqli_query($koneksi, "
    SELECT c.order_id, c.id_user, c.total_harga, c.status_checkout, c.created_at
    FROM checkout c
    LEFT JOIN transaksi_midtrans t ON c.order_id = t.order_id
    WHERE t.id_transaksi IS NULL
    ORDER BY c.created_at DESC
");
$missingCount = mysqli_num_rows($checkoutList);

// Status options yang tersedia di Midtrans
$statusOptions = array(
    'settlement' => 'Settlement (Sukses)',
    'pending' => 'Pending',
    'cancel' => 'Cancelled',
    'expire' => 'Expired',
    'deny' => 'Denied',
    'failure' => 'Failed',
    'capture' => 'Capture (Sukses)',
    'refund' => 'Refund'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Status Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Update Status Transaksi</h1>
    
    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Update Semua Transaksi -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Update SEMUA Transaksi</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update_all">
                        <div class="form-group">
                            <label>Pilih Status Baru:</label>
                            <select name="new_status" class="form-control" required>
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Semua</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Insert Missing Data -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Insert Data yang Belum Ada (<?php echo $missingCount; ?> data)</h5>
                </div>
                <div class="card-body">
                    <?php if ($missingCount > 0): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="insert_missing">
                            <div class="form-group">
                                <label>Status Default:</label>
                                <select name="default_status" class="form-control" required>
                                    <?php foreach ($statusOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">Insert <?php echo $missingCount; ?> Data</button>
                        </form>
                    <?php else: ?>
                        <p class="text-success mb-0">Semua data checkout sudah ada di transaksi_midtrans</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Transaksi -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Daftar Transaksi</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Checkout Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = mysqli_fetch_assoc($transaksiList)): ?>
                    <?php
                        $status = strtolower($row['transaction_status']);
                        if ($status === 'settlement' || $status === 'capture') {
                            $badgeClass = 'badge-success';
                        } elseif ($status === 'pending') {
                            $badgeClass = 'badge-warning';
                        } elseif ($status === 'cancel' || $status === 'deny' || $status === 'failure') {
                            $badgeClass = 'badge-danger';
                        } elseif ($status === 'expire') {
                            $badgeClass = 'badge-secondary';
                        } else {
                            $badgeClass = 'badge-dark';
                        }
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['order_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>Rp <?php echo number_format($row['gross_amount'], 0, ',', '.'); ?></td>
                        <td><span class="badge <?php echo $badgeClass; ?>"><?php echo strtoupper($row['transaction_status']); ?></span></td>
                        <td><?php echo $row['status_checkout']; ?></td>
                        <td>
                            <form method="POST" class="form-inline" style="display:inline;">
                                <input type="hidden" name="action" value="update_single">
                                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_id']); ?>">
                                <select name="new_status" class="form-control form-control-sm" style="width:120px;">
                                    <?php foreach ($statusOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo ($value === $row['transaction_status']) ? 'selected' : ''; ?>>
                                            <?php echo $value; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-info ml-1">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="admin/riwayat_transaksi.php" class="btn btn-lg btn-primary">→ Lihat Riwayat Transaksi Admin</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($koneksi); ?>
