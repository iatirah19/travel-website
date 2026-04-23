<?php
session_start();
require '../db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $role = $_POST['role'];

    // 👉 CUSTOMER → terus masuk homepage
    if ($role == "customer") {
        header("Location: homepage.php");
        exit();
    }

    // 👉 ADMIN → check login
    if ($role == "admin") {

        $email = $_POST['email'];
        $password = $_POST['password'];

        // 🔥 GUNA EMAIL (bukan name dah)
        $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        // 🔐 VERIFY PASSWORD
        if ($admin && password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_email'] = $admin['email'];

            header("Location: admindashboard.php");
            exit;

        } else {
            $error = "Email atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
		
		<div class="left-panel">
			<div class="branding">
				<img src="../picture/LOGO.png" alt="Company Logo">
				<h2>Sahabat International<span>Travel Sdn Bhd</span></h2>
			</div>
		</div>
		
		<div class="right-panel">
			<h2>Welcome</h2>
			<p id="admin-text" style="display:none;">Please login to continue</p>
			
			<?php if (!empty($error)) { ?>
				<p style="color:red;"><?php echo $error; ?></p>
			<?php } ?>
			
			<form method="POST">
				<!-- ROLE -->
				<label for="role">Role:</label>
				<select name="role" id="role" class="input-box" required onchange="handleRole()">
					<option value="">-- Select Role --</option>
					<option value="customer">Customer</option>
					<option value="admin">Admin</option>
				</select>

				<!-- CUSTOMER BUTTON -->
				<div id="customer-section" style="display:none;">
					<button type="submit" name="customer_enter" class="login-btn">Enter</button>
				</div>

				<!-- ADMIN LOGIN -->
				<div id="admin-section" style="display:none;">
					
					<!-- EMAIL -->
					<label for="username">Email:</label>
					<input type="text" name="email" placeholder="Email" class="input-box" required>
					
					<!-- PASSWORD -->
					<label for="password">Password:</label>
					<input type="password" name="password" placeholder="Password" class="input-box" required>
					
					<button type="submit" name="admin_login" class="login-btn">Login</button>
				</div>
			</form>
		</div>
	</div>
	<script>
function handleRole() {
    var role = document.getElementById("role").value;

    // hide semua dulu
    document.getElementById("customer-section").style.display = "none";
    document.getElementById("admin-section").style.display = "none";
	document.getElementById("admin-text").style.display = "none";

    if (role === "customer") {
        document.getElementById("customer-section").style.display = "block";
    } 
    else if (role === "admin") {
        document.getElementById("admin-section").style.display = "block";
		document.getElementById("admin-text").style.display = "block";
    }
}
</script>
	
<script>
document.querySelector("select[name='role']").addEventListener("change", function() {
    let isAdmin = this.value === "admin";

    document.querySelector("input[name='email']").required = isAdmin;
    document.querySelector("input[name='password']").required = isAdmin;
});
</script>
</body>
</html>