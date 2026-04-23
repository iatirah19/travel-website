<?php 
require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="aboutus.css">
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
					<li><a href="aboutus.php" class="active">Tentang Kami</a></li>
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

<div class="about-card">
    <img src="picture/wallpaper6.jpg" alt="About Image">
    
    <div class="overlay">
        <h1>Sahabat International Travel Sdn Bhd</h1>
        <h2><i>"Creating Journey, Building Bonds"</i></h2>
    </div>
</div>

<section class="about-section">
        <div class="container">
            <h2>Mengenai Sahabat Travel</h2>
            <p>Sahabat International Travel Sdn Bhd beroperasi secara profesional <span class="highlight">sejak awal 2025</span>, Sahabat International Travel Sdn Bhd komited untuk menjadi penyedia perkhidmatan pelancongan yang komprehensif, dipercayai, dan berinovasi. Bermula dengan cita-cita bersama antara beberapa orang sahabat yang ingin meneruskan usaha dan minat dalam bidang pelancongan, kami kini telah berkembang menjadi agensi pelancongan sehenti yang mengkhusus dalam pelbagai jenis perjalanan untuk memenuhi keperluan individu, keluarga, dan korporat.</p>
            <p>Kami adalah sebuah <span class="highlight">agensi pelancongan Muslim</span> yang berdaftar dan berpangkalan di <span class="highlight">Shah Alam, Selangor</span>. Kami menyediakan pelbagai perkhidmatan pelancongan domestik dan antarabangsa termasuk <span class="highlight">Umrah</span>, <span class="highlight">Muslim Tour</span>, <span class="highlight">Percutian Korporat</span>, <span class="highlight">Keluarga</span>, <span class="highlight">Syarikat</span> dan <span class="highlight">MICE</span>.</p>
            <p>Kami percaya bahawa setiap perjalanan bukan sekadar destinasi, tetapi sebuah perjalanan membina hubungan, pengalaman dan nilai kehidupan.</p>
        </div>
    </section>
	
	<section class="who-we-are-section">
    <div class="container">
        <h2>Siapa Kami?</h2>

        <div class="intro-box">
            <p>Di <strong>Sahabat Travel</strong>, kami bukan sekadar menjual pakej pelancongan; kami berjanji untuk <span class="motto-text">'Creating Journey, Building Bonds'</span>.</p>
        </div>

        <div class="split-layout">
            <div class="split-column left">
                <h3>Creating Journey</h3>
                <p>Misi kami dalam <span class="tag-gold">'Creating Journey'</span> adalah untuk merancang setiap detik perjalanan suci Anda dengan teliti, berlandaskan <span class="tag-blue">amanah dan profesionalisme</span>.</p>
            </div>
            
            <div class="split-divider"></div>

            <div class="split-column right">
                <h3>Building Bonds</h3>
                <p>Apa yang benar-benar membezakan kami adalah semangat kami dalam <span class="tag-red">'Building Bonds'</span>. Kami ingin membina ikatan persahabatan dengan Anda, antara Anda dengan para jemaah lain, dan yang paling utama, mengeratkan hubungan rohani Anda dengan Yang Maha Esa.</p>
            </div>
        </div>

        <div class="closing-box">
            <p>"Biarlah kami menjadi <strong>'sahabat'</strong> dalam perjalanan suci Anda."</p>
        </div>
    </div>
</section>

<section class="vision-mission-section">
        <div class="container">
            <h2>Visi & Misi</h2>
            <div class="cards-container">
                <!-- Visi Card -->
                <div class="vm-card">
                    <div class="vm-icon"><i class="fas fa-eye"></i></div>
                    <h3>Visi</h3>
                    <li>Menawarkan pengalaman pelancongan outbound yang memudahkan Muslim mengekalkan gaya hidup halal di mana sahaja.</li>
                    <li>Menghubungkan destinasi dengan sejarah, budaya, dan komuniti Islam untuk memperkukuh ukhuwah antarabangsa</li>
                    <li>Menyediakan mutawwif dan tour leader yang profesional serta menjadi pemimpin rohani sepanjang perjalanan</li>
                    <li>Mengintegrasikan teknologi dan inovasi dalam perkhidmatan agar perjalanan lebih lancar, selesa dan selamat</li>
                    <li>Membawa jenama Malaysia sebagai pusat rujukan pelancongan Muslim ke peringkat antarabangsa</li>
                </div>
                
                <!-- Misi Card -->
                <div class="vm-card">
                    <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Misi</h3>
                    <p>Menjadi PENGENDALI UTAMA dalam pelancongan antarabangsa berteraskan gaya hidup Muslim dan menghubungkan dunia dengan nilai Islam sebelum 2035.</p>
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