<?php
require 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinasi - Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="packages.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<!-- NAVBAR -->
    <header>
        <nav class="navbar">
            
            <!-- LOGO -->
            <div class="logo">
                <img src="picture/LOGO-SAHABAT.png" alt="Logo">
            </div>

            <!-- MENU -->
            <ul class="nav-links" id="navLinks">
                <li><a href="homepage.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                
                <!-- DROPDOWN PACKAGE -->
                <li class="dropdown">
                    <a href="packages.php">
                        Packages <i class="fa-solid fa-chevron-down"></i>
                    </a>
                    
                    <ul class="dropdown-menu">
                        <li><a href="domestic.php">Domestic Package</a></li>
                        <li><a href="international.php">International Package</a></li>
                        <li><a href="umrah.php">Umrah Package</a></li>
                    </ul>
                </li>
                
                <li><a href="review.php">Review</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>

            <div class="nav-btn">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- PROFILE DROPDOWN -->
                    <div class="profile-menu">
                        <i class="fa-solid fa-user"></i>
                        <span><?php echo $_SESSION['username']; ?></span>
                        
                        <!-- Dropdown -->
                        <div class="dropdown">
                            <a href="profile.php">My Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>

                <?php else: ?>

                    <!-- LOGIN & REGISTER -->
                    <div class="auth-btn">
                        <a href="login.php" class="btn login-btn">Login</a>
                        <a href="register.php" class="btn register-btn">Register</a>
                    </div>

                <?php endif; ?>

            </div>

            <!-- MOBILE MENU ICON -->
            <div class="menu-toggle" id="menuToggle">
                <i class="fa-solid fa-bars"></i>
            </div>

        </nav>
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