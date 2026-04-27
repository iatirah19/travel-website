<?php
require '../db.php';

// DELETE (same page)
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    mysqli_query($conn, "DELETE FROM reviews WHERE review_id = '$id'");

    // redirect supaya tak repeat delete bila refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

/* =========================
   ADD REVIEW
========================= */
if (isset($_POST['add_review'])) {

    $name = $_POST['name'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    mysqli_query($conn, "
        INSERT INTO reviews (name, star, message, created_at)
        VALUES ('$name', '$rating', '$comment', NOW())
    ");

    header("Location: admin_manage_review.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Reviews - Sahabat International Travel Sdn Bhd</title>
	<link rel="stylesheet" href="admin_manage_review.css">
</head>
<body>

<div class="main-wrapper">
    
    <form method="POST" class="review-form">
        <h2>💬 Add Review</h2>
        
        <input type="text" name="name" placeholder="Nama Pelanggan" required>

        <select name="rating" required>
            <option value="">Pilih Rating</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="2">⭐⭐</option>
            <option value="1">⭐</option>
        </select>

        <textarea name="comment" placeholder="Tulis komen anda di sini..." required></textarea>

        <button type="submit" name="add_review">Hantar Komen</button>
    </form>

    <hr style="border: 0; height: 1px; background: #ddd; margin: 30px 0;">

    <div class="reviews-list-container">
		<h2>⭐ Semua Review Pelanggan</h2>

		<table class="review-table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Star</th>
					<th>Message</th>
                    <th>Action</th>
				</tr>
			</thead>

			<tbody>
			<?php
			$reviews = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");

			while ($row = mysqli_fetch_assoc($reviews)) {
			?>
				<tr>
					<td><?php echo htmlspecialchars($row['name']); ?></td>

					<td class="stars">
						<?php echo str_repeat("⭐", (int)$row['star']); ?>
					</td>

					<td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>

                    <td>
                        <a href="?delete_id=<?php echo $row['review_id']; ?>"
                        onclick="return confirm('Are you sure want to delete this review?')"
                        class="btn-delete">
                        Delete
                        </a>
                    </td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

</div>

</body>
</html>