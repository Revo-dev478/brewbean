<?php
// fix_db.php
require_once 'config.php';

// Perbaiki tipe data kolom phone
$sql = "ALTER TABLE tabel_user MODIFY phone VARCHAR(20)";

if ($koneksi->query($sql) === TRUE) {
    echo "<h1>Database Berhasil Diperbaiki! ✅</h1>";
    echo "<p>Kolom 'phone' sekarang sudah bisa menampung nomor HP panjang.</p>";
    echo "<p>Silakan kembali ke <a href='sign-up.php'>Halaman Sign Up</a> dan coba daftar lagi.</p>";
} else {
    echo "<h1>Gagal Memperbaiki Database ❌</h1>";
    echo "<p>Error: " . $koneksi->error . "</p>";
}

$koneksi->close();
?>
