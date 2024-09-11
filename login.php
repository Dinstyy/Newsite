<?php
session_start();
include 'db.php';

// Initialize variables
$username = "";
$error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user from database based on username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            // Redirect to index.php after successful login
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="styles.css">
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
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: url('./images/image.png') no-repeat;
    background-size: cover;
    background-position: center;
    flex-direction: column;
}

nav {
    background: var(--bg-color);
    box-shadow: 0 .1rem 1rem var(--shadow-color);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
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

.wrapper {
    width: 420px;
    background: transparent;
    border: 2px solid rgba(255, 255, 255, .2);
    backdrop-filter: blur(9px);
    color: #fff;
    border-radius: 12px;
    padding: 30px 40px;
    margin-top: 100px; /* Add margin-top to prevent overlap with navbar */
}

.wrapper h1 {
    font-size: 36px;
    text-align: center;
}

.wrapper .input-box {
    position: relative;
    width: 100%;
    height: 50px;
    margin: 30px 0;
}

.input-box input {
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    outline: none;
    border: 2px solid rgba(255, 255, 255, .2);
    border-radius: 40px;
    font-size: 16px;
    color: #fff;
    padding: 20px 45px 20px 20px;
}

.input-box input::placeholder {
    color: #fff;
}

.input-box i {
    position: absolute;
    right: 20px;
    top: 30%;
    transform: translate(-50%);
    font-size: 20px;
}

.wrapper .remember-forgot {
    display: flex;
    justify-content: space-between;
    font-size: 14.5px;
    margin: -15px 0 15px;
}

.remember-forgot label input {
    accent-color: #fff;
    margin-right: 3px;
}

.remember-forgot a {
    color: #fff;
    text-decoration: none;
}

.remember-forgot a:hover {
    text-decoration: underline;
}

.wrapper .btn {
    width: 100%;
    height: 45px;
    background: #fff;
    border: none;
    outline: none;
    border-radius: 40px;
    box-shadow: 0 0 10px rgba(0, 0, 0, .1);
    cursor: pointer;
    font-size: 16px;
    color: #333;
    font-weight: 600;
}

.wrapper .register-link {
    font-size: 14.5px;
    text-align: center;
    margin: 20px 0 15px;
}

.register-link p a {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
}

.register-link p a:hover {
    text-decoration: underline;
}

.input-box .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
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
    </nav>

    <div class="wrapper">
        <form action="login.php" method="POST">
            <h1>Login</h1>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox">Remember Me</label>
                <p><a href="forgot_password.php">Forgot password?</a></p>
            </div>
            <button type="submit" class="btn" name="login">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>
