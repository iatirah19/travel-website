<?php
require_once __DIR__ . '/env_loader.php';
session_start();

$data = $_SESSION['payment_data'] ?? null;

if (!$data) {
    die("No payment session found");
}

// ======================
// GET SESSION DATA FIRST
// ======================
$payment_method   = $data['payment_method'];
$txn_order_id     = $data['txn_order_id'];
$txn_amount       = $data['txn_amount'];
$txn_buyer_name   = $data['txn_buyer_name'];
$txn_buyer_email  = $data['txn_buyer_email'];
$txn_buyer_phone  = $data['txn_buyer_phone'];

// ======================
// STATIC PRODUCT INFO
// ======================
$txn_product_name = "Travel Package";
$txn_product_desc = "Travel Booking Payment";

// ======================
// LOAD ENV BASED ON METHOD
// ======================
if ($payment_method === 'card') {
  $base_url   = $_ENV['BASE_URL_CARD'] ?? '';
  $api_key    = $_ENV['API_KEY_CARD'] ?? '';
  $pub_key    = $_ENV['PUB_KEY_CARD'] ?? '';
  $secret_key = $_ENV['SECRET_KEY_CARD'] ?? '';
} else {
  $base_url   = $_ENV['BASE_URL_FPX'] ?? '';
  $api_key    = $_ENV['API_KEY_FPX'] ?? '';
  $pub_key    = $_ENV['PUB_KEY_FPX'] ?? '';
  $secret_key = $_ENV['SECRET_KEY_FPX'] ?? '';
}

// ======================
// SIGNATURE STRING
// ======================
$stringConcat = implode('|', [
  $api_key,
  $txn_amount,
  $txn_buyer_email,
  $txn_buyer_name,
  $txn_buyer_phone,
  $txn_order_id,
  $txn_product_desc,
  $txn_product_name
]);

$signature = hash_hmac('sha256', $stringConcat, $secret_key);

// ======================
// PAYLOAD
// ======================
$payload = [
  'txn_order_id'     => $txn_order_id,
  'txn_amount'       => $txn_amount,
  'txn_buyer_name'   => $txn_buyer_name,
  'txn_buyer_email'  => $txn_buyer_email,
  'txn_buyer_phone'  => $txn_buyer_phone,
  'txn_product_name' => $txn_product_name,
  'txn_product_desc' => $txn_product_desc,
  'api_key'          => $api_key,
  'signature'        => $signature
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redirecting to Payment Gateway...</title>

  <style>
    body {
      font-family: sans-serif;
      text-align: center;
      padding-top: 100px;
      background: #f9fafb;
      color: #4b5563;
    }

    .loader {
      border: 4px solid #f3f4f6;
      border-top: 4px solid #0070f3;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body>

  <div class="loader"></div>
  <p>Connecting securely to the payment gateway...</p>
  <p style="font-size: 0.85rem; color: #9ca3af;">
    Please do not close or refresh this browser tab.
  </p>

  <form id="autoSubmitFpxForm" method="POST"
        action="<?php echo htmlspecialchars("$base_url/$pub_key"); ?>">

    <?php foreach ($payload as $key => $value): ?>
      <input type="hidden"
             name="<?php echo htmlspecialchars($key); ?>"
             value="<?php echo htmlspecialchars($value); ?>">
    <?php endforeach; ?>

  </form>

  <script>
    document.getElementById('autoSubmitFpxForm').submit();
  </script>

</body>
</html>