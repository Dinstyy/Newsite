<?php
session_start();
include 'db.php';

if (isset($_POST['forgot_password'])) {
    $email = $_POST['email'];

    // Just to simulate a user check, we assume a successful scenario.
    // In a real-world scenario, you would check if the user exists and handle errors appropriately.
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Fetch user data and handle password update
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $query = "UPDATE users SET password=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $password, $user_id);

            if ($stmt->execute()) {
                $message = "Password updated successfully.";
            } else {
                $error = "Error updating password: " . $stmt->error;
            }
        }
    } else {
        $error = "No user found with that email.";
    }

    // Redirect after processing
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
                * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('./images/image.png') no-repeat;
            background-size: cover;
            background-position: center;
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
            margin-bottom: 40px;
        }

        .back {
            font-size: 14.5px;
            text-align: center;
            margin: -15px 0 15px;
        }

        .back a {
            color: #fff;
            text-decoration: none;
        }

        .back a:hover {
            text-decoration: underline;
        }

        /* CSS Navbar */
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
        <form action="forgot_password.php" method="POST" class="auth-form">
            <h1>Forgot Password</h1>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="New Password" required>
                <i class='bx bxs-lock-open' ></i>            
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <i class='bx bxs-lock' ></i>            
            </div>
            <button type="submit" class="btn" name="forgot_password">Submit</button>
            <div class="back">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
