<?php
require_once 'config.php';
$sql = file_get_contents('add_seller_column.sql');
$commands = explode(';', $sql);
foreach ($commands as $cmd) {
    if (trim($cmd) == '') continue;
    if (mysqli_query($koneksi, $cmd)) {
        echo "Success: $cmd <br>";
    } else {
        // Check for specific error code 1060 (Duplicate column name)
        if (mysqli_errno($koneksi) == 1060) {
            echo "Skipped (Column already exists): $cmd <br>";
        } else {
            echo "Error: " . mysqli_error($koneksi) . " (Code: " . mysqli_errno($koneksi) . ")<br>";
        }
    }
}
?>
