<?php
require_once 'config.php';
echo "<h3>Table: tabel_user</h3>";
$result = mysqli_query($koneksi, "DESCRIBE tabel_user");
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}

// Check if 'user' table exists too
echo "<h3>Table: user</h3>";
$result2 = mysqli_query($koneksi, "DESCRIBE user");
if ($result2) {
    while ($row = mysqli_fetch_assoc($result2)) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "Table 'user' does not exist or error.<br>";
}
?>
