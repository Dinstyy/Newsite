<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];

    $query = "UPDATE users SET username='$username', email='$email', bio='$bio' WHERE id='$user_id'";
    if ($conn->query($query) === TRUE) {
        $message = "Profile updated successfully.";
    } else {
        $error = "Error: " . $conn->error;
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $query = "UPDATE users SET password='$password' WHERE id='$user_id'";
        $conn->query($query);
    }

    if (!empty($_FILES['profile_image']['name'])) {
        $profile_image = $_FILES['profile_image']['name'];
        $temp_image = $_FILES['profile_image']['tmp_name'];
        $target_path = "images/" . basename($profile_image);

        if (move_uploaded_file($temp_image, $target_path)) {
            $query = "UPDATE users SET profile_image='$profile_image' WHERE id='$user_id'";
            if ($conn->query($query) === TRUE) {
                if (!empty($user['profile_image'])) {
                    unlink("images/" . $user['profile_image']);
                }
                $user['profile_image'] = $profile_image;
                $message = "Profile image updated successfully.";
            } else {
                $error = "Error updating profile image: " . $conn->error;
            }
        } else {
            $error = "Error uploading file.";
        }
    }

    $query = "SELECT * FROM users WHERE id='$user_id'";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
}

if (isset($_POST['delete_image'])) {
    $query = "UPDATE users SET profile_image=NULL WHERE id='$user_id'";
    if ($conn->query($query) === TRUE) {
        if (!empty($user['profile_image'])) {
            unlink("images/" . $user['profile_image']);
        }
        $user['profile_image'] = NULL;
        $message = "Profile image deleted successfully.";
    } else {
        $error = "Error deleting profile image: " . $conn->error;
    }
}

$query = "SELECT * FROM news WHERE user_id='$user_id'";
$news_result = $conn->query($query);

$query = "SELECT COUNT(*) as news_count FROM news WHERE user_id='$user_id'";
$count_result = $conn->query($query);
$count = $count_result->fetch_assoc()['news_count'];

$query = "SELECT bookmarks.*, news.title, news.image FROM bookmarks JOIN news ON bookmarks.news_id = news.id WHERE bookmarks.user_id='$user_id'";
$bookmarks_result = $conn->query($query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Add your styles here */
        body {
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 900px;
            padding: 20px;
            margin-top: 110px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .top-container, .bottom-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 20px;
            width: 100%;
        }

        .profile-header, .news-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .profile-details {
            display: flex;
            align-items: center;
        }

        .profile-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-weight: 500;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .username {
            font-size: 24px;
            font-weight: bold;
        }

        .bio {
            font-size: 16px;
            color: #666;
        }

        .news-count {
            margin-right: 20px;
            text-align: center;
        }

        .news-count span {
            display: block;
            font-size: 18px;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #754ef9;
            font-weight: 500;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }

        .edit-profile-form {
            display: none;
            flex-direction: column;
            margin-top: 20px;
        }

        .input-box {
            margin-bottom: 20px;
            position: relative;
        }

        .input-box input {
            width: 100%;
            padding: 10px 40px 10px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #333;
        }

        .news-list {
            display: flex;
            flex-direction: column;
            margin-top: 10px;
        }

        .news-item {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .news-item img {
            max-width: 100%;
            border-radius: 5px;
        }

        .news-item h3 {
            margin: 10px 0;
            color: #333;
        }

        .news-item p {
            color: #666;
        }

        .news-actions {
            margin-top: 10px;
        }

        .news-actions .btn {
            margin-top: 5px;
            color: #fff;
            font-weight: 500;
        }

        .edit-btn {
            background-color: #ffc107;
        }

        .delete-btn-news {
            background-color: #dc3545;
        }

        .upload-news-form {
            display: none;
            flex-direction: column;
            margin-top: 20px;
        }

        .message, .error {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .message {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

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
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
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
</head>
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
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

<div class="container">
    <div class="top-container">
        <div class="profile-header">
        <div class="profile-details">
    <div class="profile-image" onclick="document.getElementById('profile_image').click();">
        <?php if (!empty($user['profile_image'])): ?>
            <img src="images/<?php echo htmlspecialchars($user['profile_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Image" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
            <span>Image</span>
        <?php endif; ?>
    </div>
    <input type="file" id="profile_image" name="profile_image" style="display: none;">
    <div class="profile-info">
        <div class="username"><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="bio"><?php echo htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
</div>
            <div class="button-group">
                <div class="news-count">
                    <span><?php echo $news_result->num_rows; ?></span>
                    News
                </div>
                <button class="btn" onclick="toggleBookmark()">Bookmark</button>
                <button class="btn" onclick="toggleEditProfile()">Edit Profile</button>
            </div>
        </div>
        <form method="post" action="profile.php" enctype="multipart/form-data" class="edit-profile-form">
            <div class="input-box">
                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="input-box">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="input-box">
                <label>Bio</label>
                <input type="text" name="bio" value="<?php echo htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="input-box">
                <input type="file" id="profile_image" name="profile_image">
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" value="<?php echo htmlspecialchars($user['password'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
    <div class="bottom-container">
        <div class="news-header">
            <h3>Uploaded News</h3>
            <button class="btn" onclick="toggleUploadNews()">Upload News</button>
        </div>
        <form method="post" action="upload_news.php" enctype="multipart/form-data" class="upload-news-form">
            <div class="input-box">
                <label>Title</label>
                <input type="text" name="title">
            </div>
            <div class="input-box">
                <label>Image</label>
                <input type="file" name="image">
            </div>
            <div class="input-box">
                <label>Description</label>
                <input type="text" name="description">
            </div>
            <div class="input-box">
                <label>Created At</label>
                <input type="date" name="created_at">
            </div>
            <button type="submit" class="btn">Upload</button>
        </form>
        <form id="edit-news-form-<?php echo $news['id']; ?>" method="post" action="profile.php" enctype="multipart/form-data" class="upload-news-form">
                <input type="hidden" name="news_id" value="<?php echo $news['id']; ?>">
                <div class="input-box">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="input-box">
                    <label>Image</label>
                    <input type="file" name="image">
                </div>
                <div class="input-box">
                    <label>Description</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($news['description'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="input-box">
                    <label>Created At</label>
                    <input type="date" name="created_at" value="<?php echo htmlspecialchars($news['created_at'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn">Done</button>
            </form>
            <div class="news-list">
    <?php if ($news_result->num_rows > 0): ?>
        <?php while ($news = $news_result->fetch_assoc()): ?>
            <div class="news-item" id="news-item-<?php echo $news['id']; ?>" style="width: 400px;">
                <?php if (!empty($news['image'])): ?>
                    <img src="images/<?php echo htmlspecialchars($news['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="News Image">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($news['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="news-actions">
                    <a class="btn edit-btn" href="edit_news.php?id=<?= $news['id'] ?>">Edit News</a>
                    <button class="btn delete-btn-news" onclick="deleteNews(<?php echo $news['id']; ?>)">Delete</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Belum ada berita yang di upload.</p>
    <?php endif; ?>
</div>
    </div>
</div>
<script>
    function toggleEditProfile() {
        const form = document.querySelector('.edit-profile-form');
        form.style.display = form.style.display === 'flex' ? 'none' : 'flex';
    }

    function toggleUploadNews() {
        const form = document.querySelector('.upload-news-form');
        form.style.display = form.style.display === 'flex' ? 'none' : 'flex';
    }

    function toggleEditNews(newsId) {
        const form = document.getElementById('edit-news-form-' + newsId);
        form.style.display = form.style.display === 'block' ? 'none' : 'block';
    }

    function toggleBookmark() {
        window.location.href = 'bookmark.php';
    }

    function deleteNews(newsId) {
        if (confirm('Are you sure you want to delete this news?')) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_news.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const newsItem = document.getElementById('news-item-' + newsId);
                        newsItem.parentNode.removeChild(newsItem);
                    } else {
                        alert('Failed to delete news: ' + response.error);
                    }
                }
            };
            xhr.send("news_id=" + newsId);
        }
    }

    document.getElementById('profile_image').addEventListener('change', function (event) {
        const reader = new FileReader();
        reader.onload = function () {
            const imgElement = document.querySelector('.profile-image img');
            if (imgElement) {
                imgElement.src = reader.result;
            } else {
                const profileImageDiv = document.querySelector('.profile-image');
                profileImageDiv.innerHTML = `<img src="${reader.result}" alt="Profile Image" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
        };
        reader.readAsDataURL(event.target.files[0]);
    });
</script>
</body>
</html>