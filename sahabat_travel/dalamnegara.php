<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakej Dalam Negara - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="dalamnegara.css">
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
                <li><a href="index.html">Utama</a></li>
                <li><a href="tentang-kami.html">Tentang Kami</a></li>
                <li><a href="destinasi.html">Destinasi</a></li>
                <li><a href="testimoni.html">Testimoni</a></li>
            </ul>
        </nav>

        <div class="header-action">
            <a href="hubungi-kami.html" class="btn-hubungi"></i> Hubungi Kami
            </a>
        </div>
        </div>
    </div>
    </header>

    <h2 class="section-title">Senarai Pakej Eksklusif</h2>

    <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Cari pakej...">
    </div>

<div class="pakej-table-container">

	<?php
	$category_id = 1;
	$sql = "SELECT * FROM packages WHERE category_id = $category_id";
	$result = $conn->query($sql);

	if ($result && $result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {
	?>

		<div class="pakej-row">
			<div class="pakej-img-box">
				<img src="uploads/<?php echo !empty($row['image']) ? $row['image'] : 'default.png'; ?>" alt="">
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

	<?php 
		}
	} else {
	?>

		<!-- EMPTY STATE -->
        <div class="empty-state">
            <h3>Tiada Pakej Dalam Negara Setakat Ini.</h3>
            <p>Pakej akan dikemaskini tidak lama lagi.</p>
        </div>

    <?php } ?>

</div>

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