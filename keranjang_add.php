<?php
session_start();
require_once 'config.php';

// Pastikan koneksi sukses
if (!isset($koneksi) || $koneksi === false) {
    die("ERROR: Koneksi database gagal!");
}

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_produk = (int)$_POST['id_produk'];
    $qty = (int)$_POST['qty'];
    $id_user = (int)$_SESSION['id_user'];

    if ($id_produk < 1 || $qty < 1 || $id_user < 1) {
        die("Data tidak valid!");
    }

    // Ambil data produk (Hanya butuh harga untuk hitungan)
    $productQ = mysqli_query($koneksi, "SELECT harga FROM tabel_product WHERE id_product = $id_produk");
    
    if ($productQ === false) {
        die("ERROR SQL saat ambil produk: " . mysqli_error($koneksi));
    }
    
    $product = mysqli_fetch_assoc($productQ);

    if (!$product) {
        die("ERROR: Produk tidak ditemukan.");
    }

    // Hanya butuh harga
    $harga = (int)$product['harga'];
    $subtotal = $harga * $qty;

    // Cek apakah produk sudah ada di keranjang
    $check = mysqli_query($koneksi, 
        "SELECT * FROM tabel_keranjang 
         WHERE id_user = $id_user AND id_product = $id_produk"
    );
    
    if ($check === false) {
        die("ERROR SQL saat cek keranjang: " . mysqli_error($koneksi));
    }

    $exist = mysqli_fetch_assoc($check);

    if ($exist) {
        // UPDATE
        $new_qty = $exist['qty'] + $qty;
        $new_subtotal = $harga * $new_qty;

        $update = mysqli_query($koneksi,
            "UPDATE tabel_keranjang 
             SET qty = $new_qty, subtotal = $new_subtotal 
             WHERE id_user = $id_user AND id_product = $id_produk"
        );

        if (!$update) {
            die("Error update keranjang: " . mysqli_error($koneksi));
        }

    } else {
        // INSERT (PERBAIKAN: Hapus nama_product dan hanya gunakan kolom yang ada di tabel)
        $insert = mysqli_query($koneksi,
            "INSERT INTO tabel_keranjang 
             (id_user, id_product, qty, harga, subtotal, Tanggal) 
             VALUES 
             ($id_user, $id_produk, $qty, $harga, $subtotal, NOW())"
        );

        if (!$insert) {
            // Error ini seharusnya sudah teratasi!
            die("Error insert keranjang: " . mysqli_error($koneksi));
        }
    }

    header("Location: keranjang.php");
    exit();
}

header("Location: menu.php");
exit();
?>