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

:root {
    --bg-color: #fdfdfd;
    --text-color: #333;
    --main-color: #754ef9;
    --white-color: #fdfdfd;
    --shadow-color: rgba(0, 0, 0, .2);
}

body {
    background: var(--bg-color);
    color: var(--text-color);
    line-height: 1.6;
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

.news-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 50px;
    top: 20px;
}

.news-item {
    flex: 0 0 calc(33.333% - 20px);
    background: var(--white-color);
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.news-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.news-item img {
    width: 80%;
    height: auto;
    border-radius: 10px;
}

.news-item h3 {
    font-size: 1em;
    margin: 10px 0;
}

.news-item p {
    font-size: 1em;
    margin: 10px 0;
}

.news-item a {
    color: #754ef9;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
}

.news-item a:hover {
    text-decoration: underline;
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
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="news-item">
                <h3><?php echo $row['title']; ?></h3>
                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['title']; ?>">
                <p><?php echo substr($row['description'], 0, 100); ?>...</p>
                <a href="news_detail.php?id=<?php echo $row['id']; ?>">Read more</a>
            </div>
        <?php endwhile; ?>
    </div>
    <script>
                function toggleBookmark(newsId) {
            // Kirim permintaan AJAX untuk menambahkan ke bookmark
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "add_bookmark.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Respons setelah berhasil menambahkan ke bookmark
                    var response = xhr.responseText;
                    if (response === "success") {
                        alert("Berita ditambahkan ke bookmark!");
                    } else {
                        alert("Gagal menambahkan ke bookmark.");
                    }
                }
            };
            xhr.send("news_id=" + encodeURIComponent(newsId));
        }
    </script>
</body>
</html>
