<?php
require '../db.php';

if (!isset($_GET['id'])) {
    die("Package ID not found.");
}

$package_id = intval($_GET['id']);

/*
|--------------------------
| FETCH PACKAGE
|--------------------------
*/
$packageQuery = mysqli_query($conn, "
    SELECT * FROM packages
    WHERE package_id='$package_id'
");

$package = mysqli_fetch_assoc($packageQuery);

if (!$package) {
    die("Package not found.");
}

/*
|--------------------------
| DROPDOWN
|--------------------------
*/
$countryQuery = mysqli_query($conn, "
    SELECT * FROM countries
    ORDER BY country_name ASC
");

$agencyQuery = mysqli_query($conn, "
    SELECT * FROM agencies
    ORDER BY agency_name ASC
");

$tourCategoryQuery = mysqli_query($conn, "
    SELECT * FROM tour_categories
    ORDER BY tour_category_name ASC
");

$packageCategoryQuery = mysqli_query($conn, "
    SELECT * FROM package_categories
    ORDER BY category_name ASC
");

/*
|--------------------------
| PRICING
|--------------------------
*/
$pricingRes = mysqli_query($conn, "
    SELECT * FROM package_pricing
    WHERE package_id='$package_id'
");

$prices = [];

while ($row = mysqli_fetch_assoc($pricingRes)) {
    $prices[$row['type']] = $row['price'];
}

/*
|--------------------------
| INCLUDE
|--------------------------
*/
$includeRes = mysqli_query($conn, "
    SELECT * FROM package_include
    WHERE package_id='$package_id'
");

$include = [
    'Fullboard' => [],
    'Halfboard' => []
];

while ($row = mysqli_fetch_assoc($includeRes)) {
    $include[$row['include_type']][] = $row['description'];
}

/*
|--------------------------
| EXCLUDE
|--------------------------
*/
$excludeRes = mysqli_query($conn, "
    SELECT * FROM package_exclude
    WHERE package_id='$package_id'
");

$exclude = [];

while ($row = mysqli_fetch_assoc($excludeRes)) {
    $exclude[] = $row['description'];
}

/*
|--------------------------
| FORMAT INCLUDE/EXCLUDE
|--------------------------
*/
$fullboards = [];
$halfboards = [];
$excludeds = [];

foreach ($include['Fullboard'] as $item) {
    $fullboards[] = ['description' => $item];
}

foreach ($include['Halfboard'] as $item) {
    $halfboards[] = ['description' => $item];
}

foreach ($exclude as $item) {
    $excludeds[] = ['description' => $item];
}

/*
|--------------------------
| DATES
|--------------------------
*/
$dateRes = mysqli_query($conn, "
    SELECT * FROM package_dates
    WHERE package_id='$package_id'
");

$dates = [];

while ($row = mysqli_fetch_assoc($dateRes)) {
    $dates[] = $row['travel_date'];
}

/*
|--------------------------
| HIGHLIGHTS
|--------------------------
*/
$highlightRes = mysqli_query($conn, "
    SELECT * FROM package_highlights
    WHERE package_id='$package_id'
");

$highlights = [];

while ($row = mysqli_fetch_assoc($highlightRes)) {
    $highlights[] = $row;
}

/*
|--------------------------
| UPDATE
|--------------------------
*/
if (isset($_POST['update_package'])) {

    $hasChanges = false;

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    $tour_category_id = intval($_POST['tour_category']);
    $agency_id = intval($_POST['agency_id']);
    $package_category_id = intval($_POST['package_category_id']);

    $country_id = !empty($_POST['country_id'])
        ? intval($_POST['country_id'])
        : null;

    $deposit = $_POST['deposit'] ?? 0;
    $min_pax = $_POST['min_pax'] ?? 1;

    $flight_details = mysqli_real_escape_string(
        $conn,
        $_POST['flight_details'] ?? ''
    );

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );

    /*
    |--------------------------
    | FILES
    |--------------------------
    */
    $itinerary_file = $package['itinerary_file'];
    $main_image = $package['main_image'];

    if (!empty($_FILES['itinerary_file']['name'])) {

        $itinerary_file =
            time() . "_" .
            uniqid() .
            $_FILES['itinerary_file']['name'];

        move_uploaded_file(
            $_FILES['itinerary_file']['tmp_name'],
            "../uploads/" . $itinerary_file
        );
    }

    if (!empty($_FILES['main_image']['name'])) {

        $main_image =
            time() . "_" .
            uniqid() .
            $_FILES['main_image']['name'];

        move_uploaded_file(
            $_FILES['main_image']['tmp_name'],
            "../uploads/" . $main_image
        );
    }

    /*
    |--------------------------
    | UPDATE PACKAGE
    |--------------------------
    */
    $packageChanged = false;

    if (
        $title != $package['title'] ||
        $duration != $package['duration_days'] ||
        $tour_category_id != $package['tour_category_id'] ||
        $agency_id != $package['agency_id'] ||
        $package_category_id != $package['package_category_id'] ||
        $country_id != $package['country_id'] ||
        $deposit != $package['deposit'] ||
        $min_pax != $package['min_pax'] ||
        $flight_details != $package['flight_details'] ||
        $status != $package['status']
    ) {
        $packageChanged = true;
    }

    if (!empty($_FILES['itinerary_file']['name'])) {
        $packageChanged = true;
    }

    if (!empty($_FILES['main_image']['name'])) {
        $packageChanged = true;
    }

    if ($packageChanged) {

        $hasChanges = true;

        mysqli_query($conn, "
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
    }

    /*
    |--------------------------
    | PRICING
    |--------------------------
    */
    $newPrices = [
        'adult_twin' => $_POST['adult_twin_triple'] ?? '',
        'single' => $_POST['single_price'] ?? '',
        'child_twin' => $_POST['child_twin'] ?? '',
        'child_no_bed' => $_POST['child_no_bed'] ?? '',
        'child_with_bed' => $_POST['child_with_bed'] ?? '',
        'infant' => $_POST['infant_price'] ?? ''
    ];

    $priceChanged = false;

    foreach ($newPrices as $type => $price) {

        $oldPrice = $prices[$type] ?? '';

        if ((string)$oldPrice !== (string)$price) {
            $priceChanged = true;
            break;
        }
    }

    if ($priceChanged) {

        $hasChanges = true;

        mysqli_query($conn, "
            DELETE FROM package_pricing
            WHERE package_id='$package_id'
        ");

        foreach ($newPrices as $type => $price) {

            if ($price !== '') {

                mysqli_query($conn, "
                    INSERT INTO package_pricing
                    (package_id, type, price)
                    VALUES
                    ('$package_id','$type','$price')
                ");
            }
        }
    }

    /*
    |--------------------------
    | INCLUDE
    |--------------------------
    */
    $newFullboard = $_POST['fullboard'] ?? [];
    $newHalfboard = $_POST['halfboard'] ?? [];

    $oldFullboard = $include['Fullboard'];
    $oldHalfboard = $include['Halfboard'];

    sort($newFullboard);
    sort($newHalfboard);

    sort($oldFullboard);
    sort($oldHalfboard);

    if (
        $newFullboard != $oldFullboard ||
        $newHalfboard != $oldHalfboard
    ) {

        $hasChanges = true;

        mysqli_query($conn, "
            DELETE FROM package_include
            WHERE package_id='$package_id'
        ");

        foreach ($_POST['fullboard'] as $p) {

            $p = trim($p);

            if ($p != '') {

                $p = mysqli_real_escape_string($conn, $p);

                mysqli_query($conn, "
                    INSERT INTO package_include
                    VALUES
                    (NULL,'$package_id','Fullboard','$p')
                ");
            }
        }

        foreach ($_POST['halfboard'] as $p) {

            $p = trim($p);

            if ($p != '') {

                $p = mysqli_real_escape_string($conn, $p);

                mysqli_query($conn, "
                    INSERT INTO package_include
                    VALUES
                    (NULL,'$package_id','Halfboard','$p')
                ");
            }
        }
    }

    /*
    |--------------------------
    | EXCLUDE
    |--------------------------
    */
    $newExclude = $_POST['excluded'] ?? [];

    sort($newExclude);
    sort($exclude);

    if ($newExclude != $exclude) {

        $hasChanges = true;

        mysqli_query($conn, "
            DELETE FROM package_exclude
            WHERE package_id='$package_id'
        ");

        foreach ($_POST['excluded'] as $p) {

            $p = trim($p);

            if ($p != '') {

                $p = mysqli_real_escape_string($conn, $p);

                mysqli_query($conn, "
                    INSERT INTO package_exclude
                    (package_id, description)
                    VALUES
                    ('$package_id','$p')
                ");
            }
        }
    }

    /*
    |--------------------------
    | DATES
    |--------------------------
    */
    $newDates = $_POST['travel_date'] ?? [];

    sort($newDates);
    sort($dates);

    if ($newDates != $dates) {

        $hasChanges = true;

        mysqli_query($conn, "
            DELETE FROM package_dates
            WHERE package_id='$package_id'
        ");

        foreach ($_POST['travel_date'] as $d) {

            if ($d != '') {

                mysqli_query($conn, "
                    INSERT INTO package_dates
                    VALUES
                    (NULL,'$package_id','$d')
                ");
            }
        }
    }

    /*
    |--------------------------
    | HIGHLIGHTS
    |--------------------------
    */
    $highlightChanged = false;

    $currentHighlights = [];

    foreach ($highlights as $h) {
        $currentHighlights[] = $h['highlight_name'];
    }

    $newHighlights = $_POST['highlight_name'] ?? [];

    if ($newHighlights != $currentHighlights) {
        $highlightChanged = true;
    }

    foreach ($_FILES['highlight_image']['name'] as $imgCheck) {
        if (!empty($imgCheck)) {
            $highlightChanged = true;
            break;
        }
    }

    if ($highlightChanged) {

        $hasChanges = true;

        mysqli_query($conn, "
            DELETE FROM package_highlights
            WHERE package_id='$package_id'
        ");

        foreach ($_POST['highlight_name'] as $key => $title) {

            $title = trim($title);

            if (
                $title == '' &&
                empty($_FILES['highlight_image']['name'][$key])
            ) {
                continue;
            }

            $title = mysqli_real_escape_string($conn, $title);

            $img = "";

            if (!empty($_FILES['highlight_image']['name'][$key])) {

                $ext = pathinfo(
                    $_FILES['highlight_image']['name'][$key],
                    PATHINFO_EXTENSION
                );

                $img =
                    time() . "_" .
                    uniqid() . "." .
                    $ext;

                move_uploaded_file(
                    $_FILES['highlight_image']['tmp_name'][$key],
                    "../uploads/" . $img
                );
            }

            mysqli_query($conn, "
                INSERT INTO package_highlights
                (package_id, highlight_name, highlight_image)
                VALUES
                ('$package_id','$title','$img')
            ");
        }
    }

    /*
    |--------------------------
    | ALERT
    |--------------------------
    */
    if ($hasChanges) {
    
        echo "
        <script>
            alert('Package ini telah diupdate');
            window.location.href='admin_manage_package.php';
        </script>
        ";
    
    } else {
    
        echo "
        <script>
            alert('Tiada perubahan berlaku untuk package ini');
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="edit_package.css">
</head>

<body>

<div class="top-header">
    <h1>Edit Package</h1>
    <p>Dashboard > Package > Edit Package</p>
</div>

<div class="layout">

    <!-- LEFT SIDEBAR STEP -->
    <div class="step-sidebar">

        <div class="step active" data-step="1">
            <span>1</span> Basic Info
        </div>

        <div class="step" data-step="2">
            <span>2</span> Pricing
        </div>

        <div class="step" data-step="3">
            <span>3</span> Extra Info
        </div>

        <div class="step" data-step="4">
            <span>4</span> Travel & Highlight
        </div>

        <div class="step" data-step="5">
            <span>5</span> Include / Exclude
        </div>

        <div class="step" data-step="6">
            <span>6</span> Final Info
        </div>

    </div>

    <!-- RIGHT CONTENT -->
    <div class="form-content">

        <form action="" method="POST" enctype="multipart/form-data">

            <!-- STEP 1 -->
            <div class="form-step active-step" data-step="1">

                <div class="form-section">
                    <h3>Basic Information</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Package Name</label>
                            <input type="text" name="title" required
                                   value="<?= htmlspecialchars($package['title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Duration</label>
                            <input type="text" name="duration" required
                                   value="<?= htmlspecialchars($package['duration_days'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Tour Category</label>
                            <select name="tour_category" id="tour-category">
                                <option value="">-- Select Category --</option>
                                <?php while($category = mysqli_fetch_assoc($tourCategoryQuery)) { ?>
                                    <option value="<?= $category['tour_category_id']; ?>"
                                        <?= ($package['tour_category_id'] == $category['tour_category_id']) ? 'selected' : '' ?>>
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
                                    <option value="<?= $country['country_id']; ?>"
                                        <?= ($package['country_id'] == $country['country_id']) ? 'selected' : '' ?>>
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
                                    <option value="<?= $agency['agency_id']; ?>"
                                        <?= ($package['agency_id'] == $agency['agency_id']) ? 'selected' : '' ?>>
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
                                    <option value="<?= $packageCategory['package_category_id']; ?>"
                                        <?= ($package['package_category_id'] == $packageCategory['package_category_id']) ? 'selected' : '' ?>>
                                        <?= $packageCategory['category_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="button" class="next-btn">Next</button>
            </div>

            <!-- STEP 2 -->
            <div class="form-step" data-step="2">

                <div class="form-section">
                    <h3>Package Price</h3>

                    <div class="price-grid">

                        <div class="form-group">
                            <label for="adult_twin_triple">Adult Twin</label>
                            <input type="number"
                                   id="adult_twin_triple"
                                   name="adult_twin_triple"
                                   value="<?= $prices['adult_twin_triple'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="single_price">Single</label>
                            <input type="number"
                                   id="single_price"
                                   name="single_price"
                                   value="<?= $prices['single_price'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="child_twin">Child Twin</label>
                            <input type="number"
                                   id="child_twin"
                                   name="child_twin"
                                   value="<?= $prices['child_twin'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="child_no_bed">Child No Bed</label>
                            <input type="number"
                                   id="child_no_bed"
                                   name="child_no_bed"
                                   value="<?= $prices['child_no_bed'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="child_with_bed">Child With Bed</label>
                            <input type="number"
                                   id="child_with_bed"
                                   name="child_with_bed"
                                   value="<?= $prices['child_with_bed'] ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="infant_price">Infant</label>
                            <input type="number"
                                   id="infant_price"
                                   name="infant_price"
                                   value="<?= $prices['infant_price'] ?? '' ?>">
                        </div>

                    </div>
                </div>

                <button type="button" class="back-btn">Back</button>
                <button type="button" class="next-btn">Next</button>

            </div>

            <!-- STEP 3 -->
            <div class="form-step" data-step="3">

                <div class="form-section">
                    <h3>Extra Information</h3>

                    <div class="form-group">
                        <label for="deposit">Deposit</label>
                        <input type="number" id="deposit" name="deposit"
                               value="<?= $package['deposit'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="min_pax">Min Pax</label>
                        <input type="number" id="min_pax" name="min_pax"
                               value="<?= $package['min_pax'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="flight_details">Flight Details</label>
                        <textarea id="flight_details" name="flight_details"><?= $package['flight_details'] ?? '' ?></textarea>
                    </div>

                </div>

                <button type="button" class="back-btn">Back</button>
                <button type="button" class="next-btn">Next</button>
            </div>

            <!-- STEP 4 -->
            <div class="form-step" data-step="4">

                <div class="form-section">
                    <h3>Travel Dates</h3>

                    <div id="travel-date-container">

                        <?php if(!empty($dates)) { ?>
                        
                            <?php foreach($dates as $date) { ?>
                                
                                <div class="travel-date-item">
                                    <input type="date"
                                           name="travel_date[]"
                                           value="<?= $date; ?>">

                                    <button type="button" class="remove-date-btn">
                                        Remove
                                    </button>
                                </div>
                            <?php } ?>

                        <?php } else { ?>

                            <div class="travel-date-item">
                                <input type="date" name="travel_date[]">

                                <button type="button" class="remove-date-btn">
                                    Remove
                                </button>
                            </div>

                        <?php } ?>

                    </div>

                    <button type="button" id="add-date-btn">+ Add Date</button>
                </div>

                <div class="form-section">
                    <h3>Highlights</h3>

                    <div id="highlight-container">

                        <?php if(!empty($highlights)) { ?>
                    
                            <?php foreach($highlights as $highlight) { ?>
                    
                                <div class="highlight-item">
                    
                                    <input type="text"
                                           name="highlight_name[]"
                                           placeholder="Highlight Name"
                                           value="<?= htmlspecialchars($highlight['highlight_name']); ?>">
                    
                                    <?php if(!empty($highlight['highlight_image'])) { ?>
                                        <img src="../uploads/<?= $highlight['highlight_image']; ?>"
                                             width="120"
                                             style="border-radius:10px; margin-bottom:10px;">
                                    <?php } ?>
                    
                                    <input type="file" name="highlight_image[]">
                    
                                    <button type="button" class="remove-highlight-btn">
                                        Remove
                                    </button>
                    
                                </div>
                    
                            <?php } ?>
                    
                        <?php } else { ?>

                            <div class="highlight-item">
                                <input type="text"
                                       name="highlight_name[]"
                                       placeholder="Highlight Name">

                                <input type="file" name="highlight_image[]">

                                <button type="button" class="remove-highlight-btn">
                                    Remove
                                </button>
                            </div>

                        <?php } ?>

                    </div>

                    <button type="button" id="add-highlight-btn">+ Add Highlight</button>
                </div>

                <button type="button" class="back-btn">Back</button>
                <button type="button" class="next-btn">Next</button>
            </div>

            <!-- STEP 5 -->
            <div class="form-step" data-step="5">

                <div class="form-section">
                    <h3>Included / Excluded</h3>

                    <h4>Fullboard</h4>
                    <div id="fullboard-container">

                        <?php if(!empty($fullboards)) { ?>
                    
                            <?php foreach($fullboards as $item) { ?>
                    
                                <div class="point-item">
                                    <input type="text"
                                           name="fullboard[]"
                                           value="<?= htmlspecialchars($item['description']); ?>">
                    
                                    <button type="button" class="remove-point-btn">
                                        Remove
                                    </button>
                                </div>
                    
                            <?php } ?>
                    
                        <?php } else { ?>
                    
                            <div class="point-item">
                                <input type="text" name="fullboard[]">
                    
                                <button type="button" class="remove-point-btn">
                                    Remove
                                </button>
                            </div>
                    
                        <?php } ?>

                    </div>
                    <button type="button" class="add-point-btn" data-target="fullboard-container">+ Add Fullboard</button>

                    <hr>

                    <h4>Halfboard</h4>
                    <div id="halfboard-container">

                        <?php if(!empty($halfboards)) { ?>

                            <?php foreach($halfboards as $item) { ?>

                                <div class="point-item">
                                    <input type="text"
                                           name="halfboard[]"
                                           value="<?= htmlspecialchars($item['description']); ?>">

                                    <button type="button" class="remove-point-btn">
                                        Remove
                                    </button>
                                </div>

                            <?php } ?>

                        <?php } else { ?>

                            <div class="point-item">
                                <input type="text" name="halfboard[]">

                                <button type="button" class="remove-point-btn">
                                    Remove
                                </button>
                            </div>

                        <?php } ?>

                    </div>
                    <button type="button" class="add-point-btn" data-target="halfboard-container">+ Add Halfboard</button>

                    <hr>

                    <h4>Excluded</h4>
                    <div id="excluded-container">

                        <?php if(!empty($excludeds)) { ?>
                    
                            <?php foreach($excludeds as $item) { ?>
                    
                                <div class="point-item">
                                    <input type="text"
                                           name="excluded[]"
                                           value="<?= htmlspecialchars($item['description']); ?>">
                    
                                    <button type="button" class="remove-point-btn">
                                        Remove
                                    </button>
                                </div>
                    
                            <?php } ?>
                    
                        <?php } else { ?>
                    
                            <div class="point-item">
                                <input type="text" name="excluded[]">
                    
                                <button type="button" class="remove-point-btn">
                                    Remove
                                </button>
                            </div>
                    
                        <?php } ?>
                    
                    </div>
                    <button type="button" class="add-point-btn" data-target="excluded-container">+ Add Excluded</button>

                </div>

                <button type="button" class="back-btn">Back</button>
                <button type="button" class="next-btn">Next</button>
            </div>

            <!-- STEP 6 -->
            <div class="form-step" data-step="6">

                <div class="form-section">
                    <h3>Final Setup</h3>

                    <div class="form-group">
                        <label>Itinerary File</label>

                        <?php if(!empty($package['itinerary_file'])) { ?>
                            <p>
                                Current File:
                                <a href="../uploads/<?= $package['itinerary_file']; ?>" target="_blank">
                                    View File
                                </a>
                            </p>
                        <?php } ?>

                        <input type="file" name="itinerary_file">
                    </div>

                    <div class="form-group">
                        <label>Main Image</label>

                        <?php if(!empty($package['main_image'])) { ?>

                            <img src="../uploads/<?= $package['main_image']; ?>"
                                 width="180"
                                 style="display:block; margin-bottom:10px; border-radius:12px;">

                        <?php } ?>

                        <input type="file" name="main_image">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="active" <?= ($package['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($package['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                </div>

                <button type="button" class="back-btn">Back</button>

                <button type="submit" name="update_package" class="submit-btn">
                    Update Package
                </button>
            </div>

        </form>

    </div>
</div>

<script>

// ================================
// STEP WIZARD SYSTEM (IMPROVED)
// ================================

let currentStep = 1;

const steps = document.querySelectorAll(".form-step");
const sidebarSteps = document.querySelectorAll(".step");

function showStep(step) {

    steps.forEach(s => s.classList.remove("active-step"));
    sidebarSteps.forEach(s => s.classList.remove("active"));

    const target = document.querySelector(`.form-step[data-step="${step}"]`);
    const sidebar = document.querySelector(`.step[data-step="${step}"]`);

    if (target) target.classList.add("active-step");
    if (sidebar) sidebar.classList.add("active");

    currentStep = step;
}

// init first step
showStep(1);


// ================================
// NEXT / BACK BUTTON (FIXED)
// ================================

document.querySelectorAll(".next-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        if (currentStep < 6) showStep(currentStep + 1);
    });
});

document.querySelectorAll(".back-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        if (currentStep > 1) showStep(currentStep - 1);
    });
});


// ================================
// SIDEBAR CLICK NAVIGATION
// ================================

sidebarSteps.forEach(step => {
    step.addEventListener("click", () => {
        const targetStep = parseInt(step.dataset.step);
        if (!isNaN(targetStep)) showStep(targetStep);
    });
});


// ================================
// TRAVEL DATE (ADD / REMOVE)
// ================================

const travelContainer = document.getElementById("travel-date-container");
const addDateBtn = document.getElementById("add-date-btn");

if (travelContainer && addDateBtn) {

    addDateBtn.addEventListener("click", function () {

        const div = document.createElement("div");
        div.classList.add("travel-date-item");

        div.innerHTML = `
            <input type="date" name="travel_date[]">
            <button type="button" class="remove-date-btn">Remove</button>
        `;

        travelContainer.appendChild(div);
    });

    travelContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-date-btn")) {
            e.target.parentElement.remove();
        }
    });
}


// ================================
// HIGHLIGHTS (ADD / REMOVE)
// ================================

const highlightContainer = document.getElementById("highlight-container");
const addHighlightBtn = document.getElementById("add-highlight-btn");

if (highlightContainer && addHighlightBtn) {

    addHighlightBtn.addEventListener("click", function () {

        const div = document.createElement("div");
        div.classList.add("highlight-item");

        div.innerHTML = `
            <input type="text" name="highlight_name[]" placeholder="Highlight Name">
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


// ================================
// FULLBOARD / HALFB0ARD / EXCLUDED (FIXED CLEAN VERSION)
// ================================

document.querySelectorAll(".add-point-btn").forEach(btn => {

    btn.addEventListener("click", function () {

        const target = this.dataset.target;
        const container = document.getElementById(target);

        if (!container) return;

        const type = target.replace("-container", "");

        const div = document.createElement("div");
        div.classList.add("point-item");

        div.innerHTML = `
            <input type="text" name="${type}[]" placeholder="Enter point">
            <button type="button" class="remove-point-btn">Remove</button>
        `;

        container.appendChild(div);
    });
});

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-point-btn")) {
        e.target.parentElement.remove();
    }
});


// ================================
// TOUR CATEGORY (COUNTRY SHOW/HIDE)
// ================================

document.addEventListener("DOMContentLoaded", function () {

    const tourCategory = document.getElementById("tour-category");
    const countryGroup = document.getElementById("country-group");
    const countrySelect = document.getElementById("country-select");

    if (!tourCategory || !countryGroup || !countrySelect) return;

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

</script>

</body>
</html>