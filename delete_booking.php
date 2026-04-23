<?php
require '../db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $sql = "DELETE FROM bookings WHERE booking_id = $id";
    mysqli_query($conn, $sql);

    header("Location: view_booking.php");
    exit();
}
?>