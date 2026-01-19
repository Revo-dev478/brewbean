<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

// Load environment variables
require_once __DIR__ . '/env_loader.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean();
    exit;
}

// RajaOngkir Komerce API (from .env)
$API_KEY = env('RAJAONGKIR_API_KEY', '');
$BASE = env('RAJAONGKIR_BASE_URL', 'https://rajaongkir.komerce.id/api/v1');

// Cache Configuration
$CACHE_DIR = __DIR__ . '/cache/';
if (!is_dir($CACHE_DIR)) {
    mkdir($CACHE_DIR, 0755, true);
}

function send_response($success, $data = array(), $message = '')
{
    ob_end_clean();
    echo json_encode(array("success" => $success, "data" => $data, "message" => $message));
    exit;
}

function get_cache($key)
{
    global $CACHE_DIR;
    $file = $CACHE_DIR . md5($key) . '.json';
    if (file_exists($file)) {
        // Cache valid for 30 days for locations
        if (time() - filemtime($file) < 2592000) {
            return json_decode(file_get_contents($file), true);
        }
    }
    return false;
}

function save_cache($key, $data)
{
    global $CACHE_DIR;
    $file = $CACHE_DIR . md5($key) . '.json';
    file_put_contents($file, json_encode($data));
}

function api_request($path, $method = 'GET', $data = [])
{
    global $API_KEY, $BASE;
    $url = $BASE . "/" . $path;

    $ch = curl_init($url);
    $headers = array(
        "key: $API_KEY",
        "Accept: application/json"
    );

    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FAILONERROR => false,
        CURLOPT_TIMEOUT => 30
    );

    if ($method == 'POST') {
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
    }

    $options[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $options);

    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return array('error' => true, 'message' => "CURL Error: $err");
    }

    $json = json_decode($res, true);
    if (!$json) {
        return array('error' => true, 'message' => "Invalid JSON from API");
    }

    return $json;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    /* ================= PROVINCE ================= */
    if ($action == 'get_provinces') {
        // Check Cache
        $cache = get_cache('provinces');
        if ($cache) {
            send_response(true, $cache);
        }

        $res = api_request("destination/province");

        if (isset($res['status']) && $res['status'] == false) {
            $msg = isset($res['message']) ? $res['message'] : 'Unknown';
            send_response(false, array(), "API Error: " . $msg);
        }

        if (isset($res['data'])) {
            $data = array();
            foreach ($res['data'] as $p) {
                $data[] = array(
                    'province_id' => $p['id'],
                    'province' => $p['name']
                );
            }
            // Save to Cache
            save_cache('provinces', $data);
            send_response(true, $data);
        }
        send_response(false, array(), "No province data found");
    }

    /* ================= CITY ================= */
    if ($action == 'get_cities') {
        $pid = isset($_GET['province_id']) ? $_GET['province_id'] : '';
        if (!$pid) send_response(false, array(), "Missing province_id");

        // Check Cache
        $cacheKey = 'cities_' . $pid;
        $cache = get_cache($cacheKey);
        if ($cache) {
            send_response(true, $cache);
        }

        $res = api_request("destination/city/" . $pid);

        if (isset($res['data'])) {
            $data = array();
            foreach ($res['data'] as $c) {
                $data[] = array(
                    'city_id' => $c['id'],
                    'city_name' => $c['name'],
                    'type' => isset($c['type']) ? $c['type'] : 'Kota',
                    'postal_code' => isset($c['postal_code']) ? $c['postal_code'] : ''
                );
            }
            // Save to Cache
            save_cache($cacheKey, $data);
            send_response(true, $data);
        }
        send_response(false, array(), "No city data found");
    }

    /* ================= DISTRICT ================= */
    if ($action == 'get_districts') {
        $cid = isset($_GET['city_id']) ? $_GET['city_id'] : '';
        if (!$cid) send_response(false, array(), "Missing city_id");

        // Check Cache
        $cacheKey = 'districts_' . $cid;
        $cache = get_cache($cacheKey);
        if ($cache) {
            send_response(true, $cache);
        }

        $res = api_request("destination/district/" . $cid);

        if (isset($res['data'])) {
            $data = array();
            foreach ($res['data'] as $d) {
                $data[] = array(
                    'district_id' => $d['id'],
                    'district_name' => $d['name']
                );
            }
            // Save to Cache
            save_cache($cacheKey, $data);
            send_response(true, $data);
        }
        send_response(false, array(), "No district data found");
    }

    /* ================= COST ================= */
    if ($action == 'get_cost') {
        $dest = isset($_POST['destination']) ? $_POST['destination'] : (isset($_POST['district_id']) ? $_POST['district_id'] : '');
        $weight = isset($_POST['weight']) ? $_POST['weight'] : 1000;
        $courier = isset($_POST['courier']) ? strtolower($_POST['courier']) : '';

        // Log input
        // file_put_contents('debug_cost_input.txt', print_r($_POST, true));

        if (!$dest || !$courier) send_response(false, array(), "Incomplete Parameters");

        // 154 = Jakarta Pusat (Default). Change this if needed.
        $payload = array(
            "origin" => 154,
            "destination" => (int)$dest,
            "weight" => (int)$weight,
            "courier" => $courier,
            "price" => "lowest"
        );

        // We don't cache costs as they might change dynamically or based on weight/courier updates
        // However, if strict savings are needed, we could cache for 1 hour.
        // For now, let's keep cost real-time as it's the most critical accuracy point.

        $res = api_request("calculate/domestic-cost", "POST", $payload);

        // Log raw response
        // file_put_contents('debug_cost_response.txt', print_r($res, true));

        if (isset($res['data'])) {
            $out = array();
            // Handle both single object or array return if API varies
            $results = isset($res['data'][0]) ? $res['data'] : array($res['data']);

            // Komerce usually returns a list of services in data
            foreach ($res['data'] as $s) {
                // Ensure we handle the structure correctly
                if (is_array($s)) {
                    $out[] = array(
                        "courier" => strtoupper($courier),
                        "service" => isset($s['service']) ? $s['service'] : 'REG',
                        "price" => isset($s['cost']) ? $s['cost'] : (isset($s['price']) ? $s['price'] : 0),
                        "etd" => isset($s['etd']) ? $s['etd'] : '-'
                    );
                }
            }
            send_response(true, $out);
        }
        send_response(false, array(), "Unavailable");
    }

    send_response(false, array(), "Invalid Action");
} catch (Exception $e) {
    send_response(false, array(), "Server Error: " . $e->getMessage());
}
