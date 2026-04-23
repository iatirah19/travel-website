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
        (title, country_id, category_id, duration, price, deposit, flight, min_pax, status, image, itinerary_file)
        VALUES
        ('$package_name', $country_value, '$category_id', '$duration', '$price', '$deposit', '$flight', '$min_pax', '$status', '$image', '$itinerary_file')
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
            status = '$status'
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
$result = mysqli_query($conn, "
SELECT 
    packages.*,
    categories.category_name,
    countries.country_name
FROM packages
LEFT JOIN categories 
    ON packages.category_id = categories.category_id
LEFT JOIN countries 
    ON packages.country_id = countries.country_id
ORDER BY packages.package_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Package - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="admin_manage_package.css">
</head>

<body>

<div class="page-header">

    <a href="admin_dashboard.php" class="btn-back">← Back</a>

    <h2>Manage Package</h2>

    <a href="add_package.php" class="btn-add">+ Add Package</a>

</div>

    <table>
        <tr>
            <th>ID</th>
			<th>Category</th>
            <th>Country</th>
            <th>Image</th>
            <th>Package Name</th>
            <th>Duration</th>
            <th>Price (RM)</th>
            <th>Deposit</th>
            <th>Flight</th>
            <th>Min Pax</th>
            <th>Action</th>
        </tr>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['package_id']; ?></td>
			
			<td><?php echo htmlspecialchars($row['category_name']); ?></td>
            
            <td><?php echo htmlspecialchars($row['country_name']); ?></td>

            <td>
                <?php if($row['image']): ?>
                    <img src="../uploads/<?php echo $row['image']; ?>" class="img-preview" alt="Package Image">
                <?php else: ?>
                    <small>No Image</small>
                <?php endif; ?>
            </td>

            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>

            <td><?php echo htmlspecialchars($row['duration']); ?></td>

            <td>RM <?php echo number_format($row['price'], 2); ?></td>

            <td>RM <?php echo number_format($row['deposit'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['flight']); ?></td>
            <td><?php echo $row['min_pax']; ?></td>

            <td>
                <div style="display: flex; gap: 5px;">
                    <a href="admin_view_package.php?id=<?php echo $row['package_id']; ?>">View</a> |
                    <a href="edit_package.php?id=<?php echo $row['package_id']; ?>">Edit</a> |
                    <a href="admin_manage_package.php?delete=<?php echo $row['package_id']; ?>" 
                       onclick="return confirm('Delete this package?')">Delete</a>
                </div>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

</body>
</html>