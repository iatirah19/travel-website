<?php
require '../db.php';

// ✅ FIX: define step (avoid undefined error)
$step = $_GET['step'] ?? 1;

$package_id = $_GET['package_id'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

$package = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");
$pack = mysqli_fetch_assoc($package);

// ✅ elak error null
if (!$pack) {
    $pack['title'] = "No package found";
}

if (isset($_POST['book'])) {

    $package_id = $_POST['package_id'];
    $travel_date = $_POST['travel_date'] ?? '';

    $adult = $_POST['adult'];
    $child = $_POST['child'];
    $pax = $adult + $child;

    $pax_names = $_POST['pax_name'];
    $pax_phones = $_POST['pax_phone'];
    $pax_gender = $_POST['pax_gender'];
    $pax_state = $_POST['pax_state'];

    $payment_method = $_POST['payment_method'];
    $bank = $_POST['bank'] ?? NULL;

    $customer_name = $pax_names[0];
    $phone = $pax_phones[0];
    $state = $pax_state[0];
    $address = $_POST['address'];

    $sql = "INSERT INTO bookings 
    (package_id, customer_name, address, phone, state, travel_date, pax, payment_method, bank, status)
    VALUES 
    ('$package_id', '$customer_name', '$address', '$phone', '$state', '$travel_date', '$pax', '$payment_method', '$bank', 'pending')";

    mysqli_query($conn, $sql);

    $booking_id = mysqli_insert_id($conn);

    for ($i = 0; $i < count($pax_names); $i++) {

        $name = $pax_names[$i];
        $phone = $pax_phones[$i];
        $gender = $pax_gender[$i];
        $state = $pax_state[$i];

        $sql_pax = "INSERT INTO booking_pax 
        (booking_id, name, phone, gender, state)
        VALUES 
        ('$booking_id', '$name', '$phone', '$gender', '$state')";

        mysqli_query($conn, $sql_pax);
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
<title>Book Package - Sahabat International Travel Sdn Bhd</title>
<link rel="stylesheet" href="book_package.css">
</head>

<body>
<form method="POST">

<div class="step-container">

<h1 class="page-title">Booking Page</h1>

<a href="javascript:history.back()" class="back-btn">← Back</a>

    <!-- PROGRESS BAR -->
    <div class="progress-container">
	<div class="progress-line" id="progressLine"></div>
        <div class="step active"><div class="circle">1</div><p>Package</p></div>
        <div class="step"><div class="circle">2</div><p>Pax</p></div>
        <div class="step"><div class="circle">3</div><p>Details</p></div>
        <div class="step"><div class="circle">4</div><p>Payment</p></div>
        <div class="step"><div class="circle">5</div><p>Success</p></div>
	</div>

    <!-- STEP 1 -->
    <div class="step-content active">
        <h3>Package Details</h3>

        <p><b>Package Name:</b> <?php echo $pack['title']; ?> <?php echo $pack['duration']; ?></p>
        <p><b>Travel Date:</b> <?php echo $travel_date; ?></p>

        <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
        <input type="hidden" name="travel_date" value="<?php echo $travel_date; ?>">

        <br><br>
        <button type="button" onclick="nextStep()">Next</button>
    </div>

    <!-- STEP 2 -->
    <div class="step-content">
        <h3>Select Pax</h3>

        <label>Dewasa:</label>
        <button type="button" onclick="changePax('adult', -1)">-</button>
        <input type="number" id="adult" name="adult" value="0" required>
        <button type="button" onclick="changePax('adult', 1)">+</button>

        <br><br>

        <label>Kanak-kanak:</label>
        <button type="button" onclick="changePax('child', -1)">-</button>
        <input type="number" id="child" name="child" value="0" required>
        <button type="button" onclick="changePax('child', 1)">+</button>

        <br><br>

        <button type="button" onclick="prevStep()">Back</button>
        <button type="button" onclick="validateStep2()">Next</button>
    </div>

    <!-- STEP 3 -->
    <div class="step-content">
        <h3>Pax Details</h3>

        <div id="paxForm"></div>

        <label>Address:</label>
        <textarea name="address" required></textarea>

        <br><br>
        <button type="button" onclick="prevStep()">Back</button>
        <button type="button" onclick="validateStep3()">Next</button>
    </div>

    <!-- STEP 4 -->
    <div class="step-content">
        <h3>Payment</h3>

        <select name="payment_method" id="payment_method" onchange="showPaymentForm()" required>
            <option value="">-- Select Payment --</option>
            <option value="card">Card</option>
            <option value="fpx">FPX</option>
            <option value="cash">Cash</option>
        </select>

        <div id="paymentDetails"></div>

        <br><br>
        <button type="button" onclick="prevStep()">Back</button>
        <button type="submit" name="book">Pay Now</button>
    </div>

	<div class="step-content">
		<h3>Success</h3>
		<p>Booking complete!</p>
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
    const progressLine = document.getElementById("progressLine");
    const percent = (currentStep / (steps.length - 1)) * 100;
    progressLine.style.width = percent + "%";
}

function prevStep() {
    if (currentStep > 0) {
        showStep(currentStep - 1);
    }
}

// =====================
// FIXED NEXT STEP (CLEAN VERSION)
// =====================
function nextStep() {

    // STEP 2: pax validation (IMPORTANT FIX)
    if (currentStep === 1) {

        let adult = parseInt(document.getElementById("adult").value) || 0;
        let child = parseInt(document.getElementById("child").value) || 0;

        if (adult + child < 1) {
            alert("Please select at least 1 pax");
            return;
        }

        generatePaxForm();
    }

    // STEP 3: validate pax form + address
    if (currentStep === 2) {

        let paxInputs = document.querySelectorAll("#paxForm input, #paxForm select");
        let address = document.querySelector("textarea[name='address']");

        if (paxInputs.length === 0) {
            alert("Please complete Step 2 first");
            return;
        }

        for (let field of paxInputs) {
            if (!field.checkValidity()) {
                field.reportValidity();
                return;
            }
        }

        if (!address.checkValidity()) {
            address.reportValidity();
            return;
        }
    }

    // STEP 4: payment validation
    if (currentStep === 3) {

        let payment = document.getElementById("payment_method").value;

        if (payment === "") {
            alert("Please select payment method");
            return;
        }
    }

    // move step
    if (currentStep < contents.length - 1) {
        showStep(currentStep + 1);
    }
}

// INIT
showStep(0);


// =====================
// PAX FUNCTION
// =====================
function changePax(type, value) {
    let input = document.getElementById(type);
    let current = parseInt(input.value);

    if (current + value >= 0) {
        input.value = current + value;
    }
}

function generatePaxForm() {

    let adult = parseInt(document.getElementById("adult").value) || 0;
    let child = parseInt(document.getElementById("child").value) || 0;

    let total = adult + child;

    if (total < 1) {
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


// =====================
// PAYMENT
// =====================
function showPaymentForm() {

    let method = document.getElementById("payment_method").value;
    let html = "";

    if (method === "card") {
        html = `
            <input type="text" name="card_number" placeholder="Card Number" required>
            <input type="text" name="expiry" placeholder="Expiry Date" required>
            <input type="text" name="cvv" placeholder="CVV" required>
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
        html = `<p><b>Pay at counter upon arrival.</b></p>`;
    }

    document.getElementById("paymentDetails").innerHTML = html;
}
</script>

</body>
</html>