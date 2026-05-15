<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require 'db.php';
?>

<!-- =========================
     NAVBAR
========================= -->
<header>

    <?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar <?php echo ($currentPage == 'homepage.php') ? 'navbar-light' : 'navbar-dark'; ?>">

        <!-- LOGO -->
        <div class="logo">
            <a href="homepage.php">
                <img src="picture/LOGO-SAHABAT-BACKGROUND-WHITE.PNG" alt="Logo">
            </a>
        </div>

        <!-- MENU (DESKTOP) -->
        <ul class="nav-links" id="navLinks">

            <li><a href="homepage.php">Home</a></li>

            <li><a href="about.php">About Us</a></li>

            <!-- DROPDOWN -->
            <li class="dropdown">

                <button class="dropdown-btn">
                    Packages
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

                <ul class="dropdown-menu">

                    <li>
                        <a href="domestic.php">Domestic Package</a>
                    </li>

                    <!-- SUB DROPDOWN -->
                    <li class="sub-dropdown">

                        <button class="sub-dropdown-btn">
                            International Package
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <ul class="sub-dropdown-menu">

                            <?php
                            $result = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");

                            while($row = mysqli_fetch_assoc($result)){
                            ?>

                                <li>
                                    <a href="country.php?slug=<?= $row['country_slug']; ?>">
                                        <?= $row['country_name']; ?>
                                    </a>
                                </li>

                            <?php } ?>

                        </ul>

                    </li>

                    <li>
                        <a href="umrah.php">Umrah Package</a>
                    </li>

                </ul>

            </li>

            <li><a href="review.php">Review</a></li>

            <li><a href="contact.php">Contact</a></li>

        </ul>

        <!-- AUTH -->
        <div class="nav-btn">

            <?php if(isset($_SESSION['user_id'])): ?>

                <!-- PROFILE MENU -->
                <div class="profile-dropdown">

                    <button class="profile-btn">

                        <i class="fa-solid fa-user"></i>

                        <span>
                            <?php echo $_SESSION['username']; ?>
                        </span>

                        <i class="fa-solid fa-chevron-down"></i>

                    </button>

                    <div class="profile-dropdown-menu">

                        <a href="profile.php">
                            <i class="fa-solid fa-user"></i>
                            Profile
                        </a>

                        <a href="mybooking.php">
                            <i class="fa-solid fa-suitcase"></i>
                            My Booking
                        </a>

                        <a href="logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </a>

                    </div>

                </div>

            <?php else: ?>

                <!-- LOGIN / REGISTER -->
                <div class="auth-btn">

                    <a href="login.php" class="btn login-btn">
                        Login
                    </a>

                    <a href="register.php" class="btn register-btn">
                        Register
                    </a>

                </div>

            <?php endif; ?>

        </div>

        <!-- MOBILE TOGGLE -->
        <div class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
        </div>

    </nav>

</header>

<!-- =========================
     MOBILE SIDEBAR
========================= -->
<div class="mobile-sidebar" id="mobileSidebar">

    <!-- CLOSE -->
    <div class="close-btn" id="closeMenu">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <ul>

        <li>
            <a href="homepage.php">Home</a>
        </li>

        <li>
            <a href="about.php">About Us</a>
        </li>

        <!-- MOBILE DROPDOWN -->
        <li class="mobile-dropdown">

            <div class="mobile-dropdown-btn">

                Packages

                <i class="fa-solid fa-chevron-down"></i>

            </div>

            <ul class="mobile-dropdown-menu">

                <li>
                    <a href="domestic.php">Domestic Package</a>
                </li>

                <!-- MOBILE SUB -->
                <li class="mobile-sub-dropdown">

                    <div class="mobile-sub-btn">

                        International Package

                        <i class="fa-solid fa-chevron-down"></i>

                    </div>

                    <ul class="mobile-sub-menu">

                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM countries ORDER BY country_name ASC");

                        while($row = mysqli_fetch_assoc($result)){
                        ?>

                            <li>
                                <a href="country.php?slug=<?= $row['country_slug']; ?>">
                                    <?= $row['country_name']; ?>
                                </a>
                            </li>

                        <?php } ?>

                    </ul>

                </li>

                <li>
                    <a href="umrah.php">Umrah Package</a>
                </li>

            </ul>

        </li>

        <li>
            <a href="review.php">Review</a>
        </li>

        <li>
            <a href="contact.php">Contact</a>
        </li>

        <!-- MOBILE AUTH -->
        <?php if(isset($_SESSION['user_id'])): ?>

            <li>
                <a href="profile.php">
                    Profile
                </a>
            </li>

            <li>
                <a href="mybooking.php">
                    My Booking
                </a>
            </li>

            <li>
                <a href="logout.php">
                    Logout
                </a>
            </li>

        <?php else: ?>

            <li>
                <a href="login.php">
                    Login
                </a>
            </li>

            <li>
                <a href="register.php">
                    Register
                </a>
            </li>

        <?php endif; ?>

    </ul>
</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

<!-- =========================
     JAVASCRIPT
========================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    /* MOBILE SIDEBAR */
    const menuToggle = document.getElementById("menuToggle");
    const mobileSidebar = document.getElementById("mobileSidebar");
    const closeMenu = document.getElementById("closeMenu");
    const overlay = document.getElementById("overlay");

    menuToggle.addEventListener("click", () => {
        mobileSidebar.classList.add("active");
        overlay.classList.add("active");
    });

    closeMenu.addEventListener("click", () => {
        mobileSidebar.classList.remove("active");
        overlay.classList.remove("active");
    });

    overlay.addEventListener("click", () => {
        mobileSidebar.classList.remove("active");
        overlay.classList.remove("active");
    });

    /* MOBILE DROPDOWN (FIXED) */
    const mobileDropdown = document.querySelector(".mobile-dropdown");
    const mobileDropdownBtn = document.querySelector(".mobile-dropdown-btn");

    mobileDropdownBtn.addEventListener("click", () => {
        mobileDropdown.classList.toggle("active");
    });

    /* MOBILE SUB DROPDOWN (FIXED) */
    const mobileSubDropdown = document.querySelector(".mobile-sub-dropdown");
    const mobileSubBtn = document.querySelector(".mobile-sub-btn");

    mobileSubBtn.addEventListener("click", () => {
        mobileSubDropdown.classList.toggle("active");
    });

});
</script>