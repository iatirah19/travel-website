<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
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
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

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
</body>
</html>