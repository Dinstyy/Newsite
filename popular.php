<?php
session_start();
include 'db.php';

// Query to get news
$query = "SELECT * FROM news ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <nav>
        <div class="logo">HotNews</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="new.php">Terbaru</a></li>
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
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="news-item">
                <h3><?php echo $row['title']; ?></h3>
                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                <p><?php echo substr($row['description'], 0, 100); ?>...</p>
                <a href="news_detail.php?id=<?php echo $row['id']; ?>">Read more</a>
            </div>
        <?php endwhile; ?>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
