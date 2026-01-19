<?php
require_once 'config.php';

// Script untuk mengubah tipe data kolom phone menjadi VARCHAR
// Agar bisa menampung nomor telepon yang panjang (lebih dari angka 2 Milyar)

$sql = "ALTER TABLE tabel_user MODIFY phone VARCHAR(20) NOT NULL";

if ($koneksi->query($sql) === TRUE) {
    echo "<h1>Berhasil!</h1>";
    echo "<p>Kolom 'phone' pada 'tabel_user' berhasil diubah menjadi VARCHAR(20).</p>";
    echo "<p>Silakan coba daftar kembali.</p>";
} else {
    echo "<h1>Gagal</h1>";
    echo "<p>Error: " . $koneksi->error . "</p>";
}
