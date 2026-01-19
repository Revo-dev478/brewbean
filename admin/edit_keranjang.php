<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: tabel_keranjang.php");
    exit();
}

$id = $_GET['id'];

// Mengambil data keranjang beserta harga produk untuk kalkulasi subtotal
$query = "
    SELECT k.*, p.nama_product, p.harga AS harga_satuan 
    FROM tabel_keranjang k
    JOIN tabel_product p ON k.id_product = p.id_product
    WHERE k.id_keranjang = '$id'
";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Data keranjang tidak ditemukan!";
    exit();
}

if (isset($_POST['submit'])) {
    $qty_baru = $_POST['qty'];

    // Hitung subtotal baru
    $harga_satuan = $row['harga_satuan'];
    $subtotal_baru = $qty_baru * $harga_satuan;

    // Update query
    $updateQuery = "UPDATE tabel_keranjang SET qty='$qty_baru', subtotal='$subtotal_baru' WHERE id_keranjang='$id'";

    if (mysqli_query($koneksi, $updateQuery)) {
        echo "<script>alert('Data keranjang berhasil diupdate!'); window.location='tabel_keranjang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Keranjang - Admin BrewBeans</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Edit Keranjang Belanja</h4>
            </div>
            <div class="card-body">
                <form method="POST">

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_product']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Harga Satuan</label>
                        <input type="text" class="form-control" value="Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Qty (Jumlah)</label>
                        <input type="number" class="form-control" name="qty" value="<?= $row['qty'] ?>" min="1" required>
                        <small class="text-muted">Subtotal akan otomatis dihitung ulang saat disimpan.</small>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Update</button>
                    <a href="tabel_keranjang.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>