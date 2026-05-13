<?php
require 'db.php';

if ($conn->connect_error) {
    die("Sambungan gagal: " . $conn->connect_error);
}

// 2. Ambil data dari table 'reviews'
$sql = "SELECT review_text, rating, name FROM reviews";
$result = $conn->query($sql);

$reviews_data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Tukar angka 5 kepada string "★★★★★"
        $row['stars_html'] = str_repeat("★", $row['rating']);
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
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="review.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- NAVBAR -->
<header>
    <nav class="navbar">

        <!-- LOGO -->
        <div class="logo">
            <img src="picture/LOGO-SAHABAT.png" alt="Logo">
        </div>

        <!-- MENU (DESKTOP) -->
        <ul class="nav-links" id="navLinks">

            <li><a href="homepage.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>

            <!-- DROPDOWN (DESKTOP ONLY) -->
            <li class="dropdown">

                <button class="dropdown-btn">
                    Packages <i class="fa-solid fa-chevron-down"></i>
                </button>

                <ul class="dropdown-menu">

                    <li><a href="domestic.php">Domestic Package</a></li>

                    <li class="sub-dropdown">

                        <button class="sub-dropdown-btn">
                            International Package
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <ul class="sub-dropdown-menu">
                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");
                            while($row = mysqli_fetch_assoc($result)) {
                            ?>
                            
                                <li>
                                    <a href="country.php?slug=<?= $row['country_slug'] ?>">
                                        <?= $row['country_name'] ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                    <li><a href="umrah.php">Umrah Package</a></li>

                </ul>
            </li>

            <li><a href="review.php">Review</a></li>
            <li><a href="contact.php">Contact</a></li>

        </ul>

        <!-- AUTH -->
        <div class="nav-btn">
            <?php if(isset($_SESSION['user_id'])): ?>

                <div class="profile-menu">
                    <i class="fa-solid fa-user"></i>
                    <span><?php echo $_SESSION['username']; ?></span>
                </div>

            <?php else: ?>

                <div class="auth-btn">
                    <a href="login.php" class="btn login-btn">Login</a>
                    <a href="register.php" class="btn register-btn">Register</a>
                </div>

            <?php endif; ?>
        </div>

        <!-- MOBILE BUTTON -->
        <div class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
        </div>

    </nav>
</header>

<!-- MOBILE SIDEBAR -->
<div class="mobile-sidebar" id="mobileSidebar">

    <div class="close-btn" id="closeMenu">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <ul>
        <li><a href="homepage.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>

        <!-- MOBILE DROPDOWN -->
        <li class="mobile-dropdown">

            <div class="mobile-dropdown-btn">
                Packages
                <i class="fa-solid fa-chevron-down"></i>
            </div>

            <ul class="mobile-dropdown-menu">

                <li><a href="domestic.php">Domestic Package</a></li>

                <!-- MOBILE SUB -->
                <li class="mobile-sub-dropdown">

                    <div class="mobile-sub-btn">
                        International Package
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>

                    <ul class="mobile-sub-menu">
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");
                        while($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <li>
                                <a href="country.php?slug=<?= $row['country_slug'] ?>">
                                    <?= $row['country_name'] ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>

                </li>

                <li><a href="umrah.php">Umrah Package</a></li>

            </ul>

        </li>

        <li><a href="review.php">Review</a></li>
        <li><a href="contact.php">Contact</a></li>

    </ul>
</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

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
                    <p class="message">"${data.review_text}"</p>
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

        // Autoplay
        setInterval(() => changeSlide(1), 5000);

        // Initial Load
        document.addEventListener('DOMContentLoaded', () => renderTestimonial(0));
    </script>
	
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