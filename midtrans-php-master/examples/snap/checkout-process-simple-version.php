<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';

// Load environment variables from project root
$envPath = dirname(__FILE__) . '/../../../.env';
if (file_exists($envPath)) {
    require_once dirname(__FILE__) . '/../../../env_loader.php';
}

// Set Your server key - loaded from environment
Config::$serverKey = function_exists('env') ? env('MIDTRANS_SERVER_KEY', '') : 'your-server-key-here';
Config::$clientKey = function_exists('env') ? env('MIDTRANS_CLIENT_KEY', '') : 'your-client-key-here';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// Uncomment for production environment
// Config::$isProduction = true;
Config::$isSanitized = Config::$is3ds = true;

// Required
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 'ORDER-' . time();
$gross_amount = isset($_POST['gross_amount']) ? (int)$_POST['gross_amount'] : 0;

// Optional
$item_details = array(
    array(
        'id' => 'item1',
        'price' => $gross_amount,
        'quantity' => 1,
        'name' => isset($_POST['item_name']) ? $_POST['item_name'] : "Product"
    ),
);

// Optional
$customer_name = isset($_POST['customer_name']) ? $_POST['customer_name'] : 'Customer';
$customer_email = isset($_POST['customer_email']) ? $_POST['customer_email'] : 'customer@example.com';
$customer_phone = isset($_POST['customer_phone']) ? $_POST['customer_phone'] : '0812345678';

$transaction_details = array(
    'order_id' => $order_id,
    'gross_amount' => $gross_amount,
);

$customer_details = array(
    'first_name'    => $customer_name,
    'email'         => $customer_email,
    'phone'         => $customer_phone,
);

// Fill transaction details
$transaction = array(
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $item_details,
);

$snap_token = '';
try {
    $snap_token = Snap::getSnapToken($transaction);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

function printExampleWarningMessage()
{
    if (strpos(Config::$serverKey, 'your ') != false) {
        echo "<h4>⚠️ Silakan set Server Key dan Client Key Anda</h4>";
        die();
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout - Midtrans Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Silakan Lakukan Pembayaran</h2>
        <button id="pay-button">Bayar Sekarang</button>
    </div>

    <!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo Config::$clientKey; ?>"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            // SnapToken acquired from previous step
            snap.pay('<?php echo $snap_token; ?>', {
                onSuccess: function(result) {
                    window.location.href = 'confirmation.php?order_id=<?php echo $order_id; ?>&status=success';
                },
                onPending: function(result) {
                    window.location.href = 'confirmation.php?order_id=<?php echo $order_id; ?>&status=pending';
                },
                onError: function(result) {
                    window.location.href = 'confirmation.php?order_id=<?php echo $order_id; ?>&status=error';
                },
                onClose: function() {
                    console.log('Payment popup closed.');
                }
            });
        };
    </script>
</body>

</html>