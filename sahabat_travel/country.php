<?php
require 'db.php';

$country_id = isset($_GET['country_id']) ? intval($_GET['country_id']) : 0;

if ($country_id == 0) {
    die("Country ID not valid");
}

$country = $conn->query("SELECT * FROM countries WHERE country_id=$country_id")->fetch_assoc();

if (!$country) {
    die("Country not found in database");
}

// get packages
$packages = $conn->query("SELECT * FROM packages WHERE country_id=$country_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $country['country_name']; ?> Tour Packages</title>
    <link rel="stylesheet" href="country.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
    <div class="header-container">
        <div class="logo">
            <img src="picture/LOGO-SAHABAT.png" alt="Logo Sahabat International Travel">
        </div>

        <div class="menu-toggle">☰</div>

        <div class="header-right">
        <nav class="nav-menu">
            <ul>
                <li><a href="homepage.php">Utama</a></li>
                <li><a href="aboutus.php">Tentang Kami</a></li>
                <li><a href="destinations.php">Destinasi</a></li>
                <li><a href="review.php">Testimoni</a></li>
            </ul>
        </nav>

        <div class="header-action">
            <a href="contactus.php" class="btn-hubungi"></i> Hubungi Kami</a>
        </div>
        </div>
    </div>
    </header>

<header class="hero-banner" style="background: url('uploads/<?php echo $country['country_image']; ?>') center/cover no-repeat;">
    
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1><?php echo $country['country_name']; ?></h1>
    </div>
</header>


    <h2 class="section-title">Senarai Pakej Eksklusif</h2>

<div class="pakej-table-container">

<?php if($packages->num_rows > 0) { ?>

    <?php while($row = $packages->fetch_assoc()) { ?>

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

    <?php } ?>

<?php } else { ?>

    <!-- EMPTY STATE -->
    <div class="empty-state">
        <h3>Tiada Pakej Setakat Ini.</h3>
        <p>Pakej akan dikemaskini tidak lama lagi.</p>
    </div>

<?php } ?>
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

<!-- JS FOR TOGGLE -->
<script>
    const menuToggle = document.querySelector(".menu-toggle");
    const headerRight = document.querySelector(".header-right");
    
    menuToggle.addEventListener("click", function(){
        headerRight.classList.toggle("active");
        });
</script>

</body>
</html>