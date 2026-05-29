<?php

require '../db.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: homepage.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| DELETE PACKAGE
|--------------------------------------------------------------------------
*/

if(isset($_GET['delete'])){

    $delete_id = intval($_GET['delete']);

    /* DELETE PACKAGE DATES */
    mysqli_query($conn, "
        DELETE FROM package_dates
        WHERE package_id = '$delete_id'
    ");

    /* DELETE PACKAGE HIGHLIGHTS */
    mysqli_query($conn, "
        DELETE FROM package_highlights
        WHERE package_id = '$delete_id'
    ");

    /* DELETE PACKAGE INCLUDES */
    mysqli_query($conn, "
        DELETE FROM package_include
        WHERE package_id = '$delete_id'
    ");

    /* DELETE PACKAGE */
    $deleteQuery = mysqli_query($conn, "
        DELETE FROM packages
        WHERE package_id = '$delete_id'
    ");

    if($deleteQuery){

        echo "
        <script>
            alert('Package deleted successfully');
            window.location.href='admin_manage_package.php';
        </script>
        ";

    }else{

        echo "
        <script>
            alert('Failed to delete package');
        </script>
        ";
    }
}

/*
|--------------------------------------------------------------------------
| FETCH FILTER AGENCIES
|--------------------------------------------------------------------------
*/

$filterAgencyQuery = mysqli_query($conn, "
    SELECT *
    FROM agencies
    ORDER BY agency_name ASC
");

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$where = "";

if(isset($_GET['type_filter']) && $_GET['type_filter'] != ""){

    $type_filter = intval($_GET['type_filter']);

    $where = "WHERE packages.agency_id = '$type_filter'";
}

/*
|--------------------------------------------------------------------------
| GET PACKAGE DATA
|--------------------------------------------------------------------------
*/

$query = "
SELECT 
    packages.*,
    package_categories.category_name,
    countries.country_name,
    agencies.agency_name,
    tour_categories.tour_category_name

FROM packages

LEFT JOIN countries
ON packages.country_id = countries.country_id

LEFT JOIN agencies
ON packages.agency_id = agencies.agency_id

LEFT JOIN tour_categories
ON packages.tour_category_id = tour_categories.tour_category_id

LEFT JOIN package_categories
ON packages.package_category_id = package_categories.package_category_id

$where

ORDER BY packages.package_id DESC
";

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Package - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
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
        <!-- CLOSE BUTTON -->
        <div class="close-btn" id="closeBtn"><i class="fa-solid fa-xmark"></i></div>
        <h2 class="logo">Admin Panel</h2>
        <ul>
            <li>
                <a href="admin_dashboard.php">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="admin_manage_country.php">
                    <i class="fa-solid fa-earth-asia"></i>
                    Manage Country
                </a>
            </li>

            <li>
                <a href="admin_manage_package.php">
                    <i class="fa-solid fa-box"></i>
                    Manage Package
                </a>
            </li>

            <li>
                <a href="admin_manage_review.php">
                    <i class="fa-solid fa-star"></i>
                    Manage Review
                </a>
            </li>

            <li>
                <a href="auth.php">
                    <i class="fa-solid fa-user-plus"></i>
                    Add Admin
                </a>
            </li>

            <li>
                <a href="#" onclick="confirmLogout(event)">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- OVERLAY -->
    <div class="overlay" id="overlay"></div>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>
                Manage Package
            </h1>
            <p>
                Dashboard > Package
            </p>
        </div>
    </div>

    <!-- TOP BAR -->
    <div class="top-bar">

        <!-- FILTER -->
        <form method="GET" class="filter-form">

            <label>
                Filter Agency:
            </label>

            <select name="type_filter"
                    class="filter-box"
                    onchange="this.form.submit()">

                <option value="">
                    -- All Agencies --
                </option>

                <?php while($filterAgency = mysqli_fetch_assoc($filterAgencyQuery)) { ?>

                    <option
                        value="<?= $filterAgency['agency_id']; ?>"

                        <?php
                        if(
                            isset($_GET['type_filter']) &&
                            $_GET['type_filter'] == $filterAgency['agency_id']
                        ){
                            echo 'selected';
                        }
                        ?>
                    >

                        <?= $filterAgency['agency_name']; ?>

                    </option>

                <?php } ?>

            </select>

        </form>

        <!-- ADD BUTTON -->
        <a href="add_package.php" class="add-btn">

            + Add Package

        </a>

    </div>

    <!-- TABLE -->
    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Package Name</th>

                    <th>Tour Category</th>

                    <th>Country</th>

                    <th>Agency</th>

                    <th>Package Category</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

            <?php

            if(mysqli_num_rows($result) > 0){

                while($row = mysqli_fetch_assoc($result)){

                    $package_id       = $row['package_id'];

                    $title            = $row['title'];

                    $tour_category    = $row['tour_category_name'];

                    $country_name     = $row['country_name'];

                    $agency           = $row['agency_name'];

                    $package_category = $row['category_name'];

                    $status           = $row['status'];

            ?>

                <tr>

                    <!-- ID -->
                    <td>

                        <?php echo $package_id; ?>

                    </td>

                    <!-- PACKAGE NAME -->
                    <td>

                        <?php echo htmlspecialchars($title); ?>

                    </td>

                    <!-- TOUR CATEGORY -->
                    <td>

                        <?php echo htmlspecialchars($tour_category); ?>

                    </td>

                    <!-- COUNTRY -->
                    <td>

                        <?php

                        if($tour_category == "International"){

                            echo htmlspecialchars($country_name);

                        }else{

                            echo "-";
                        }

                        ?>

                    </td>

                    <!-- AGENCY -->
                    <td>

                        <?php echo htmlspecialchars($agency); ?>

                    </td>

                    <!-- PACKAGE CATEGORY -->
                    <td>

                        <?php echo htmlspecialchars($package_category); ?>

                    </td>

                    <!-- STATUS -->
                    <td>

                        <?php if($status == "active"){ ?>

                            <span class="status popular">Active</span>

                        <?php } else { ?>

                            <span class="status not-popular">Inactive</span>

                        <?php } ?>

                    </td>

                    <!-- ACTION -->
                    <td class="action-btns">

                        <!-- EDIT -->
                        <a href="edit_package.php?id=<?php echo $package_id; ?>"
                           class="edit-btn">

                            <i class="fa-solid fa-pen"></i>

                        </a>

                        <!-- DELETE -->
                        <a href="admin_manage_package.php?delete=<?php echo $package_id; ?>"
                           class="delete-btn"
                           onclick="return confirm('Are you sure want to delete this package?')">

                            <i class="fa-solid fa-trash"></i>

                        </a>

                    </td>

                </tr>

            <?php

                }

            }else{

            ?>

                <tr>

                    <td colspan="8"
                        style="text-align:center; padding:20px;">

                        No packages found

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

    <!-- SCRIPT -->
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
        
        /* CLOSE OVERLAY */
        overlay.addEventListener("click", () => {
        
            sidebar.classList.remove("active");
        
            overlay.classList.remove("active");
        
        });
        
        /* LOGOUT */
        function confirmLogout(event) {
        
            event.preventDefault();
        
            if(confirm("Are you sure you want to logout?")){
        
                window.location.href = "admin_dashboard.php?logout=1";
        
            }
        
        }
        
    </script>

</body>
</html>