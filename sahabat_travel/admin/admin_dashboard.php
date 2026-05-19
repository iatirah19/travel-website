<?php
require '../db.php';

/* =======================
   TOTAL USERS
======================= */
$userQuery = "SELECT COUNT(*) AS total_users FROM users";
$userResult = mysqli_query($conn, $userQuery);
$totalUsers = mysqli_fetch_assoc($userResult)['total_users'];

/* =======================
   TOTAL COUNTRIES
======================= */
$countryQuery = "SELECT COUNT(*) AS total_countries FROM countries";
$countryResult = mysqli_query($conn, $countryQuery);
$totalCountries = mysqli_fetch_assoc($countryResult)['total_countries'];

/* =======================
   TOTAL PACKAGES
======================= */
$packageQuery = "SELECT COUNT(*) AS total_packages FROM packages";
$packageResult = mysqli_query($conn, $packageQuery);
$totalPackages = mysqli_fetch_assoc($packageResult)['total_packages'];

/* =======================
   BOOKINGS DATA
======================= */
$bookingQuery = "
    SELECT 
        bookings.*,
        users.username,
        packages.title
    FROM bookings
    JOIN users ON bookings.user_id = users.user_id
    JOIN packages ON bookings.package_id = packages.package_id
    ORDER BY bookings.booking_id DESC
";

$bookingResult = mysqli_query($conn, $bookingQuery);

/* =======================
   DELETE MESSAGE (INLINE)
======================= */
if (isset($_GET['delete_message_id'])) {

    $id = intval($_GET['delete_message_id']);

    $deleteQuery = "DELETE FROM contact_messages WHERE contact_id = $id";
    mysqli_query($conn, $deleteQuery);

    header("Location: admin_dashboard.php");
    exit();
}


if(isset($_GET['toggle_status_id'])){

    $id = $_GET['toggle_status_id'];

    // ambil status current
    $getStatus = mysqli_query($conn, "SELECT status FROM contact_messages WHERE contact_id='$id'");
    $data = mysqli_fetch_assoc($getStatus);

    $newStatus = ($data['status'] == 'done') ? 'undone' : 'done';

    mysqli_query($conn, "
        UPDATE contact_messages 
        SET status='$newStatus' 
        WHERE contact_id='$id'
    ");

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<!-- TOGGLE -->
<div class="menu-toggle" id="menuToggle">
    <i class="fa-solid fa-bars"></i>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

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
        <li><a href="" onclick="confirmLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</div>

<div class="overlay" id="overlay"></div>

<!-- HEADER -->
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Dashboard Overview</p>
    </div>
</div>

<!-- CARDS -->
<div class="dashboard-cards">

    <div class="card">
        <h2>Total Users</h2>
        <h4><?php echo $totalUsers; ?></h4>
    </div>

    <div class="card">
        <h2>Total Countries</h2>
        <h4><?php echo $totalCountries; ?></h4>
    </div>

    <div class="card">
        <h2>Total Packages</h2>
        <h4><?php echo $totalPackages; ?></h4>
    </div>

</div>

<!-- BOOKINGS TABLE -->
<div class="table-container">

<table>
    <thead>
        <tr>
            <th>Bil</th>
            <th>User</th>
            <th>Package</th>
            <th>Travel Date</th>
            <th>Total Pax</th>
            <th>Payment Method</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
    <?php if(mysqli_num_rows($bookingResult) > 0){ ?>

        <?php $bil = 1; ?>
        <?php while($row = mysqli_fetch_assoc($bookingResult)){ ?>

        <tr>
            <td><?php echo $bil++; ?></td>

            <td><?php echo htmlspecialchars($row['username']); ?></td>

            <td><?php echo htmlspecialchars($row['package_name']); ?></td>

            <td><?php echo date('d M Y', strtotime($row['travel_date'])); ?></td>

            <td><?php echo $row['total_pax']; ?></td>

            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>

            <td>
                <?php
                if($row['payment_status'] == 'Paid'){
                    echo "<span class='status paid'>Paid</span>";
                } else {
                    echo "<span class='status pending'>Pending</span>";
                }
                ?>
            </td>
        </tr>

        <?php } ?>

    <?php } else { ?>
        <tr>
            <td colspan="7" style="text-align:center;">No bookings found</td>
        </tr>
    <?php } ?>
    </tbody>

</table>

</div>

<!-- CONTACT TABLE -->
<div class="table-container" style="margin-top:30px;">

<h3 style="margin-bottom:15px;">User Contacts</h3>

<table>

    <thead>
        <tr>
            <th>Bil</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Date Sent</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>

    <?php
    $contactQuery = "
        SELECT * FROM contact_messages
        ORDER BY created_at DESC
    ";

    $contactResult = mysqli_query($conn, $contactQuery);
    ?>

    <?php if(mysqli_num_rows($contactResult) > 0){ ?>

        <?php $bil = 1; ?>

        <?php while($row = mysqli_fetch_assoc($contactResult)){ ?>

        <tr>
            <td><?php echo $bil++; ?></td>

            <td><?php echo htmlspecialchars($row['name']); ?></td>

            <td>
                <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>">
                    <?php echo htmlspecialchars($row['email']); ?>
                </a>
            </td>

            <td>
                <?php echo substr(htmlspecialchars($row['phone_number']), 0, 80); ?>
            </td>

            <td>
                <?php echo date('d M Y', strtotime($row['created_at'])); ?>
            </td>

            <td>
    <?php 
        $status = $row['status'];

        if($status == 'done'){
            echo "<span style='color:white;background:green;padding:3px 8px;border-radius:5px;'>Done</span>";
        } else {
            echo "<span style='color:white;background:orange;padding:3px 8px;border-radius:5px;'>Undone</span>";
        }
    ?>
</td>

            <td>

    <!-- DELETE -->
    <a href="admin_dashboard.php?delete_message_id=<?php echo $row['contact_id']; ?>"
       onclick="return confirm('Are you sure want to delete this message?')">
        <i class="fa-solid fa-trash"></i>
    </a>

    <!-- TOGGLE STATUS -->
    <a href="admin_dashboard.php?toggle_status_id=<?php echo $row['contact_id']; ?>"
       onclick="return confirm('Change status?')"
       style="margin-left:10px;">
        <i class="fa-solid fa-check"></i>
    </a>

</td>
        </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="6" style="text-align:center;">No messages found</td>
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

menuToggle.addEventListener("click", () => {
    sidebar.classList.add("active");
    overlay.classList.add("active");
});

closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "auth.php";
    }
}
</script>

</body>
</html>