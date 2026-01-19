<?php
require_once 'config.php';
$result = mysqli_query($koneksi, "SELECT * FROM tabel_penjual LIMIT 5");
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row['id'] . " - Nama: " . $row['nama'] . "<br>";
    }
} else {
    echo "No sellers found. Creating default seller.<br>";
    mysqli_query($koneksi, "INSERT INTO tabel_penjual (nama, password, tempat, umur, tanggal) VALUES ('Default Seller', '123', 'Bandung', 25, '2023-01-01')");
    echo "Created default seller with ID: " . mysqli_insert_id($koneksi);
}
?>
