<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/$image");
    }

    $query = "INSERT INTO news (title, description, image, user_id) VALUES ('$title', '$description', '$image', '$user_id')";
    if ($conn->query($query) === TRUE) {
        header("Location: profile.php");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload News</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <form action="upload_news.php" method="POST" enctype="multipart/form-data">
        <h2>Upload News</h2>
        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="image">Image:</label>
        <input type="file" id="image" name="image">
        <button type="submit">Upload</button>
    </form>
</body>
</html>
