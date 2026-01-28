<?php
include 'config.php';

$id = $_GET['id'];

// Ambil nama gambar sebelum produk dihapus
$data = false;
$gambar = "";
if ($koneksi) {
    $data = mysqli_query($koneksi, "SELECT gambar FROM tabel_product WHERE id_product='$id'");
}

if ($data) {
    $row = mysqli_fetch_assoc($data);
    $gambar = $row['gambar'];
}

// Hapus file fisik jika ada
if ($gambar != "" && file_exists("../images/" . $gambar)) {
    unlink("../images/" . $gambar);
}

if ($koneksi) {
    mysqli_query($koneksi, "DELETE FROM tabel_product WHERE id_product='$id'");
}

header("Location: daftar-product.php");
