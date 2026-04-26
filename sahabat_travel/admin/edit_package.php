<?php
require '../db.php';

if (!isset($_GET['id'])) {
    echo "No package selected";
    exit;
}

$id = (int) $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM packages WHERE package_id = $id");
$row = mysqli_fetch_assoc($result);

$package_id = $row['package_id'];

$category_query = mysqli_query($conn, "SELECT * FROM categories");
$country_query  = mysqli_query($conn, "SELECT * FROM countries");

$date_query = mysqli_query($conn, "SELECT * FROM package_dates WHERE package_id = $package_id");
$highlight_query = mysqli_query($conn, "SELECT * FROM package_highlights WHERE package_id = $package_id");

function getList($conn, $id, $type = null) {
    $q = $type
        ? "SELECT description FROM package_include WHERE package_id=$id AND type='$type'"
        : "SELECT description FROM package_exclude WHERE package_id=$id";

    $res = mysqli_query($conn, $q);

    $arr = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $arr[] = $r['description'];
    }
    return $arr;
}

$halfboard = getList($conn, $package_id, 'halfboard');
$fullboard = getList($conn, $package_id, 'fullboard');
$exclude   = getList($conn, $package_id);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Package</title>
<link rel="stylesheet" href="edit_package.css">
</head>

<body>

<div class="page-header">
    <a href="admin_manage_package.php" class="btn-back">← Back</a>
    <h2>✏️ Edit Package</h2>
</div>

<div class="form-container">

<form action="admin_manage_package.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="update_package" value="1">
<input type="hidden" name="package_id" value="<?php echo $id; ?>">

<!-- PACKAGE NAME -->
<div class="form-group">
    <label>Package Name</label>
    <input type="text" name="package_name"
           value="<?php echo htmlspecialchars($row['title']); ?>" required>
</div>

<!-- CATEGORY -->
<div class="form-group">
    <label>Category</label>
    <select name="category_id" id="categorySelect" required>
        <option value="">-- Select Category --</option>
        <?php while ($c = mysqli_fetch_assoc($category_query)) { ?>
            <option value="<?php echo $c['category_id']; ?>"
                <?php if ($c['category_id'] == $row['category_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($c['category_name']); ?>
            </option>
        <?php } ?>
    </select>
</div>

<!-- COUNTRY -->
<div class="form-group" id="countryBox" style="display:none;">
    <label>Country</label>
    <select name="country_id" id="countrySelect">
        <option value="">-- Select Country --</option>
        <?php while ($c = mysqli_fetch_assoc($country_query)) { ?>
            <option value="<?php echo $c['country_id']; ?>"
                <?php if ($c['country_id'] == $row['country_id']) echo 'selected'; ?>>
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
        <option value="SIT" <?php if($row['package_type']=='SIT') echo 'selected'; ?>>SIT</option>
        <option value="MTB" <?php if($row['package_type']=='MTB') echo 'selected'; ?>>MTB</option>
        <option value="JJ" <?php if($row['package_type']=='JJ') echo 'selected'; ?>>JJ</option>
        <option value="SUKA" <?php if($row['package_type']=='SUKA') echo 'selected'; ?>>SUKA</option>
    </select>
</div>

<!-- DURATION -->
<div class="form-group">
    <label>Duration</label>
    <input type="text" name="duration" value="<?php echo $row['duration']; ?>">
</div>

<!-- PRICE -->
<div class="form-group">
    <label>Price (RM)</label>
    <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>">
</div>

<!-- DEPOSIT -->
<div class="form-group">
    <label>Deposit (RM)</label>
    <input type="number" step="0.01" name="deposit" value="<?php echo $row['deposit']; ?>">
</div>

<!-- FLIGHT -->
<div class="form-group">
    <label>Flight Details</label>
    <input type="text" name="flight" value="<?php echo htmlspecialchars($row['flight']); ?>">
</div>

<!-- MIN PAX -->
<div class="form-group">
    <label>Minimum Pax</label>
    <input type="number" name="min_pax" value="<?php echo $row['min_pax']; ?>">
</div>

<!-- DATES -->
<div class="form-group">
    <label>Travel Dates</label>
    <div class="date-wrapper">
        <?php while ($d = mysqli_fetch_assoc($date_query)) { ?>
            <div class="date-item">
                <span><?php echo date('d M Y', strtotime($d['departure_date'])); ?></span>
                <a href="delete_date.php?id=<?php echo $d['date_id']; ?>&package_id=<?php echo $id; ?>">❌</a>
            </div>
        <?php } ?>
    </div>
    <button type="button" class="btn-add-date" onclick="addDate()">+ Add Date</button>
</div>

<!-- HIGHLIGHT -->
<div class="form-group">
<label>Highlight Places</label>

<div id="highlight-wrapper">

<?php $i = 0; while ($h = mysqli_fetch_assoc($highlight_query)) { ?>

    <div class="highlight-item">

        <!-- ID -->
        <input type="hidden" name="highlight_id[]" value="<?php echo $h['highlight_id']; ?>">

        <!-- NAME -->
        <input type="text" name="highlight_name[]"
               value="<?php echo htmlspecialchars($h['name']); ?>">

        <!-- EXISTING IMAGE -->
        <input type="hidden" name="existing_image[]" value="<?php echo $h['image']; ?>">

        <!-- NEW IMAGE -->
        <input type="file" name="highlight_image[]">

        <!-- PREVIEW -->
        <?php if (!empty($h['image'])) { ?>
            <img src="../uploads/<?php echo $h['image']; ?>" width="80">
        <?php } ?>

    </div>

<?php $i++; } ?>

</div>

<button type="button" class="btn-add-date" onclick="addHighlight()">+ Add Highlight</button>
</div>

<!-- INCLUDE -->
<div class="form-group">
<label>Halfboard Included</label>
<textarea name="include_halfboard" placeholder="1 item per line"><?php echo implode("\n", $halfboard); ?></textarea>
</div>

<div class="form-group">
<label>Fullboard Included</label>
<textarea name="include_fullboard" placeholder="1 item per line"><?php echo implode("\n", $fullboard); ?></textarea>
</div>

<!-- EXCLUDE -->
<div class="form-group">
<label>Exclude</label>
<textarea name="exclude"><?php echo implode("\n", $exclude); ?></textarea>
</div>

<!-- PDF -->
<div class="form-group">
    <label>Upload Itinerary File</label>
    <input type="file" name="itinerary_file">
    <br>
    <?php if (!empty($row['itinerary_file'])) { ?>
        <a href="../uploads/<?php echo $row['itinerary_file']; ?>" target="_blank">View Current PDF</a>
    <?php } ?>
</div>

<!-- STATUS -->
<div class="form-group">
<label>Status</label>
<select name="status">
<option value="active" <?php if($row['status']=='active') echo 'selected'; ?>>Popular</option>
<option value="inactive" <?php if($row['status']=='inactive') echo 'selected'; ?>>Not Popular</option>
</select>
</div>

<!-- IMAGE -->
<div class="form-group">
<label>Upload Main Image</label>
<input type="file" name="image">
<br>
<img src="../uploads/<?php echo $row['image']; ?>" width="120">
</div>

<button type="submit" class="btn-submit">Update Package</button>

</form>
</div>

<script>
let highlightIndex = <?php echo $i ?? 0; ?>;

function addHighlight() {
    let div = document.createElement("div");
    div.innerHTML = `
        <input type="text" name="highlight_name[${highlightIndex}]">
        <input type="file" name="highlight_image[${highlightIndex}]">
    `;
    document.getElementById("highlight-wrapper").appendChild(div);
    highlightIndex++;
}

function addDate() {
    let div = document.createElement("div");
    div.innerHTML = `<input type="date" name="new_dates[]">`;
    document.querySelector(".date-wrapper").appendChild(div);
}

document.addEventListener("DOMContentLoaded", function () {
    const categorySelect = document.getElementById("categorySelect");
    const countryBox = document.getElementById("countryBox");

    function toggleCountry() {
        countryBox.style.display = (categorySelect.value === "2") ? "block" : "none";
    }

    categorySelect.addEventListener("change", toggleCountry);
    toggleCountry();
});
</script>

</body>
</html>