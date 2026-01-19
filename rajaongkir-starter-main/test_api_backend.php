<?php
header('Content-Type: text/html; charset=utf-8');

$api_key = isset($_POST['api_key']) ? $_POST['api_key'] : '';

if (!$api_key) {
    echo '<div class="alert alert-danger">API Key tidak boleh kosong!</div>';
    exit;
}

// Test API
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.rajaongkir.com/starter/city?id=501',
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

echo '<div class="card">';
echo '<div class="card-body">';

if ($err) {
    echo '<div class="alert alert-danger">';
    echo '<strong>❌ cURL Error:</strong><br>';
    echo htmlspecialchars($err);
    echo '</div>';
    exit;
}

echo '<h5>📊 Hasil Test:</h5>';
echo '<table class="table table-bordered">';
echo '<tr><th width="200">HTTP Code</th><td><strong>' . $http_code . '</strong></td></tr>';

$result = json_decode($response, true);

if ($result && isset($result['rajaongkir']['status'])) {
    $status = $result['rajaongkir']['status'];
    
    echo '<tr><th>Status Code</th><td>' . $status['code'] . '</td></tr>';
    echo '<tr><th>Description</th><td>' . htmlspecialchars($status['description']) . '</td></tr>';
    echo '</table>';
    
    if ($status['code'] == 200) {
        echo '<div class="alert alert-success mt-3">';
        echo '<h5>✅ API KEY VALID DAN AKTIF!</h5>';
        echo '<p class="mb-0">API Key Anda berfungsi dengan baik. Silakan gunakan di aplikasi Anda.</p>';
        echo '</div>';
        
        if (isset($result['rajaongkir']['results'])) {
            echo '<div class="mt-3">';
            echo '<strong>Contoh Data Kota (Yogyakarta):</strong><br>';
            $city = $result['rajaongkir']['results'];
            echo '<pre class="bg-light p-3 mt-2">';
            echo json_encode($city, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo '</pre>';
            echo '</div>';
        }
        
        echo '<div class="alert alert-info mt-3">';
        echo '<strong>📝 Cara Menggunakan:</strong><br>';
        echo '1. Copy API Key ini<br>';
        echo '2. Paste ke file <code>kota.php</code> baris 5<br>';
        echo '3. Paste ke file <code>cek_ongkir.php</code> baris 4<br>';
        echo '4. Refresh halaman utama';
        echo '</div>';
        
    } else {
        echo '<div class="alert alert-danger mt-3">';
        echo '<h5>❌ API KEY TIDAK VALID!</h5>';
        
        if ($status['code'] == 410) {
            echo '<p><strong>Error 410 - Gone</strong></p>';
            echo '<p>Kemungkinan penyebab:</p>';
            echo '<ul>';
            echo '<li>API Key salah atau typo</li>';
            echo '<li>API Key sudah expired/tidak aktif</li>';
            echo '<li>Akun RajaOngkir belum diverifikasi</li>';
            echo '<li>Menggunakan API Key untuk tipe akun berbeda</li>';
            echo '</ul>';
            echo '<p class="mb-0"><strong>Solusi:</strong></p>';
            echo '<ol>';
            echo '<li>Login ke <a href="https://rajaongkir.com/akun/panel" target="_blank">Dashboard RajaOngkir</a></li>';
            echo '<li>Periksa status akun Anda</li>';
            echo '<li>Copy ulang API Key yang benar</li>';
            echo '<li>Pastikan tipe akun adalah "Starter"</li>';
            echo '</ol>';
        } else {
            echo '<p>Error: ' . htmlspecialchars($status['description']) . '</p>';
        }
        
        echo '</div>';
    }
} else {
    echo '</table>';
    echo '<div class="alert alert-warning mt-3">';
    echo '<strong>⚠️ Response tidak valid</strong><br>';
    echo 'Raw Response:<br>';
    echo '<pre class="bg-light p-3 mt-2" style="max-height: 300px; overflow: auto;">';
    echo htmlspecialchars($response);
    echo '</pre>';
    echo '</div>';
}

echo '</div>';
echo '</div>';
?>