<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $number = trim($_POST['phone']);

    // Default status
    $status = "Unread";

    // Validation
    if (empty($name) || empty($email) || empty($number)) {
        echo "<script>
                alert('Sila lengkapkan semua maklumat!');
                window.history.back();
              </script>";
        exit;
    }

    // Optional email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                alert('Format email tidak sah!');
                window.history.back();
              </script>";
        exit;
    }

    // Insert database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone_number, status) VALUES (?, ?, ?, ?)");

    $stmt->bind_param("ssss", $name, $email, $number, $status);

    if ($stmt->execute()) {

        echo "<script>
                alert('Mesej berjaya dihantar!');
                window.location.href='contact.php';
              </script>";

    } else {

        echo "Error: " . $stmt->error;

    }

    $stmt->close();
    $conn->close();
}
?>