<?php
require '../db.php';

if (!isset($_GET['id'])) {
    echo "No message selected";
    exit;
}

$id = (int) $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM contacts WHERE contact_id = $id");

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Message not found";
    exit;
}

$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Message - Sahabat International Travel Sdn Bhd</title>
    <link rel="stylesheet" href="view_message.css">
</head>

<body>

<div class="container">

    <h2>Message Details</h2>

    <div class="card">

        <p><b>Name:</b> <?php echo $row['name']; ?></p>
        <p><b>Email:</b> <?php echo $row['email']; ?></p>

        <div class="message-box">
            <b>Message:</b>
            <p><?php echo nl2br($row['message']); ?></p>
        </div>

        <a href="admin_dashboard.php" class="back-btn">← Back</a>

    </div>

</div>

</body>
</html>