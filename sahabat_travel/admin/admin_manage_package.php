<?php
require '../db.php';

/* =========================
   DELETE PACKAGE
========================= */
if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    $res = mysqli_query($conn, "SELECT image FROM packages WHERE package_id=$id");
    $data = mysqli_fetch_assoc($res);

    if (!empty($data['image']) && file_exists(__DIR__ . "/../uploads/" . $data['image'])) {
        unlink(__DIR__ . "/../uploads/" . $data['image']);
    }

    mysqli_query($conn, "DELETE FROM package_dates WHERE package_id=$id");
    mysqli_query($conn, "DELETE FROM package_highlights WHERE package_id=$id");
    mysqli_query($conn, "DELETE FROM package_include WHERE package_id=$id");
    mysqli_query($conn, "DELETE FROM package_exclude WHERE package_id=$id");
    mysqli_query($conn, "DELETE FROM packages WHERE package_id=$id");

    header("Location: admin_manage_package.php");
    exit();
}


/* =========================
   ADD PACKAGE (UPDATED)
========================= */
if (isset($_POST['add_package'])) {

    $package_name = mysqli_real_escape_string($conn, $_POST['package_name']);
    $country_id   = $_POST['country_id'] ?? null;
    $category_id  = (int)$_POST['category_id'];
    $duration     = mysqli_real_escape_string($conn, $_POST['duration']);
    $price        = $_POST['price'];
    $deposit      = $_POST['deposit'];
    $flight       = mysqli_real_escape_string($conn, $_POST['flight']);
    $min_pax      = (int)$_POST['min_pax'];
    $status       = $_POST['status'];
    $package_type = $_POST['package_type'];
    $package_category = $_POST['package_category'] ?? 'group';

// kalau bukan MTB → auto group
if ($package_type !== 'MTB') {
    $package_category = 'group';
}

    if ($country_id === "" || $country_id === "0") {
        $country_id = null;
    }

    /* CATEGORY LOGIC */
    if ($category_id == 1 || $category_id == 3) {
        $country_id = null;
    } elseif ($category_id == 2 && empty($country_id)) {
        die("Country wajib untuk International");
    }

    $country_value = ($country_id !== null && $country_id !== "")
        ? "'$country_id'"
        : "NULL";

    /* IMAGE */
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
    }

    /* ITINERARY FILE */
    $itinerary_file = '';
    if (!empty($_FILES['itinerary_file']['name'])) {
        $itinerary_file = time() . "_" . $_FILES['itinerary_file']['name'];
        move_uploaded_file($_FILES['itinerary_file']['tmp_name'], "../uploads/" . $itinerary_file);
    }

    /* INSERT PACKAGE */
    mysqli_query($conn, "
        INSERT INTO packages
        (title, country_id, category_id, duration, price, deposit, flight, min_pax, status, image, itinerary_file, package_type, package_category)
        VALUES 
        ('$package_name', $country_value, '$category_id', '$duration', '$price', '$deposit', '$flight', '$min_pax', '$status', '$image', '$itinerary_file', '$package_type', '$package_category')
    ");

    $new_package_id = mysqli_insert_id($conn);

    /* =========================
       ✅ INSERT TRAVEL DATES (FIX)
    ========================= */
    if (!empty($_POST['travel_dates'])) {
        foreach ($_POST['travel_dates'] as $date) {

            $date = mysqli_real_escape_string($conn, $date);

            mysqli_query($conn, "
                INSERT INTO package_dates (package_id, departure_date)
                VALUES ($new_package_id, '$date')
            ");
        }
    }

    /* HALFBOARD */
    if (!empty($_POST['include_halfboard'])) {
        foreach (explode("\n", $_POST['include_halfboard']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_include (package_id, type, description)
                VALUES ($new_package_id, 'halfboard', '$line')
            ");
        }
    }

    /* FULLBOARD */
    if (!empty($_POST['include_fullboard'])) {
        foreach (explode("\n", $_POST['include_fullboard']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_include (package_id, type, description)
                VALUES ($new_package_id, 'fullboard', '$line')
            ");
        }
    }

    /* EXCLUDE */
    if (!empty($_POST['exclude'])) {
        foreach (explode("\n", $_POST['exclude']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_exclude (package_id, description)
                VALUES ($new_package_id, '$line')
            ");
        }
    }

    /* HIGHLIGHTS */
    if (!empty($_POST['highlights'])) {
        foreach (explode("\n", $_POST['highlights']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_highlights (package_id, highlight_name)
                VALUES ($new_package_id, '$line')
            ");
        }
    }

    header("Location: admin_manage_package.php?success=1");
    exit();
}


/* =========================
   UPDATE PACKAGE (FULL FIXED)
========================= */
if (isset($_POST['update_package'])) {

    $id           = (int)$_POST['package_id'];
    $package_name = mysqli_real_escape_string($conn, $_POST['package_name']);
    $country_id   = $_POST['country_id'] ?? null;
    $category_id  = (int)$_POST['category_id'];
    $duration     = mysqli_real_escape_string($conn, $_POST['duration']);
    $price        = $_POST['price'];
    $deposit      = $_POST['deposit'];
    $flight       = mysqli_real_escape_string($conn, $_POST['flight']);
    $min_pax      = (int)$_POST['min_pax'];
    $status       = $_POST['status'];
    $package_type = $_POST['package_type'];
    $package_category = $_POST['package_category'] ?? 'group';

if ($package_type !== 'MTB') {
    $package_category = 'group';
}

    if ($country_id === "" || $country_id === "0") {
        $country_id = null;
    }

    if ($category_id == 1 || $category_id == 3) {
        $country_id = null;
    } elseif ($category_id == 2 && empty($country_id)) {
        die("Country wajib untuk International");
    }

    $country_value = ($country_id !== null && $country_id !== "")
        ? "'" . $country_id . "'"
        : "NULL";

    /* =========================
       FILE UPLOAD (IMAGE)
    ========================= */
    $image_sql = "";
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
        $image_sql = ", image='$image'";
    }

    /* =========================
       FILE UPLOAD (ITINERARY)
    ========================= */
    $itinerary_sql = "";
    if (!empty($_FILES['itinerary_file']['name'])) {
        $itinerary_file = time() . "_" . $_FILES['itinerary_file']['name'];
        move_uploaded_file($_FILES['itinerary_file']['tmp_name'], "../uploads/" . $itinerary_file);
        $itinerary_sql = ", itinerary_file='$itinerary_file'";
    }

    /* =========================
       UPDATE MAIN PACKAGE
    ========================= */
    mysqli_query($conn, "
        UPDATE packages SET
            title = '$package_name',
            country_id = $country_value,
            category_id = '$category_id',
            duration = '$duration',
            price = '$price',
            deposit = '$deposit',
            flight = '$flight',
            min_pax = '$min_pax',
            status = '$status',
            package_type = '$package_type',
            package_category = '$package_category'
            $image_sql
            $itinerary_sql
        WHERE package_id = $id
    ");

    /* =========================
       RESET INCLUDE / EXCLUDE
    ========================= */
    mysqli_query($conn, "DELETE FROM package_include WHERE package_id=$id");
    mysqli_query($conn, "DELETE FROM package_exclude WHERE package_id=$id");

    /* =========================
       INCLUDE HALFBOARD
    ========================= */
    if (!empty($_POST['include_halfboard'])) {
        foreach (explode("\n", $_POST['include_halfboard']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_include (package_id, type, description)
                VALUES ($id, 'halfboard', '$line')
            ");
        }
    }

    /* =========================
       INCLUDE FULLBOARD
    ========================= */
    if (!empty($_POST['include_fullboard'])) {
        foreach (explode("\n", $_POST['include_fullboard']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_include (package_id, type, description)
                VALUES ($id, 'fullboard', '$line')
            ");
        }
    }

    /* =========================
       EXCLUDE
    ========================= */
    if (!empty($_POST['exclude'])) {
        foreach (explode("\n", $_POST['exclude']) as $line) {
            $line = trim($line);
            if ($line == '') continue;

            $line = mysqli_real_escape_string($conn, $line);

            mysqli_query($conn, "
                INSERT INTO package_exclude (package_id, description)
                VALUES ($id, '$line')
            ");
        }
    }

    /* =========================
       DATES (FULL FIX)
    ========================= */
    if (!empty($_POST['travel_dates'])) {

        mysqli_query($conn, "DELETE FROM package_dates WHERE package_id=$id");

        foreach ($_POST['travel_dates'] as $date) {

            $date = mysqli_real_escape_string($conn, $date);

            mysqli_query($conn, "
                INSERT INTO package_dates (package_id, departure_date)
                VALUES ($id, '$date')
            ");
        }
    }

    /* =========================
   HIGHLIGHTS (FULL STABLE FIX)
========================= */

if (isset($_POST['highlight_name'])) {

    foreach ($_POST['highlight_name'] as $key => $name) {

        $hid  = $_POST['highlight_id'][$key] ?? '';
        $name = mysqli_real_escape_string($conn, $name);

        // default image = existing image
        $image = $_POST['existing_image'][$key] ?? '';

        // kalau user upload image baru
        if (!empty($_FILES['highlight_image']['name'][$key])) {

            $image = time() . "_" . $_FILES['highlight_image']['name'][$key];

            move_uploaded_file(
                $_FILES['highlight_image']['tmp_name'][$key],
                "../uploads/" . $image
            );
        }

        // kalau ada ID → UPDATE
        if (!empty($hid)) {

            mysqli_query($conn, "
                UPDATE package_highlights 
                SET name='$name', image='$image'
                WHERE highlight_id=$hid
            ");

        } else {

            // kalau baru → INSERT
            mysqli_query($conn, "
                INSERT INTO package_highlights (package_id, name, image)
                VALUES ($id, '$name', '$image')
            ");
        }
    }
}

    header("Location: admin_manage_package.php?updated=1");
    exit();
}


/* =========================
   GET DATA
========================= */
$type_filter = $_GET['type_filter'] ?? '';

$sql = "
SELECT 
    packages.*,
    package_categories.category_name,
    countries.country_name
FROM packages
LEFT JOIN package_categories 
    ON packages.category_id = package_categories.category_id
LEFT JOIN countries 
    ON packages.country_id = countries.country_id
";

if ($type_filter != '') {
    $sql .= " WHERE packages.package_type = '$type_filter' ";
}

$sql .= " ORDER BY packages.package_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Package - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="admin_manage_package.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>
<!-- TOGGLE BUTTON -->
<div class="menu-toggle" id="menuToggle">
        <i class="fa-solid fa-bars"></i>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">

    <h2 class="logo">Admin Panel</h2>

    <ul>
        <li><a href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="admin_manage_country.php"><i class="fa-solid fa-earth-asia"></i> Manage Country</a></li>
        <li><a href="admin_manage_package.php"><i class="fa-solid fa-box"></i> Manage Package</a></li>
        <li><a href="admin_manage_review.php"><i class="fa-solid fa-star"></i> Manage Review</a></li>
        <li><a href=""><i class="fa-solid fa-star"></i> Add Admin</a></li>
        <li><a href="" onclick="confirmLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</div>  

    <!-- OVERLAY -->
    <div class="overlay" id="overlay"></div>

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1>Manage Countries</h1>
        <p>Dashboard > Countries</p>
        
    </div>
</div>

<form method="GET" style="margin-bottom: 15px;">
    <label>Filter Type:</label>
    
    <select name="type_filter" class="filter-box" onchange="this.form.submit()">
        <option value="">-- All Types --</option>
        <option value="SIT" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='SIT') echo 'selected'; ?>>SIT</option>
        <option value="MTB" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='MTB') echo 'selected'; ?>>MTB</option>
        <option value="JJ" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='JJ') echo 'selected'; ?>>JJ</option>
        <option value="SUKA" <?php if(isset($_GET['type_filter']) && $_GET['type_filter']=='SUKA') echo 'selected'; ?>>SUKA</option>
    </select>
</form>
<a href="add_package.php" class="btn-add">+ Add Package</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Country</th>
            <th>Package Name</th>
            <th>Agency</th>
            <th>Package Category</th>
            <th>Duration</th>
            <th>Action</th>
        </tr>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['package_id']; ?></td>
			
			<td><?php echo htmlspecialchars($row['category_name']); ?></td>
            
            <td><?php echo htmlspecialchars($row['country_name']); ?></td>
            
            <td><?php echo htmlspecialchars($row['package_type']); ?></td>

            <td>
                <?php 
                if ($row['package_type'] == 'MTB') {
                    echo ucfirst($row['package_category']);
                } else {
                    echo '-';
                }
                ?>
            </td>

            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>

            <td><?php echo htmlspecialchars($row['duration']); ?></td>

            <td>RM <?php echo number_format($row['price'], 2); ?></td>
            
            <td><?php echo htmlspecialchars($row['flight']); ?></td>
            
            <td><?php echo $row['min_pax']; ?></td>

            <td>
                <div style="display: flex; gap: 5px;">
                    <a href="admin_view_package.php?id=<?php echo $row['package_id']; ?>">View</a> |
                    <a href="edit_package.php?id=<?php echo $row['package_id']; ?>">Edit</a> |
                    <a href="admin_manage_package.php?delete=<?php echo $row['package_id']; ?>" 
                       onclick="return confirm('Are you sure want to delete this package?')">Delete</a>
                </div>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <script>
const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const closeBtn = document.getElementById("closeBtn");
const overlay = document.getElementById("overlay");

/* OPEN SIDEBAR */
menuToggle.addEventListener("click", () => {
    sidebar.classList.add("active");
    overlay.classList.add("active");
});

/* CLOSE SIDEBAR */
closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

/* CLOSE WHEN CLICK OVERLAY */
overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "login.php";
    }
}
</script>
</body>
</html>