<?php
require 'db.php';

if ($conn->connect_error) {
    die("Sambungan gagal: " . $conn->connect_error);
}

// 2. Ambil data dari table 'reviews'
$sql = "SELECT message, star, name, image FROM reviews";
$result = $conn->query($sql);

$reviews_data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Tukar angka 5 kepada string "★★★★★"
        $row['stars_html'] = str_repeat("★", $row['star']);
        $reviews_data[] = $row;
    }
}

// Tukar ke JSON untuk kegunaan JavaScript
$json_reviews = json_encode($reviews_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni - Sahabat International Travel</title>
    <link rel="stylesheet" href="review.css">
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
						<li><a href="review.php" class="active">Testimoni</a></li>
					</ul>
				</nav>

				<div class="header-action">
					<a href="contactus.php" class="btn-hubungi"></i> Hubungi Kami</a>
				</div>
			</div>
        </div>
    </header>

    <section class="testimonial-section">
      <div class="overlay"></div>
      <div class="content-wrapper">
        <div class="header-text">
          <h2>APA KATA PELANGGAN SAHABAT TRAVEL</h2>
          <p>Maklum balas pelanggan yang dah bercuti bersama Sahabat Travel</p>
        </div>

        <div class="slider-container">
          <button class="arrow" onclick="changeSlide(-1)">&#10094;</button>
          <div class="testimonial-card" id="testimonial-display">
              </div>
          <button class="arrow" onclick="changeSlide(1)">&#10095;</button>
        </div>

        <div class="dots-nav" id="dots-container"></div>
      </div>
    </section>

    <!-- Footer -->
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
        // Data dari PHP
        const testimonials = <?php echo $json_reviews; ?>;
        let currentIndex = 0;

        function renderTestimonial(index) {
            const display = document.getElementById('testimonial-display');
            const dotsContainer = document.getElementById('dots-container');
            
            if (testimonials.length === 0) {
                display.innerHTML = "<p>Tiada testimoni setakat ini.</p>";
                return;
            }

            const data = testimonials[index];

            // Render Card Content
            display.innerHTML = `
                <div class="quote-content">
                    <p class="message">"${data.message}"</p>
                    <div class="stars">${data.stars_html}</div>
                </div>
                <div class="client-info">
					<h3 class="name">${data.name}</h3>
                </div>
            `;

            // Render Dots
            dotsContainer.innerHTML = '';
            testimonials.forEach((_, i) => {
                const dot = document.createElement('span');
                dot.className = `dot ${i === index ? 'active' : ''}`;
                dot.onclick = () => { currentIndex = i; renderTestimonial(i); };
                dotsContainer.appendChild(dot);
            });
        }

        function changeSlide(step) {
            if (testimonials.length === 0) return;
            currentIndex += step;
            if (currentIndex >= testimonials.length) currentIndex = 0;
            if (currentIndex < 0) currentIndex = testimonials.length - 1;
            renderTestimonial(currentIndex);
        }

        // Mobile Menu Logic
        const menuToggle = document.querySelector(".menu-toggle");
        const headerRight = document.querySelector(".header-right");
        menuToggle.addEventListener("click", () => headerRight.classList.toggle("active"));

        // Autoplay
        setInterval(() => changeSlide(1), 5000);

        // Initial Load
        document.addEventListener('DOMContentLoaded', () => renderTestimonial(0));
    </script>
	
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