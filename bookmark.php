<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data bookmark dari database
$query = "SELECT bookmarks.*, news.title, news.image FROM bookmarks JOIN news ON bookmarks.news_id = news.id WHERE bookmarks.user_id='$user_id'";
$bookmarks_result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookmarks</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    text-decoration: none;
    border: none;
    outline: none;
    font-family: 'Poppins', sans-serif;
    scrollbar-width: none;
}

        .bookmark-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 900px;
            padding: 20px;
            margin: 20px auto;
        }

        .bookmark-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bookmark-list {
            display: flex;
            flex-direction: column;
            margin-top: 10px;
        }

        .bookmark-item {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .bookmark-item img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
            margin-right: 20px;
        }

        .bookmark-item h3 {
            margin: 0;
            color: #333;
        }

        .bookmark-actions {
            margin-left: auto;
        }

        .unlike-btn {
            background-color: #dc3545;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
        }

        .empty-message {
            text-align: center;
            margin-top: 50px;
        }

        .empty-message p {
            font-size: 15px;
            color: #666;
        }

        .explore-btn {
            background-color: #754ef9;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
<?php include 'db.php'; ?>
    <div class="bookmark-container">
        <div class="bookmark-header">
            <h2>Bookmarks</h2>
        </div>
        <?php if ($bookmarks_result->num_rows > 0): ?>
            <div class="bookmark-list">
                <?php while ($bookmark = $bookmarks_result->fetch_assoc()): ?>
                    <div class="bookmark-item">
                        <img src="images/<?php echo $bookmark['image']; ?>" alt="Bookmark Image">
                        <h3><?php echo $bookmark['title']; ?></h3>
                        <div class="bookmark-actions">
                            <form action="remove_bookmark.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $bookmark['id']; ?>">
                                <button type="submit" class="unlike-btn">Unlike</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <p>Belum ada yang disukai, eksplor berita untuk melihat berita yang mungkin Anda sukai.</p>
                <a href="index.php" class="explore-btn">Telusuri</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>