<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$comment_id = $_GET['id'];
$news_id = $_GET['news_id'];

// Check if the current user is the author of the news
$query = "SELECT user_id FROM news WHERE id='$news_id'";
$result = $conn->query($query);
$news = $result->fetch_assoc();

if ($_SESSION['user_id'] != $news['user_id']) {
    echo "You are not authorized to delete this comment.";
    exit;
}

$query = "DELETE FROM comments WHERE id='$comment_id'";
if ($conn->query($query) === TRUE) {
    header("Location: news_detail.php?id=$news_id");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>
