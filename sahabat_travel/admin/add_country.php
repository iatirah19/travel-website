<?php
require '../db.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: homepage.php");
    exit();
}

if (isset($_POST['add_country'])) {

    $name = mysqli_real_escape_string($conn, $_POST['country_name']);

    // generate slug
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');

    // file info
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    // create unique file name
    $newImageName = time() . "_" . basename($image);
    $folder = "../uploads/" . $newImageName;

    // allowed file type
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($newImageName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('File type tidak dibenarkan!');</script>";
        exit;
    }

    if (move_uploaded_file($tmp, $folder)) {

        $sql = "INSERT INTO countries 
                (country_name, country_slug, country_image)
                VALUES 
                ('$name', '$slug', '$newImageName')";

        mysqli_query($conn, $sql);

        echo "<script>
                alert('Country berjaya ditambah!');
                window.location.href='admin_manage_country.php';
              </script>";

    } else {
        echo "<script>alert('Upload image gagal!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Country - Sahabat International Travel</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="add_country.css">
</head>

<body>

<div class="page-wrapper">

    <div class="top-header">
        <h1>Add Country</h1>
        <p>Dashboard > Countries > Add Country</p>
    </div>

    <div class="form-card">

        <form method="POST" enctype="multipart/form-data">

            <!-- LEFT -->
            <div class="left-side">

                <h3>Country Image</h3>

                <label class="upload-box">

                    <input type="file" name="image" id="imageInput" hidden required>

                    <div class="upload-content">
                        <div class="upload-icon">☁</div>
                        <p>Drag & drop image here</p>
                        <span>or click to browse</span>
                    </div>

                </label>

                <img id="previewImage" src="" alt="">

            </div>

            <!-- RIGHT -->
            <div class="right-side">

                <div class="form-group">
                    <label>Country Name</label>
                    <input type="text" name="country_name" placeholder="Enter country name" required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" id="slug" readonly>
                    <small>The “Slug” is the URL-friendly version of the name.</small>
                </div>

                <div class="button-group">
                    <a href="admin_manage_country.php" class="cancel-btn">Cancel</a>
                    <button type="submit" name="add_country" class="save-btn">
                        Save Country
                    </button>
                </div>

            </div>

        </form>

    </div>

</div>

<script>

// AUTO SLUG
const countryInput = document.querySelector('[name="country_name"]');
const slugInput = document.getElementById('slug');

countryInput.addEventListener('keyup', () => {

    let slug = countryInput.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    slugInput.value = slug;

});

// IMAGE PREVIEW
const imageInput = document.getElementById('imageInput');
const previewImage = document.getElementById('previewImage');

imageInput.addEventListener('change', function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
        }

        reader.readAsDataURL(file);
    }

});

</script>

</body>
</html>