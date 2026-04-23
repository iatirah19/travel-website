<?php
require 'db.php';

// GET ID
$package_id = $_GET['id'] ?? null;

$data = null;
$highlights = null;
$date_query = null;
$halfboard = [];
$fullboard = [];
$exclude = [];

if ($package_id) {

    // PACKAGE
    $package = mysqli_query($conn, 
        "SELECT * FROM packages WHERE package_id='$package_id'");
    
    if (!$package) {
        die("Query Error: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($package);

    // HIGHLIGHTS
    $highlights = mysqli_query($conn, 
        "SELECT * FROM package_highlights WHERE package_id='$package_id'");

    if (!$highlights) {
        die("Query Error: " . mysqli_error($conn));
    }

    // DATES
    $date_query = mysqli_query($conn, "
        SELECT departure_date
        FROM package_dates
        WHERE package_id='$package_id'
        ORDER BY departure_date ASC
    ");

    // INCLUDE
    $include_query = mysqli_query($conn, "
        SELECT type, description 
        FROM package_include 
        WHERE package_id='$package_id'
    ");

    while ($i = mysqli_fetch_assoc($include_query)) {
        if ($i['type'] == 'halfboard') {
            $halfboard[] = $i['description'];
        }
        if ($i['type'] == 'fullboard') {
            $fullboard[] = $i['description'];
        }
    }

    // EXCLUDE
    $exclude_query = mysqli_query($conn, "
        SELECT description 
        FROM package_exclude 
        WHERE package_id='$package_id'
    ");

    while ($e = mysqli_fetch_assoc($exclude_query)) {
        $exclude[] = $e['description'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(strtolower($data['title'] . ' ' . $data['duration'])); ?></title>
    <link rel="stylesheet" href="view_package 1.css">
    
</head>

<body>

<section class="hero">
    <div class="hero-overlay"></div>
    <a href="javascript:history.back()" class="back-btn">← Back</a>
    <?php if($data) { ?>
        <img src="uploads/<?php echo $data['image']; ?>" class="hero-img">
    <?php } ?>
</section>

<div class="page-container">

    <!-- TITLE -->
    <?php if($data) { ?>
        <h1 class="package-title">
            <?php echo $data['title']; ?> <?php echo $data['duration']; ?>
        </h1>
    <?php } else { ?>
        <h1 class="package-title">Package not found</h1>
    <?php } ?>

    <div class="layout-wrapper">

        <!-- LEFT SIDE -->
        <div class="left-side">

            <!-- HIGHLIGHT -->
            <div class="box">
                <h2 class="section-title">Highlight Places</h2>

                <div class="highlight-container">
                    <?php if ($highlights && mysqli_num_rows($highlights) > 0) { ?>
                        <?php while($row = mysqli_fetch_assoc($highlights)) { ?>
                            <div class="highlight-card">
                                <img src="uploads/<?php echo $row['image']; ?>">
                                <div class="highlight-name">
                                    <?php echo $row['name']; ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p style="text-align:center;">No highlights found</p>
                    <?php } ?>
                </div>
            </div>

            <!-- DATE -->
            <div class="box">
                <h2 class="section-title">Available Travel Dates</h2>

                <table class="date-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if ($date_query && mysqli_num_rows($date_query) > 0) {
                            while ($d = mysqli_fetch_assoc($date_query)) {
                                $date = date('d M Y', strtotime($d['departure_date']));
                        ?>
                                <tr>
                                    <td><?php echo $date; ?></td>
                                    <td>
                                        <a href="book_package.php?package_id=<?php echo $package_id; ?>&travel_date=<?php echo $d['departure_date']; ?>" class="btn-book">
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

        </div>

        <!-- RIGHT SIDE -->
        <div class="right-side">

            <div class="box">
                <h2 class="section-title">Tour Information</h2>

                <div class="tour-row">
                    <?php if ($data) { ?>
                        <p>💰 Price <span class="highlight-word">RM <?php echo number_format($data['price'] ?? 0, 2); ?></span></p>
                        <p>💵 Deposit per pax <span class="highlight-word">RM <?php echo $data['deposit'] ?? '0'; ?></span></p>
                        <p>👨‍👩‍👧‍👦 Group Departure</p>
                        <p>👥 Min <?php echo $data['min_pax'] ?? '0'; ?> Pax</p>
                        <p>✈️ Flight: <?php echo $data['flight'] ?? 'TBA'; ?></p>
                    <?php } else { ?>
                        <p style="text-align:center;">No package data found</p>
                    <?php } ?>

                    <p>
                        📋 <a href="#" class="view-link" onclick="openPopup(); return false;">
                            Tour Details
                        </a>
                    </p>

                    <p>
                        📄 Download Itinerary:
                        <a href="uploads/<?php echo $data['itinerary_file'] ?? '#'; ?>" class="view-link" download>
                            Click here
                        </a>
                    </p>
                </div>
            </div>

        </div>

    </div>

    <!-- POPUP (UNCHANGED) -->
    <div id="popupBox" class="popup-overlay">
        <div class="popup-content">

            <span class="close-btn" onclick="closePopup()">&times;</span>

            <?php if (!empty($halfboard) || !empty($fullboard)) { ?>
            <div class="include-box">

                <h4>✅ Included</h4>

                <?php if (!empty($halfboard)) { ?>
                <div class="sub-include">
                    <h5>🍽️ Halfboard</h5>
                    <ul>
                        <?php foreach ($halfboard as $item) { ?>
                            <li><?php echo $item; ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>

                <?php if (!empty($fullboard)) { ?>
                <div class="sub-include">
                    <h5>🍴 Fullboard</h5>
                    <ul>
                        <?php foreach ($fullboard as $item) { ?>
                            <li><?php echo $item; ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <?php } ?>

            </div>
            <?php } ?>

            <?php if (!empty($exclude)) { ?>
            <div class="exclude-box">
                <h4>❌ Not Included</h4>
                <ul>
                    <?php foreach ($exclude as $item) { ?>
                        <li><?php echo $item; ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>

        </div>
    </div>

</div>

<script>
function openPopup() {
    document.getElementById("popupBox").classList.add("active");
}

function closePopup() {
    document.getElementById("popupBox").classList.remove("active");
}

// klik luar popup tutup
window.onclick = function(e) {
    let popup = document.getElementById("popupBox");
    if (e.target === popup) {
        popup.classList.remove("active");
    }
}
</script>
</body>
</html>