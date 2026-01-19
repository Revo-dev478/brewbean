<?php
include 'config.php';

$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM tabel_product WHERE id_product='$id'");
$row = mysqli_fetch_assoc($data);

if (isset($_POST['submit'])) {

    $nama = $_POST['nama_product'];
    $desk = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $harga = $_POST['harga'];
    $qty = $_POST['qty'];
    $berat = $_POST['berat'];
    $kategori = $_POST['id_kategori'];
    $penjual = $_POST['id_penjual'];

    // upload gambar baru jika ada
    $filename = $_FILES["gambar"]["name"];
    $tmp = $_FILES["gambar"]["tmp_name"];

    if ($filename != "") {
        // Perbaikan: Pastikan folder images ada di root
        if (!is_dir("../images")) {
            mkdir("../images", 0777, true);
        }

        // Perbaikan: Gunakan nama file unik untuk mencegah bentrok
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = time() . "_" . uniqid() . "." . $extension;
        $path = "../images/" . $filename;
        move_uploaded_file($tmp, $path);

        mysqli_query($koneksi, "UPDATE tabel_product SET 
            nama_product='$nama',
            deskripsi='$desk',
            harga='$harga',
            qty='$qty',
            berat='$berat',
            id_kategori='$kategori',
            id_penjual='$penjual',
            gambar='$filename'
            WHERE id_product='$id'");
    } else {
        mysqli_query($koneksi, "UPDATE tabel_product SET 
            nama_product='$nama',
            deskripsi='$desk',
            harga='$harga',
            qty='$qty',
            berat='$berat',
            id_kategori='$kategori',
            id_penjual='$penjual'
            WHERE id_product='$id'");
    }

    header("Location: daftar-product.php");
}
// Fetch sellers for dropdown
$sellers = mysqli_query($koneksi, "SELECT * FROM tabel_penjual");
$seller_list = [];
while ($s = mysqli_fetch_assoc($sellers)) {
    $seller_list[] = $s;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-4">

        <h2>Edit Product</h2>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Nama Product</label>
                <input type="text" class="form-control" name="nama_product" value="<?= $row['nama_product'] ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea class="form-control" name="deskripsi" required><?= $row['deskripsi'] ?></textarea>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input type="number" class="form-control" name="harga" value="<?= $row['harga'] ?>" required>
            </div>

            <div class="form-group">
                <label>Stok (Qty)</label>
                <input type="number" class="form-control" name="qty" value="<?= $row['qty'] ?>">
            </div>

            <div class="form-group">
                <label>Berat (gram)</label>
                <input type="number" class="form-control" name="berat" value="<?= isset($row['berat']) ? $row['berat'] : 1000 ?>">
            </div>

            <div class="form-group">
                <label>ID Kategori</label>
                <input type="number" class="form-control" name="id_kategori" value="<?= $row['id_kategori'] ?>" required>
            </div>

            <div class="form-group">
                <label>Penjual</label>
                <select name="id_penjual" class="form-control" required>
                    <option value="">-- Pilih Penjual --</option>
                    <?php foreach ($seller_list as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($row['id_penjual'] == $s['id']) ? 'selected' : '' ?>>
                            <?= $s['nama'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Gambar</label><br>
                <?php if ($row['gambar']) { ?>
                    <img src="../images/<?= $row['gambar'] ?>" width="100"><br><br>
                <?php } ?>
                <input type="file" name="gambar" class="form-control">
            </div>

            <button type="submit" name="submit" class="btn btn-warning">Update</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>

    </div>
</body>

</html>