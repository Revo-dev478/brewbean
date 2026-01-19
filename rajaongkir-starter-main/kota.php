<?php
header('Content-Type: text/html; charset=utf-8');

// Load environment variables
require_once __DIR__ . '/../env_loader.php';

// API Key RajaOngkir Sandbox (from .env)
$api_key = env('RAJAONGKIR_SANDBOX_API_KEY', '');

// Cek apakah ada cache
$cache_file = 'cache_cities.json';
$cache_time = 86400; // 24 jam

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    // Gunakan data dari cache
    $cities = json_decode(file_get_contents($cache_file), true);
    
    if ($cities && is_array($cities)) {
        // Output options untuk dropdown
        foreach ($cities as $city) {
            $city_id = htmlspecialchars($city['city_id']);
            $type = isset($city['type']) ? $city['type'] . ' ' : '';
            $city_name = htmlspecialchars($type . $city['city_name'] . ', ' . $city['province']);
            echo "<option value='{$city_id}'>{$city_name}</option>";
        }
        exit;
    }
}

// Gunakan endpoint SANDBOX RajaOngkir
$curl = curl_init();

curl_setopt_array($curl, [
    // ENDPOINT SANDBOX - Bukan starter atau pro
    CURLOPT_URL => 'https://api.sandbox.rajaongkir.com/api/city',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'key: ' . $api_key
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo '<option value="">Error cURL: ' . htmlspecialchars($err) . '</option>';
    exit;
}

if ($http_code != 200) {
    echo '<option value="">Error HTTP ' . $http_code . ': Periksa API Key Sandbox</option>';
    exit;
}

$result = json_decode($response, true);

if (!$result) {
    echo '<option value="">Error: Response tidak valid</option>';
    exit;
}

if (!isset($result['rajaongkir']['status']['code'])) {
    echo '<option value="">Error: Format response salah</option>';
    exit;
}

if ($result['rajaongkir']['status']['code'] != 200) {
    $error_desc = isset($result['rajaongkir']['status']['description']) 
        ? $result['rajaongkir']['status']['description'] 
        : 'Unknown error';
    echo '<option value="">Error API: ' . htmlspecialchars($error_desc) . '</option>';
    exit;
}

if (!isset($result['rajaongkir']['results']) || empty($result['rajaongkir']['results'])) {
    echo '<option value="">Error: Tidak ada data kota</option>';
    exit;
}

$cities = $result['rajaongkir']['results'];

// Simpan ke cache
file_put_contents($cache_file, json_encode($cities));

// Output options untuk dropdown
foreach ($cities as $city) {
    $city_id = htmlspecialchars($city['city_id']);
    $type = isset($city['type']) ? $city['type'] . ' ' : '';
    $city_name = htmlspecialchars($type . $city['city_name'] . ', ' . $city['province']);
    echo "<option value='{$city_id}'>{$city_name}</option>";
}
?>