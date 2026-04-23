<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sahabat_travel";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset (important untuk elak error text / emoji)
$conn->set_charset("utf8mb4");
?>