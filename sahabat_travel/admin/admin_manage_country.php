<?php
require '../db.php';

/* =========================
   ADD COUNTRY
========================= */
if (isset($_POST['add_country'])) {
    $name = $_POST['country_name'];

    $image = time() . "_" . basename($_FILES['image']['name']);
    $target = "../uploads/" . $image;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {

        $sql = "INSERT INTO countries (country_name, country_image)
                VALUES ('$name', '$image')";

        mysqli_query($conn, $sql);

        header("Location: admin_manage_country.php");
        exit();

    } else {
        echo "<script>alert('Upload image gagal!');</script>";
    }
}

/* =========================
   DELETE COUNTRY
========================= */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // ambil image dulu
    $get = mysqli_query($conn, "SELECT country_image FROM countries WHERE country_id=$id");
    $data = mysqli_fetch_assoc($get);

    // delete image file
    if ($data && !empty($data['country_image'])) {
        $file = "../uploads/" . $data['country_image'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // delete database
    mysqli_query($conn, "DELETE FROM countries WHERE country_id=$id");

    header("Location: admin_manage_country.php");
    exit();
}

/* =========================
   GET DATA
========================= */
$result = mysqli_query($conn, "SELECT * FROM countries");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Country - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="admin_manage_country.css">
</head>

<body>

<div class="page-header">

    <a href="admin_dashboard.php" class="btn-back">← Back</a>

    <h2>Manage Country</h2>

    <a href="add_country.php" class="btn-add">+ Add Country</a>

</div>

<!-- TABLE DISPLAY -->
<table>
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Country</th>
        <th>Action</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0) { ?>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['country_id']; ?></td>

            <td>
                <img src="../uploads/<?php echo htmlspecialchars($row['country_image']); ?>" class="img-preview">
            </td>

            <td>
                <?php echo htmlspecialchars($row['country_name']); ?>
            </td>

            <td>
                <a href="edit_country.php?id=<?php echo $row['country_id']; ?>">Edit</a> |
				<a href="admin_manage_country.php?delete=<?php echo $row['country_id']; ?>" 
                   onclick="return confirm('Are you sure want to delete this country?')">Delete</a>
            </td>
        </tr>
        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="4">No country found</td>
        </tr>

    <?php } ?>

</table>

</body>
</html>