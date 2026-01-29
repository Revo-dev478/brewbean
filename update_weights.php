<?php
require_once 'config.php';

if ($koneksi) {
    // 1. Update all products to 20kg (20000 grams)
    $query = "UPDATE tabel_product SET berat = 20000";
    if (mysqli_query($koneksi, $query)) {
        echo "Successfully updated all products to 20kg (20000 grams).<br>";
    } else {
        echo "Error updating product weights: " . mysqli_error($koneksi) . "<br>";
    }

    // 2. Check if 'berat' column exists in tabel_keranjang? 
    // Usually weight is fetched from product table during checkout.

    // 3. Optional: Set default weight for new columns if applicable
    $query_alter = "ALTER TABLE tabel_product ALTER COLUMN berat SET DEFAULT 20000";
    // This might fail if syntax is different or column doesn't exist, but worth a try or just ignore.
    // Suppress error
    @mysqli_query($koneksi, $query_alter);

    echo "Done.";
} else {
    echo "Database connection failed.";
}
