<?php
require 'config.php';

echo "Starting Database Fix...\n";

// 1. Ubah tipe kolom dari INT ke VARCHAR agar bisa menampung '08...' tanpa limit integer
$sql_alter = "ALTER TABLE tabel_user MODIFY phone VARCHAR(25) NOT NULL";
if (mysqli_query($koneksi, $sql_alter)) {
    echo "[SUCCESS] Kolom 'phone' berhasil diubah menjadi VARCHAR.\n";
} else {
    echo "[ERROR] Gagal mengubah kolom: " . mysqli_error($koneksi) . "\n";
}

// 2. Perbaiki data yang sudah rusak (2147483647)
// Kita ubah menjadi data dummy '081234567890' agar user tidak melihat angka aneh
// User harus mengupdate kembali nomor aslinya nanti
$sql_update = "UPDATE tabel_user SET phone = '081234567890' WHERE phone = '2147483647'";
if (mysqli_query($koneksi, $sql_update)) {
    $affected = mysqli_affected_rows($koneksi);
    echo "[SUCCESS] Berhasil memperbaiki $affected data user yang corrupt.\n";
} else {
    echo "[ERROR] Gagal mengupdate data user: " . mysqli_error($koneksi) . "\n";
}

echo "Selesai.\n";
?>
