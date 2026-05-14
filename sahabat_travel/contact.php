<?php
require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
    <!-- NAVBAR -->
<header>
    <nav class="navbar">

        <!-- LOGO -->
        <div class="logo">
            <img src="picture/LOGO-SAHABAT-BACKGROUND-WHITE.png" alt="Logo">
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