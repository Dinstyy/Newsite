<!-- add_bookmark.php -->
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "error";
    exit;
}

$user_id = $_SESSION['user_id'];
$news_id = $_POST['news_id'];

// Periksa apakah berita sudah ada di bookmark
$query_check = "SELECT * FROM bookmarks WHERE user_id='$user_id' AND news_id='$news_id'";
$result_check = $conn->query($query_check);

if ($result_check->num_rows > 0) {
    echo "exists";
    exit;
}

// Tambahkan berita ke bookmark
$query_add = "INSERT INTO bookmarks (user_id, news_id) VALUES ('$user_id', '$news_id')";
if ($conn->query($query_add) === TRUE) {
    echo "success";
} else {
    echo "error";
}
?>
