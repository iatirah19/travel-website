<?php
require 'db.php';

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // check empty
    if (empty($username) || empty($email) || empty($gender) || empty($address) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = "Sila isi semua field!";
    }

    // check password match
    elseif ($password !== $confirm_password) {
        $error = "Password tidak sama!";
    }

    else {

        // check email exist
        $check = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email sudah wujud!";
        } else {

            // hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // insert user
            $sql = "INSERT INTO users (username, email, gender, address, phone, password_hash)
                    VALUES ('$username', '$email', '$gender', '$address', '$phone', '$hashedPassword')";

            if (mysqli_query($conn, $sql)) {
                $success = "Register berjaya! Sila login.";
            } else {
                $error = "Register gagal. Cuba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<div class="box">
    <h2>Register</h2>

    <?php if ($error != "") echo "<p class='error'>$error</p>"; ?>
    <?php if ($success != "") echo "<p class='success'>$success</p>"; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>

        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <textarea name="address" placeholder="Address" required></textarea>

        <input type="text" name="phone" placeholder="Phone Number" required>

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

</body>
</html>