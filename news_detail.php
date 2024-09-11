<?php
session_start();
include 'db.php';

$news_id = $_GET['id'];
$query = "SELECT news.*, users.username FROM news JOIN users ON news.user_id = users.id WHERE news.id='$news_id'";
$result = $conn->query($query);
$news = $result->fetch_assoc();

// Check if the current user is the author of the news
$is_author = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $news['user_id'];

$comments_query = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE news_id='$news_id'";
$comments_result = $conn->query($comments_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['comment'])) {
        $comment = $_POST['comment'];
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO comments (news_id, user_id, comment) VALUES ('$news_id', '$user_id', '$comment')";
        $conn->query($query);
        header("Location: news_detail.php?id=$news_id");
        exit;
    } elseif (isset($_POST['like'])) {
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO likes (news_id, user_id) VALUES ('$news_id', '$user_id')";
        $conn->query($query);
        header("Location: news_detail.php?id=$news_id");
        exit;
    }
}

$likes_query = "SELECT COUNT(*) as likes_count FROM likes WHERE news_id='$news_id'";
$likes_result = $conn->query($likes_query);
$likes_count = $likes_result->fetch_assoc()['likes_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

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

:root {
    --bg-color: #fdfdfd;
    --text-color: #333;
    --main-color: #754ef9;
    --white-color: #fdfdfd;
    --shadow-color: rgba(0, 0, 0, .2);
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
}

/* Mengatur container umum untuk news list */
.news-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

/* Mengatur setiap item berita */
.news-item {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    width: 100%;
    max-width: 350px;
}

.news-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

/* Mengatur gambar dalam item berita */
.news-item img {
    width: 100%;
    height: auto;
}

/* Mengatur teks dalam item berita */
.news-item-content {
    padding: 15px;
}

.news-item h3 {
    font-size: 1.40rem;
    color: #333;
    margin: 10px 0;
    line-height: 1.4;
}

.news-item p {
    color: #777;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 10px;
}

/* Tombol aksi dalam item berita */
.news-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 15px 15px 15px;
}

.news-actions .btn {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.3s, color 0.3s;
}

.news-actions .btn-read-more {
    background-color: #007bff;
    color: #fff;
}

.news-actions .btn-read-more:hover {
    background-color: #0056b3;
    color: #fff;
}

.news-actions .btn-edit {
    background-color: #ffc107;
    color: #333;
}

.news-actions .btn-edit:hover {
    background-color: #e0a800;
    color: #333;
}

.news-actions .btn-delete {
    background-color: #dc3545;
    color: #fff;
}

.news-actions .btn-delete:hover {
    background-color: #c82333;
    color: #fff;
}

/* Responsive design */
@media (max-width: 768px) {
    .news-container {
        justify-content: center;
    }

    .news-item {
        max-width: 100%;
    }
}

/* Mengatur container untuk halaman detail berita */
.news-detail {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
}

/* Mengatur form komentar */
.comment-form {
    margin-top: 20px;
}

.comment-form textarea {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 10px;
    font-size: 1rem;
    line-height: 1.5;
    resize: none; /* Mencegah perubahan ukuran textarea */
}

/* Mengatur tampilan komentar */
.comments {
    margin-top: 20px;
}

.comment {
    background-color: #f4f4f4;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 10px;
}

.comment p {
    margin: 5px 0;
    font-size: 0.9rem;
    color: #555;
}

nav {
    background: var(--bg-color);
    box-shadow: 0 .1rem 1rem var(--shadow-color);
    padding: 17px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

nav .logo {
    font-size: 1.3rem;
    color: var(--main-color);
    font-weight: 700;
    margin-left: 30px;
}

nav .nav-links {
    list-style: none;
    display: flex;
    gap: 10px;
    font-size: 14px;
}

nav .nav-links li {
    margin-left: 20px;
}

nav .nav-links li a {
    color: var(--text-color);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
}

nav .nav-links li a:hover {
    color: var(--main-color);
}

.bg2 {
            display: flex;
            align-items: center;
            position: relative;
        }

        .bg2 i {
            font-size: 20px;
            position: absolute;
            left: 15px;
            top: 10px;
            color: #333;
        }

        .search-input {
            border: 1px solid #333;
            outline: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 5px;
            padding: 7px 30px 7px 30px;
            width: 300px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-indent: 13px;
        }

nav .auth-links {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-right: 30px;
}

nav .auth-links a {
    padding: 0.5rem 1rem;
    background: var(--main-color);
    border-radius: .6rem;
    font-size: 13px;
    color: var(--white-color);
    font-weight: 600;
    border: .2rem solid transparent;
    transition: .5s ease;
}

nav .auth-links a:hover {
    background: transparent;
    color: var(--main-color);
    border-color: var(--main-color);
}

.nav-toggle {
    display: none;
}

@media (max-width: 768px) {
    nav {
        flex-wrap: wrap;
    }

    nav .nav-links, nav .auth-links {
        flex-direction: column;
        display: none;
        width: 100%;
        text-align: center;
    }

    nav .nav-links.nav-links-responsive, nav .auth-links.nav-links-responsive {
        display: flex;
    }

    nav .nav-toggle {
        display: block;
        cursor: pointer;
    }

    nav .search-form {
        margin: 10px auto;
    }
}
</style>
<body>
<nav>
    <div class="logo">HotNews</div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="latest.php">Terbaru</a></li>
        <li><a href="popular.php">Populer</a></li>
        <li><a href="politics.php">Politik</a></li>
        <li><a href="sports.php">Olahraga</a></li>
        <li><a href="international.php">International</a></li>
    </ul>
    <div class="bg2">
        <form class="search-form" action="search.php" method="GET">
            <input type="text" class="search-input" placeholder="Search">
            <i class='bx bx-search'></i>
        </form>
    </div>
    <div class="auth-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>
<div class="news-container">
    <div class="news-item news-detail">
        <img src="images/<?php echo htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="News Image">
        <div class="news-item-content">
            <h3><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p><?php echo htmlspecialchars($news['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Posted on: <?php echo htmlspecialchars($news['created_at'], ENT_QUOTES, 'UTF-8'); ?> by <?php echo htmlspecialchars($news['username'], ENT_QUOTES, 'UTF-8'); ?></p>
            <form action="news_detail.php?id=<?php echo $news_id; ?>" method="POST">
                <button type="submit" name="like" class="btn btn-read-more">Like</button> <?php echo $likes_count; ?> Likes
            </form>
            <h3>Comments</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="news_detail.php?id=<?php echo $news_id; ?>" method="POST" class="comment-form">
                    <textarea name="comment" required placeholder="Add a comment..."></textarea>
                    <button type="submit" class="btn btn-read-more">Comment</button>
                </form>
            <?php else: ?>
                <p><a href="login.php" class="btn btn-read-more">Login</a> to comment.</p>
            <?php endif; ?>
            <div class="comments">
                <?php while ($comment = $comments_result->fetch_assoc()): ?>
                    <div class="comment">
                        <p><?php echo htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p>by <?php echo htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8'); ?> on <?php echo htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php if ($is_author): ?>
                            <a href="delete_comment.php?id=<?php echo $comment['id']; ?>&news_id=<?php echo $news_id; ?>" class="btn btn-delete">Delete</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
