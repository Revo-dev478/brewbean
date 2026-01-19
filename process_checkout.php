<?php
session_start();
require_once 'config.php';

// Set JSON Header
header('Content-Type: application/json');

// 1. Auth Check
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Silakan login terlebih dahulu.']);
    exit;
}

$id_user = $_SESSION['id_user'];
$serverKey = env('MIDTRANS_SERVER_KEY', '');
$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
$apiUrl = $isProduction
    ? 'https://app.midtrans.com/snap/v1/transactions'
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

// 2. Validate Inputs
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid Request Method']);
    exit;
}

// Getting data from $_POST (sent by checkout.php)
$nama = $_POST['nama'] ?? 'Guest';
$email = $_POST['email'] ?? 'guest@example.com';
$phone = $_POST['phone'] ?? '08123456789';
$amount = (int)($_POST['total'] ?? 0);
$shipping_cost = (int)($_POST['shipping_cost'] ?? 0);
$courier = $_POST['courier'] ?? 'JNE';

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Total amount invalid']);
    exit;
}

$order_id = 'ORD-' . time() . '-' . rand(100, 999);

// 3. Database Insert (Header)
// Using table 'checkout' as seen in midtrans_token.php
$query = "INSERT INTO checkout (
            order_id, id_user, total_harga, status_checkout, metode_pembayaran, created_at
          ) VALUES (
            '$order_id', '$id_user', '$amount', 'pending', 'midtrans', NOW()
          )";

if (!mysqli_query($koneksi, $query)) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . mysqli_error($koneksi)]);
    exit;
}

// 4. Prepare Items for Midtrans & DB Detail
$item_details = [];

// A. Product Items
$cartQuery = mysqli_query($koneksi, "
    SELECT k.qty, k.subtotal, p.nama_product, p.harga, p.id_product 
    FROM tabel_keranjang k 
    JOIN tabel_product p ON k.id_product = p.id_product 
    WHERE k.id_user = '$id_user'
");

while ($item = mysqli_fetch_assoc($cartQuery)) {
    $pName = $item['nama_product'];
    $pPrice = (int)$item['harga'];
    $pQty = (int)$item['qty'];

    // Validate Subtotal
    $msgSubtotal = $pPrice * $pQty;

    // Add to Midtrans Items
    $item_details[] = [
        'id' => $item['id_product'],
        'price' => $pPrice,
        'quantity' => $pQty,
        'name' => substr($pName, 0, 50)
    ];

    // Insert to checkout_item table
    $pNameEscaped = mysqli_real_escape_string($koneksi, $pName);
    mysqli_query($koneksi, "
        INSERT INTO checkout_item (order_id, product_name, price, quantity, subtotal)
        VALUES ('$order_id', '$pNameEscaped', '$pPrice', '$pQty', '$msgSubtotal')
    ");
}

// B. Shipping Cost Item
if ($shipping_cost > 0) {
    $item_details[] = [
        'id' => 'SHIP',
        'price' => $shipping_cost,
        'quantity' => 1,
        'name' => 'Ongkos Kirim (' . strtoupper($courier) . ')'
    ];
}

// 5. Build Midtrans Payload
$transaction_details = [
    'order_id' => $order_id,
    'gross_amount' => $amount // Must match sum of items
];

// Verify Total Calculation for Midtrans (Strict validation)
$calculatedTotal = 0;
foreach ($item_details as $d) {
    $calculatedTotal += ($d['price'] * $d['quantity']);
}
if ($calculatedTotal != $amount) {
    // Correction if frontend total differs slightly (e.g. strict rounding)
    // We trust the calculated total from backend items + shipping
    $transaction_details['gross_amount'] = $calculatedTotal;

    // Update DB to match real total
    mysqli_query($koneksi, "UPDATE checkout SET total_harga = '$calculatedTotal' WHERE order_id = '$order_id'");
}

$params = [
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
    'customer_details' => [
        'first_name' => $nama,
        'email' => $email,
        'phone' => $phone
    ]
];

// 6. Call Midtrans API
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($params),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$resData = json_decode($response, true);

if ($httpCode == 201 && isset($resData['token'])) {
    // 7. Success

    // Clear Cart
    mysqli_query($koneksi, "DELETE FROM tabel_keranjang WHERE id_user = '$id_user'");

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'snap_token' => $resData['token'] // checkout.php expects snap_token
    ]);
} else {
    // 8. Error
    echo json_encode([
        'success' => false,
        'message' => 'Midtrans Error: ' . ($resData['error_messages'][0] ?? 'Unknown Error'),
        'debug' => $resData
    ]);
}
