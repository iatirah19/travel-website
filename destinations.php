<?php
require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinasi - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="destinations.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>

<!-- NAVBAR -->
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
					<li><a href="destinations.php" class="active">Destinasi</a></li>
					<li><a href="review.php">Testimoni</a></li>
				</ul>
			</nav>

			<div class="header-action">
				<a href="contactus.php" class="btn-hubungi"></i> Hubungi Kami</a>
			</div>
        </div>
    </div>
</header>

<header class="hero-banner">
    <img src="picture/wallpaper.png" alt="Hero Banner Image" class="hero-img">
    <div class="hero-overlay">
        <div class="hero-content">
            <h1>Pilihan Destinasi Untuk Setiap Perjalanan</h1>
            <p>Pilih pakej dalam dan luar negara yang sesuai dengan impian anda</p>
        </div>
    </div>
</header>

<section>
    <h2>Destinasi Pilihan Anda</h2>
    <h3>Terokai pakej dalam negara, luar negara dan umrah dengan mudah</h3>
    <div class="pakej-container">
        <div class="card-pakej">
            <div class="image-container">
                <img src="picture/pakej dalam negara.png" alt="pakej dalam negara" class="pakej-img">
                <div class="overlay">
                    <div class="pakej-info">
                        <h3>Pakej Dalam Negara</h3>
                        <a href="dalamnegara.php" class="btn-details">Lihat Pakej</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-pakej">
            <div class="image-container">
                <img src="picture/pakej luar negara.png" alt="pakej luar negara" class="pakej-img">
                <div class="overlay">
                    <div class="pakej-info">
                        <h3>Pakej Luar Negara</h3>
                        <a href="luarnegara.php" class="btn-details">Lihat Pakej</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-pakej">
            <div class="image-container">
                <img src="picture/pakej umrah.png" alt="pakej umrah" class="pakej-img">
                <div class="overlay">
                    <div class="pakej-info">
                        <h3>Pakej Umrah</h3>
                        <a href="umrah.php" class="btn-details">Lihat Pakej</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

	// Opsional: Autoplay setiap 5 saat
	setInterval(() => {
        changeSlide(1);
    }, 5000);
	
    const menuToggle = document.querySelector(".menu-toggle");
    const headerRight = document.querySelector(".header-right");
    
    menuToggle.addEventListener("click", function(){
        headerRight.classList.toggle("active");
        });
</script>

</body>
</html>