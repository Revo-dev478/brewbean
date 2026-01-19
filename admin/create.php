    <?php
    include 'config.php';

    if (isset($_POST['submit'])) {

        $nama = $_POST['nama_product'];
        $desk = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $qty = $_POST['qty'];
        $berat = $_POST['berat'];
        $kategori = $_POST['id_kategori'];
        $penjual = $_POST['id_penjual'];

        // Upload gambar
        $filename = $_FILES["gambar"]["name"];
        $tmp = $_FILES["gambar"]["tmp_name"];

        // Perbaikan: Pastikan folder images ada di root
        if (!is_dir("../images")) {
            mkdir("../images", 0777, true);
        }

        // Perbaikan: Gunakan nama file unik untuk mencegah bentrok
        if ($filename != "") {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = time() . "_" . uniqid() . "." . $extension;
            $path = "../images/" . $filename;
            move_uploaded_file($tmp, $path);
        }

        mysqli_query($koneksi, "INSERT INTO tabel_product (nama_product, deskripsi, harga, qty, berat, id_kategori, gambar, id_penjual) VALUES(
            '$nama',
            '$desk',
            '$harga',
            '$qty',
            '$berat',
            '$kategori',
            '$filename',
            '$penjual'
        )");

        header("Location: daftar-product.php");
    }

    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Tambah Product</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>

    <body>
        <div class="container mt-4">
            <h2>Tambah Product</h2>
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Nama Product</label>
                    <input type="text" class="form-control" name="nama_product" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" name="deskripsi" required></textarea>
                </div>

                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" class="form-control" name="harga" required>
                </div>

                <div class="form-group">
                    <label>Stok (Qty)</label>
                    <input type="number" class="form-control" name="qty" required>
                </div>

                <div class="form-group">
                    <label>Berat (gram)</label>
                    <input type="number" class="form-control" name="berat" placeholder="1000" required>
                </div>

                <div class="form-group">
                    <label>ID Kategori</label>
                    <input type="number" class="form-control" name="id_kategori" required>
                </div>

                <div class="form-group">
                    <label>Penjual</label>
                    <select name="id_penjual" class="form-control" required>
                        <option value="">-- Pilih Penjual --</option>
                        <?php
                        $sellers = mysqli_query($koneksi, "SELECT * FROM tabel_penjual");
                        while ($s = mysqli_fetch_assoc($sellers)) {
                            echo "<option value='" . $s['id'] . "'>" . $s['nama'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Gambar</label>
                    <input type="file" name="gambar" class="form-control">
                </div>

                <button type="submit" name="submit" class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </body>

    </html>