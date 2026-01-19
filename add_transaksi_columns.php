<?php
require 'config.php';

// Add columns if they don't exist
$alterQueries = [
    "ALTER TABLE transaksi_midtrans ADD COLUMN detail_item TEXT NULL AFTER payment_type",
    "ALTER TABLE transaksi_midtrans ADD COLUMN ongkir INT(11) DEFAULT 0 AFTER detail_item"
];

foreach ($alterQueries as $query) {
    if (mysqli_query($koneksi, $query)) {
        echo "Success: $query\n";
    } else {
        echo "Note (may already exist): " . mysqli_error($koneksi) . "\n";
    }
}
echo "Database update complete.";
?>
