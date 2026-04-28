<?php
require 'db.php';

$booking_id = $_GET['id'] ?? 0;
$booking_id = mysqli_real_escape_string($conn, $booking_id);

// =======================
// GET BOOKING INFO
// =======================
$query = "
SELECT b.*, p.title 
FROM bookings b
JOIN packages p ON b.package_id = p.package_id
WHERE b.booking_id = '$booking_id'
";

$result = mysqli_query($conn, $query);
$booking = mysqli_fetch_assoc($result);

if (!$booking) {
    die("Booking not found.");
}

// =======================
// GET PAX LIST
// =======================
$pax_query = "
SELECT * FROM bookings_pax 
WHERE booking_id = '$booking_id'
";

$pax_result = mysqli_query($conn, $pax_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="view_booking.css">
</head>

<body>

<div class="container">

<a href="javascript:history.back()" class="back">← Back</a>

<h2>Booking Details</h2>

<div class="info">
    <p><b>Customer:</b> <?php echo $booking['customer_name']; ?></p>
    <p><b>Phone:</b> <?php echo $booking['phone']; ?></p>
    <p><b>Package:</b> <?php echo $booking['title']; ?></p>
    <p><b>Travel Date:</b> <?php echo date("d M Y", strtotime($booking['travel_date'])); ?></p>
    <p><b>Total Pax:</b> <?php echo $booking['pax']; ?></p>
    <p><b>Payment:</b> <?php echo strtoupper($booking['payment_method']); ?></p>
    <p>
        <b>Status:</b> 
        <span class="status <?php echo strtolower($booking['status']); ?>">
            <?php echo ucfirst($booking['status']); ?>
        </span>
    </p>
</div>

<hr>

<h3>Pax List</h3>

<table>
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Gender</th>
        <th>State</th>
    </tr>

    <?php
    $i = 1;

    if(mysqli_num_rows($pax_result) > 0){
        while($row = mysqli_fetch_assoc($pax_result)){
    ?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['gender']; ?></td>
        <td><?php echo $row['state']; ?></td>
    </tr>
    <?php 
        }
    } else {
        echo "<tr><td colspan='5'>No pax data</td></tr>";
    }
    ?>

</table>

</div>

</body>
</html>