<?php
session_start();
require 'config.php'; // Panggil config.php untuk koneksi dan pengecekan database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = htmlspecialchars($_POST['emailOrUsername']);
    $password = $_POST['password'];
    
    // Cek apakah email atau username terdaftar
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php"); // Redirect ke dashboard
        exit();
    } else {
        $error_message = "Wrong username/email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DoNow.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General style */
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-weight: 400;
            font-style: normal;
            background: #ffffff;
            overflow-y: auto; /* Allow scrolling */
        }

        .container-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            padding: 0 20px;
            gap: 15px;
            padding-bottom: 20px; /* Add bottom padding to prevent button from overlapping footer */
        }

        /* Left side content */
        .left-content {
            max-width: 45%;
            margin-left: 90px;
            margin-bottom: 10px; /* Ensure spacing below on small screens */
            order: 1;
            text-align: left;
            display: flex;
            flex-direction: column;
        }

        .left-content h1 {
            font-size: 1.7rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .form-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #666;
            outline: none;
            padding: 10px 1px;
            font-size: 1rem;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1px;
            align-items: center; /* Untuk memastikan form berada di tengah */
        }

        .btn-login {
            background-color: #000;
            color: #fff;
            padding: 7px 17px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: fit-content;
            display: inline-block;
            margin-top: 10px;
            align-self: flex-start;
        }

        .btn-login:hover {
            background-color: #333;
        }

        /* Right side image */
        .right-content {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            order: 2;
        }

        .right-content img {
            max-width: 80%;
            height: auto;
        }

        .left-content, .right-content {
            margin-bottom: 30px; /* Biar ada sedikit jarak ke bawah, lebih lega */
        }

        /* Remove underline for branding link */
        .branding {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 2rem;
            font-weight: bold;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            z-index: 2000;
            text-decoration: none;
        }

        /* Footer bar */
        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background-color: #7E78BE;
            z-index: 1000;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-wrapper {
                flex-direction: column;
                text-align: center;
                padding: 20px;
                gap: 10px;
            }

            .left-content h1 {
                font-size: 1.5rem;
                font-weight: bold;
                color: #333;
                margin-bottom: 20px;
            }

            .left-content {
                max-width: 100%;
                margin: 0;
                margin-bottom: 20px;
                order: 2;
                text-align: center;
            }

            .right-content {
                order: 1;
                margin-bottom: 20px;
            }

            .btn-login {
                font-size: 1rem;
                padding: 8px 16px;
                align-self: center; /* Untuk memastikan button tetap di tengah pada tampilan mobile */
            }

            .right-content img {
                max-width: 55%;
                height: auto;
                margin-top: 25px;
            }

            .form-input {
                width: 70%; /* Buat fieldnya lebih gede di layar kecil */
                display: flex;
                flex-direction: column;
            }

            .left-content, .right-content {
                margin-bottom: 10px; /* Margin lebih kecil di mobile biar muat */
            }

            .branding {
                font-size: 1.5rem;
                left: 15px;
                top: 10px;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="branding">
        DoNow.
    </a>

    <div class="container-wrapper">
        <!-- Left Content -->
        <div class="left-content">
            <h1>Welcome Back to DoNow!<br>Log in and conquer your tasks.</h1>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <input type="text" name="emailOrUsername" placeholder="Username or Email" class="form-input" required>
                <input type="password" name="password" placeholder="Password" class="form-input" required>
                <a href="forgot_password.php">Forgot Password?</a>
                <button type="submit" class="btn-login">Login</button>
            </form>
        </div>

        <!-- Right Content (Image) -->
        <div class="right-content">
            <img src="image/banner.jpg" alt="To-Do List Illustration"> <!-- Replace with your image -->
        </div>
    </div>

    <!-- Footer Bar -->
    <div class="footer-bar"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
