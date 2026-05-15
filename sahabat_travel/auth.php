<?php
session_start();
require 'db.php';

$error = "";
$success = "";

/* =========================
   REGISTER
========================= */
if (isset($_POST['register'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['register_email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (
        empty($username) || empty($email) || empty($gender) ||
        empty($address) || empty($phone) ||
        empty($password) || empty($confirm_password)
    ) {
        $error = "Please fill all fields!";
    }

    elseif ($password !== $confirm_password) {
        $error = "Password does not match!";
    }

    else {

        $check = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email already exists!";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users
            (username, email, gender, address, phone_number, password_hash)
            VALUES
            ('$username', '$email', '$gender', '$address', '$phone', '$hashedPassword')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['success'] = "Register successful!";
                header("Location: auth.php");
                exit();
            } else {
                $error = "Register failed!";
            }
        }
    }
}

/* =========================
   LOGIN
========================= */
if (isset($_POST['login'])) {

    $email = trim(mysqli_real_escape_string($conn, $_POST['login_email']));
    $password = $_POST['password'];

    $sql = "SELECT user_id, username, password_hash
FROM users
WHERE email='$email'
LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password_hash'])) {

            session_start();

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            header("Location: homepage.php");
            exit();

        } else {
            $error = "Wrong password!";
        }

    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Authentication</title>

    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<div class="container" id="container">

    <!-- REGISTER -->
    <div class="form-container sign-up">

        <form method="POST">

            <h1 class="title-h1">Create Account</h1>

            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="register_email" placeholder="Email" required>

            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <textarea name="address" placeholder="Address" required></textarea>

            <input type="text" name="phone" placeholder="Phone Number" required>

            <input type="password" name="password" placeholder="Password" required>

            <input type="password" name="confirm_password"
                placeholder="Confirm Password" required>

            <button type="submit" name="register">Register</button>

        </form>
    </div>

    <!-- LOGIN -->
    <div class="form-container sign-in">

        <form method="POST">

            <h1 class="title-h1">Login</h1>

            <input type="email" name="login_email" placeholder="Email" required>

            <input type="password" name="password"
                placeholder="Password" required>

            <button type="submit" name="login">Login</button>

        </form>
    </div>

    <!-- TOGGLE PANEL -->
    <div class="toggle-container">

        <div class="toggle">

            <div class="toggle-panel toggle-left">

                <h1 class="toggle-left-h1">Welcome Back!</h1>

                <p>Already have an account?</p>

                <button class="hidden" id="login">Login</button>

            </div>

            <div class="toggle-panel toggle-right">

                <h1 class="toggle-left-h1">Hello Friend!</h1>

                <p>Don't have an account?</p>

                <button class="hidden" id="register">Register</button>

            </div>

        </div>
    </div>

</div>

<?php if ($error != "") : ?>
    <p class="message error"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success != "") : ?>
    <p class="message success"><?php echo $success; ?></p>

    <script>
        document.getElementById("container").classList.remove("active");
    </script>

<?php endif; ?>

<script src="auth.js"></script>

</body>
</html>