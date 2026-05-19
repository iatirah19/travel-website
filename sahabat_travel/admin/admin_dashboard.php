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
        <li><a href="admin_manage_packages.php"><i class="fa-solid fa-box"></i> Manage Package</a></li>
        <li><a href="admin_manage_review.php"><i class="fa-solid fa-star"></i> Manage Review</a></li>
        <li><a href="" onclick="confirmLogout()"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</div>  

    <!-- OVERLAY -->
    <div class="overlay" id="overlay"></div>

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Dashboard</p>
        </div>
    </div>

    <!-- DASHBOARD CARDS -->
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

    <!-- USER BOOKINGS TABLE -->
    <div class="table-container">
        <h3 style="margin-bottom: 15px;">User Bookings</h3>
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
            SELECT b.*, p.packname
            FROM bookings b
            JOIN packages p ON b.package_id = p.package_id
            
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

                <td><?php echo htmlspecialchars($row['packname']); ?></td>

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
    </div>

    <!-- USER CONTACTS TABLE -->
    <div class="table-container" style="margin-top: 30px;">
        <h3 style="margin-bottom: 15px;">User Contacts</h3>
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
                FROM contact_messages
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

    <!-- TOGGLE BUTTON SCRIPT -->
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
            window.location.href = "auth.php";
        }
    }
    </script>
    </body>
    </html>