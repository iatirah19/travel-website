<?php
require '../db.php';

$package_id = $_GET['package_id'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

$package = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");
$pack = mysqli_fetch_assoc($package);

if (isset($_POST['book'])) {

    $package_id = $_POST['package_id'];
    $travel_date = $_POST['travel_date'];
    $pax = $_POST['pax'];

    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $state = $_POST['state'];

    $payment_method = $_POST['payment_method'];
    $bank = $_POST['bank'] ?? NULL;

    $sql = "INSERT INTO bookings 
    (package_id, customer_name, address, phone, state, travel_date, pax, payment_method, bank, status)
    VALUES 
    ('$package_id', '$name', '$address', '$phone', '$state', '$travel_date', '$pax', '$payment_method', '$bank', 'pending')";

    mysqli_query($conn, $sql);

    echo "<script>
        alert('Booking berjaya!');
        window.location='my_booking.php';
    </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Package</title>
<link rel="stylesheet" href="book_package.css">
</head>

<body>

<div class="box">
<h2>Book Package</h2>

<form method="POST">

<input type="hidden" name="package_id" value="<?= $package_id ?>">

<!-- ================= STEP 1 ================= -->
<div class="step" id="step1">
    <h3>Step 1: Package Details</h3>

    <label>Package</label>
    <input type="text" value="<?= $pack['title'] ?? '' ?>" disabled>

    <label>Travel Date</label>
    <input type="date" name="travel_date" value="<?= $travel_date ?>" required>

    <button type="button" onclick="nextStep(2)">Next</button>
</div>

<!-- ================= STEP 2 ================= -->
<div class="step" id="step2" style="display:none;">
    <h3>Step 2: Pax</h3>

    <label>Pax</label>
    <input type="number" name="pax" min="1" required>

    <button type="button" onclick="nextStep(1)">Back</button>
    <button type="button" onclick="nextStep(3)">Next</button>
</div>

<!-- ================= STEP 3 ================= -->
<div class="step" id="step3" style="display:none;">
    <h3>Step 3: Customer Details</h3>

    <label>Name</label>
    <input type="text" name="name" required>

    <label>Address</label>
    <input type="text" name="address" required>

    <label>Phone</label>
    <input type="text" name="phone" required>

    <label>State</label>
    <input type="text" name="state" required>

    <button type="button" onclick="nextStep(2)">Back</button>
    <button type="button" onclick="nextStep(4)">Next</button>
</div>

<!-- ================= STEP 4 ================= -->
<div class="step" id="step4" style="display:none;">
    <h3>Step 4: Payment</h3>

    <label>Payment Method</label>
    <select name="payment_method" required>
        <option value="">-- Select --</option>
        <option value="stripe">Card</option>
        <option value="billplz">FPX</option>
        <option value="cash">Cash</option>
    </select>

    <label>Select Bank (FPX)</label>
    <input type="text" name="bank">

    <button type="button" onclick="nextStep(3)">Back</button>
    <button type="submit" name="book">Submit Booking</button>
</div>

</form>
</div>

<script>
function nextStep(step) {
    document.querySelectorAll(".step").forEach(s => s.style.display = "none");
    document.getElementById("step" + step).style.display = "block";
}
</script>

</body>
</html>