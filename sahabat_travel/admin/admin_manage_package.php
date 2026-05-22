<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div class="close-btn" id="closeBtn">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <h2 class="logo">Admin Panel</h2>

    <ul>
        <li><a href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="admin_manage_country.php"><i class="fa-solid fa-earth-asia"></i> Manage Country</a></li>
        <li><a href="admin_manage_package.php"><i class="fa-solid fa-box"></i> Manage Package</a></li>
        <li><a href="admin_manage_review.php"><i class="fa-solid fa-star"></i> Manage Review</a></li>
        <li><a href=""><i class="fa-solid fa-star"></i> Add Admin</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1>Manage Package</h1>
        <p>Dashboard > Package</p>
    </div>
</div>

<!-- TOP BAR -->
<div class="top-bar">

    <!-- FILTER -->
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
                <th>Country</th>
                <th>Package Name</th>
                <th>Agency</th>
                <th>Package Category</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            <?php
            if(mysqli_num_rows($result) > 0){

                while($row = mysqli_fetch_assoc($result)){

                    $package_id       = $row['package_id'];
                    $country_name     = $row['country_name'];
                    $package_name     = $row['package_name'];
                    $agency_name      = $row['agency_name'];
                    $category_name    = $row['category_name'];
            ?>

            <tr>

                <!-- ID -->
                <td>
                    <?php echo $package_id; ?>
                </td>

                <!-- COUNTRY -->
                <td>
                    <?php echo htmlspecialchars($country_name); ?>
                </td>

                <!-- PACKAGE NAME -->
                <td>
                    <?php echo htmlspecialchars($package_name); ?>
                </td>

                <!-- AGENCY -->
                <td>
                    <?php echo htmlspecialchars($agency_name); ?>
                </td>

                <!-- PACKAGE CATEGORY -->
                <td>
                    <?php echo htmlspecialchars($category_name); ?>
                </td>

                <!-- ACTION -->
                <td class="action-btns">

                    <!-- EDIT -->
                    <a href="edit_package.php?id=<?php echo $package_id; ?>">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    <!-- DELETE -->
                    <a href="admin_manage_package.php?delete=<?php echo $package_id; ?>"
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
                <td colspan="6" style="text-align:center;">
                    No packages found
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