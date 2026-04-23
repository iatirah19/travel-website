<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data dari form
    $name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$message = trim($_POST['message']);

    // 2. VALIDATION (LETak SINI 🔥)
    if(empty($name) || empty($email) || empty($message)){
        echo "<script>
                alert('Sila lengkapkan semua maklumat!');
                window.history.back();
              </script>";
        exit;
    }

    // 3. INSERT database
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<script>
                alert('Mesej berjaya dihantar!');
                window.location.href='contactus.php';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>