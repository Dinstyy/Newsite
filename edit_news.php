<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$news_id = $_GET['id'];
$query = "SELECT * FROM news WHERE id='$news_id' AND user_id='".$_SESSION['user_id']."'";
$result = $conn->query($query);
$news = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "images/$image");
        $query = "UPDATE news SET title='$title', description='$description', image='$image' WHERE id='$news_id'";
    } else {
        $query = "UPDATE news SET title='$title', description='$description' WHERE id='$news_id'";
    }

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
    <title>Edit News</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Mengatur font dan dasar */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        /* Mengatur container form */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 100px; /* Memberi jarak dari navbar */
            padding: 20px;
            margin-top: 30px;
        }

        form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Mengatur heading */
        form h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Mengatur label */
        form label {
            font-weight: 500;
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        /* Mengatur input text dan textarea */
        form input[type="text"],
        form textarea,
        form input[type="file"] {
            width: calc(100% - 20px); /* Mengurangi padding */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 0.9rem;
        }

        /* Mengatur textarea */
        form textarea {
            resize: none;
            height: 150px;
        }

        /* Mengatur tombol submit */
        form button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        form button:hover {
            background-color: #0056b3;
        }

        /* Mengatur pesan error */
        form p {
            color: #ff0000;
            font-size: 0.9rem;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Mengatur navbar */
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
        nav {
    background: var(--bg-color);
    box-shadow: 0 .1rem 1rem var(--shadow-color);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav .logo {
    display: flex;
    align-items: center;
    font-size: 1.5rem;
    color: #754ef9;
    font-weight: 700;
    cursor: default;
    margin-left: 30px;
}

nav .nav-links {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 15px; /* Adjusted gap between navbar items */
}

nav .nav-links li {
    margin-left: 20px;
}

nav .nav-links li a:hover {
    color: #754ef9;
}

nav .nav-links li a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
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
    border: 1px solid #333; /* Border 1px black */
    outline: none;
    font-size: 15px;
    font-weight: 600;
    border-radius: 5px; /* Border-radius for better appearance */
    padding: 9px 35px 9px 35px; /* Padding for better appearance */
    width: 350px; /* Set width to 200px */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Added shadow */
    text-indent: 13px;
}

nav .auth-links {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-right: 30px;
}

nav .auth-links a {
    padding: 0.7rem 1rem;
    background: #754ef9;
    border-radius: .6rem;
    font-size: 13px;
    color: white;
    letter-spacing: .0rem;
    font-weight: 600;
    border: .2rem solid transparent;
    transition: .5s ease;
}

nav .auth-links a:hover {
    background: transparent;
    color: #754ef9;
    border-color: #754ef9;
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

    <div class="form-container">
        <form action="edit_news.php?id=<?php echo $news_id; ?>" method="POST" enctype="multipart/form-data">
            <h2>Edit News</h2>
            <?php if (isset($error)): ?>
                <p><?php echo $error; ?></p>
            <?php endif; ?>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $news['title']; ?>" required>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo $news['description']; ?></textarea>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image">
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
