<?php

require '../db.php';

$countryQuery = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");

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

<form action="add_package.php" method="POST" enctype="multipart/form-data">

    <!-- ROW 1 -->
<div class="form-row">

    <!-- Package Name -->
    <div class="form-group">
        <label>Package Name</label>
        <input type="text" name="title" required>
    </div>

    <!-- Duration -->
    <div class="form-group">
        <label>Duration</label>
        <input type="text" name="duration" placeholder="Example: 5D4N" required>
    </div>

</div>

<!-- ROW 2 -->
<div class="form-row">

    <!-- Tour Categories -->
    <div class="form-group">
        <label>Tour Category</label>

        <select name="tour_category" id="tour-category" required>
            <option value="">-- Select Category --</option>
            <option value="Domestic">Domestic</option>
            <option value="International">International</option>
            <option value="Umrah">Umrah</option>
        </select>
    </div>

    <!-- Country -->
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

<!-- ROW 3 -->
<div class="form-row">

    <!-- Agency -->
    <div class="form-group">
        <label>Agency</label>

        <select name="agency" required>
            <option value="">-- Select Agency --</option>
            <option value="MTB">MTB</option>
            <option value="SIT">SIT</option>
            <option value="SUKA">SUKA</option>
            <option value="JOMJALAN">JOMJALAN</option>
        </select>
    </div>

    <!-- Package Categories -->
    <div class="form-group">
        <label>Package Category</label>

        <select name="package_category" required>
            <option value="">-- Select Package Type --</option>
            <option value="Group">Group</option>
            <option value="Private">Private</option>
            <option value="Honeymoon">Honeymoon</option>
        </select>
    </div>

</div>

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
            <label>Child No Bed (3 - 6 Years)</label>
            <input type="number" step="0.01" name="child_no_bed">
        </div>

        <div class="form-group">
            <label>Child With Bed (7 - 12 Years)</label>
            <input type="number" step="0.01" name="child_with_bed">
        </div>

        <div class="form-group">
            <label>Infant</label>
            <input type="number" step="0.01" name="infant_price">
        </div>

    </div>

    <!-- ROW: Deposit / Flight Detail / Minimum Pax -->
    <div class="form-row">

        <!-- Deposit -->
        <div class="form-group">
            <label>Deposit Per Pax</label>
            <input type="number" step="0.01" name="deposit_per_pax">
        </div>

        <!-- Flight Detail -->
        <div class="form-group">
            <label>Flight Detail</label>
            <input name="flight_detail" rows="4"></input>
        </div>

        <!-- Minimum Pax -->
        <div class="form-group">
            <label>Minimum Pax</label>
            <input type="number" name="min_pax">
        </div>

    </div>

    <!-- Travel Dates -->
    <div class="form-group">

        <label>Travel Dates</label>

        <div id="travel-date-container">

            <div class="travel-date-item">
                <input type="date" name="travel_date[]">
                <button type="button" class="remove-date-btn">Remove</button>
            </div>

        </div>

        <button type="button" id="add-date-btn">
            + Add Date
        </button>

    </div>

    <!-- Highlight Places -->
    <div class="form-group">

        <label>Highlight Places</label>

        <div id="highlight-container">

            <div class="highlight-item">

                <input type="text" name="highlight_title[]" placeholder="Place name">

                <input type="file" name="highlight_image[]">

                <button type="button" class="remove-highlight-btn">
                    Remove
                </button>

            </div>

        </div>

        <button type="button" id="add-highlight-btn">
            + Add Highlight
        </button>

    </div>

    <!-- INCLUDED -->
<div class="included-row">

    <!-- FULLBOARD -->
    <div class="included-box">
        <h4>Fullboard</h4>

        <div id="fullboard-container" class="included-points"></div>

        <button type="button" onclick="addPoint('fullboard')">
            + Add Fullboard Point
        </button>
    </div>

    <!-- HALFB0ARD -->
    <div class="included-box">
        <h4>Halfboard</h4>

        <div id="halfboard-container" class="included-points"></div>

        <button type="button" onclick="addPoint('halfboard')">
            + Add Halfboard Point
        </button>
    </div>

</div>

    <!-- Excluded -->
    <div class="form-group">
        <label>Excluded</label>
        <textarea name="excluded" rows="4"></textarea>
    </div>

    <!-- ROW: Itinerary / Status / Main Image -->
    <div class="form-row">

        <!-- Upload Itinerary -->
        <div class="form-group">
            <label>Upload Itinerary File</label>
            <input type="file" name="itinerary_file">
        </div>

        <!-- Status -->
        <div class="form-group">
            <label>Status</label>

            <select name="status">
                <option value="">-- Select Status --</option>
                <option value="Popular">Popular</option>
                <option value="Not Popular">Not Popular</option>
            </select>
        </div>

        <!-- Main Image -->
        <div class="form-group">
            <label>Upload Main Image</label>
            <input type="file" name="main_image" accept="image/*">
        </div>

    </div>

    <!-- Submit -->
    <button type="submit" name="submit_package">
        Add Package
    </button>

</form>

<!-- TRAVEL DATE -->
<script>

const travelContainer = document.getElementById("travel-date-container");

// ADD DATE
document.getElementById("add-date-btn").addEventListener("click", function () {

    const newDateField = document.createElement("div");

    newDateField.classList.add("travel-date-item");

    newDateField.innerHTML = `
        <input type="date" name="travel_date[]">
        <button type="button" class="remove-date-btn">Remove</button>
    `;

    travelContainer.appendChild(newDateField);

});

// REMOVE DATE
travelContainer.addEventListener("click", function (e) {

    if (e.target.classList.contains("remove-date-btn")) {
        e.target.parentElement.remove();
    }

});

</script>

<!-- HIGHLIGHT -->
<script>

const highlightContainer = document.getElementById("highlight-container");

// ADD HIGHLIGHT
document.getElementById("add-highlight-btn").addEventListener("click", function () {

    const newItem = document.createElement("div");

    newItem.classList.add("highlight-item");

    newItem.innerHTML = `
        <input type="text" name="highlight_title[]" placeholder="Place name">
        <input type="file" name="highlight_image[]">
        <button type="button" class="remove-highlight-btn">Remove</button>
    `;

    highlightContainer.appendChild(newItem);

});

// REMOVE HIGHLIGHT
highlightContainer.addEventListener("click", function (e) {

    if (e.target.classList.contains("remove-highlight-btn")) {
        e.target.parentElement.remove();
    }

});

</script>

<!-- COUNTRY SHOW/HIDE -->
<script>

const tourCategory = document.getElementById("tour-category");
const countryGroup = document.getElementById("country-group");
const countrySelect = document.getElementById("country-select");

tourCategory.addEventListener("change", function () {

    if (this.value === "International") {

        countryGroup.style.display = "block";

    } else {

        countryGroup.style.display = "none";

        countrySelect.value = "";

    }

});

</script>

<!-- FULLBOARD / HALFBOARD -->
<script>
function addPoint(type) {

    let container = document.getElementById(type + "-container");

    let div = document.createElement("div");
    div.classList.add("point-item");

    div.innerHTML = `
        <input type="text" name="${type}_points[]" placeholder="Enter point">
        <button type="button" onclick="this.parentElement.remove()">X</button>
    `;

    container.appendChild(div);
}
</script>

</body>
</html>