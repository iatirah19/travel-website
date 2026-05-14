<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <!-- TOGGLE BUTTON -->
<div class="menu-toggle" id="menuToggle">
    <i class="fa-solid fa-bars"></i>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <!-- CLOSE BUTTON -->
    <div class="close-btn" id="closeBtn">
        <i class="fa-solid fa-xmark"></i>
    </div>

    <h2 class="logo">Admin Panel</h2>

    <ul>
        <li><a href="#"><i class="fa-solid fa-house"></i> Dashboard</a></li>
        <li><a href="#"><i class="fa-solid fa-earth-asia"></i> Manage Country</a></li>
        <li><a href="#"><i class="fa-solid fa-box"></i> Manage Package</a></li>
        <li><a href="#"><i class="fa-solid fa-star"></i> Manage Review</a></li>
        <li><a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay"></div>

<script>

const menuToggle = document.getElementById("menuToggle");
const sidebar = document.getElementById("sidebar");
const closeBtn = document.getElementById("closeBtn");
const overlay = document.getElementById("overlay");

/* OPEN SIDEBAR */
menuToggle.addEventListener("click", () => {
    sidebar.classList.add("active");
    overlay.classList.add("active");
});

/* CLOSE SIDEBAR */
closeBtn.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

/* CLOSE WHEN CLICK OVERLAY */
overlay.addEventListener("click", () => {
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
});

</script>
</body>
</html>