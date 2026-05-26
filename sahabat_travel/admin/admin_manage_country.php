<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Country - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="admin_manage_country.css">
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
    <div class="close-btn" id="closeBtn">

        <i class="fa-solid fa-xmark"></i>

    </div>

    <h2 class="logo">
        Admin Panel
    </h2>

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
            <a href="#">

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
        <h1>Manage Countries</h1>
        <p>Dashboard > Countries</p>
    </div>
</div>

<!-- TOP BAR -->
<div class="top-bar">

    <!-- SEARCH -->
    <form method="GET" class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="search" placeholder="Search countries..."
               value="<?php echo $_GET['search'] ?? ''; ?>">
    </form>

    <!-- ADD BUTTON -->
    <a href="add_country.php" class="add-btn">
        + Add Country
    </a>

</div>

<?php
require '../db.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: homepage.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit();
}

/* GET ALL COUNTRIES */
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $sql = "SELECT * FROM countries 
            WHERE country_name LIKE '%$search%' 
            ORDER BY country_id DESC";
} else {
    $sql = "SELECT * FROM countries 
            ORDER BY country_id DESC";
}
$result = mysqli_query($conn, $sql);
?>

<!-- TABLE -->
<div class="table-container">

    <table>

        <thead>
            <tr>
                <th>Image</th>
                <th>Country Name</th>
                <th>Packages</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            if(mysqli_num_rows($result) > 0){

                while($row = mysqli_fetch_assoc($result)){

                    $country_id = $row['country_id'];
                    $country_name = $row['country_name'];
                    $country_image = $row['country_image'];

                    /* COUNT PACKAGES */
                    $package_sql = "SELECT c.country_id, c.country_name, c.country_image,
                                    COUNT(p.package_id) AS total_packages
                                    FROM countries c
                                    LEFT JOIN packages p ON c.country_id = p.country_id
                                    GROUP BY c.country_id
                                    ORDER BY c.country_id DESC";

                    $package_result = mysqli_query($conn, $package_sql);
                    $package_data = mysqli_fetch_assoc($package_result);

                    $total_packages = $package_data['total_packages'];
            ?>

            <tr>

                <!-- IMAGE -->
                <td>
                    <img src="../uploads/<?php echo $country_image; ?>" class="country-img">
                </td>

                <!-- COUNTRY NAME -->
                <td>
                    <?php echo htmlspecialchars($country_name); ?>
                </td>

                <!-- TOTAL PACKAGES -->
                <td>
                    <?php echo $total_packages; ?>
                </td>

                <!-- ACTION -->
                <td class="action-btns">

                    <!-- EDIT -->
                    <a href="edit_country.php?id=<?php echo $country_id; ?>">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    <!-- DELETE -->
                    <a href="delete_country.php?id=<?php echo $country_id; ?>" 
                       onclick="return confirm('Are you sure want to delete this country?')">

                        <i class="fa-solid fa-trash"></i>
                    </a>

                </td>

            </tr>

            <?php
                }
            }else{
            ?>

            <tr>
                <td colspan="4" style="text-align:center;">
                    No countries found
                </td>
            </tr>

            <?php } ?>

        </tbody>

    </table>

</div>


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

function confirmLogout(event) {
    event.preventDefault(); // stop link behavior

    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "admin_dashboard.php?logout=1";
    }
}
</script>
</body>
</html>