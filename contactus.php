<?php
require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="contactus.css">
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

<div class="contact-banner">
    <div class="banner-content">
        <h1>Hubungi Kami</h1>
        <p>Kami sedia membantu anda.</p>
    </div>
</div>

<section class="info-highlight-section">
    <div class="company-title">
        <h2>SAHABAT INTERNATIONAL TRAVEL SDN. BHD.</h2>
    </div>

    <div class="contact-info-bar">
        <div class="info-box">
            <div class="icon">📍</div>
            <h4>Alamat Pejabat</h4>
            <p>No. 22C, Jalan Jubli Perak 22/1,<br>Shah Alam, Selangor</p>
        </div>

        <div class="info-box">
            <div class="icon">📞</div>
            <h4>Hubungi & Emel</h4>
            <p>+603-6102 0330 / 014-8803100<br>sahabattravelmalaysia@gmail.com</p>
        </div>

        <div class="info-box">
            <div class="icon">⏰</div>
            <h4>Waktu Operasi</h4>
            <p>Isnin - Jumaat<br>9:00 AM - 4:30 PM</p>
        </div>
    </div>
</section>

<section class="contact-interaction-section">
    <div class="content-wrapper">
        <div class="map-section">
            <h3>Lokasi Kami</h3>
            <div class="map-responsive">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d53204.160642956056!2d101.48957703125002!3d3.071452000000016!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4d3558112ae9%3A0x8bedc47f344ec27b!2sSahabat%20International%20Travel%20Sdn%20Bhd!5e1!3m2!1sen!2smy!4v1773107877374!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

		<div class="form-section">
            <h3>Borang Pertanyaan</h3>
			<form action="contact_process.php" method="POST">
				<div class="input-group">
					<label>Nama Penuh</label>
					<input type="text" name="name" placeholder="Masukkan Nama Anda" required>
				</div>

				<div class="input-group">
					<label>Alamat Emel</label>
					<input type="email" name="email" placeholder="Masukkan Alamat Email Anda" required>
				</div>

				<div class="input-group">
					<label>Mesej Anda</label>
					<textarea name="message" placeholder="Sila Type Message Anda Di Sini" rows="4" required></textarea>
				</div>

				<button type="submit" class="btn-submit">Hantar Sekarang</button>
			</form>
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