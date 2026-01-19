<?php
require_once 'config.php';

// Create checkout_item table
$query = "CREATE TABLE IF NOT EXISTS checkout_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    id_product INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    INDEX (order_id),
    INDEX (id_product)
)";

if (mysqli_query($koneksi, $query)) {
    echo "Table 'checkout_item' created or already exists successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($koneksi) . "\n";
}

mysqli_close($koneksi);
