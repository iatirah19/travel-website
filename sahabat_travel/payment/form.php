<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>BP PG Payment Simulation</title>
  <style>
    body {
    font-family: 'Segoe UI', sans-serif;
    min-height: 70vh;
    padding: 20px;
}

    .checkout-card {
    background: white;
    
    width: 100%;
    padding: 20px;
    border-radius: 12px;
    
    margin: 20px 0; /* Added slight margin so it sits nicely inside the step */
}

/* Scoped headings and labels specifically for checkout */
.checkout-card h2 {
    margin-top: 0;
    color: #111827;
    font-size: 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 15px;
}

.checkout-card .form-group {
    margin-bottom: 18px;
}

.checkout-card label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    font-size: 0.875rem;
    color: #374151;
    text-transform: none; /* Overrides the booking form uppercase labels */
    letter-spacing: normal;
}

.checkout-card input[type="text"],
.checkout-card input[type="email"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 0.95rem;
}

.checkout-card input:focus {
    outline: 2px solid #143f56;
    border-color: transparent;
    box-shadow: none; /* Prevents booking form box-shadow conflict */
}

/* Payment Methods (Unified) */
.payment-methods {
    margin-top: 20px;
}

.method-option {
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #d1d5db;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: border-color 0.2s;
}

.method-option:hover {
    border-color: #0070f3;
}

.method-option input {
    margin-right: 12px;
}

.checkout-card .price-box {
    background: #f3f4f6;
    padding: 12px;
    border-radius: 6px;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    font-weight: 600;
    color: #1f2937;
}

/* Scoped Checkout Button */
.checkout-card button {
    background: #143f56;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 6px;
    width: 100%;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 15px;
    transition: background 0.2s;
}

.checkout-card button:hover {
    background: #143f56;
    transform: none; /* Overrides booking hover bounce */
    box-shadow: none; /* Overrides booking hover shadow */
}
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