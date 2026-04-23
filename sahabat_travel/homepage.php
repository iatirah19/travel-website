<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="homepage.css">
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
					<li><a href="homepage.php" class="active">Utama</a></li>
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

<!-- HERO SECTION -->
<div class="hero-banner">
    <div class="slides-container">
        <div class="slide active">
            <img src="picture/Wallpaper1.jpg.jpeg" alt="Destinasi Percutian 1" class="slide-img">
            <div class="overlay"></div>
            <div class="overlay-content">
                <h2>Selamat Datang ke Sahabat International Travel</h2>
                <h4>Rakan perjalanan anda untuk menerokai dunia tanpa batasan.</h4>
                <div class="btn-group">
                    <a href="destinations.php" class="hero-btn primary-btn">Terokai Sekarang</a>
                </div>
            </div>
        </div>

        <div class="slide">
            <img src="picture/wallpaper3.jpg" alt="Destinasi Percutian 2" class="slide-img">
            <div class="overlay"></div>
            <div class="overlay-content">
                <h2>Percutian Impian Bermula Di Sini</h2>
                <h4>Nikmati pakej pelancongan ke seluruh dunia dengan harga berpatutan dan perkhidmatan terbaik.</h4>
            </div>
        </div>

        <div class="slide">
            <img src="picture/wallpaper4.jpg" alt="Destinasi Percutian 3" class="slide-img">
            <div class="overlay"></div>
            <div class="overlay-content">
                <h2>Destinasi Global, Pengalaman Eksklusif</h2>
                <h4>Dari Asia ke Eropah, kami uruskan setiap perjalanan anda dengan profesional dan teliti.</h4>
            </div>
        </div>

        <div class="slide">
            <img src="picture/wallpaper5.jpg" alt="Destinasi Percutian 4" class="slide-img">
            <div class="overlay"></div>
            <div class="overlay-content">
                <h2>Perjalanan Selesa & Selamat Untuk Semua</h2>
                <h4>Sama ada percutian keluarga, bulan madu atau trip berkumpulan - kami sedia membantu.</h4>
                <div class="btn-group">
                    <a href="contactus.php" class="hero-btn primary-btn">Hubungi Kami</a>
                </div>
            </div>
        </div>

        <button class="prev" onclick="changeSlide(-1)">&#10094;</button>
        <button class="next" onclick="changeSlide(1)">&#10095;</button>
    </div>
</div>

<script src="homepage.js"></script>

<!-- POPULAR PACKAGES -->
<section class="section">
    <h2 class="pakej-title">Koleksi Pakej Terbaik</h2>
    <h3 class="pakej-subtitle">Kembara ke destinasi impian anda dengan pakej terbaik kami.</h3>

    <div class="card-container">

<?php
$sql = "SELECT * FROM packages WHERE status='active' LIMIT 4";
$result = $conn->query($sql);

if ($result->num_rows > 0):
    while($row = $result->fetch_assoc()):
?>

    <div class="card">
        <img src="uploads/<?= $row['image'] ?>" alt="<?= $row['title'] ?>">

        <div class="card-overlay">
            <h3><?= $row['title'] ?></h3>
            <p class="details"><?= $row['duration'] ?></p>

            <a href="package.php?id=<?= $row['package_id'] ?>" class="btn-details">
                Lihat Detail
            </a>
        </div>
    </div>

<?php 
    endwhile;
else:
?>

    <p class="no-data">Tiada pakej tersedia buat masa ini.</p>

<?php endif; ?>

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