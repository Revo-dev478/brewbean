<?php
require_once 'config.php';
$sql = file_get_contents('add_weight_column.sql');
$commands = explode(';', $sql);
foreach ($commands as $cmd) {
    if (trim($cmd) == '') continue;
    if (mysqli_query($koneksi, $cmd)) {
        echo "Success: $cmd <br>";
    } else {
         if (mysqli_errno($koneksi) == 1060) {
            echo "Skipped (Column already exists): $cmd <br>";
        } else {
            echo "Error: " . mysqli_error($koneksi) . "<br>";
        }
    }
}
?>
