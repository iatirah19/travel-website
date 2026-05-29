<?php
require '../db.php';

session_start();

if ($_SESSION['role'] != 'admin') {
    header("Location: homepage.php");
    exit();
}

// CHECK ID
if (!isset($_GET['id'])) {
    die("Country ID not found.");
}

$country_id = intval($_GET['id']);

// FETCH COUNTRY
$query = mysqli_query($conn, "SELECT * FROM countries WHERE country_id = '$country_id'");
$country = mysqli_fetch_assoc($query);

if (!$country) {
    die("Country not found.");
}


/*
|--------------------------------------------------------------------------
| IMAGE PATH (FIX FOR DISPLAY)
|--------------------------------------------------------------------------
*/
$image = $country['country_image'] ?? '';

// remove "uploads/" if accidentally stored in DB
$image = str_replace('uploads/', '', $image);

// build path
$imagePath = "../uploads/" . $image;

// fallback image
if (empty($image) || !file_exists($imagePath)) {
    $imagePath = "../uploads/default.png";
}


/*
|--------------------------------------------------------------------------
| UPDATE COUNTRY
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_country'])) {

    $name = mysqli_real_escape_string($conn, $_POST['country_name']);

    // generate slug
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');

    // current data
    $oldName = $country['country_name'];
    $oldSlug = $country['country_slug'];
    $oldImage = $country['country_image'];

    // default image
    $newImageName = $oldImage;
    $imageChanged = false;

    /*
    |--------------------------------------------------------------------------
    | HANDLE IMAGE UPLOAD
    |--------------------------------------------------------------------------
    */
    if (!empty($_FILES['image']['name'])) {

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            echo "<script>alert('File type tidak dibenarkan');</script>";
            exit;
        }

        $newImageName = time() . "_" . preg_replace('/[^a-zA-Z0-9.]/', '_', $image);

        move_uploaded_file($tmp, "../uploads/" . $newImageName);

        // delete old image
        if (!empty($oldImage)) {

            $oldImageClean = str_replace('uploads/', '', $oldImage);
            $oldPath = "../uploads/" . $oldImageClean;

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $imageChanged = true;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK IF ANY CHANGE
    |--------------------------------------------------------------------------
    */
    $dataChanged =
        ($name != $oldName) ||
        ($slug != $oldSlug) ||
        $imageChanged;

    if (!$dataChanged) {

        echo "
        <script>
            alert('Tiada perubahan untuk country ini.');
            window.location.href='admin_manage_country.php';
        </script>
        ";
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    */
    $sql = "UPDATE countries SET
            country_name = '$name',
            country_slug = '$slug',
            country_image = '$newImageName'
            WHERE country_id = '$country_id'";

    mysqli_query($conn, $sql);

    echo "
    <script>
        alert('Country berjaya dikemaskini!');
        window.location.href='admin_manage_country.php';
    </script>
    ";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Country - Sahabat International Travel</title>
    <link rel="icon" type="image/png" href="../picture/LOGO.png">
    <link rel="stylesheet" href="edit_country.css">
</head>

<body>

<div class="page-wrapper">

    <div class="top-header">
        <h1>Edit Country</h1>
        <p>Dashboard > Countries > Edit Country</p>
    </div>

    <div class="form-card">

        <form method="POST" enctype="multipart/form-data">

            <!-- LEFT -->
            <div class="left-side">

                <h3>Country Image</h3>

                <label class="upload-box">

                    <input type="file"
                           name="image"
                           id="imageInput"
                           hidden>

                    <div class="upload-content">

                        <div class="upload-icon">☁</div>

                        <p>Drag & drop image here</p>

                        <span>or click to browse</span>

                    </div>

                </label>

                <!-- CURRENT IMAGE -->
                <img id="previewImage"
                     src="<?= $imagePath; ?>"
                     alt="Country Image"
                     style="width:100%; height:190px; display:block; margin-top:10px; border-radius:8px;">

            </div>

            <!-- RIGHT -->
            <div class="right-side">

                <div class="form-group">

                    <label>Country Name</label>

                    <input type="text"
                           name="country_name"
                           id="countryName"
                           value="<?= htmlspecialchars($country['country_name']); ?>"
                           required>

                </div>

                <div class="form-group">

                    <label>Slug</label>

                    <input type="text"
                           id="slug"
                           value="<?= htmlspecialchars($country['country_slug']); ?>"
                           readonly>

                    <small>
                        The “Slug” is the URL-friendly version of the name.
                    </small>

                </div>

                <div class="button-group">

                    <a href="admin_manage_country.php"
                       class="cancel-btn">

                        Cancel

                    </a>

                    <button type="submit"
                            name="update_country"
                            class="save-btn">

                        Update Country

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<script>

// AUTO SLUG
const countryName = document.getElementById("countryName");
const slug = document.getElementById("slug");

countryName.addEventListener("keyup", function(){

    let value = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    slug.value = value;

});

// IMAGE PREVIEW
const imageInput = document.getElementById("imageInput");
const previewImage = document.getElementById("previewImage");

imageInput.addEventListener("change", function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){

            previewImage.src = e.target.result;

        }

        reader.readAsDataURL(file);

    }

});

</script>

</body>
</html>