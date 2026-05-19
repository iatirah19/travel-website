<?php
require '../db.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: homepage.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth.php");
    exit();
}

// DELETE (same page)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    mysqli_query($conn, "DELETE FROM reviews WHERE review_id = '$id'");

    // redirect supaya tak repeat delete bila refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

/* =========================
   ADD REVIEW
========================= */
if (isset($_POST['add_review'])) {

    $name = $_POST['name'];
    $rating = $_POST['rating'];
    $comment = $_POST['review_text'];

    mysqli_query($conn, "
        INSERT INTO reviews (name, rating, review_text, created_at)
        VALUES ('$name', '$rating', '$comment', NOW())
    ");

    header("Location: admin_manage_review.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Review - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="admin_manage_review.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<!-- TOGGLE BUTTON -->
<div class="menu-toggle" id="menuToggle">
    <i class="fa-solid fa-bars"></i>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <!-- CLOSE BUTTON -->
    <div class="close-btn" id="closeBtn">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <h2 class="logo">Admin Panel</h2>

    <ul>
        <li><a href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="admin_manage_country.php"><i class="fa-solid fa-earth-asia"></i> Manage Country</a></li>
        <li><a href="admin_manage_package.php"><i class="fa-solid fa-box"></i> Manage Package</a></li>
        <li><a href="admin_manage_review.php"><i class="fa-solid fa-star"></i> Manage Review</a></li>
        <li><a href=""><i class="fa-solid fa-star"></i> Add Admin</a></li>
        <li><a href="#" onclick="confirmLogout(event)"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

<div class="main-wrapper">
    
    <form method="POST" class="review-form">
        <h2>💬 Add Review</h2>
        
        <input type="text" name="name" placeholder="Nama Pelanggan" required>

        <select name="rating" required>
            <option value="">Pilih Rating</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
        </select>

        <textarea name="review_text" placeholder="Tulis komen anda di sini..." required></textarea>

        <button type="submit" name="add_review">Hantar Komen</button>
    </form>

    <hr style="border: 0; height: 1px; background: #ddd; margin: 30px 0;">

    <div class="reviews-list-container">
		<h2>⭐ Semua Review Pelanggan</h2>

		<table class="review-table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Rating</th>
					<th>Message</th>
                    <th>Action</th>
				</tr>
			</thead>

			<tbody>
			<?php
			$reviews = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");

			while ($row = mysqli_fetch_assoc($reviews)) {
			?>
				<tr>
					<td><?php echo htmlspecialchars($row['name']); ?></td>

					<td class="stars">
						<?php echo str_repeat("⭐", (int)$row['rating']); ?>
					</td>

					<td><?php echo nl2br(htmlspecialchars($row['review_text'])); ?></td>

                    <td>
                        <a href="?delete_id=<?php echo $row['review_id']; ?>"
                        onclick="return confirm('Are you sure want to delete this review?')"
                        class="btn-delete">
                        Delete
                        </a>
                    </td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

</div>
<script>
const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const closeBtn = document.getElementById("closeBtn");
const overlay = document.getElementById("overlay");

/* OPEN SIDEBAR */
menuToggle.addEventListener("click", () => {
    sidebar.classList.add("active");
    overlay.classList.add("active");
});

/* CLOSE SIDEBAR */
closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

/* CLOSE WHEN CLICK OVERLAY */
overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

function confirmLogout(event) {
    event.preventDefault(); // stop link behavior

    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "admin_dashboard.php?logout=1";
    }
}
</script>
</body>
</html>