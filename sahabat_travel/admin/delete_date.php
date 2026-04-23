<?php
require '../db.php';

if (!isset($_GET['id']) || !isset($_GET['package_id'])) {
    echo "Invalid request";
    exit;
}

$date_id = (int) $_GET['id'];
$package_id = (int) $_GET['package_id'];

// delete 1 date sahaja
mysqli_query($conn, "DELETE FROM package_dates WHERE date_id = $date_id");

// redirect balik
header("Location: edit_package.php?id=$package_id");
exit;
?>