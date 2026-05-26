<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>BP PG Payment Simulation</title>
  </style>
</head>

<body>

  <div class="checkout-card">
    <h2>Confirm Payment Details</h2>
    <form action="payment_redirect.php" method="POST">
      <input type="hidden" name="txn_order_id" value="<?php echo time() . '-testphp'; ?>">
      <input type="hidden" name="txn_amount" value="250.00">

      <div class="form-group">
        <label>Buyer Name</label>
        <input type="text" name="txn_buyer_name" value="Jane Doe" required>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="txn_buyer_email" value="janedoe@example.com" required>
      </div>

      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="txn_buyer_phone" value="60178910111" required>
      </div>

      <div class="payment-methods">
        <label>Select Payment Method</label>

        <label class="method-option">
          <input type="radio" name="payment_method" value="fpx" checked>
          <span>FPX Online Banking</span>
        </label>

        <label class="method-option">
          <input type="radio" name="payment_method" value="card">
          <span>Credit / Debit Card</span>
        </label>
      </div>

      <div class="price-box">
        <span>Total Payable:</span>
        <span>RM 250.00</span>
      </div>

      <button type="submit">Proceed to Payment</button>
    </form>
  </div>

</body>

</html>