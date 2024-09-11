<!-- remove_bookmark.php -->
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$bookmark_id = $_POST['id'];

// Hapus dari daftar bookmark
$query_remove = "DELETE FROM bookmarks WHERE id='$bookmark_id' AND user_id='$user_id'";
if ($conn->query($query_remove) === TRUE) {
    header("Location: bookmark.php");
    exit;
} else {
    echo "Error deleting bookmark: " . $conn->error;
}
?>
