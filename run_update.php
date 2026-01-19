<?php
/**
 * EXECUTE SQL UPDATE
 * Menjalankan script update_delivery_schema.sql
 */
require_once 'config.php';

// Baca file SQL
$sqlFile = 'update_delivery_schema.sql';
if (!file_exists($sqlFile)) {
    die("File SQL tidak ditemukan.");
}

$sqlContent = file_get_contents($sqlFile);

// Hapus komentar dan pisahkan per command
$sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
$commands = array_filter(array_map('trim', explode(';', $sqlContent)));

echo "<h1>Executing Database Update...</h1>";

foreach ($commands as $cmd) {
    if (empty($cmd)) continue;
    
    // Skip optional update commands that are commented out in the file logic but might have been read
    if (stripos($cmd, 'UPDATE transaksi_midtrans') !== false && stripos($cmd, 'LIMIT') !== false) {
        // Ini bagian contoh data, kita skip atau jalankan? User minta "eksekusi plan", 
        // Plan hanya bilang update schema. Mari kita jalankan saja biar ada data testing jika tabel kosong kolomnya.
        // Tapi tunggu, script SQL yang saya buat sebelumnya memiliki bagian commented out.
        // `file_get_contents` membaca semuanya.
        // Regex di atas menghapus baris komentar '-- '.
        // Jadi aman.
    }

    echo "<div style='margin-bottom:10px; padding:10px; border:1px solid #ddd;'>";
    echo "<strong>Command:</strong> <pre>" . htmlspecialchars(substr($cmd, 0, 100)) . "...</pre>";
    
    if (mysqli_query($koneksi, $cmd)) {
        echo "<span style='color:green'>✓ Success</span>";
    } else {
        // Ignore error if column already exists (Duplicate column name)
        if (strpos(mysqli_error($koneksi), "Duplicate column name") !== false) {
            echo "<span style='color:orange'>⚠ Column already exists (Skipped)</span>";
        } else {
            echo "<span style='color:red'>✗ Error: " . mysqli_error($koneksi) . "</span>";
        }
    }
    echo "</div>";
}

echo "<h3>Update Selesai!</h3>";
echo "<a href='riwayat_pemesanan.php'>Lihat Riwayat Pemesanan</a>";
?>
