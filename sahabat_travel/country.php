<?php
session_start();
require 'db.php';

// ambil slug dari URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    die("Slug not valid");
}

// cari country berdasarkan country_slug
$stmt = $conn->prepare("SELECT * FROM countries WHERE country_slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();

$result = $stmt->get_result();
$country = $result->fetch_assoc();

if (!$country) {
    die("Country not found in database");
}

// get packages ikut country_id
$country_id = $country['country_id'];

$stmt2 = $conn->prepare("SELECT * FROM packages WHERE country_id = ?");
$stmt2->bind_param("i", $country_id);
$stmt2->execute();

$packages = $stmt2->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $country['country_name']; ?> Tour Packages</title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="country.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="home">
<?php include 'navbar.php'; ?>

<!-- HERO SECTION -->
<section class="hero" style="background: url('uploads/<?php echo $country['country_image']; ?>') center/cover no-repeat;">

    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1><?php echo $country['country_name']; ?></h1>
    </div>

</section>

<!-- TITLE -->
<h2 class="section-title">Senarai Pakej Eksklusif</h2>

<!-- PACKAGE CONTAINER -->
<div class="pakej-table-container">

<?php if ($packages->num_rows > 0): ?>

    <?php while ($row = $packages->fetch_assoc()): ?>

        <div class="pakej-row">

            <div class="pakej-img-box">
                <img src="uploads/<?php echo !empty($row['image']) ? $row['image'] : 'default.jpg'; ?>" alt="">
            </div>

            <div class="pakej-main-info">
                <h3><?php echo $row['title']; ?></h3>
            </div>

            <div class="pakej-action">
                <a href="view_package.php?id=<?php echo $row['package_id']; ?>" class="btn-lihat">
                    Lihat Details
                </a>
            </div>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <?php
        echo "<p style='text-align:center; grid-column:1/-1;'>
                No package available.
              </p>";
    ?>

<?php endif; ?>

</div>

<!-- FOOTER SECTION -->
<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-column">
            <h3>Sahabat International Travel</h3>
            <p class="license-no">(No. Syarikat: 2025010241909/1626322-H) (No. Lesen: KPK/LN 12734)</p><br>
            <p class="tagline">Kami adalah sahabat kembara anda, berdedikasi untuk "creating journey, building bonds".</p>
        </div>

        <div class="footer-column">
            <h3>Hubungi Kami</h3>
            <ul class="contact-list">
                <li><i class="fas fa-map-marker-alt"></i> No. 22C, Jalan Jubli Perak 22/1, Shah Alam, Selangor</li>
                <li><i class="fas fa-phone"></i> +603-6102 0330 / 014-8803100</li>
                <li><i class="fas fa-envelope"></i> sahabattravelmalaysia@gmail.com</li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Ikuti Kami</h3>
            <div class="social-icons">
                <a href="https://www.facebook.com/SahabatTravelMalaysia/"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com/sahabattravelmalaysia/"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@sahabattravelmalaysia"><i class="fab fa-tiktok"></i></a>
                <a href="https://wa.me/+60148803100"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2025 Sahabat International Travel Sdn Bhd. Hak Cipta Terpelihara.</p>
    </div>
</footer>

<script>

const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("mobileSidebar");
const closeMenu = document.getElementById("closeMenu");
const overlay = document.getElementById("overlay");

menuToggle.onclick = () => {
    sidebar.classList.add("active");
    overlay.classList.add("active");
}

closeMenu.onclick = () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
}

overlay.onclick = () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
}

/* MOBILE DROPDOWN */
document.querySelectorAll(".mobile-dropdown-btn")
.forEach(btn => {
    btn.addEventListener("click", () => {
        btn.parentElement.classList.toggle("active");
    });
});

/* MOBILE SUB DROPDOWN */
document.querySelectorAll(".mobile-sub-btn")
.forEach(btn => {
    btn.addEventListener("click", () => {
        btn.parentElement.classList.toggle("active");
    });
});

</script>

</body>
</html>