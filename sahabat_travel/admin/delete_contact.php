<?php
require '../db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $sql = "DELETE FROM contacts WHERE contact_id = $id";
    mysqli_query($conn, $sql);

    header("Location: admin_dashboard.php");
    exit();
}
?>