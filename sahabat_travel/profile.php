<?php
session_start();
include("db.php");

// check login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="profile-container">

    <!-- TOP -->
    <div class="profile-top">
        <div class="profile-info">
            <h1><?php echo $user['username']; ?></h1>
            <p>User Profile</p>
        </div>
    </div>

    <!-- DETAILS -->
    <div class="profile-details">

        <!-- ACCOUNT -->
        <div class="detail-card">
            <h3>
                <i class="fa-solid fa-user"></i>
                Username
            </h3>
            <p><?php echo $user['username']; ?></p>
        </div>

        <div class="detail-card">
            <h3>
                <i class="fa-solid fa-user-shield"></i>
                Account Role
            </h3>
            <p>User</p>
        </div>

        <!-- CONTACT -->
        <div class="detail-card">
            <h3>
                <i class="fa-solid fa-envelope"></i>
                Email Address
            </h3>
            <p><?php echo $user['email']; ?></p>
        </div>

        <div class="detail-card">
            <h3>
                <i class="fa-solid fa-phone"></i>
                Phone Number
            </h3>
            <p><?php echo $user['phone_number']; ?></p>
        </div>

        <!-- PERSONAL -->
        <div class="detail-card">
            <h3>
                <i class="fa-solid fa-venus-mars"></i>
                Gender
            </h3>
            <p><?php echo $user['gender']; ?></p>
        </div>

        <div class="detail-card address-card">
            <h3>
                <i class="fa-solid fa-location-dot"></i>
                Address
            </h3>
            <p><?php echo $user['address']; ?></p>
        </div>

    </div>

    <!-- BUTTON -->
    <div class="profile-buttons">

        <a href="edit_profile.php" class="edit-btn">
            <i class="fa-solid fa-pen"></i>
            Edit Profile
        </a>

        <a href="change_password.php" class="password-btn">
            <i class="fa-solid fa-lock"></i>
            Change Password
        </a>

    </div>

</div>

</body>
</html>