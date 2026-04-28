<?php
require '../db.php';

// TOTAL USER
$userQuery = "SELECT COUNT(*) AS total_users FROM users";
$userResult = mysqli_query($conn, $userQuery);
$totalUsers = mysqli_fetch_assoc($userResult)['total_users'];

// TOTAL COUNTRIES
$countryQuery = "SELECT COUNT(*) AS total_countries FROM countries";
$countryResult = mysqli_query($conn, $countryQuery);
$totalCountries = mysqli_fetch_assoc($countryResult)['total_countries'];

// TOTAL PACKAGES
$packageQuery = "SELECT COUNT(*) AS total_packages FROM packages";
$packageResult = mysqli_query($conn, $packageQuery);
$totalPackages = mysqli_fetch_assoc($packageResult)['total_packages'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>

<body>

<div class="topbar">
    <button id="toggle-btn">☰</button>
    <h1>Admin Dashboard</h1>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>🌍 Admin Panel</h2>
        <p>Sahabat International Travel Sdn Bhd</p>
    </div>

    <div class="sidebar-menu">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="admin_manage_country.php">Manage Countries</a>
        <a href="admin_manage_package.php">Manage Packages</a>
		<a href="admin_manage_review.php">Manage Reviews</a>
		<a href="register.php">Add New Admin</a>
    </div>

    <div class="sidebar-footer">
		<a href="#" class="logout" onclick="confirmLogout()">🚪 Logout</a>
	</div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">

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

<h3>User Bookings</h3>

<table>
    <tr>
        <th>Bil</th>
        <th>Customer</th>
        <th>Package</th>
        <th>Travel Date</th>
        <th>Total Pax</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php
    $bil = 1;

    $query = "
    SELECT b.*, p.title 
    FROM bookings b
    JOIN packages p ON b.package_id = p.package_id
    ORDER BY b.created_at DESC
    ";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
    ?>
    <tr>
        <td><?php echo $bil++; ?></td>

        <!-- MAIN CUSTOMER ONLY -->
        <td>
            <?php echo htmlspecialchars($row['customer_name']); ?><br>
            <small><?php echo htmlspecialchars($row['phone']); ?></small>
        </td>

        <td><?php echo htmlspecialchars($row['title']); ?></td>

        <td><?php echo date("d M Y", strtotime($row['travel_date'])); ?></td>

        <td><?php echo $row['pax']; ?></td>

        <td class="status <?php echo strtolower($row['status']); ?>">
            <?php echo ucfirst($row['status']); ?>
        </td>

        <td>
            <a href="view_booking.php?id=<?php echo $row['booking_id']; ?>">View</a> | 
            <a href="delete_booking.php?id=<?php echo $row['booking_id']; ?>" 
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php 
        }
    } else {
        echo "<tr><td colspan='7'>No bookings found</td></tr>";
    }
    ?>
</table>

<h3>User Contacts</h3>

<table>
    <tr>
        <th>Bil</th>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Date Sent</th>
        <th>Action</th>
    </tr>

    <?php
    $bil = 1;

    $query = "
        SELECT * 
        FROM contacts
        ORDER BY created_at DESC
    ";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <tr>
        <td><?php echo $bil++; ?></td>

        <td><?php echo htmlspecialchars($row['name']); ?></td>

        <td>
            <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>">
                <?php echo htmlspecialchars($row['email']); ?>
            </a>
        </td>

        <td>
			<?php echo substr(htmlspecialchars($row['message']), 0, 80) . '...'; ?>
		</td>

        <td>
            <?php echo date("d M Y", strtotime($row['created_at'])); ?>
        </td>

        <td>
            <a href="view_message.php?id=<?php echo $row['contact_id']; ?>">View</a> | 
            <a href="delete_contact.php?id=<?php echo $row['contact_id']; ?>" 
               onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php 
        }
    } else {
        echo "<tr><td colspan='6'>No messages found</td></tr>";
    }
    ?>
</table>
</div>

<!-- TOGGLE BUTTON -->
<script>
const toggleBtn = document.getElementById("toggle-btn");
const sidebar = document.getElementById("sidebar");
const mainContent = document.getElementById("mainContent");

toggleBtn.onclick = function(){
    sidebar.classList.toggle("active");
    mainContent.classList.toggle("shift");
    toggleBtn.classList.toggle("move");

    toggleBtn.innerHTML = sidebar.classList.contains("active") ? "✖" : "☰";
};
</script>

<!-- LOGOUT -->
<script>
function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "login.php";
    }
}
</script>
</body>
</html>