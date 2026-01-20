<?php

/**
 * MIDTRANS TOKEN HANDLER + DATABASE CHECKOUT
 * FIXED | SAFE | NO UI CHANGE
 */

// Enable error reporting untuk debugging (disable di production nanti)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

session_start();
require_once 'config.php';

/* ==========================
   MIDTRANS CONFIG (from .env)
   ========================== */
$serverKey = env('MIDTRANS_SERVER_KEY', '');
$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

$apiUrl = $isProduction
    ? 'https://app.midtrans.com/snap/v1/transactions'
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

/* ==========================
   VALIDASI USER
   ========================== */
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id_user = $_SESSION['id_user'];

/* ==========================
   AMBIL DATA FRONTEND
   ========================== */
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$order_id = 'ORDER-' . time() . '-' . rand(100, 999);
$amount   = (int) $input['amount'];

/* ==========================
   INSERT KE TABLE CHECKOUT
   ========================== */
mysqli_query($koneksi, "
    INSERT INTO checkout (
        order_id,
        id_user,
        total_harga,
        status_checkout,
        metode_pembayaran,
        created_at
    ) VALUES (
        '$order_id',
        '$id_user',
        '$amount',
        'pending',
        'midtrans',
        NOW()
    )
");

/* ==========================
   INSERT ITEMS TO DB
   ========================== */
$cart_query = mysqli_query($koneksi, "
    SELECT k.qty, p.nama_product, p.harga, p.id_product 
    FROM tabel_keranjang k 
    JOIN tabel_product p ON k.id_product = p.id_product 
    WHERE k.id_user = '$id_user'
");

if ($cart_query) {
    while ($item = mysqli_fetch_assoc($cart_query)) {
        $pName = mysqli_real_escape_string($koneksi, $item['nama_product']);
        $pPrice = $item['harga'];
        $pQty = $item['qty'];
        $pSubtotal = $pPrice * $pQty;
        $pId = $item['id_product'];

        // Insert to checkout_item
        mysqli_query($koneksi, "
            INSERT INTO checkout_item (order_id, id_product, product_name, price, quantity, subtotal)
            VALUES ('$order_id', '$pId', '$pName', '$pPrice', '$pQty', '$pSubtotal')
        ");
    }
}

/* ==========================
   PAYLOAD MIDTRANS
   ========================== */
$params = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $amount
    ],
    'customer_details' => [
        'first_name' => $input['first_name'],
        'email' => $input['email'],
        'phone' => $input['phone'],
        'shipping_address' => [
            'first_name' => $input['first_name'],
            'address' => $input['address'],
            'city' => $input['city'],
            'postal_code' => $input['postal_code'],
            'country_code' => 'IDN'
        ]
    ],
    'item_details' => [
        [
            'id' => 'TOTAL',
            'price' => $amount,
            'quantity' => 1,
            'name' => 'Total Pembelian'
        ]
    ]
];

/* ==========================
   CURL KE MIDTRANS
   ========================== */
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


/* ==========================
   RESPONSE
   ========================== */
if ($httpCode == 201) {
    $responseData = json_decode($response, true);

    // Tambahkan order_id ke response
    $responseData['order_id'] = $order_id;

    // --- INSERT KE TABLE TRANSAKSI_MIDTRANS (PENDING) ---
    $gross_amount = $amount;
    $curr_time = date('Y-m-d H:i:s');

    // Prepare items encoded for 'detail_item'
    $items_json = json_encode($params['item_details']);
    $items_escaped = mysqli_real_escape_string($koneksi, $items_json);

    // Note: detail_item column mungkin tidak ada, gunakan column yang ada saja
    $query_insert = "
        INSERT INTO transaksi_midtrans (
            order_id, id_user, gross_amount, payment_type, transaction_status, transaction_time
        ) VALUES (
            '$order_id', '$id_user', '$gross_amount', 'midtrans', 'pending', '$curr_time'
        )
    ";

    if (!mysqli_query($koneksi, $query_insert)) {
        // Log error explicitly
        file_put_contents('debug_midtrans_insert.txt', date('Y-m-d H:i:s') . " - Error: " . mysqli_error($koneksi) . "\n", FILE_APPEND);
    } else {
        file_put_contents('debug_midtrans_insert.txt', date('Y-m-d H:i:s') . " - Success Insert: $order_id\n", FILE_APPEND);
    }

    // --- CLEAR KERANJANG DISINI ---
    // Karena order sudah tercatat di tabel checkout dan akan dibayar,
    // kita kosongkan keranjang user ini.
    mysqli_query($koneksi, "DELETE FROM tabel_keranjang WHERE id_user = '$id_user'");

    echo json_encode($responseData);
} else {
    echo json_encode([
        'error' => 'Midtrans Error',
        'http_code' => $httpCode,
        'response' => json_decode($response, true)
    ]);
}
