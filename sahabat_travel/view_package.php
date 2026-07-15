<?php
require 'db.php';

/* =========================================
   GET PACKAGE ID
========================================= */

$package_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$data = null;
$highlights = null;
$date_query = null;

$halfboard = [];
$fullboard = [];
$exclude = [];

/* =========================================
   FETCH PACKAGE
========================================= */

if ($package_id > 0) {

    // PACKAGE
    $package = mysqli_query($conn, "
        SELECT 
            p.*,
            MAX(pp.price) AS starting_price
        FROM packages p
        LEFT JOIN package_pricing pp 
        ON p.package_id = pp.package_id
        WHERE p.package_id = '$package_id'
        GROUP BY p.package_id
    ");

    if (!$package) {
        die("Package Query Error: " . mysqli_error($conn));
    }

    $data = mysqli_fetch_assoc($package);

    if (!$data) {
        die("Package not found.");
    }

    /* =========================================
       PACKAGE TYPE
    ========================================= */

    $type = strtolower($data['agency_id'] ?? 'sit');

    /* =========================================
       HIGHLIGHTS
    ========================================= */

    $highlights = mysqli_query($conn, "
        SELECT *
        FROM package_highlights
        WHERE package_id = '$package_id'
    ");

    if (!$highlights) {
        die("Highlight Query Error: " . mysqli_error($conn));
    }

    /* =========================================
       TRAVEL DATES
    ========================================= */

    $date_query = mysqli_query($conn, "
        SELECT travel_date
        FROM package_dates
        WHERE package_id = '$package_id'
        ORDER BY travel_date ASC
    ");

    if (!$date_query) {
        die("Date Query Error: " . mysqli_error($conn));
    }

    /* =========================================
       INCLUDED
    ========================================= */

    $include_query = mysqli_query($conn, "
        SELECT include_type, description
        FROM package_include
        WHERE package_id = '$package_id'
    ");

    if (!$include_query) {
        die("Include Query Error: " . mysqli_error($conn));
    }

    while ($i = mysqli_fetch_assoc($include_query)) {

        if ($i['include_type'] == 'Halfboard') {
            $halfboard[] = $i['description'];
        }

        if ($i['include_type'] == 'Fullboard') {
            $fullboard[] = $i['description'];
        }
    }

    /* =========================================
       EXCLUDED
    ========================================= */

    $exclude_query = mysqli_query($conn, "
        SELECT description
        FROM package_exclude
        WHERE package_id = '$package_id'
    ");

    if (!$exclude_query) {
        die("Exclude Query Error: " . mysqli_error($conn));
    }

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
    <title><?php echo ucwords(strtolower($data['title'] . ' ' . $data['duration_days'])); ?></title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="view_package.css">
</head>
<body>

<section class="hero">
    <div class="hero-overlay"></div>
    <a href="javascript:history.back()" class="back-btn">← Back</a>
    <?php if($data) { ?>
        <img src="uploads/<?php echo $data['main_image']; ?>" class="hero-img">
    <?php } ?>
</section>

<div class="page-container">

    <!-- TITLE -->
    <?php if($data) { ?>
        <h1 class="package-title">
            <?php echo $data['title']; ?> <?php echo $data['duration_days']; ?>
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
                                <img src="uploads/<?php echo $row['highlight_image']; ?>">
                                <div class="highlight-name">
                                    <?php echo $row['highlight_name']; ?>
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
                            <th class="th-<?php echo $type; ?>">Date</th>
                            <th class="th-<?php echo $type; ?>">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if ($date_query && mysqli_num_rows($date_query) > 0) {
                            while ($d = mysqli_fetch_assoc($date_query)) {
                                $date = date('d M Y', strtotime($d['travel_date']));
                        ?>
                                <tr>
                                    <td><?php echo $date; ?></td>
                                    <td>
                                        <a href="book_package.php?package_id=<?php echo $package_id; ?>&travel_date=<?php echo $d['travel_date']; ?>" 
                                        class="btn-book btn-<?php echo $type; ?>">
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

                    <p>💰 Starting From <span class="highlight-word">
                        RM <?php echo number_format($data['starting_price'] ?? 0, 2); ?>
                    </span></p>

                    <p>💵 Deposit per pax <span class="highlight-word">
                        RM <?php echo number_format($data['deposit'] ?? 0, 2); ?>
                    </span></p>

                    <?php if (($data['package_type'] ?? '') == 'MTB') { ?>

                        <?php if (($data['package_category'] ?? '') == 'group') { ?>
                            <p>👨‍👩‍👧‍👦 Group Package</p>

                        <?php } elseif (($data['package_category'] ?? '') == 'private') { ?>
                            <p>🚶 Private Package</p>

                        <?php } elseif (($data['package_category'] ?? '') == 'honeymoon') { ?>
                            <p>🧑‍🤝‍🧑 Honeymoon Package</p>
                        <?php } ?>

                    <?php } ?>

                    <p>👥 Min <?php echo $data['min_pax'] ?? '0'; ?> Pax</p>

                    <p>✈️ Flight:
                        <?php echo !empty($data['flight_details']) ? htmlspecialchars($data['flight_details']) : 'TBA'; ?>
                    </p>

                    <p>
                        📋
                        <a href="#" class="view-link" onclick="openPopup(); return false;">
                            Tour Details
                        </a>
                    </p>

                    <?php
                    $file = $data['itinerary_file'] ?? '';
                    $filePath = 'uploads/' . $file;
                    ?>

                    <p>
                        📄 Download Itinerary:

                        <?php if (!empty($file) && file_exists($filePath)) { ?>
                            <a href="<?php echo htmlspecialchars($filePath); ?>" class="view-link" download>
                                Click here
                            </a>
                        <?php } else { ?>
                            <span class="unavailable-link">Unavailable</span>
                        <?php } ?>
                    </p>

                <?php } ?>
            </div>

            <!-- WhatsApp Box -->
            <div class="box whatsapp-box">

                <h2 class="section-title">Need Assistance?</h2>

                <p class="wa-text">
                    Have any questions regarding this tour?<br>
                    Scan the QR code or click the WhatsApp button below to chat with us.
                </p>

                <img src="picture/QR ws.png" alt="WhatsApp QR Code" class="wa-qr">
        
                    <a href="https://wa.me/60148803100" target="_blank" class="wa-btn">
                        💬 Chat via WhatsApp
                    </a>
            </div>
        </div>

    </div>

    <!-- POPUP -->
    <div id="popupBox" class="popup-overlay">
        <div class="popup-content">

            <span class="close-btn" onclick="closePopup()">&times;</span>

            <!-- Included -->
            <div class="include-box">
                <h4>✅ Included</h4>

                <!-- Halfboard -->
                <div class="sub-include">
                    <h5>🍽️ Halfboard</h5>

                    <?php if (!empty($halfboard)) { ?>
                        <ul>
                            <?php foreach ($halfboard as $item) { ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p class="no-data">No halfboard details available.</p>
                    <?php } ?>
                </div>

                <!-- Fullboard -->
                <div class="sub-include">
                    <h5>🍴 Fullboard</h5>

                    <?php if (!empty($fullboard)) { ?>
                        <ul>
                            <?php foreach ($fullboard as $item) { ?>
                                <li><?php echo htmlspecialchars($item); ?></li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p class="no-data">No fullboard details available.</p>
                    <?php } ?>
                </div>
            </div>

            <!-- Excluded -->
            <div class="exclude-box">
                <h4>❌ Not Included</h4>

                <?php if (!empty($exclude)) { ?>
                    <ul>
                        <?php foreach ($exclude as $item) { ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="no-data">No excluded details available.</p>
                <?php } ?>
            </div>

        </div>
    </div>

</div>

<!-- POPUP JS -->
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