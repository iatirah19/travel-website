<?php

require '../db.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: homepage.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH DATA
|--------------------------------------------------------------------------
*/

$countryQuery = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");
$agencyQuery = mysqli_query($conn, "SELECT * FROM agencies ORDER BY agency_name ASC");
$tourCategoryQuery = mysqli_query($conn, "SELECT * FROM tour_categories ORDER BY tour_category_name ASC");
$packageCategoryQuery = mysqli_query($conn, "SELECT * FROM package_categories ORDER BY category_name ASC");


/*
|--------------------------------------------------------------------------
| ADD PACKAGE
|--------------------------------------------------------------------------
*/

if (isset($_POST['submit_package'])) {

    /*
    |--------------------------------------------------------------------------
    | BASIC INFO
    |--------------------------------------------------------------------------
    */

    $title               = mysqli_real_escape_string($conn, $_POST['title']);
    $duration            = mysqli_real_escape_string($conn, $_POST['duration']);
    $tour_category_id    = intval($_POST['tour_category']);
    $country_id          = !empty($_POST['country_id']) ? intval($_POST['country_id']) : "NULL";
    $agency_id           = intval($_POST['agency_id']);
    $package_category_id = intval($_POST['package_category_id']);

    /*
    |--------------------------------------------------------------------------
    | PRICE
    |--------------------------------------------------------------------------
    */

    $adult_twin_triple  = $_POST['adult_twin_triple'] ?? 0;
    $single_price       = $_POST['single_price'] ?? 0;
    $child_twin         = $_POST['child_twin'] ?? 0;
    $child_no_bed       = $_POST['child_no_bed'] ?? 0;
    $child_with_bed     = $_POST['child_with_bed'] ?? 0;
    $infant_price       = $_POST['infant_price'] ?? 0;

    /*
    |--------------------------------------------------------------------------
    | EXTRA INFO
    |--------------------------------------------------------------------------
    */

    $deposit        = $_POST['deposit'] ?? 0;
    $flight_details = mysqli_real_escape_string($conn, $_POST['flight_details']);
    $min_pax        = $_POST['min_pax'] ?? 1;

    $status = mysqli_real_escape_string($conn, $_POST['status']);

    /*
    |--------------------------------------------------------------------------
    | FILE UPLOAD
    |--------------------------------------------------------------------------
    */

    $uploadDir = "../uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $itinerary_file = "";
    if (!empty($_FILES['itinerary_file']['name'])) {
        $itinerary_file = time() . "_" . $_FILES['itinerary_file']['name'];
        move_uploaded_file($_FILES['itinerary_file']['tmp_name'], $uploadDir . $itinerary_file);
    }

    $main_image = "";
    if (!empty($_FILES['main_image']['name'])) {
        $main_image = time() . "_" . $_FILES['main_image']['name'];
        move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadDir . $main_image);
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT PACKAGE (ONLY MAIN TABLE)
    |--------------------------------------------------------------------------
    */

    $insertPackage = mysqli_query($conn, "
        INSERT INTO packages (
            title,
            duration_days,
            tour_category_id,
            country_id,
            agency_id,
            package_category_id,
            deposit,
            flight_details,
            min_pax,
            itinerary_file,
            status,
            main_image
        )
        VALUES (
            '$title',
            '$duration',
            '$tour_category_id',
            " . ($country_id === "NULL" ? "NULL" : "'$country_id'") . ",
            '$agency_id',
            '$package_category_id',
            '$deposit',
            '$flight_details',
            '$min_pax',
            '$itinerary_file',
            '$status',
            '$main_image'
        )
    ");

    if (!$insertPackage) {
        die(mysqli_error($conn));
    }

    $package_id = mysqli_insert_id($conn);

    /*
    |--------------------------------------------------------------------------
    | PACKAGE PRICING
    |--------------------------------------------------------------------------
    */

    $pricingData = [
        ['Adult Twin / Triple', $adult_twin_triple],
        ['Single', $single_price],
        ['Child Twin', $child_twin],
        ['Child No Bed', $child_no_bed],
        ['Child With Bed', $child_with_bed],
        ['Infant', $infant_price]
    ];

    foreach ($pricingData as $p) {
        mysqli_query($conn, "
            INSERT INTO package_pricing (package_id, type, price)
            VALUES ('$package_id', '{$p[0]}', '{$p[1]}')
        ");
    }

    /*
    |--------------------------------------------------------------------------
    | PACKAGE INCLUDES
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['fullboard_points'])) {
        foreach ($_POST['fullboard_points'] as $point) {
            $point = mysqli_real_escape_string($conn, trim($point));
            if ($point) {
                mysqli_query($conn, "
                    INSERT INTO package_include (package_id, include_type, description)
                    VALUES ('$package_id', 'Fullboard', '$point')
                ");
            }
        }
    }

    if (!empty($_POST['halfboard_points'])) {
        foreach ($_POST['halfboard_points'] as $point) {
            $point = mysqli_real_escape_string($conn, trim($point));
            if ($point) {
                mysqli_query($conn, "
                    INSERT INTO package_include (package_id, include_type, description)
                    VALUES ('$package_id', 'Halfboard', '$point')
                ");
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PACKAGE EXCLUDES (FIXED PART)
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['exclude_points'])) {
        foreach ($_POST['exclude_points'] as $point) {
            $point = mysqli_real_escape_string($conn, trim($point));
            if ($point) {
                mysqli_query($conn, "
                    INSERT INTO package_exclude (package_id, description)
                    VALUES ('$package_id', '$point')
                ");
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TRAVEL DATES
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['travel_date'])) {
        foreach ($_POST['travel_date'] as $date) {
            mysqli_query($conn, "
                INSERT INTO package_dates (package_id, travel_date)
                VALUES ('$package_id', '$date')
            ");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HIGHLIGHTS
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['highlight_title'])) {

        foreach ($_POST['highlight_title'] as $key => $title) {

            $title = mysqli_real_escape_string($conn, trim($title));

            if ($title) {

                $img = "";

                if (!empty($_FILES['highlight_image']['name'][$key])) {
                    $img = time() . "_" . $_FILES['highlight_image']['name'][$key];
                    move_uploaded_file($_FILES['highlight_image']['tmp_name'][$key], $uploadDir . $img);
                }

                mysqli_query($conn, "
                    INSERT INTO package_highlights (
                        package_id,
                        highlight_title,
                        highlight_image
                    )
                    VALUES (
                        '$package_id',
                        '$title',
                        '$img'
                    )
                ");
            }
        }
    }

    header("Location: admin_manage_package.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="add_package.css">
</head>
<body>

<h1 class="page-title">Add New Package</h1>

<form action="" method="POST" enctype="multipart/form-data">

<!-- ===================== BASIC INFO ===================== -->
<div class="form-section">

    <h3>Basic Information</h3>

    <div class="form-row">

        <div class="form-group">
            <label>Package Name</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label>Duration</label>
            <input type="text" name="duration" placeholder="Example: 5D4N" required>
        </div>

    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Tour Category</label>
            <select name="tour_category" id="tour-category">
                <option value="">-- Select Category --</option>
                <?php while($category = mysqli_fetch_assoc($tourCategoryQuery)) { ?>
                    <option value="<?= $category['tour_category_id']; ?>">
                        <?= $category['tour_category_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group" id="country-group" style="display:none;">
            <label>Country</label>
            <select name="country_id" id="country-select">
                <option value="">-- Select Country --</option>
                <?php while($country = mysqli_fetch_assoc($countryQuery)) { ?>
                    <option value="<?= $country['country_id']; ?>">
                        <?= $country['country_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Agency</label>
            <select name="agency_id">
                <option value="">-- Select Agency --</option>
                <?php while($agency = mysqli_fetch_assoc($agencyQuery)) { ?>
                    <option value="<?= $agency['agency_id']; ?>">
                        <?= $agency['agency_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Package Category</label>
            <select name="package_category_id">
                <option value="">-- Select Package Type --</option>
                <?php while($packageCategory = mysqli_fetch_assoc($packageCategoryQuery)) { ?>
                    <option value="<?= $packageCategory['package_category_id']; ?>">
                        <?= $packageCategory['category_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

    </div>

</div>


<!-- ===================== PRICE ===================== -->
<div class="form-section">

    <h3>Package Price</h3>

    <div class="price-grid">

        <div class="form-group">
            <label>Adult Twin / Triple</label>
            <input type="number" step="0.01" name="adult_twin_triple">
        </div>

        <div class="form-group">
            <label>Single</label>
            <input type="number" step="0.01" name="single_price">
        </div>

        <div class="form-group">
            <label>Child Twin</label>
            <input type="number" step="0.01" name="child_twin">
        </div>

        <div class="form-group">
            <label>Child No Bed</label>
            <input type="number" step="0.01" name="child_no_bed">
        </div>

        <div class="form-group">
            <label>Child With Bed</label>
            <input type="number" step="0.01" name="child_with_bed">
        </div>

        <div class="form-group">
            <label>Infant</label>
            <input type="number" step="0.01" name="infant_price">
        </div>

    </div>

</div>


<!-- ===================== EXTRA INFO ===================== -->
<div class="form-section">

    <h3>Extra Information</h3>

    <div class="form-row">

        <div class="form-group">
            <label>Deposit Per Pax</label>
            <input type="number" step="0.01" name="deposit">
        </div>

        <div class="form-group">
            <label>Minimum Pax</label>
            <input type="number" name="min_pax">
        </div>

    </div>

    <div class="form-group full-width">
        <label>Flight Details</label>
        <textarea name="flight_details" rows="4"></textarea>
    </div>

</div>


<!-- ===================== TRAVEL DATES ===================== -->
<div class="form-section">

    <h3>Travel Dates</h3>

    <div id="travel-date-container">

        <div class="travel-date-item">
            <input type="date" name="travel_date[]">
            <button type="button" class="remove-date-btn">Remove</button>
        </div>

    </div>

    <button type="button" id="add-date-btn">+ Add Date</button>

</div>


<!-- ===================== HIGHLIGHTS ===================== -->
<div class="form-section">

    <h3>Highlight Places</h3>

    <div id="highlight-container">

        <div class="highlight-item">

            <input type="text" name="highlight_name[]" placeholder="Place name">
            <input type="file" name="highlight_image[]">

            <button type="button" class="remove-highlight-btn">Remove</button>

        </div>

    </div>

    <button type="button" id="add-highlight-btn">+ Add Highlight</button>

</div>


<!-- ===================== INCLUDED ===================== -->
<div class="form-section">

    <h3>Package Includes</h3>

    <div class="included-row">

        <div class="included-box">
            <h4>Fullboard</h4>
            <div id="fullboard-container"></div>
            <button type="button" onclick="addPoint('fullboard')">+ Add Point</button>
        </div>

        <div class="included-box">
            <h4>Halfboard</h4>
            <div id="halfboard-container"></div>
            <button type="button" onclick="addPoint('halfboard')">+ Add Point</button>
        </div>

    </div>

</div>


<!-- ===================== EXCLUDED ===================== -->
<div class="form-section">

    <h3>Excluded</h3>

    <div class="included-box excluded-box">

        <div id="excluded-container"></div>

        <button type="button" onclick="addPoint('excluded')">+ Add Point</button>

    </div>

</div>


<!-- ===================== FINAL ===================== -->
<div class="form-section">

    <h3>Final Setup</h3>

    <div class="form-row">

        <div class="form-group">
            <label>Upload Itinerary File</label>
            <input type="file" name="itinerary_file">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">-- Select Status --</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="form-group">
            <label>Main Image</label>
            <input type="file" name="main_image" accept="image/*">
        </div>

    </div>

</div>


<!-- SUBMIT -->
<button type="submit" name="submit_package">
    Add Package
</button>

</form>

<!-- JS -->
<script>

// ================================
// TRAVEL DATE
// ================================

const travelContainer = document.getElementById("travel-date-container");

document.getElementById("add-date-btn").addEventListener("click", function () {

    const div = document.createElement("div");

    div.classList.add("travel-date-item");

    div.innerHTML = `
        <input type="date" name="travel_date[]">

        <button type="button" class="remove-date-btn">
            Remove
        </button>
    `;

    travelContainer.appendChild(div);

});

travelContainer.addEventListener("click", function (e) {

    if (e.target.classList.contains("remove-date-btn")) {
        e.target.parentElement.remove();
    }

});

// ================================
// HIGHLIGHT
// ================================

const highlightContainer = document.getElementById("highlight-container");

document.getElementById("add-highlight-btn").addEventListener("click", function () {

    const div = document.createElement("div");

    div.classList.add("highlight-item");

    div.innerHTML = `
        <input type="text" name="highlight_title[]" placeholder="Place name">

        <input type="file" name="highlight_image[]">

        <button type="button" class="remove-highlight-btn">
            Remove
        </button>
    `;

    highlightContainer.appendChild(div);

});

highlightContainer.addEventListener("click", function (e) {

    if (e.target.classList.contains("remove-highlight-btn")) {
        e.target.parentElement.remove();
    }

});

// ================================
// TOUR CATEGORY
// ================================

document.addEventListener("DOMContentLoaded", function () {

    const tourCategory = document.getElementById("tour-category");
    const countryGroup = document.getElementById("country-group");
    const countrySelect = document.getElementById("country-select");

    if (!tourCategory) return;

    tourCategory.addEventListener("change", function () {

        const selectedText = this.options[this.selectedIndex].text.trim();

        if (selectedText === "International") {
            countryGroup.style.display = "block";
        } else {
            countryGroup.style.display = "none";
            countrySelect.value = "";
        }

    });

});

// ================================
// FULLBOARD / HALFB0ARD
// ================================

function addPoint(type) {

    let container = document.getElementById(type + "-container");

    let div = document.createElement("div");

    div.classList.add("point-item");

    div.innerHTML = `
        <input 
            type="text" 
            name="${type}_points[]" 
            placeholder="Enter point"
        >

        <button 
            type="button" 
            onclick="this.parentElement.remove()"
        >
            X
        </button>
    `;

    container.appendChild(div);
}

</script>

</body>
</html>