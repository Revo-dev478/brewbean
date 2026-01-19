<?php
require_once 'config.php';

function describeTable($conn, $table) {
    echo "<h3>Table: $table</h3>";
    $result = mysqli_query($conn, "DESCRIBE $table");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    } else {
        echo "Table $table does not exist or error: " . mysqli_error($conn) . "<br>";
    }
}

describeTable($koneksi, 'tabel_product');
describeTable($koneksi, 'tabel_penjual');
?>
