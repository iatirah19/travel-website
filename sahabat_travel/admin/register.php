<?php
session_start();
require '../db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // ✅ CHECK PASSWORD MATCH
    if ($password !== $confirm) {
        $error = "Password tidak sama!";
    } else {

        // ✅ CHECK EMAIL DUPLICATE
        $check = mysqli_prepare($conn, "SELECT * FROM admin WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email sudah digunakan!";
        } else {

            // 🔐 HASH PASSWORD
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // ✅ INSERT DATA
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO admin (name, email, password) VALUES (?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);
            mysqli_stmt_execute($stmt);

            // ✅ AUTO LOGIN
            $_SESSION['admin_id'] = mysqli_insert_id($conn);
            $_SESSION['admin_email'] = $email;

            header("Location: admindashboard.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<div class="login-container">

    <div class="left-panel">
        <div class="branding">
            <img src="../picture/LOGO.png">
            <h2>Sahabat International<span>Travel Sdn Bhd</span></h2>
        </div>
    </div>

    <div class="right-panel">
        <h2>Register</h2>
        <p>Create your admin account</p>

        <?php if (!empty($error)) { ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php } ?>

        <form method="POST">

            <!-- NAME -->
            <label>Name:</label>
            <input type="text" name="name" placeholder="Name" class="input-box" required>

            <!-- EMAIL -->
            <label>Email:</label>
            <input type="email" name="email" placeholder="Email" class="input-box" required>

            <!-- PASSWORD -->
            <label>Password:</label>
            <input type="password" name="password" placeholder="Password" class="input-box" required>

            <!-- CONFIRM PASSWORD -->
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password" class="input-box" required>

            <button type="submit" class="login-btn">Register</button>

        </form>
    </div>

</div>

</body>
</html>