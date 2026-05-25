<?php
require '../db.php';

/*
|--------------------------------------------------------------------------
| GET PACKAGE
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id'])) {
    die("Package ID not found.");
}

$package_id = intval($_GET['id']);

/*
|--------------------------------------------------------------------------
| FETCH PACKAGE
|--------------------------------------------------------------------------
*/

$packageQuery = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");
$package = mysqli_fetch_assoc($packageQuery);

if (!$package) {
    die("Package not found.");
}

/*
|--------------------------------------------------------------------------
| DROPDOWN DATA
|--------------------------------------------------------------------------
*/

$countryQuery = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");
$agencyQuery = mysqli_query($conn, "SELECT * FROM agencies ORDER BY agency_name ASC");
$tourCategoryQuery = mysqli_query($conn, "SELECT * FROM tour_categories ORDER BY tour_category_name ASC");
$packageCategoryQuery = mysqli_query($conn, "SELECT * FROM package_categories ORDER BY category_name ASC");

/*
|--------------------------------------------------------------------------
| PRICING
|--------------------------------------------------------------------------
*/

$pricingRes = mysqli_query($conn, "SELECT * FROM package_pricing WHERE package_id='$package_id'");
$pricing = [];

while ($row = mysqli_fetch_assoc($pricingRes)) {
    $pricing[$row['type']] = $row['price'];
}

/*
|--------------------------------------------------------------------------
| INCLUDE / EXCLUDE / DATES / HIGHLIGHTS
|--------------------------------------------------------------------------
*/

// INCLUDE
$includeRes = mysqli_query($conn, "SELECT * FROM package_include WHERE package_id='$package_id'");

$include = [
    'Fullboard' => [],
    'Halfboard' => []
];

while ($row = mysqli_fetch_assoc($includeRes)) {
    if ($row['include_type'] == 'Fullboard') {
        $include['Fullboard'][] = $row['description'];
    } else {
        $include['Halfboard'][] = $row['description'];
    }
}

// EXCLUDE
$excludeRes = mysqli_query($conn, "SELECT * FROM package_exclude WHERE package_id='$package_id'");
$exclude = [];

while ($row = mysqli_fetch_assoc($excludeRes)) {
    $exclude[] = $row['description'];
}

// DATES
$dateRes = mysqli_query($conn, "SELECT * FROM package_dates WHERE package_id='$package_id'");
$dates = [];

while ($row = mysqli_fetch_assoc($dateRes)) {
    $dates[] = $row['travel_date'];
}

// HIGHLIGHTS
$highlightRes = mysqli_query($conn, "SELECT * FROM package_highlights WHERE package_id='$package_id'");
$highlights = [];

while ($row = mysqli_fetch_assoc($highlightRes)) {
    $highlights[] = $row;
}

/*
|--------------------------------------------------------------------------
| UPDATE PACKAGE
|--------------------------------------------------------------------------
*/

if (isset($_POST['submit_package'])) {

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    $tour_category_id = intval($_POST['tour_category']);
    $agency_id = intval($_POST['agency_id']);
    $package_category_id = intval($_POST['package_category_id']);

    $country_id = !empty($_POST['country_id']) ? intval($_POST['country_id']) : null;

    $deposit = $_POST['deposit'] ?? 0;
    $flight_details = mysqli_real_escape_string($conn, $_POST['flight_details'] ?? '');
    $min_pax = $_POST['min_pax'] ?? 1;
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    /*
    |--------------------------------------------------------------------------
    | FILE UPLOAD (ITINERARY + IMAGE)
    |--------------------------------------------------------------------------
    */

    $itinerary_file = $package['itinerary_file'];
    $main_image = $package['main_image'];

    if (!empty($_FILES['itinerary_file']['name'])) {

        $itinerary_file = time() . "_" . uniqid() . "_" . $_FILES['itinerary_file']['name'];

        move_uploaded_file(
            $_FILES['itinerary_file']['tmp_name'],
            "../uploads/" . $itinerary_file
        );
    }

    if (!empty($_FILES['main_image']['name'])) {

        $main_image = time() . "_" . uniqid() . "_" . $_FILES['main_image']['name'];

        move_uploaded_file(
            $_FILES['main_image']['tmp_name'],
            "../uploads/" . $main_image
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PACKAGE
    |--------------------------------------------------------------------------
    */

    $updateQuery = mysqli_query($conn, "
        UPDATE packages SET
        title='$title',
        duration_days='$duration',
        tour_category_id='$tour_category_id',
        country_id=" . ($country_id === null ? "NULL" : "'$country_id'") . ",
        agency_id='$agency_id',
        package_category_id='$package_category_id',
        deposit='$deposit',
        flight_details='$flight_details',
        min_pax='$min_pax',
        status='$status',
        itinerary_file='$itinerary_file',
        main_image='$main_image'
        WHERE package_id='$package_id'
    ");

    $packageChanged = mysqli_affected_rows($conn);

    /*
    |--------------------------------------------------------------------------
    | DELETE OLD DATA
    |--------------------------------------------------------------------------
    */

    mysqli_query($conn, "DELETE FROM package_pricing WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_include WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_exclude WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_dates WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_highlights WHERE package_id='$package_id'");

    /*
    |--------------------------------------------------------------------------
    | PRICING INSERT
    |--------------------------------------------------------------------------
    */

    $pricingData = [
        ['Adult Twin / Triple', $_POST['adult_twin_triple'] ?? 0],
        ['Single', $_POST['single_price'] ?? 0],
        ['Child Twin', $_POST['child_twin'] ?? 0],
        ['Child No Bed', $_POST['child_no_bed'] ?? 0],
        ['Child With Bed', $_POST['child_with_bed'] ?? 0],
        ['Infant', $_POST['infant_price'] ?? 0]
    ];

    foreach ($pricingData as $p) {
        mysqli_query($conn, "
            INSERT INTO package_pricing VALUES (NULL,'$package_id','{$p[0]}','{$p[1]}')
        ");
    }

    /*
    |--------------------------------------------------------------------------
    | INCLUDE
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['fullboard_points'])) {
        foreach ($_POST['fullboard_points'] as $p) {
            $p = mysqli_real_escape_string($conn, trim($p));
            mysqli_query($conn, "INSERT INTO package_include VALUES (NULL,'$package_id','Fullboard','$p')");
        }
    }

    if (!empty($_POST['halfboard_points'])) {
        foreach ($_POST['halfboard_points'] as $p) {
            $p = mysqli_real_escape_string($conn, trim($p));
            mysqli_query($conn, "INSERT INTO package_include VALUES (NULL,'$package_id','Halfboard','$p')");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EXCLUDE (TEXTAREA VERSION)
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['exclude'])) {

        $lines = explode("\n", $_POST['exclude']);

        foreach ($lines as $p) {

            $p = trim($p);

            if ($p != '') {
                $p = mysqli_real_escape_string($conn, $p);
                mysqli_query($conn, "INSERT INTO package_exclude VALUES (NULL,'$package_id','$p')");
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DATES
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['travel_date'])) {
        foreach ($_POST['travel_date'] as $d) {
            mysqli_query($conn, "INSERT INTO package_dates VALUES (NULL,'$package_id','$d')");
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HIGHLIGHTS
    |--------------------------------------------------------------------------
    */

    if (!empty($_POST['highlight_title'])) {

        foreach ($_POST['highlight_title'] as $key => $title) {

            $title = mysqli_real_escape_string($conn, $title);
            $img = "";

            if (!empty($_FILES['highlight_image']['name'][$key])) {

                $ext = pathinfo($_FILES['highlight_image']['name'][$key], PATHINFO_EXTENSION);
                $img = time() . "_" . uniqid() . "." . $ext;

                move_uploaded_file(
                    $_FILES['highlight_image']['tmp_name'][$key],
                    "../uploads/packages/" . $img
                );
            }

            mysqli_query($conn, "
                INSERT INTO package_highlights VALUES (NULL,'$package_id','$title','$img')
            ");
        }
    }

    if ($packageChanged > 0) {

        echo "
        <script>
            alert('Package sudah di update');
            window.location.href='admin_manage_package.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Package tidak ada perubahan');
            window.location.href='admin_manage_package.php';
        </script>
        ";

    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Package</title>

    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="add_package.css">
</head>

<body>

<h1 class="page-title">Edit Package</h1>

<form method="POST" enctype="multipart/form-data">

<!-- ROW 1 -->
<div class="form-row">

    <div class="form-group">
        <label>Package Name</label>
        <input type="text" name="title" value="<?= $package['title'] ?>">
    </div>

    <div class="form-group">
        <label>Duration</label>
        <input type="text" name="duration" value="<?= $package['duration_days'] ?>">
    </div>

</div>

<!-- ROW 2 -->
<div class="form-row">

    <div class="form-group">
        <label>Tour Category</label>
        <select name="tour_category" id="tour-category">

            <?php while($c = mysqli_fetch_assoc($tourCategoryQuery)) { ?>
                <option value="<?= $c['tour_category_id'] ?>"
                    <?= $c['tour_category_id']==$package['tour_category_id']?'selected':'' ?>>
                    <?= $c['tour_category_name'] ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <div class="form-group" id="country-group">

        <label>Country</label>
        <select name="country_id" id="country-select">

            <option value="">-- Select Country --</option>

            <?php while($c = mysqli_fetch_assoc($countryQuery)) { ?>
                <option value="<?= $c['country_id'] ?>"
                    <?= $c['country_id']==$package['country_id']?'selected':'' ?>>
                    <?= $c['country_name'] ?>
                </option>
            <?php } ?>

        </select>
    </div>

</div>

<!-- ROW 3 -->
<div class="form-row">

    <div class="form-group">
        <label>Agency</label>
        <select name="agency_id">

            <?php while($a = mysqli_fetch_assoc($agencyQuery)) { ?>
                <option value="<?= $a['agency_id'] ?>"
                    <?= $a['agency_id']==$package['agency_id']?'selected':'' ?>>
                    <?= $a['agency_name'] ?>
                </option>
            <?php } ?>

        </select>
    </div>

    <div class="form-group">
        <label>Package Category</label>
        <select name="package_category_id">

            <?php while($p = mysqli_fetch_assoc($packageCategoryQuery)) { ?>
                <option value="<?= $p['package_category_id'] ?>"
                    <?= $p['package_category_id']==$package['package_category_id']?'selected':'' ?>>
                    <?= $p['category_name'] ?>
                </option>
            <?php } ?>

        </select>
    </div>

</div>

<!-- PRICE -->
<h3>Package Price</h3>

<div class="price-grid">

    <div class="form-group">
        <label>Adult Twin / Triple</label>
        <input type="number" step="0.01" name="adult_twin_triple"
            value="<?= $pricing['Adult Twin / Triple'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Single Supplement</label>
        <input type="number" step="0.01" name="single_price"
            value="<?= $pricing['Single'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Child Twin Sharing</label>
        <input type="number" step="0.01" name="child_twin"
            value="<?= $pricing['Child Twin'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Child No Bed</label>
        <input type="number" step="0.01" name="child_no_bed"
            value="<?= $pricing['Child No Bed'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Child With Bed</label>
        <input type="number" step="0.01" name="child_with_bed"
            value="<?= $pricing['Child With Bed'] ?? '' ?>">
    </div>

    <div class="form-group">
        <label>Infant Price</label>
        <input type="number" step="0.01" name="infant_price"
            value="<?= $pricing['Infant'] ?? '' ?>">
    </div>

</div>

<!-- EXTRA -->
<div class="form-row">

    <div class="form-group">
        <label>Deposit Per Pax</label>
        <input type="number" step="0.01" name="deposit"
            value="<?= $package['deposit'] ?>">
    </div>

    <div class="form-group">
        <label>Flight Details</label>
        <textarea name="flight_details" rows="4"><?= $package['flight_details'] ?></textarea>
    </div>

    <div class="form-group">
        <label>Minimum Pax</label>
        <input type="number" name="min_pax"
            value="<?= $package['min_pax'] ?>">
    </div>

</div>

<!-- TRAVEL DATE -->
<div class="form-group">

    <label>Travel Dates</label>

    <div id="travel-date-container">

        <?php foreach($dates as $d) { ?>
        <div class="travel-date-item">
            <input type="date" name="travel_date[]" value="<?= $d ?>">
            <button type="button" class="remove-date-btn">Remove</button>
        </div>
        <?php } ?>

    </div>

    <button type="button" id="add-date-btn">+ Add Date</button>
</div>

<!-- HIGHLIGHT -->
<div class="form-group">

    <label>Highlight Places</label>

    <div id="highlight-container">

        <?php foreach($highlights as $h) { ?>
        <div class="highlight-item">

            <input type="text" name="highlight_title[]"
                value="<?= $h['highlight_title'] ?>">

            <input type="file" name="highlight_image[]">

            <button type="button" class="remove-highlight-btn">Remove</button>

        </div>
        <?php } ?>

    </div>

    <button type="button" id="add-highlight-btn">+ Add Highlight</button>
</div>

<!-- INCLUDED -->
<div class="included-row">

    <div class="included-box">
        <h4>Fullboard</h4>
        <div id="fullboard-container">

            <?php foreach($include['Fullboard'] ?? [] as $p) { ?>
                <div class="point-item">
                    <input type="text" name="fullboard_points[]" value="<?= $p ?>">
                    <button type="button" onclick="this.parentElement.remove()">X</button>
                </div>
            <?php } ?>

        </div>
        <button type="button" onclick="addPoint('fullboard')">+ Add Point</button>
    </div>

    <div class="included-box">
        <h4>Halfboard</h4>
        <div id="halfboard-container">

            <?php foreach($include['Halfboard'] ?? [] as $p) { ?>
                <div class="point-item">
                    <input type="text" name="halfboard_points[]" value="<?= $p ?>">
                    <button type="button" onclick="this.parentElement.remove()">X</button>
                </div>
            <?php } ?>

        </div>
        <button type="button" onclick="addPoint('halfboard')">+ Add Point</button>
    </div>

</div>

<!-- EXCLUDED -->
<div class="form-group">

    <label>Excluded</label>

    <textarea name="exclude" rows="6" placeholder="Enter each item on new line"><?php

    if (!empty($exclude)) {
        foreach ($exclude as $e) {
            echo htmlspecialchars(trim($e)) . "\n";
        }
    }

    ?></textarea>

</div>

<!-- FINAL -->
<div class="form-row">

    <div class="form-group">
        <label>Upload Itinerary File</label>

        <input type="file" name="itinerary_file">

        <?php if (!empty($package['itinerary_file'])) { ?>

        <?php 
            $fileName = basename($package['itinerary_file']); 
        ?>

        <p>
            📄 Current File:
            <a href="../uploads/packages/<?= $package['itinerary_file'] ?>" target="_blank">
                <?= $fileName ?>
            </a>
        </p>

        <?php } ?>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status">

            <option value="active" <?= $package['status']=='active'?'selected':'' ?>>Active</option>
            <option value="inactive" <?= $package['status']=='inactive'?'selected':'' ?>>Inactive</option>

        </select>
    </div>

    <div class="form-group">
        <label>Main Image</label>

        <input type="file" name="main_image">

        <?php if (!empty($package['main_image'])) { ?>
            <div style="margin-top:10px;">
                <p>Current Image:</p>
                <img src="../uploads/packages/<?= $package['main_image'] ?>" 
                     style="width:150px;height:100px;object-fit:cover;border-radius:8px;">
            </div>
        <?php } ?>
    </div>

</div>

<button type="submit" name="submit_package">
    Update Package
</button>

</form>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // ======================
    // TRAVEL DATE
    // ======================
    const dateContainer = document.getElementById("travel-date-container");
    const addDateBtn = document.getElementById("add-date-btn");

    if (dateContainer && addDateBtn) {

        // ADD DATE
        addDateBtn.addEventListener("click", function () {
            const div = document.createElement("div");
            div.classList.add("travel-date-item");

            div.innerHTML = `
                <input type="date" name="travel_date[]">
                <button type="button" class="remove-date-btn">Remove</button>
            `;

            dateContainer.appendChild(div);
        });

        // REMOVE DATE (event delegation)
        dateContainer.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-date-btn")) {
                e.target.parentElement.remove();
            }
        });
    }

    // ======================
    // HIGHLIGHT
    // ======================
    const highlightContainer = document.getElementById("highlight-container");
    const addHighlightBtn = document.getElementById("add-highlight-btn");

    if (highlightContainer && addHighlightBtn) {

        addHighlightBtn.addEventListener("click", function () {

            const div = document.createElement("div");
            div.classList.add("highlight-item");

            div.innerHTML = `
                <input type="text" name="highlight_title[]" placeholder="Place name">
                <input type="file" name="highlight_image[]">
                <button type="button" class="remove-highlight-btn">Remove</button>
            `;

            highlightContainer.appendChild(div);
        });

        highlightContainer.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-highlight-btn")) {
                e.target.parentElement.remove();
            }
        });
    }

    // ======================
    // INCLUDED - FULLBOARD & HALFOARD
    // ======================

    window.addPoint = function(type) {

        let containerId = "";

        if (type === "fullboard") {
            containerId = "fullboard-container";
        } 
        else if (type === "halfboard") {
            containerId = "halfboard-container";
        }

        const container = document.getElementById(containerId);

        if (!container) return;

        const div = document.createElement("div");
        div.classList.add("point-item");

        div.innerHTML = `
            <input type="text" name="${type}_points[]" placeholder="Enter point">
            <button type="button" onclick="this.parentElement.remove()">X</button>
        `;

        container.appendChild(div);
    };

});
</script>
</body>
</html>