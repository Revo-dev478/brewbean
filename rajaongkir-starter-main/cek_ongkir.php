<?php
header('Content-Type: text/html; charset=utf-8');

// Load environment variables
require_once __DIR__ . '/../env_loader.php';

// API Key RajaOngkir Sandbox (from .env)
$api_key = env('RAJAONGKIR_SANDBOX_API_KEY', '');

$origin = isset($_POST['origin_city']) ? $_POST['origin_city'] : '';
$destination = isset($_POST['destination_city']) ? $_POST['destination_city'] : '';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '';
$courier = isset($_POST['courier']) ? $_POST['courier'] : '';

// Validasi input
if (!$origin || !$destination || !$weight || !$courier) {
    echo '<div class="alert alert-danger">Data tidak lengkap. Pastikan semua field terisi.</div>';
    exit;
}

if ($weight < 1) {
    echo '<div class="alert alert-danger">Berat minimal 1 gram.</div>';
    exit;
}

// Gunakan endpoint SANDBOX RajaOngkir
$url = 'https://api.sandbox.rajaongkir.com/api/cost';
$data = [
    'origin' => $origin,
    'destination' => $destination,
    'weight' => $weight,
    'courier' => $courier
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => [
        'key: ' . $api_key
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($err) . '</div>';
    exit;
}

$result = json_decode($response, true);

// Cek apakah response valid
if (!isset($result['rajaongkir']['status']['code']) || $result['rajaongkir']['status']['code'] != 200) {
    $error_msg = isset($result['rajaongkir']['status']['description']) 
        ? $result['rajaongkir']['status']['description'] 
        : 'Terjadi kesalahan. Periksa API Key Sandbox Anda.';
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($error_msg) . '</div>';
    exit;
}

// Cek apakah ada hasil
if (!isset($result['rajaongkir']['results'][0]['costs']) || empty($result['rajaongkir']['results'][0]['costs'])) {
    echo '<div class="alert alert-warning">Tidak ada layanan tersedia untuk rute ini.</div>';
    exit;
}

$costs = $result['rajaongkir']['results'][0]['costs'];
$courier_name = strtoupper($result['rajaongkir']['results'][0]['name']);

echo '<div class="alert alert-success"><strong>Hasil Ongkir ' . htmlspecialchars($courier_name) . '</strong></div>';
echo '<div class="alert alert-info"><small>🧪 Menggunakan Sandbox API RajaOngkir</small></div>';
echo '<table class="table table-striped table-hover">';
echo '<thead class="table-dark">';
echo '<tr><th>Layanan</th><th>Deskripsi</th><th>Harga</th><th>Estimasi</th></tr>';
echo '</thead>';
echo '<tbody>';

foreach ($costs as $cost) {
    $service = htmlspecialchars($cost['service']);
    $description = htmlspecialchars($cost['description']);
    $price = $cost['cost'][0]['value'];
    $etd = htmlspecialchars($cost['cost'][0]['etd']);
    
    echo '<tr>';
    echo '<td><strong>' . $service . '</strong></td>';
    echo '<td>' . $description . '</td>';
    echo '<td class="text-success"><strong>Rp ' . number_format($price, 0, ',', '.') . '</strong></td>';
    echo '<td>' . $etd . ' hari</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
?>