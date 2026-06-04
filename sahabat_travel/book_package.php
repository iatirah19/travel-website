<?php
session_start();
require 'db.php';

/* =======================
   LOGIN CHECK
======================= */

if (!isset($_SESSION['user_id'])) {
    echo "
    <script>
        alert('Please login first before booking a package.');
        window.location.href='auth.php';
    </script>
    ";
    exit();
}

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

// --- NEW: FETCH DYNAMIC PRICES BASED ON TYPE ---
// 1. Fetch Adult Price
$adult_query = mysqli_query($conn, "SELECT price FROM package_pricing WHERE package_id='$package_id' AND type='adult'");
$price_adult = ($adult_row = mysqli_fetch_assoc($adult_query)) ? $adult_row['price'] : 0;

// 2. Fetch Child Price
$child_query = mysqli_query($conn, "SELECT price FROM package_pricing WHERE package_id='$package_id' AND type='child'");
$price_child = ($child_row = mysqli_fetch_assoc($child_query)) ? $child_row['price'] : 0;
// -----------------------------------------------

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

    // --- NEW T&C VARIABLES ---
    $digital_signature = mysqli_real_escape_string($conn, $_POST['digital_signature'] ?? '');
    $requested_copy = mysqli_real_escape_string($conn, $_POST['wants_tnc_copy'] ?? 'no');
    $tnc_accepted = isset($_POST['accept_tnc']) ? 1 : 0; 
    $current_time = date('Y-m-d H:i:s');
    // -------------------------

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
        (package_id, customer_name, phone, state, travel_date, pax, payment_method, bank, status, tnc_accepted, digital_signature, requested_tnc_copy, tnc_accepted_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssississs",
        $package_id,
        $customer_name,
        $phone,
        $state,
        $travel_date,
        $pax,
        $payment_method,
        $bank,
        $tnc_accepted,
        $digital_signature,
        $requested_copy,
        $current_time
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
    <meta charset="UTF-8">
    <title>Book Package</title>
    <link rel="stylesheet" href="book_package.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<form id="bookingForm" method="POST">

<div class="step-container">

<h1 class="page-title">Booking Page</h1>

<div class="progress-container">
    <div class="progress-line" id="progressLine"></div>

    <div class="step active"><div class="circle">1</div><p>Package</p></div>
    <div class="step"><div class="circle">2</div><p>Pax</p></div>
    <div class="step"><div class="circle">3</div><p>Details</p></div>
    <div class="step"><div class="circle">4</div><p>Payment</p></div>
</div>

<div class="step-content active">
    <h3>Package Details</h3>

    <p><b>Package:</b> <?php echo $pack['title']; ?></p>
    <p><b>Travel Date:</b> <?php echo date("d M Y", strtotime($travel_date)); ?></p>

    <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
    <input type="hidden" name="travel_date" value="<?php echo $travel_date; ?>">

    <button type="button" onclick="nextStep()">Next</button>
</div>

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

<div class="step-content">
    <h3>Pax Details</h3>

    <div id="paxForm"></div>

    <label>Address:</label>
    <textarea name="address" required></textarea>


    <button type="button" onclick="prevStep()">Back</button>
    <button type="button" onclick="nextStep()">Next</button>
</div>

<div class="step-content">
    <?php include 'payment/form.php';?> 
    
    <div class="tnc-section">
        <h4>Terms & Conditions</h4>
        
        <div class="tnc-scrollbox">
            <p>RESERVATION</p>
            <p>- To confirm your reservation, a booking deposit to be paid to the Company or its licensed representatives.</p>
            <p>- If reservation is made less than 45 days before departure, the full applicable tour fare is payable at the point of reservation.</p>
            <P>- Balance payment of the applicable tour fare shall be made no later than 45 days before departure. Failure to do so may result in your reservation being cancelled and deposits forfeited.</p>
            <p>- All payments can be made via bank transfer, cheque or cash.</p>
            <p>- Credit cards are accepted at certain licensed representatives with an administrative charge.</p>
            <p>TOUR FARE INCLUSIONS</p>
            <p>- Your tour fare includes all airfares, airport taxes, accommodation, entrance fees, meals and gratuities to drivers and tour managers as specified in the tour brochure.</p>
            <p>TOUR FARE EXCLUSIONS</p>
            <p>- Your tour fare excludes travel insurance, visa fees (if any), excess baggage charges, optional tour activities (if any) and all items of a personal nature.</p>
            <p>CHANGE OF TOUR DATE OR TOUR PACKAGE</p>
            <p>- At any time up to 45 days before departure, you can request to change your booking to another departure date or a different similar tour at no extra charges. Beyond this time frame, you are deemed to have cancelled your tour and the following cancellation charges apply.</p>
            <p>CANCELLATION CHARGES</p>
            <p>- A cancellation of booking at your request must be made in writing to avoid dispute on the timing of cancellation as different charges applies.</p>
            <p>- Failure to show up on departure date or denied boarding for whatever reason shall be deemed to a cancellation of tour at last minute. If you wish to rejoin the tour at your own costs, please inform the Company ahead with the understanding there is no refund for any unutilized services.</p>
            <p>TOUR CANCELLATION BY THE COMPANY</p>
            <p>- The confirmation of all tour departures are subject to minimum group size of 20 per departure.</p>
            <p>- If it becomes necessary for the Company to cancel any departure due to poor responses, all payments made to the Company will be refunded in full within 14 days of tour cancellation notice.</p>
            <p>- The safety of all tour members and tour managers is our paramount priority. Hence, the Company will abide by any travel prohibition/ advisory issued by the authorities to cancel any departure. However, "fear of travel" by any individual in the absence of such prohibition/ advisory will be subjected to normal cancellation charges.</p>
            <p>VALID TRAVEL DOCUMENTS</p>
            <p>- It is your responsibility to ensure your passport has at least 6 months validity from the date of the last departure point for home</p>
            <p>- It is your responsibility to obtain the necessary visa or health certificate (based on your nationality as required by the respective authorities of the countries visited during the tour.</p>
            <p>- The Company is not liable for any compensation or refund to you shall you be denied travelling due to the above non-compliances.</p>
            <p>TRAVEL INSURANCE</p>
            <p>We strongly advised you to purchase your preferred travel insurance coverage to minimize your losses due to enforced trip cancellation, medical and hospitalization costs, theft, baggage lost etc. We will be pleased to assist you on this on request.</p>
            <p>CHANGES TO THE ITINERARY</p>
            <p>- While we endeavor to deliver all services according to specifications as detailed in our tour brochures, the Company reserve the right to alter the itinerary due to unusual traffic conditions, adverse weather, natural disasters and any reasons beyond our control.</p>
            <p>45 days and above : Deposit forfeiture</p>
            <p>30 - 44 days : 50% of tour fare forfeited</p>
            <p> Below 30 days : 100% of tour fare forfeited</p>

        </div>

        <label class="tnc-checkbox-label">
            <input type="checkbox" id="accept_tnc" name="accept_tnc" required>
            I have read and accept the Terms & Conditions
        </label>
        
        <input type="text" name="digital_signature" class="signature-input" placeholder="Type your full name as signature" required>
    </div>

    <button type="button" onclick="prevStep()">Back</button>

</div>

</div>

<div id="copyModal" class="custom-modal">
    <div class="modal-content">
        <h3>Keep a Copy?</h3>
        <p>You have successfully accepted the Terms & Conditions. Would you like us to send a copy to your email for your records?</p>
        <div class="modal-buttons">
            <button type="button" id="btnYesCopy" class="btn-yes">Yes, email me a copy</button>
            <button type="button" id="btnNoCopy" class="btn-no">No, skip this</button>
        </div>
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
    const percent = (currentStep / (steps.length - 1)) * 75;
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

// =================
// T&C MODAL LOGIC
// =================
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm'); 
    const modal = document.getElementById('copyModal');
    const btnYes = document.getElementById('btnYesCopy');
    const btnNo = document.getElementById('btnNoCopy');

    // Intercept form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault(); 
        modal.style.display = 'flex';
    });

    btnYes.addEventListener('click', function() {
        submitFormWithPreference('yes');
    });

    btnNo.addEventListener('click', function() {
        submitFormWithPreference('no');
    });

    function submitFormWithPreference(choice) {
        // Create hidden input for email preference
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'wants_tnc_copy';
        hiddenInput.value = choice;
        form.appendChild(hiddenInput);

        // CREATE HIDDEN BUTTON TO TRIGGER PHP
        const submitSimulator = document.createElement('input');
        submitSimulator.type = 'hidden';
        submitSimulator.name = 'book';
        submitSimulator.value = '1';
        form.appendChild(submitSimulator);
        
        modal.style.display = 'none';
        form.submit(); 
    }
});
</script>

</body>
</html>