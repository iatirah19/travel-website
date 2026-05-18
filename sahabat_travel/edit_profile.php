<?php
session_start();
include("db.php");

// Example user session
$user_id = $_SESSION['user_id'];

// Get current user data
$query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($query);

// Update profile
if(isset($_POST['update_profile'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update = mysqli_query($conn, "
        UPDATE users 
        SET 
            username='$username',
            address='$address',
            phone_number='$phone'
        WHERE user_id='$user_id'
    ");

    if($update){
        echo "<script>alert('Profile updated successfully!');</script>";

        // Refresh data
        $query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
        $user = mysqli_fetch_assoc($query);

    }else{
        echo "<script>alert('Failed to update profile!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', sans-serif;
}

body{
    background:#f5f7fb;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
}

.profile-container{
    width:100%;
    max-width:650px;
    background:#fff;
    border-radius:25px;
    padding:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.profile-container h2{
    font-size:30px;
    margin-bottom:30px;
    color:#222;
    text-align:center;
}

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:block;
    margin-bottom:8px;
    font-weight:600;
    color:#444;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:14px 16px;
    border:1px solid #ddd;
    border-radius:14px;
    font-size:15px;
    transition:0.3s ease;
    background:#fafafa;
}

.form-group input:focus,
.form-group textarea:focus{
    border-color:#7c4dff;
    outline:none;
    background:#fff;
}

.form-group input[readonly]{
    background:#ececec;
    cursor:not-allowed;
    color:#666;
}

textarea{
    resize:none;
    height:120px;
}

.save-btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:14px;
    background:#7c4dff;
    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s ease;
}

.save-btn:hover{
    background:#6937ff;
}

@media(max-width:600px){

    .profile-container{
        padding:25px;
    }

    .profile-container h2{
        font-size:24px;
    }

}

</style>
</head>
<body>

<div class="profile-container">

    <h2>Edit Profile</h2>

    <form method="POST">

        <!-- Username -->
        <div class="form-group">
            <label>Username</label>
            <input type="text" 
                   name="username" 
                   value="<?php echo $user['username']; ?>" 
                   required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" 
                   value="<?php echo $user['email']; ?>" 
                   readonly>
        </div>

        <!-- Gender -->
        <div class="form-group">
            <label>Gender</label>
            <input type="text" 
                   value="<?php echo $user['gender']; ?>" 
                   readonly>
        </div>

        <!-- Address -->
        <div class="form-group">
            <label>Address</label>
            <textarea name="address"><?php echo $user['address']; ?></textarea>
        </div>

        <!-- Phone -->
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" 
                   name="phone" 
                   value="<?php echo $user['phone_number']; ?>">
        </div>

        <button type="submit" 
                name="update_profile" 
                class="save-btn">
            Save Changes
        </button>

    </form>

</div>

</body>
</html>