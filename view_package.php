<?php
require 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "Invalid package ID";
    exit;
}

/* =========================
   GET PACKAGE
========================= */
$sql = "
    SELECT packages.*, countries.country_name
    FROM packages
    LEFT JOIN countries ON packages.country_id = countries.country_id
    WHERE packages.package_id = $id
";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Package not found";
    exit;
}

/* =========================
   GET DATES
========================= */
$date_query = mysqli_query($conn, "
    SELECT departure_date
    FROM package_dates
    WHERE package_id = $id
    ORDER BY departure_date ASC
");

/* =========================
   GET INCLUDE (HALF + FULL)
========================= */
$halfboard = [];
$fullboard = [];

$include_query = mysqli_query($conn, "
    SELECT type, description 
    FROM package_include 
    WHERE package_id = $id
");

while ($i = mysqli_fetch_assoc($include_query)) {

    if ($i['type'] == 'halfboard') {
        $halfboard[] = $i['description'];
    }

    if ($i['type'] == 'fullboard') {
        $fullboard[] = $i['description'];
    }
}

/* =========================
   GET EXCLUDE (OPTIONAL)
========================= */
$exclude = [];

$exclude_query = mysqli_query($conn, "
    SELECT description 
    FROM package_exclude 
    WHERE package_id = $id
");

while ($e = mysqli_fetch_assoc($exclude_query)) {
    $exclude[] = $e['description'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?></title>
    <link rel="stylesheet" href="view_package.css">
</head>

<body style="
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
    url('uploads/<?php echo htmlspecialchars($row['image']); ?>');
    background-size: cover;
">

<?php
$package_id = (int)$row['package_id'];

$highlight_check = mysqli_query($conn, "
    SELECT 1 FROM package_highlights 
    WHERE package_id = $package_id
    LIMIT 1
");

$has_highlight = mysqli_num_rows($highlight_check) > 0;
?>

<!-- ================= NAV ================= -->
<div class="package-short-nav">
  <?php if ($has_highlight) { ?>
    <button class="nav-box active" onclick="showSection('highlight')">📍 Tarikan</button>
  <?php } ?>
  
  <button class="nav-box" onclick="showSection('itinerary')">📅 Itinerary</button>
  <button class="nav-box" onclick="showSection('harga')">💰 Harga</button>
  <button class="nav-box" onclick="showSection('tarikh')">🗓️ Tarikh</button>
</div>

<h1>
<?php 
echo strtoupper(htmlspecialchars($row['title'])) . " " . htmlspecialchars($row['duration']);
?>
</h1>

<div id="highlight" class="content-section">
<?php
$package_id = (int)$row['package_id'];

$highlight = mysqli_query($conn, "
    SELECT * FROM package_highlights 
    WHERE package_id = $package_id
");

if (mysqli_num_rows($highlight) > 0) {
?>

<!-- ================= HIGHLIGHTS ================= -->
    <h2>Tarikan Utama</h2>

    <div class="highlight-container">

        <?php while ($h = mysqli_fetch_assoc($highlight)) { ?>

        <div class="card">

            <?php if (!empty($h['image'])) { ?>
                <img src="uploads/<?php echo htmlspecialchars($h['image']); ?>" alt="">
            <?php } ?>

            <h3><?php echo htmlspecialchars($h['name']); ?></h3>

        </div>

        <?php } ?>

    </div>

<?php
} // END IF
?>
</div>

<div id="itinerary" class="content-section">
<!-- ================= ITINERARY ================= -->
    <div class="accordion">

        <?php
        if (!empty($row['itinerary'])) {

            $itinerary = str_replace("\r", "", $row['itinerary']);
			$days = preg_split("/(?=Day\s*\d+)/", $itinerary);

            foreach ($days as $day) {
                $day = trim($day);
                if ($day == '') continue;

                $lines = explode("\n", $day);
                $title = array_shift($lines);
        ?>

        <button class="accordion-btn"><?php echo htmlspecialchars($title); ?></button>

        <div class="accordion-content">
            <ul>
                <?php foreach ($lines as $item) {
                    $item = trim($item);
                    if ($item == '') continue;
                ?>
                <li><?php echo htmlspecialchars($item); ?></li>
                <?php } ?>
            </ul>
        </div>

        <?php
            }
        } else {
            echo "<p>No itinerary available.</p>";
        }
        ?>

    </div>
</div>

<div id="harga" class="content-section">

<!-- ================= INCLUDE ================= -->
<div class="include-wrapper">

    <?php if (!empty($halfboard)) { ?>
        <div class="include-box">
            <h3>✔ Halfboard Included</h3>
            <ul>
                <?php foreach ($halfboard as $item) { ?>
                    <li><?php echo htmlspecialchars($item); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if (!empty($fullboard)) { ?>
        <div class="include-box">
            <h3>✔ Fullboard Included</h3>
            <ul>
                <?php foreach ($fullboard as $item) { ?>
                    <li><?php echo htmlspecialchars($item); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

</div>

<!-- ================= EXCLUDE (OPTIONAL) ================= -->
<?php if (!empty($exclude)) { ?>
<div class="exclude-box">
    <h3>✘ Not Included</h3>
    <ul>
        <?php foreach ($exclude as $item) { ?>
            <li><?php echo htmlspecialchars($item); ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

</div>

<div id="tarikh" class="content-section">
<!-- ================= TRAVEL DATES (BOTTOM) ================= -->
    <h2>Available Travel Dates</h2>

    <table class="date-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php
        if (mysqli_num_rows($date_query) > 0) {
            while ($d = mysqli_fetch_assoc($date_query)) {

                $date = date('d M Y', strtotime($d['departure_date']));
        ?>
            <tr>
                <td><?php echo $date; ?></td>

                <td>
                    <a href="book_package.php?package_id=<?php echo $row['package_id']; ?>&travel_date=<?php echo $d['departure_date']; ?>" class="btn-book">
					Book Now
					</a>
                </td>
            </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='2'>No travel dates available.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>



<script>
const acc = document.querySelectorAll(".accordion-btn");

acc.forEach(btn => {
    btn.addEventListener("click", () => {
        const content = btn.nextElementSibling;
        content.style.display =
            content.style.display === "block" ? "none" : "block";
    });
});
</script>

<script>
function showSection(sectionId) {

  // hide semua content
  document.querySelectorAll('.content-section').forEach(sec => {
    sec.classList.remove('active');
  });

  // buang active nav
  document.querySelectorAll('.nav-box').forEach(btn => {
    btn.classList.remove('active');
  });

  // show section dipilih
  document.getElementById(sectionId).classList.add('active');

  // highlight button
  event.target.classList.add('active');
}

// default buka section pertama
document.addEventListener("DOMContentLoaded", function() {
  showSection('itinerary'); // boleh tukar default
});
</script>
</body>
</html>