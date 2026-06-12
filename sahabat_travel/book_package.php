<?php
session_start();
require 'db.php';

/* =======================
   LOGIN CHECK
======================= */
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please login first before booking a package.');
        window.location.href='auth.php';
    </script>";
    exit();
}

/* =======================
   GET DATA
======================= */
$package_id = $_GET['package_id'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

if (empty($package_id)) {
    die("Package ID missing");
}

$package_id = mysqli_real_escape_string($conn, $package_id);

/* =======================
   GET PACKAGE
======================= */
$package = mysqli_query($conn, "
    SELECT packages.*, agencies.agency_name
    FROM packages
    LEFT JOIN agencies
    ON packages.agency_id = agencies.agency_id
    WHERE packages.package_id='$package_id'
");

$pack = mysqli_fetch_assoc($package);

if (!$pack) {
    die("Invalid package selected");
}

/* =======================
   GET PRICING (ALL TYPES)
======================= */
$prices = [];

$q = mysqli_query($conn, "
    SELECT type, price 
    FROM package_pricing 
    WHERE package_id='$package_id'
");

while ($row = mysqli_fetch_assoc($q)) {
    $prices[$row['type']] = $row['price'];
}

/* =======================
   SUBMIT BOOKING
======================= */
if (isset($_POST['book'])) {

    $package_id = mysqli_real_escape_string($conn, $_POST['package_id']);
    $travel_date = $_POST['travel_date'] ?? '';

    $payment_method = $_POST['payment_method'] ?? '';

    /* =======================
       NORMAL PAX
    ======================= */
    $adult = (int)($_POST['adult'] ?? 0);
    $child = (int)($_POST['child'] ?? 0);

    /* =======================
       SUKA / MTB PAX
    ======================= */
    $adult_twin = (int)($_POST['Adult Twin / Triple'] ?? 0);
    $single = (int)($_POST['Single'] ?? 0);
    $child_twin = (int)($_POST['Child Twin'] ?? 0);
    $child_no_bed = (int)($_POST['Child No Bed'] ?? 0);
    $child_with_bed = (int)($_POST['Child With Bed'] ?? 0);
    $infant = (int)($_POST['Infant'] ?? 0);

    /* =======================
       CALCULATE TOTAL
    ======================= */
    $agency = strtoupper($pack['agency_name']);

    if (in_array($agency, ['SUKA', 'MTB'])) {

        $total_amount =
            ($adult_twin * ($prices['adult_twin'] ?? 0)) +
            ($single * ($prices['single'] ?? 0)) +
            ($child_twin * ($prices['child_twin'] ?? 0)) +
            ($child_no_bed * ($prices['child_no_bed'] ?? 0)) +
            ($child_with_bed * ($prices['child_with_bed'] ?? 0)) +
            ($infant * ($prices['infant'] ?? 0));

        $total_pax = $adult_twin + $single + $child_twin + $child_no_bed + $child_with_bed + $infant;

    } else {

        $total_amount =
            ($adult * ($prices['adult'] ?? 0)) +
            ($child * ($prices['child'] ?? 0));

        $total_pax = $adult + $child;
    }

    /* =======================
       PAX DETAILS
    ======================= */
    $pax_names = $_POST['pax_name'] ?? [];
    $pax_phones = $_POST['pax_phone'] ?? [];
    $pax_gender = $_POST['pax_gender'] ?? [];
    $pax_state = $_POST['pax_state'] ?? [];

    $address = $_POST['address'] ?? '';

    /* =======================
       CUSTOMER DATA
    ======================= */
    $customer_name = $pax_names[0] ?? '';
    $phone = $pax_phones[0] ?? '';
    $state = $pax_state[0] ?? '';

    if ($customer_name == '' || $phone == '') {
        die("Customer details required");
    }

    $user_id = $_SESSION['user_id'];

    /* =======================
       INSERT BOOKING
    ======================= */
    $stmt = $conn->prepare("
        INSERT INTO bookings
        (
            user_id,
            package_id,
            customer_name,
            phone,
            state,
            travel_date,
            total_pax,
            payment_method,
            status,
            tnc_accepted,
            digital_signature,
            requested_tnc_copy,
            tnc_accepted_at,
            total_amount
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?)
    ");

    $digital_signature = $_POST['digital_signature'] ?? '';
    $requested_copy = $_POST['wants_tnc_copy'] ?? 'no';
    $tnc_accepted = isset($_POST['accept_tnc']) ? 1 : 0;
    $current_time = date('Y-m-d H:i:s');

    $stmt->bind_param(
        "iissssississsd",
        $user_id,
        $package_id,
        $customer_name,
        $phone,
        $state,
        $travel_date,
        $total_pax,
        $payment_method,
        $tnc_accepted,
        $digital_signature,
        $requested_copy,
        $current_time,
        $total_amount
    );

    if (!$stmt->execute()) {
        die("Booking failed: " . $stmt->error);
    }

    $booking_id = $stmt->insert_id;

    /* =======================
       INSERT PAX
    ======================= */
    $stmt_pax = $conn->prepare("
        INSERT INTO bookings_pax
        (booking_id, name, phone, gender, state, address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $count = count($pax_names);

    for ($i = 0; $i < $count; $i++) {

        $name = $pax_names[$i] ?? '';
        if (!$name) continue;

        $stmt_pax->bind_param(
            "isssss",
            $booking_id,
            $name,
            $pax_phones[$i],
            $pax_gender[$i],
            $pax_state[$i],
            $address
        );

        $stmt_pax->execute();
    }

    /* =======================
       PAYMENT SESSION
    ======================= */
    $_SESSION['payment_data'] = [
        'booking_id' => $booking_id,
        'payment_method' => $payment_method,
        'txn_order_id' => $booking_id,
        'txn_amount' => $total_amount,
        'txn_buyer_name' => $customer_name,
        'txn_buyer_email' => $_POST['txn_buyer_email'] ?? '',
        'txn_buyer_phone' => $_POST['txn_buyer_phone'] ?? '',
    ];

    header("Location: payment/payment_redirect.php");
    exit();
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

<form id="bookingForm" method="POST" action="">

<div class="step-container">

    <h1 class="page-title">Booking Page</h1>

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

        <p><b>Package:</b> <?php echo $pack['title']; ?></p>
        <p><b>Travel Date:</b> <?php echo date("d M Y", strtotime($travel_date)); ?></p>

        <input type="hidden" name="package_id" value="<?php echo $package_id; ?>">
        <input type="hidden" name="travel_date" value="<?php echo $travel_date; ?>">

        <button type="button" onclick="nextStep()">Next</button>
    </div>

    <!-- STEP 2 -->
    <div class="step-content">
        <h3>Select Pax</h3>

        <?php
            $agency = strtoupper($pack['agency_name']);
            if (in_array($agency, ['SUKA', 'MTB'])) {
        ?>

            <!-- SUKA PACKAGE ONLY -->
            <label>Adult Twin / Triple: RM<?= number_format($prices['Adult Twin / Triple'] ?? 0, 2) ?></label>
            <button type="button" onclick="changePax('adult_twin', -1)">-</button>
            <input type="number" id="adult_twin" name="adult_twin" value="0">
            <button type="button" onclick="changePax('adult_twin', 1)">+</button>

            <br><br>

            <label>Single: RM<?= number_format($prices['Single'] ?? 0, 2) ?></label>
            <button type="button" onclick="changePax('single', -1)">-</button>
            <input type="number" id="single" name="single" value="0">
            <button type="button" onclick="changePax('single', 1)">+</button>

            <br><br>

            <label>Child Twin: RM<?= number_format($prices['Child Twin'] ?? 0, 2) ?></label>
            <button type="button" onclick="changePax('child_twin', -1)">-</button>
            <input type="number" id="child_twin" name="child_twin" value="0">
            <button type="button" onclick="changePax('child_twin', 1)">+</button>

            <br><br>

            <label>Child No Bed: RM<?= number_format($prices['Child No Bed'] ?? 0, 2) ?></label>
            <button type="button" onclick="changePax('child_no_bed', -1)">-</button>
            <input type="number" id="child_no_bed" name="child_no_bed" value="0">
            <button type="button" onclick="changePax('child_no_bed', 1)">+</button>

            <br><br>

            <label>Child With Bed: RM<?= number_format($prices['Child With Bed'] ?? 0, 2) ?></label>
            <button type="button" onclick="changePax('child_with_bed', -1)">-</button>
            <input type="number" id="child_with_bed" name="child_with_bed" value="0">
            <button type="button" onclick="changePax('child_with_bed', 1)">+</button>

            <br><br>

            <label>Infant: RM<?= number_format($prices['Infant'] ?? 0, 2) ?></label>
            <button type="button" onclick="changePax('infant', -1)">-</button>
            <input type="number" id="infant" name="infant" value="0">
            <button type="button" onclick="changePax('infant', 1)">+</button>

        <?php } else { ?>

            <!-- NORMAL PACKAGE -->
            <label>Dewasa:</label>
            <button type="button" onclick="changePax('adult', -1)">-</button>
            <input type="number" id="adult" name="adult" value="0">
            <button type="button" onclick="changePax('adult', 1)">+</button>

            <br><br>

            <label>Kanak-kanak:</label>
            <button type="button" onclick="changePax('child', -1)">-</button>
            <input type="number" id="child" name="child" value="0">
            <button type="button" onclick="changePax('child', 1)">+</button>

        <?php } ?>

        <br><br>

        <button type="button" onclick="prevStep()">Back</button>
        <button type="button" onclick="validatePax()">Next</button>
    </div>

    <!-- STEP 3 -->
    <div class="step-content">
        <h3>Pax Details</h3>

        <div id="paxForm"></div>

        <label>Address:</label>
        <textarea name="address" required></textarea>

        <br><br>

        <button type="button" onclick="prevStep()">Back</button>
        <button type="button" onclick="nextStep()">Next</button>
    </div>

    <!-- STEP 4 -->
    <div class="step-content">

        <h3>Payment</h3>

        <div class="checkout-card">
            <h2>Confirm Payment Details</h2>

            <input type="hidden" name="txn_order_id" value="<?php echo time(); ?>">
            <input type="hidden" name="txn_amount" value="250.00">

            <div class="form-group">
                <label>Buyer Name</label>
                <input type="text" name="txn_buyer_name" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="txn_buyer_email" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="txn_buyer_phone" required>
            </div>

            <div class="payment-methods">
                <label>Select Payment Method</label>

                <label>
                    <input type="radio" name="payment_method" value="fpx" checked>
                    FPX Online Banking
                </label>

                <label>
                    <input type="radio" name="payment_method" value="card">
                    Credit / Debit Card
                </label>
            </div>

            <div class="price-box">
                <span>Total Payable:</span>
                <span>RM 250.00</span>
            </div>

        </div>

        <!-- TNC -->
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

        <br>

        <button type="button" onclick="prevStep()">Back</button>
        <button type="submit" name="book">Proceed to Payment</button>

    </div>

    <!-- STEP 5 (FIXED - MISSING BEFORE) -->
    <div class="step-content">
        <h3>Success</h3>

        <p>🎉 Your booking has been submitted successfully!</p>
        <p>Please check your email for confirmation.</p>

    </div>

</div>

</form>

<script>
    window.packageAgency = "<?= $pack['agency_name'] ?>";
</script>

<script>
let currentStep = 0;

const steps = document.querySelectorAll(".progress-container .step");
const contents = document.querySelectorAll(".step-content");

// =================
// PACKAGE CHECK
// =================
function isSpecialPackage() {
    return ["SUKA", "MTB"].includes(window.packageAgency);
}

// =================
// STEP NAVIGATION
// =================
function showStep(index) {

    contents.forEach(content => {
        content.classList.remove("active");
    });

    steps.forEach((step, i) => {

        step.classList.remove("active", "completed");

        if (i < index) {
            step.classList.add("completed");
            step.querySelector(".circle").textContent = "✓";
        } else {
            step.querySelector(".circle").textContent = i + 1;
        }
    });

    steps[index].classList.add("active");
    contents[index].classList.add("active");

    currentStep = index;
    updateProgressLine();
}

function updateProgressLine() {

    const percent =
        (currentStep / (steps.length - 1)) * 75;

    document.getElementById("progressLine").style.width =
        percent + "%";
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
// PAX COUNTER
// =================
function changePax(type, value) {

    let input = document.getElementById(type);
    if (!input) return;

    let current = parseInt(input.value) || 0;
    let newValue = current + value;

    if (newValue >= 0) {
        input.value = newValue;
    }
}

// =================
// VALIDATE PAX
// =================
function validatePax() {

    const fields = isSpecialPackage()
        ? ["adult_twin","single","child_twin","child_no_bed","child_with_bed","infant"]
        : ["adult","child"];

    let total = 0;

    fields.forEach(f => {
        const el = document.getElementById(f);

        if (!el) {
            console.log("Missing input:", f);
            return;
        }

        const val = Number(el.value);
        console.log(f, val);

        total += val || 0;
    });

    console.log("TOTAL PAX:", total);

    if (total <= 0) {
        alert("Please select at least 1 pax");
        return;
    }

    generatePaxForm();
    nextStep();
}

// =================
// GENERATE PAX FORM
// =================
function generatePaxForm() {

    let total = 0;

    if (isSpecialPackage()) {

        const fields = [
            "adult_twin",
            "single",
            "child_twin",
            "child_no_bed",
            "child_with_bed",
            "infant"
        ];

        fields.forEach(f => {
            let el = document.getElementById(f);
            if (el) total += parseInt(el.value) || 0;
        });

    } else {

        let adult = parseInt(document.getElementById("adult")?.value) || 0;
        let child = parseInt(document.getElementById("child")?.value) || 0;

        total = adult + child;
    }

    let html = "";

    for (let i = 1; i <= total; i++) {

        html += `
            <div class="pax-card">

                <h4>Pax ${i}</h4>

                <input type="text" name="pax_name[]" placeholder="Full Name" required>
                <input type="text" name="pax_phone[]" placeholder="Phone Number" required>

                <select name="pax_gender[]" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <input type="text" name="pax_state[]" placeholder="State" required>

                <hr>

            </div>
        `;
    }

    document.getElementById("paxForm").innerHTML = html;
}

// =================
// SUBMIT CONFIRM
// =================
document.addEventListener("DOMContentLoaded", function () {

    const form =
        document.getElementById("bookingForm");

    form.addEventListener("submit", function (e) {

        console.log("FORM SUBMIT");

        const confirmSubmit = confirm(
            "Confirm booking and proceed to payment?"
        );

        if (!confirmSubmit) {
            e.preventDefault();
        }
    });

});
</script>

</body>
</html>