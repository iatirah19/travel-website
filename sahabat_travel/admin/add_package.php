<?php
require '../db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Package - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="add_package.css">
</head>

<body>

<div class="page-header">
    <a href="admin_manage_package.php" class="btn-back">← Back</a>
    <h2>✏️ Add New Package</h2>
</div>

<div class="form-container">

<form action="admin_manage_package.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="add_package" value="1">

<!-- PACKAGE NAME -->
<div class="form-group">
    <label>Package Name</label>
    <input type="text" name="package_name" required>
</div>

<!-- CATEGORY -->
<div class="form-group">
    <label>Category</label>
    <select name="category_id" id="categorySelect" required>
        <option value="">-- Select Category --</option>
        <?php
        $cat = mysqli_query($conn, "SELECT * FROM categories");
        while ($c = mysqli_fetch_assoc($cat)) {
        ?>
        <option value="<?php echo $c['category_id']; ?>">
            <?php echo htmlspecialchars($c['category_name']); ?>
        </option>
        <?php } ?>
    </select>
</div>

<!-- COUNTRY -->
<div class="form-group" id="countryBox" style="display:none;">
    <label>Country</label>
    <select name="country_id">
        <option value="">-- Select Country --</option>
        <?php
        $country = mysqli_query($conn, "SELECT * FROM countries");
        while ($c = mysqli_fetch_assoc($country)) {
        ?>
        <option value="<?php echo $c['country_id']; ?>">
            <?php echo htmlspecialchars($c['country_name']); ?>
        </option>
        <?php } ?>
    </select>
</div>

<!-- PACKAGE TYPE -->
<div class="form-group">
    <label>Package Type</label>
    <select name="package_type" required>
        <option value="">-- Select Type --</option>
        <option value="SIT">SIT</option>
        <option value="MTB">MTB</option>
        <option value="JJ">JJ</option>
        <option value="SUKA">SUKA</option>
    </select>
</div>

<!-- PACKAGE CATEGORY (MTB ONLY) -->
<div class="form-group" id="packageCategoryBox" style="display:none;">
    <label>Package Category</label>
    <select name="package_category">
        <option value="">-- Select Category --</option>
        <option value="group">Group Package</option>
        <option value="private">Private Package</option>
        <option value="honeymoon">Honeymoon Package</option>
    </select>
</div>

<!-- DURATION -->
<div class="form-group">
    <label>Duration</label>
    <input type="text" name="duration" required placeholder="Example: 6D5N">
</div>

<!-- PRICE -->
<div class="form-group">
    <label>Price (RM)</label>
    <input type="number" step="0.01" name="price">
</div>

<!-- DEPOSIT -->
<div class="form-group">
    <label>Deposit (RM)</label>
    <input type="number" step="0.01" name="deposit">
</div>

<!-- FLIGHT -->
<div class="form-group">
    <label>Flight Details</label>
    <input type="text" name="flight" placeholder="Example: MH123 / AirAsia / Qatar Airways">
</div>

<!-- MIN PAX -->
<div class="form-group">
    <label>Minimum Pax</label>
    <input type="number" name="min_pax" required>
</div>

<!-- TRAVEL DATES -->
<div class="form-group">
    <label>Travel Dates</label>

    <div id="date-wrapper">
        <input type="date" name="travel_dates[]">
    </div>

    <button type="button" class="btn-add-date" onclick="addDate()">
        + Add More Date
    </button>
</div>

<!-- HIGHLIGHT -->
<div class="form-group">
    <label>Highlight Places</label>

    <div id="highlight-wrapper">
        <div class="highlight-item">
            <input type="text" name="highlight_name[]" placeholder="Place Name">
            <input type="file" name="highlight_image[]">
        </div>
    </div>

    <button type="button" class="btn-add-date" onclick="addHighlight()">
        + Add More Highlight
    </button>
</div>

<!-- HALFBOARD -->
<div class="form-group">
    <label>Halfboard Included</label>
    <textarea name="include_halfboard" rows="4" placeholder="1 item per line"></textarea>
</div>

<!-- FULLBOARD -->
<div class="form-group">
    <label>Fullboard Included</label>
    <textarea name="include_fullboard" rows="4" placeholder="1 item per line"></textarea>
</div>

<!-- EXCLUDED -->
<div class="form-group">
    <label>Exclude</label>
    <textarea name="exclude" rows="6" placeholder="1 item per line"></textarea>
</div>

<!-- ITINERARY FILE -->
<div class="form-group">
    <label>Upload Itinerary File</label>
    <input type="file" name="itinerary_file" accept=".pdf,.doc,.docx">
</div>

<!-- STATUS -->
<div class="form-group">
    <label>Status</label>
    <select name="status" required>
        <option value="">-- Select Status --</option>
        <option value="active">Popular</option>
        <option value="inactive">Not Popular</option>
    </select>
</div>

<!-- MAIN IMAGE -->
<div class="form-group">
    <label>Upload Main Image</label>
    <input type="file" name="image" accept="image/*" required>
</div>

<button type="submit" class="btn-submit">Add Package</button>

</form>
</div>

<!-- JS: DATE -->
<script>
function addDate() {
    let wrapper = document.getElementById("date-wrapper");

    let input = document.createElement("input");
    input.type = "date";
    input.name = "travel_dates[]";
    input.style.display = "block";
    input.style.marginTop = "8px";

    wrapper.appendChild(input);
}
</script>

<!-- JS: HIGHLIGHT -->
<script>
function addHighlight() {
    let wrapper = document.getElementById("highlight-wrapper");

    let div = document.createElement("div");
    div.classList.add("highlight-item");

    div.innerHTML = `
        <input type="text" name="highlight_name[]" placeholder="Place Name" required>
        <input type="file" name="highlight_image[]" accept="image/*" required>
    `;

    wrapper.appendChild(div);
}
</script>

<!-- COUNTRY AND PACKAGE CATEGORY TOGGLE -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const categorySelect = document.getElementById("categorySelect");
    const countryBox = document.getElementById("countryBox");
    const countrySelect = countryBox.querySelector("select");

    const packageType = document.querySelector("select[name='package_type']");
    const packageCategoryBox = document.getElementById("packageCategoryBox");
    const packageCategorySelect = packageCategoryBox.querySelector("select");

    function toggleCountry() {
        let category = categorySelect.value;

        if (category === "2") {
            countryBox.style.display = "block";
        } else {
            countryBox.style.display = "none";
            countrySelect.value = "";
        }
    }

    function togglePackageCategory() {
        let type = packageType.value;

        if (type === "MTB") {
            packageCategoryBox.style.display = "block";
        } else {
            packageCategoryBox.style.display = "none";
            packageCategorySelect.value = "";
        }
    }

    categorySelect.addEventListener("change", toggleCountry);
    packageType.addEventListener("change", togglePackageCategory);

    toggleCountry();
    togglePackageCategory();
});
</script>

</body>
</html>