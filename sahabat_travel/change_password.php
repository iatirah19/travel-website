<?php
session_start();
include("db.php");

// contoh user login
$user_id = $_SESSION['user_id'] ?? null;

// kalau tak login
if(!$user_id){
    header("Location: login.php");
    exit();
}

// update password
if(isset($_POST['change_password'])){

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // ambil password lama dari DB
    $query = mysqli_query($conn, "SELECT password FROM users WHERE user_id='$user_id'");
    $user = mysqli_fetch_assoc($query);

    if(!password_verify($current, $user['password'])){
        echo "<script>alert('Current password salah!');</script>";
    }
    elseif($new !== $confirm){
        echo "<script>alert('Password baru tak sama!');</script>";
    }
    else{
        $hashed = password_hash($new, PASSWORD_DEFAULT);

        mysqli_query($conn, "
            UPDATE users 
            SET password='$hashed'
            WHERE user_id='$user_id'
        ");

        echo "
        <script>
            alert('Password successfully changed!');
            window.location.href='profile.php';
        </script>
        ";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Change Password</title>

<style>

body{
    margin:0;
    font-family:Segoe UI;
    background:#f5f7fb;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.container{
    width:100%;
    max-width:500px;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    margin-bottom:25px;
    color:#222;
}

.form-group{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:6px;
    font-weight:600;
}

input{
    width:100%;
    padding:12px 14px;
    border-radius:12px;
    border:1px solid #ddd;
    outline:none;
    font-size:14px;
    transition:0.3s;
}

input:focus{
    border-color:#7c4dff;
}

button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#7c4dff;
    color:#fff;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
}

button:hover{
    background:#6937ff;
}

.back{
    text-align:center;
    margin-top:15px;
}

.back a{
    text-decoration:none;
    color:#7c4dff;
}

</style>

</head>
<body>

<div class="container">

    <h2>Change Password</h2>

    <form method="POST">

        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>

        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" name="change_password">Update Password</button>

    </form>

    <div class="back">
        <a href="profile.php">← Back to Profile</a>
    </div>

</div>

</body>
</html>