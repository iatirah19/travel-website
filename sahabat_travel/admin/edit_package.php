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
    $packageQuery = mysqli_query($conn, "SELECT * FROM packages WHERE package_id='$package_id'");
    $package = mysqli_fetch_assoc($packageQuery);

    if (!$package) {
        die("Package not found.");
    }

    /*
    |--------------------------
    | DROPDOWN
    |--------------------------
    */
    $countryQuery = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");
    $agencyQuery = mysqli_query($conn, "SELECT * FROM agencies ORDER BY agency_name ASC");
    $tourCategoryQuery = mysqli_query($conn, "SELECT * FROM tour_categories ORDER BY tour_category_name ASC");
    $packageCategoryQuery = mysqli_query($conn, "SELECT * FROM package_categories ORDER BY category_name ASC");

    /*
    |--------------------------
    | PRICING
    |--------------------------
    */
    $pricingRes = mysqli_query($conn, "SELECT * FROM package_pricing WHERE package_id='$package_id'");
    $prices = [];

    while ($row = mysqli_fetch_assoc($pricingRes)) {
        $prices[$row['type']] = $row['price'];
    }

    /*
    |--------------------------
    | INCLUDE
    |--------------------------
    */
    $includeRes = mysqli_query($conn, "SELECT * FROM package_include WHERE package_id='$package_id'");

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
    $excludeRes = mysqli_query($conn, "SELECT * FROM package_exclude WHERE package_id='$package_id'");
    $exclude = [];

    while ($row = mysqli_fetch_assoc($excludeRes)) {
        $exclude[] = $row['description'];
    }

    /*
    |--------------------------
    | DATES
    |--------------------------
    */
    $dateRes = mysqli_query($conn, "SELECT * FROM package_dates WHERE package_id='$package_id'");
    $dates = [];

    while ($row = mysqli_fetch_assoc($dateRes)) {
        $dates[] = $row['travel_date'];
    }

    /*
    |--------------------------
    | HIGHLIGHTS
    |--------------------------
    */
    $highlightRes = mysqli_query($conn, "SELECT * FROM package_highlights WHERE package_id='$package_id'");
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

        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $duration = mysqli_real_escape_string($conn, $_POST['duration']);
        $tour_category_id = intval($_POST['tour_category']);
        $agency_id = intval($_POST['agency_id']);
        $package_category_id = intval($_POST['package_category_id']);
        $country_id = !empty($_POST['country_id']) ? intval($_POST['country_id']) : null;

        $deposit = $_POST['deposit'] ?? 0;
        $min_pax = $_POST['min_pax'] ?? 1;

        $flight_details = mysqli_real_escape_string($conn, $_POST['flight_details'] ?? '');
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        /*
        |--------------------------
        | FILES
        |--------------------------
        */
        $itinerary_file = $package['itinerary_file'];
        $main_image = $package['main_image'];

        if (!empty($_FILES['itinerary_file']['name'])) {
            $itinerary_file = time()."_".uniqid().$_FILES['itinerary_file']['name'];
            move_uploaded_file($_FILES['itinerary_file']['tmp_name'], "../uploads/".$itinerary_file);
        }

        if (!empty($_FILES['main_image']['name'])) {
            $main_image = time()."_".uniqid().$_FILES['main_image']['name'];
            move_uploaded_file($_FILES['main_image']['tmp_name'], "../uploads/".$main_image);
        }

        /*
        |--------------------------
        | UPDATE PACKAGE
        |--------------------------
    */
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

    /*
    |--------------------------
    | DELETE OLD DATA
    |--------------------------
    */
    mysqli_query($conn, "DELETE FROM package_pricing WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_include WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_exclude WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_dates WHERE package_id='$package_id'");
    mysqli_query($conn, "DELETE FROM package_highlights WHERE package_id='$package_id'");

    /*
    |--------------------------
    | PRICING
    |--------------------------
    */
    if (!empty($_POST['price'])) {
        foreach ($_POST['price'] as $type => $price) {
            mysqli_query($conn, "
                INSERT INTO package_pricing (package_id,type,price)
                VALUES ('$package_id','$type','$price')
            ");
        }
    }

    /*
    |--------------------------
    | INCLUDE
    |--------------------------
    */
    if (!empty($_POST['fullboard_points'])) {
        foreach ($_POST['fullboard_points'] as $p) {
            if ($p != '') {
                $p = mysqli_real_escape_string($conn, $p);
                mysqli_query($conn, "INSERT INTO package_include VALUES (NULL,'$package_id','Fullboard','$p')");
            }
        }
    }

    if (!empty($_POST['halfboard_points'])) {
        foreach ($_POST['halfboard_points'] as $p) {
            if ($p != '') {
                $p = mysqli_real_escape_string($conn, $p);
                mysqli_query($conn, "INSERT INTO package_include VALUES (NULL,'$package_id','Halfboard','$p')");
            }
        }
    }

    /*
    |--------------------------
    | EXCLUDE
    |--------------------------
    */
    if (!empty($_POST['excluded'])) {

        // delete only kalau ada data baru nak replace
        mysqli_query($conn, "DELETE FROM package_exclude WHERE package_id='$package_id'");

        foreach ($_POST['excluded'] as $p) {
            if (trim($p) != '') {
                $p = mysqli_real_escape_string($conn, $p);

                mysqli_query($conn, "
                    INSERT INTO package_exclude (package_id, description)
                    VALUES ('$package_id', '$p')
                ");
            }
        }
    }

    /*
    |--------------------------
    | DATES
    |--------------------------
    */
    if (!empty($_POST['travel_date'])) {
        foreach ($_POST['travel_date'] as $d) {
            if ($d != '') {
                mysqli_query($conn, "INSERT INTO package_dates VALUES (NULL,'$package_id','$d')");
            }
        }
    }

    /*
    |--------------------------
    | HIGHLIGHTS
    |--------------------------
    */
    if (!empty($_POST['highlight_name'])) {

        foreach ($_POST['highlight_name'] as $key => $title) {

            $title = mysqli_real_escape_string($conn, $title);
            $img = "";

            if (!empty($_FILES['highlight_image']['name'][$key])) {

                $ext = pathinfo($_FILES['highlight_image']['name'][$key], PATHINFO_EXTENSION);
                $img = time()."_".uniqid().".".$ext;

                move_uploaded_file(
                    $_FILES['highlight_image']['tmp_name'][$key],
                    "../uploads/packages/".$img
                );
            }

            mysqli_query($conn, "
                INSERT INTO package_highlights (package_id,highlight_name,highlight_image)
                VALUES ('$package_id','$title','$img')
            ");
        }
    }

    echo "<script>
        alert('Package updated successfully');
        window.location.href='admin_manage_package.php';
    </script>";

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

<h1 class="page-title">Edit Package</h1>

<form action="" method="POST" enctype="multipart/form-data">

<input type="hidden" name="package_id" value="<?= $package_id ?>">

<!-- ================= BASIC INFO ================= -->
<div class="form-section">
    <h3>Basic Information</h3>

    <div class="form-row">

        <div class="form-group">
            <label>Package Name</label>
            <input type="text" name="title" value="<?= $package['title'] ?>" required>
        </div>

        <div class="form-group">
            <label>Duration</label>
            <input type="text" name="duration" value="<?= $package['duration_days'] ?>" required>
        </div>

    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Tour Category</label>
            <select name="tour_category">
                <option value="">-- Select Category --</option>
                <?php while($category = mysqli_fetch_assoc($tourCategoryQuery)) { ?>
                    <option value="<?= $category['tour_category_id'] ?>"
                        <?= $package['tour_category_id'] == $category['tour_category_id'] ? 'selected' : '' ?>>
                        <?= $category['tour_category_name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Country</label>
            <select name="country_id">
                <option value="">-- Select Country --</option>
                <?php while($country = mysqli_fetch_assoc($countryQuery)) { ?>
                    <option value="<?= $country['country_id'] ?>"
                        <?= $package['country_id'] == $country['country_id'] ? 'selected' : '' ?>>
                        <?= $country['country_name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Agency</label>
            <select name="agency_id">
                <?php while($agency = mysqli_fetch_assoc($agencyQuery)) { ?>
                    <option value="<?= $agency['agency_id'] ?>"
                        <?= $package['agency_id'] == $agency['agency_id'] ? 'selected' : '' ?>>
                        <?= $agency['agency_name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Package Category</label>
            <select name="package_category_id">
                <?php while($pc = mysqli_fetch_assoc($packageCategoryQuery)) { ?>
                    <option value="<?= $pc['package_category_id'] ?>"
                        <?= $package['package_category_id'] == $pc['package_category_id'] ? 'selected' : '' ?>>
                        <?= $pc['category_name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

    </div>
</div>

<!-- ================= PRICE ================= -->
<div class="form-section">
    <h3>Package Price</h3>

    <?php
    $pricingQuery = mysqli_query($conn, "SELECT * FROM package_pricing WHERE package_id='$package_id'");

    $prices = [];

    while ($p = mysqli_fetch_assoc($pricingQuery)) {
        $type = strtolower(str_replace([' ', '/'], '_', $p['type']));
        $prices[$type] = $p['price'];
    }
    ?>

    <div class="price-grid">

        <div class="form-group">
            <label>Adult Twin / Triple</label>
            <input type="number" step="0.01"
                name="price[adult_twin_triple]"
                value="<?= $prices['adult_twin_triple'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Single</label>
            <input type="number" step="0.01"
                name="price[single]"
                value="<?= $prices['single'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Child Twin</label>
            <input type="number" step="0.01"
                name="price[child_twin]"
                value="<?= $prices['child_twin'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Child No Bed</label>
            <input type="number" step="0.01"
                name="price[child_no_bed]"
                value="<?= $prices['child_no_bed'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Child With Bed</label>
            <input type="number" step="0.01"
                name="price[child_with_bed]"
                value="<?= $prices['child_with_bed'] ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Infant</label>
            <input type="number" step="0.01"
                name="price[infant]"
                value="<?= $prices['infant'] ?? '' ?>">
        </div>

    </div>
</div>

<!-- ================= EXTRA INFO ================= -->
<div class="form-section">
    <h3>Extra Information</h3>

    <div class="form-row">

        <div class="form-group">
            <label>Deposit Per Pax</label>
            <input type="number" name="deposit" step="0.01"
                value="<?= $package['deposit'] ?>">
        </div>

        <div class="form-group">
            <label>Minimum Pax</label>
            <input type="number" name="min_pax"
                value="<?= $package['min_pax'] ?>">
        </div>

    </div>

    <div class="form-group full-width">
        <label>Flight Details</label>
        <textarea name="flight_details" rows="4"><?= $package['flight_details'] ?></textarea>
    </div>
</div>

<!-- ================= TRAVEL DATES ================= -->
<div class="form-section">
    <h3>Travel Dates</h3>

    <div id="travel-date-container">
        <?php foreach ($dates as $d) { ?>
            <div class="travel-date-item">
                <input type="date" name="travel_date[]" value="<?= $d ?>">
                <button type="button" class="remove-date-btn">Remove</button>
            </div>
        <?php } ?>
    </div>

    <button type="button" id="add-date-btn">+ Add Date</button>
</div>

<!-- ================= HIGHLIGHTS ================= -->
<div class="form-section">
    <h3>Highlight Places</h3>

    <div id="highlight-container">

        <?php foreach ($highlights as $h) { ?>
        <div class="highlight-item">

            <input type="text" name="highlight_name[]" value="<?= $h['highlight_name'] ?>">

            <input type="file" name="highlight_image[]">

            <?php if (!empty($h['highlight_image'])) { ?>
                <img src="uploads/packages/<?= $h['highlight_image'] ?>" width="80">
            <?php } ?>

            <button type="button" class="remove-highlight-btn">Remove</button>

        </div>
        <?php } ?>

    </div>

    <button type="button" id="add-highlight-btn">+ Add Highlight</button>
</div>

<!-- ================= INCLUDED ================= -->
<div class="form-section">
    <h3>Package Includes</h3>

    <div class="included-row">

        <div class="included-box">
            <h4>Fullboard</h4>

            <div id="fullboard-container">
                <?php if (!empty($include['Fullboard'])) { ?>
                    <?php foreach ($include['Fullboard'] as $point) { ?>
                        <div class="point-item">
                            <input type="text" name="fullboard_points[]" value="<?= $point ?>">
                            <button type="button" onclick="this.parentElement.remove()">X</button>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <button type="button" onclick="addPoint('fullboard')">+ Add Point</button>
        </div>

        <div class="included-box">
            <h4>Halfboard</h4>

            <div id="halfboard-container">
                <?php if (!empty($include['Halfboard'])) { ?>
                    <?php foreach ($include['Halfboard'] as $point) { ?>
                        <div class="point-item">
                            <input type="text" name="halfboard_points[]" value="<?= $point ?>">
                            <button type="button" onclick="this.parentElement.remove()">X</button>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <button type="button" onclick="addPoint('halfboard')">+ Add Point</button>
        </div>

    </div>
</div>

<!-- ================= EXCLUDED ================= -->
<div class="form-section">
    <h3>Excluded</h3>

    <div class="included-box excluded-box">

        <div id="excluded-container">

            <?php if (!empty($exclude)) { ?>
                <?php foreach ($exclude as $point) { ?>
                    
                    <div class="point-item">
                        <input type="text" name="excluded[]" value="<?= htmlspecialchars($point) ?>">
                        
                        <button type="button" onclick="this.parentElement.remove()">
                            X
                        </button>
                    </div>

                <?php } ?>
            <?php } ?>

        </div>

        <button type="button" onclick="addPoint('excluded')">
            + Add Point
        </button>

    </div>
</div>

<!-- ================= FINAL ================= -->
<div class="form-section">
    <h3>Final Setup</h3>

    <div class="form-row">

        <!-- ITINERARY FILE -->
        <div class="form-group">
            <label>Upload Itinerary File</label>
            <input type="file" name="itinerary_file">

            <?php if (!empty($package['itinerary_file'])) { ?>
                <small>
                    Current File: 
                    <a href="../uploads/<?= $package['itinerary_file'] ?>" target="_blank">
                        View File
                    </a>
                </small>
            <?php } ?>
        </div>

        <!-- STATUS -->
        <div class="form-group">
            <label>Package Status</label>
            <select name="status">
                <option value="active" <?= $package['status']=='active'?'selected':'' ?>>Active</option>
                <option value="inactive" <?= $package['status']=='inactive'?'selected':'' ?>>Inactive</option>
            </select>
        </div>

        <!-- MAIN IMAGE -->
        <div class="form-group">
            <label>Main Image</label>
            <input type="file" name="main_image">

            <?php if (!empty($package['main_image'])) { ?>
                <div style="margin-top:10px;">
                    <small>Current Image:</small><br>
                    <img src="../uploads/<?= $package['main_image'] ?>" width="120">
                </div>
            <?php } ?>
        </div>

    </div>
</div>

<!-- SUBMIT -->
<button type="submit" name="update_package">
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
    // INCLUDED / EXCLUDED
    // ======================

    window.addPoint = function(type) {

        let containerId = "";
        let inputName = "";

        if (type === "fullboard") {
            containerId = "fullboard-container";
            inputName = "fullboard_points[]";
        } 
        else if (type === "halfboard") {
            containerId = "halfboard-container";
            inputName = "halfboard_points[]";
        }
        else if (type === "excluded") {
            containerId = "excluded-container";
            inputName = "excluded[]";
        }

        const container = document.getElementById(containerId);
        if (!container) return;

        const div = document.createElement("div");
        div.classList.add("point-item");

        div.innerHTML = `
            <input type="text" name="${inputName}" placeholder="Enter point">
            <button type="button" onclick="this.parentElement.remove()">X</button>
        `;

        container.appendChild(div);
    };
});
</script>
</body>
</html>