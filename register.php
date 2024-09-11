<?php
session_start();
include 'db.php';

// Initialize variables
$username = "";
$email = "";
$error = "";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password for security

    // Check if username already exists
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        // Insert new user into database
        $insert_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
        if ($conn->query($insert_query) === TRUE) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            $error = "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: url('./images/image.png') no-repeat;
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .wrapper {
            width: 420px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(9px);
            color: #fff;
            border-radius: 12px;
            padding: 30px 40px;
            margin-top: 80px; /* Adjusted margin to separate from navbar */
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
        }

        .input-box {
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

        .remember-forgot {
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

        .btn {
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

        .register-link {
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

        /* CSS Navbar */
        nav {
            background: white;
            box-shadow: 0 .1rem 1rem var(--shadow-color);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
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
            border: 1px solid #333;
            outline: none;
            font-size: 15px;
            font-weight: 600;
            border-radius: 5px;
            padding: 9px 35px 9px 35px;
            width: 350px;
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
            padding: 0.7rem 1rem;
            background: #754ef9;
            border-radius: .6rem;
            font-size: 13px;
            color: white;
            letter-spacing: .0rem;
            font-weight: 600;
            border: .2rem solid transparent;
            transition: .5s ease;
            text-decoration: none;
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
                justify-content: center;
            }

            nav .nav-links, nav .auth-links {
                flex-direction: column;
                display: none;
                width: 100%;
                text-align: center;
            }

            nav .nav-links.nav-links-responsive, nav .auth-links.nav-links-responsive {
                display: flex;
                gap: 10px;
                margin-top: 10px;
            }

            nav .nav-toggle {
                display: block;
                cursor: pointer;
                margin-right: 30px;
            }

            nav .search-form {
                margin: 10px auto;
            }

            .wrapper {
                margin-top: 80px; /* Adjusted margin to separate from navbar in mobile view */
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
    </nav>

    <div class="wrapper">
        <form action="register.php" method="POST">
            <h1>Register</h1>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" class="btn" name="register">Register</button>
            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
