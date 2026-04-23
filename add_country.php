<?php
require '../db.php';

if (isset($_POST['add_country'])) {

    $name = mysqli_real_escape_string($conn, $_POST['country_name']);

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

        $sql = "INSERT INTO countries (country_name, country_image)
                VALUES ('$name', '$newImageName')";

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
    <title>Add Country - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="add_country.css">
</head>

<body>

<div class="card">
    <h2>🌍 Add Country</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Country Name</label>
        <input type="text" name="country_name" placeholder="Enter country name" required>

        <label>Country Image</label>
        <input type="file" name="image" required>

        <button type="submit" name="add_country">+ Add Country</button>
    </form>

    <div class="back">
        <a href="admin_manage_country.php">← Back to Manage Country</a>
    </div>
</div>

</body>
</html>