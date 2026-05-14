<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahabat International Travel Sdn Bhd</title>
    <link rel="icon" type="image/png" href="picture/LOGO.png">
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<!-- NAVBAR -->
<header>
    <nav class="navbar">

        <!-- LOGO -->
        <div class="logo">
            <img src="picture/LOGO-SAHABAT-BACKGROUND-WHITE.PNG" alt="Logo">
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

    <!-- HERO SECTION -->
    <section class="hero">

    <div class="hero-content">
        <h1>Selamat Datang ke Sahabat International Travel</h1>
        <p>Rakan perjalanan anda untuk menerokai dunia tanpa batasan.</p>
        <div class="hero-btns">
            <a href="contact.php" class="btn-primary">Contact Us</a>
        </div>
    </div>

</section>

<section class="popular-package">
    <h2>Popular Packages</h2>

    <div class="package-container">

        <?php
        include 'db.php';

        $sql = "SELECT * FROM packages WHERE is_active='popular' LIMIT 4";
        $result = $conn->query($sql);

        if($result->num_rows > 0):
            while($row = $result->fetch_assoc()):
        ?>

        <div class="package-card">
            <img src="uploads/<?php echo $row['packimage']; ?>" alt="Package Image">

            <div class="package-info">
                <h3><?php echo $row['packname']; ?></h3>
                <p class="details"><?= $row['duration'] ?></p>

                <a href="package_detail.php?id=<?php echo $row['package_id']; ?>" class="view-btn">
                    View Details
                </a>
            </div>
        </div>

        <?php
            endwhile;
        else:
            echo "<p>No popular package available.</p>";
        endif;
        ?>

    </div>
</section>

<!-- WHY CHOOSE US SECTION -->
<section class="kenapa-pilih-section">
    <div class="container-full">
        <h2 class="section-title">Kenapa Pilih Sahabat Travel?</h2>
        <h3 class="section-subtitle">Kami bukan sekadar agensi pelancongan, kami adalah sahabat kembara anda.</h3>

        <div class="why-cards-container">
            <div class="why-card">
                <div><i class="fas fa-shield-alt icon-blue"></i></div>
                <h3>Amanah & Profesional</h3>
                <p>Sebagai agensi berlesen, kami menjamin perjalanan anda selamat, lancar dan diuruskan secara profesional dari awal hingga akhir.</p>
            </div>

            <div class="why-card">
                <div><i class="fas fa-star icon-gold"></i></div>
                <h3>Pengalaman Emas</h3>
                <p>Setiap pakej direka teliti untuk memberikan pengalaman premium dan kenangan terindah dengan harga yang sangat berpatutan.</p>
            </div>

            <div class="why-card">
                <div><i class="fas fa-users icon-red"></i></div>
                <h3>Ikatan Persahabatan</h3>
                <p>Selaras dengan slogan kami, kami sentiasa memberikan perkhidmatan mesra untuk membina ikatan sejati dengan setiap pelanggan kami.</p>
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