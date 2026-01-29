<?php
require_once 'config.php';

// Create review table
$query = "CREATE TABLE IF NOT EXISTS tabel_review (
    id_review INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    order_id VARCHAR(50) NOT NULL,
    id_product INT NOT NULL,
    id_user INT NOT NULL,
    rating INT NOT NULL,
    review_text TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (order_id),
    INDEX (id_product),
    INDEX (id_user)
)";

if (mysqli_query($koneksi, $query)) {
    echo "Table 'tabel_review' created or already exists successfully.\n";
} else {
    echo "Error creating table: " . mysqli_error($koneksi) . "\n";
}
