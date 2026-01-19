<?php
/**
 * FORCE SYNC MIDTRANS
 * Debug dan langsung update database
 */
session_start();
require_once 'config.php';

echo "<h1>Force Sync Midtrans</h1>";

// Konfigurasi Midtrans - Load from environment
$serverKeys = array(
    env('MIDTRANS_SERVER_KEY_ALT', ''),
    env('MIDTRANS_SERVER_KEY', '')
);

$isProduction = false;
$apiUrl = $isProduction
    ? 'https://api.midtrans.com/v2'
    : 'https://api.sandbox.midtrans.com/v2';

echo "<p><strong>API URL:</strong> $apiUrl</p>";

// Ambil semua order
$orders = mysqli_query($koneksi, "SELECT order_id, id_user, total_harga, status_checkout, created_at FROM checkout ORDER BY created_at DESC");

echo "<h2>Testing API dengan berbagai Server Key:</h2>";

$testOrderId = null;
while ($row = mysqli_fetch_assoc($orders)) {
    if ($testOrderId === null) {
        $testOrderId = $row['order_id'];
    }
}

// Test dengan order pertama
if ($testOrderId) {
    foreach ($serverKeys as $key) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<h4>Testing Server Key: " . substr($key, 0, 20) . "...</h4>";
        
        $url = $apiUrl . '/' . $testOrderId . '/status';
        echo "<p>URL: $url</p>";
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($key . ':')
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 30
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
        
        if ($curlError) {
            echo "<p style='color:red;'>CURL Error: $curlError</p>";
        } else {
            $data = json_decode($response, true);
            echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
            
            if (isset($data['transaction_status']) && $data['transaction_status'] === 'settlement') {
                echo "<p style='color:green; font-weight:bold;'>✓ Server Key ini BENAR! Status: " . $data['transaction_status'] . "</p>";
            }
        }
        echo "</div>";
    }
}

echo "<hr><h2>Force Update semua transaksi ke Settlement</h2>";

// Berdasarkan screenshot Midtrans, semua transaksi sudah settlement
// Kita akan force update database

$forceUpdate = isset($_GET['force']) && $_GET['force'] == '1';

if ($forceUpdate) {
    // Update semua checkout ke success
    $updateCheckout = mysqli_query($koneksi, "UPDATE checkout SET status_checkout = 'success' WHERE status_checkout = 'pending'");
    $affectedCheckout = mysqli_affected_rows($koneksi);
    echo "<p style='color:green;'>✓ Updated $affectedCheckout rows di tabel checkout ke 'success'</p>";
    
    // Update semua transaksi_midtrans ke settlement
    $updateTrans = mysqli_query($koneksi, "UPDATE transaksi_midtrans SET transaction_status = 'settlement', payment_type = 'bank_transfer' WHERE transaction_status = 'pending'");
    $affectedTrans = mysqli_affected_rows($koneksi);
    echo "<p style='color:green;'>✓ Updated $affectedTrans rows di tabel transaksi_midtrans ke 'settlement'</p>";
    
    // Insert yang belum ada di transaksi_midtrans
    $checkouts = mysqli_query($koneksi, "SELECT c.* FROM checkout c LEFT JOIN transaksi_midtrans t ON c.order_id = t.order_id WHERE t.id_transaksi IS NULL");
    $inserted = 0;
    while ($checkout = mysqli_fetch_assoc($checkouts)) {
        $id_user = $checkout['id_user'];
        $order_id = $checkout['order_id'];
        $total = (float)$checkout['total_harga'];
        $created = $checkout['created_at'];
        
        mysqli_query($koneksi, "INSERT INTO transaksi_midtrans (id_user, order_id, transaction_status, payment_type, gross_amount, transaction_time) VALUES ($id_user, '$order_id', 'settlement', 'bank_transfer', $total, '$created')");
        $inserted++;
    }
    echo "<p style='color:green;'>✓ Inserted $inserted new rows ke transaksi_midtrans</p>";
    
    echo "<p><a href='admin/riwayat_transaksi.php' style='font-size: 18px;'>→ Kembali ke Riwayat Transaksi</a></p>";
} else {
    echo "<p>Klik tombol di bawah untuk FORCE UPDATE semua transaksi yang pending menjadi settlement:</p>";
    echo "<a href='?force=1' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; font-size: 18px; border-radius: 5px;'>FORCE UPDATE KE SETTLEMENT</a>";
    
    echo "<h3>Data yang akan diupdate:</h3>";
    
    // Show pending checkouts
    $pendingCheckouts = mysqli_query($koneksi, "SELECT * FROM checkout WHERE status_checkout = 'pending'");
    echo "<p><strong>Checkout dengan status pending:</strong> " . mysqli_num_rows($pendingCheckouts) . " rows</p>";
    
    // Show pending transaksi
    $pendingTrans = mysqli_query($koneksi, "SELECT * FROM transaksi_midtrans WHERE transaction_status = 'pending'");
    echo "<p><strong>Transaksi_midtrans dengan status pending:</strong> " . mysqli_num_rows($pendingTrans) . " rows</p>";
    
    // Show missing transaksi
    $missing = mysqli_query($koneksi, "SELECT c.* FROM checkout c LEFT JOIN transaksi_midtrans t ON c.order_id = t.order_id WHERE t.id_transaksi IS NULL");
    echo "<p><strong>Checkout yang belum ada di transaksi_midtrans:</strong> " . mysqli_num_rows($missing) . " rows</p>";
}

mysqli_close($koneksi);
?>
