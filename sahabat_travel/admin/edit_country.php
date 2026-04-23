<?php
require '../db.php';

/* =========================
   GET OLD DATA
========================= */
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = $_GET['id'];

$sql = "SELECT * FROM countries WHERE country_id = $id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Country not found");
}

/* =========================
   UPDATE PROCESS
========================= */
if (isset($_POST['update_country'])) {

    $name = $_POST['country_name'];

    $oldName = $row['country_name'];
    $oldImage = $row['country_image'];

    $newImageUploaded = !empty($_FILES['image']['name']);

    $changes = false;
    $finalImage = $oldImage;

    // check nama berubah
    if ($name != $oldName) {
        $changes = true;
    }

    // check image baru
    if ($newImageUploaded) {

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        $folder = "../uploads/" . basename($image);

        if (move_uploaded_file($tmp, $folder)) {
            $finalImage = $image;
            $changes = true;
        } else {
            echo "<script>alert('Upload image gagal!');</script>";
            exit;
        }
    }

    // kalau ada perubahan
    if ($changes) {

        $sql = "UPDATE countries 
                SET country_name='$name', country_image='$finalImage'
                WHERE country_id=$id";

        mysqli_query($conn, $sql);

        echo "<script>
                alert('Country berjaya diupdate!');
                window.location.href='admin_manage_country.php';
              </script>";

    } else {

        echo "<script>
                alert('Tiada perubahan berlaku');
                window.location.href='admin_manage_country.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Country - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="edit_country.css">
</head>

<body>

<div class="card">
    <h2>✏️ Edit Country</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Country Name</label>
        <input type="text" name="country_name" 
               value="<?php echo $row['country_name']; ?>" required>

        <label>Current Image</label><br>
        <img src="../uploads/<?php echo $row['country_image']; ?>" width="100"><br><br>

        <label>Change Image (optional)</label>
        <input type="file" name="image">

        <button type="submit" name="update_country">Update Country</button>
    </form>

    <div class="back">
        <a href="admin_manage_country.php">← Back to Manage Country</a>
    </div>
</div>

</body>
</html>