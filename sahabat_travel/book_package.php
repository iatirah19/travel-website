<?php
require 'db.php';

$package_id = $_GET['package_id'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

if (empty($package_id)) {
    die("Package ID missing");
}

$package_id = mysqli_real_escape_string($conn, $package_id);

$package = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");
$pack = mysqli_fetch_assoc($package);

if (!$pack) {
    die("Invalid package selected");
}

// =======================
// SUBMIT BOOKING
// =======================
if (isset($_POST['book'])) {

    $package_id = mysqli_real_escape_string($conn, $_POST['package_id']);
    $travel_date = $_POST['travel_date'] ?? '';

    $adult = (int)($_POST['adult'] ?? 0);
    $child = (int)($_POST['child'] ?? 0);
    $pax = $adult + $child;

    $pax_names = $_POST['pax_name'] ?? [];
    $pax_phones = $_POST['pax_phone'] ?? [];
    $pax_gender = $_POST['pax_gender'] ?? [];
    $pax_state = $_POST['pax_state'] ?? [];

    $payment_method = $_POST['payment_method'] ?? '';
    $bank = $_POST['bank'] ?? NULL;

    // MAIN CUSTOMER (first pax)
    $customer_name = $pax_names[0] ?? '';
    $phone = $pax_phones[0] ?? '';
    $state = $pax_state[0] ?? '';

    if ($customer_name == '' || $phone == '') {
        die("Customer details required");
    }

    // =======================
    // INSERT INTO BOOKINGS
    // =======================
    $stmt = $conn->prepare("
        INSERT INTO bookings 
        (package_id, customer_name, phone, state, travel_date, pax, payment_method, bank, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");

    $stmt->bind_param(
        "issssiss",
        $package_id,
        $customer_name,
        $phone,
        $state,
        $travel_date,
        $pax,
        $payment_method,
        $bank
    );

    $stmt->execute();
    $booking_id = $stmt->insert_id;

    // =======================
    // INSERT INTO BOOKINGS_PAX
    // =======================
    $count = min(
        count($pax_names),
        count($pax_phones),
        count($pax_gender),
        count($pax_state)
    );

    $stmt_pax = $conn->prepare("
        INSERT INTO bookings_pax 
        (booking_id, name, phone, gender, state)
        VALUES (?, ?, ?, ?, ?)
    ");

    for ($i = 0; $i < $count; $i++) {

        $name = $pax_names[$i] ?? '';
        $phone_pax = $pax_phones[$i] ?? '';
        $gender = $pax_gender[$i] ?? '';
        $state_pax = $pax_state[$i] ?? '';

        if ($name == '') continue;

        $stmt_pax->bind_param(
            "issss",
            $booking_id,
            $name,
            $phone_pax,
            $gender,
            $state_pax
        );

        $stmt_pax->execute();
    }

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
<form method="POST">

<div class="step-container">

<h1 class="page-title">Booking Page</h1>

<a href="javascript:history.back()" class="back-btn">← Back</a>

<!-- PROGRESS -->
<div class="progress-container">
    <div class="progress-line" id="progressLine"></div>

    <div class="step active"><div class="circle">1</div><p>Package</p></div>
    <div class="step"><div class="circle">2</div><p>Pax</p></div>
    <div class="step"><div class="circle">3</div><p>Details</p></div>
    <div class="step"><div class="circle">4</div><p>Success</p></div>
</div>

<!-- STEP 1 -->
<div class="step-content active">
    <h3>Package Details</h3>

    <p><b>Package:</b> <?php echo $pack['title']; ?></p>
    <p><b>Travel Date:</b> <?php echo date("d M Y", strtotime($travel_date)); ?></p>

    <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
    <input type="hidden" name="travel_date" value="<?php echo $travel_date; ?>">

    <button type="button" onclick="nextStep()">Next</button>
</div>

<!-- STEP 2 -->
<div class="step-content">
    <h3>Select Pax</h3>

    <label>Dewasa:</label>
    <button type="button" onclick="changePax('adult', -1)">-</button>
    <input type="number" id="adult" name="adult" value="0">
    <button type="button" onclick="changePax('adult', 1)">+</button>

    <br><br>

    <label>Kanak-kanak:</label>
    <button type="button" onclick="changePax('child', -1)">-</button>
    <input type="number" id="child" name="child" value="0">
    <button type="button" onclick="changePax('child', 1)">+</button>

    <br><br>

    <button type="button" onclick="prevStep()">Back</button>
    <button type="button" onclick="generatePaxForm(); nextStep()">Next</button>
</div>

<!-- STEP 3 -->
<div class="step-content">
    <h3>Pax Details</h3>

    <div id="paxForm"></div>

    <label>Address:</label>
    <textarea name="address" required></textarea>

    <br><br>

    <!-- PAYMENT -->
    <label>Payment Method:</label>
    <select name="payment_method" id="payment_method" onchange="showPaymentForm()" required>
        <option value="">Select</option>
        <option value="card">Card</option>
        <option value="fpx">FPX</option>
        <option value="cash">Cash</option>
    </select>

    <div id="paymentDetails"></div>

    <br><br>

    <button type="button" onclick="prevStep()">Back</button>
    <button type="button" onclick="nextStep()">Next</button>
</div>

<!-- STEP 4 -->
<div class="step-content">
    <h3>Confirm Booking</h3>
    <p>Click confirm to submit your booking.</p>

    <button type="button" onclick="prevStep()">Back</button>
    <button type="submit" name="book">Confirm Booking</button>
</div>

</div>

</form>

<script>
let currentStep = 0;

const steps = document.querySelectorAll(".progress-container .step");
const contents = document.querySelectorAll(".step-content");

function showStep(index) {
    contents.forEach(c => c.classList.remove("active"));

    steps.forEach((s, i) => {
        s.classList.remove("active", "completed");

        if (i < index) {
            s.classList.add("completed");
            s.querySelector(".circle").textContent = "✓";
        } else {
            s.querySelector(".circle").textContent = i + 1;
        }
    });

    steps[index].classList.add("active");
    contents[index].classList.add("active");

    currentStep = index;
    updateProgressLine();
}

function updateProgressLine() {
    const percent = (currentStep / (steps.length - 1)) * 100;
    document.getElementById("progressLine").style.width = percent + "%";
}

function nextStep() {
    if (currentStep < contents.length - 1) {
        showStep(currentStep + 1);
    }
}

function prevStep() {
    if (currentStep > 0) {
        showStep(currentStep - 1);
    }
}

showStep(0);

// =================
// PAX
// =================
function changePax(type, value) {
    let input = document.getElementById(type);
    let current = parseInt(input.value) || 0;

    if (current + value >= 0) {
        input.value = current + value;
    }
}

function generatePaxForm() {
    let adult = parseInt(document.getElementById("adult").value) || 0;
    let child = parseInt(document.getElementById("child").value) || 0;

    let total = adult + child;

    if (total <= 0) {
        alert("Please select at least 1 pax");
        return;
    }

    let html = "";

    for (let i = 1; i <= total; i++) {
        html += `
            <h4>Pax ${i}</h4>
            <input type="text" name="pax_name[]" placeholder="Name" required>
            <input type="text" name="pax_phone[]" placeholder="Phone" required>

            <select name="pax_gender[]" required>
                <option value="">Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <input type="text" name="pax_state[]" placeholder="State" required>
            <hr>
        `;
    }

    document.getElementById("paxForm").innerHTML = html;
}

// =================
// PAYMENT
// =================
function showPaymentForm() {
    let method = document.getElementById("payment_method").value;
    let html = "";

    if (method === "card") {
        html = `
            <input type="text" placeholder="Card Number" required>
            <input type="text" placeholder="Expiry Date" required>
            <input type="text" placeholder="CVV" required>
        `;
    }

    else if (method === "fpx") {
        html = `
            <select name="bank" required>
                <option value="">Select Bank</option>
                <option value="Maybank">Maybank</option>
                <option value="CIMB">CIMB</option>
                <option value="Bank Islam">Bank Islam</option>
            </select>
        `;
    }

    else if (method === "cash") {
        html = `<p><b>Pay at counter</b></p>`;
    }

    document.getElementById("paymentDetails").innerHTML = html;
}
</script>

</body>
</html>