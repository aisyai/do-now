<?php
session_start();
require 'config.php'; // Include database config

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoNow.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
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
            padding: 30px;
            gap: 30px;
            padding-bottom: 10px; /* Add bottom padding to prevent button from overlapping footer */
        }

        /* Left side content */
        .left-content {
            max-width: 45%;
            margin-left: 100px;
            margin-bottom: 20px; /* Ensure spacing below on small screens */
            order: 1;
            text-align: left;
        }

        .left-content h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .left-content p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-join {
            background-color: #007bff;
            color: #fff;
            padding: 5px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .btn-join:hover {
            background-color: #0056b3;
        }

        /* Right side image */
        .right-content {
            flex-grow: 0;
            flex-basis: 40%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 100px;
            margin-bottom: 20px; /* Ensure spacing below on small screens */
            order: 2;
        }

        .right-content img {
            max-width: 80%;
            height: auto;
        }

        /* Login Button on top right */
        .login-btn {
            position: absolute;
            top: 20px;
            right: 30px;
            background-color: #000;
            color: #fff;
            padding: 5px 15px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
            z-index: 2000;
        }

        .login-btn:hover {
            background-color: #333;
        }

        /* Footer bar */
        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 40px;
            background-color: #3f8f3f;
            z-index: 1000;
        }

        /* Branding Logo */
        .branding {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 2rem;
            font-weight: bold;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #333;
            z-index: 2000;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-wrapper {
                flex-direction: column;
                text-align: center;
                padding: 10px;
                margin-bottom: 10px;
                flex-basis: 100%;
                gap: 30px;
            }

            .left-content h1 {
                font-size: 35px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }

            .right-content img {
                margin-top: 40px;
                max-width: 60%;
                height: 50%;
            }

            .left-content p {
                font-size: 14px;
                color: #666;
                margin-bottom: 20px;
                line-height: 1.6;
            }

            .left-content {
                max-width: 100%;
                margin-left: 0;
                margin-bottom: 20px;
                order: 2;
                text-align: center;
            }

            .right-content {
                order: 1;
                margin-bottom: 10px;
            }

            .left-content, .right-content {
                margin: 0; /* Margin lebih kecil di mobile biar muat */
            }

            .btn-join {
                font-size: 16px;
                padding: 8px 16px;
            }

            .branding {
                font-size: 1.5rem;
                left: 15px;
                top: 10px;
            }

            .login-btn {
                top: 10px;
                right: 15px;
                font-size: 0.9rem;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="branding">
        DoNow.
    </div>
    <a href="login.php" class="login-btn">Log in</a>
    <div class="container-wrapper">
        <!-- Left Content -->
        <div class="left-content">
            <h1>Get Things Done, <br> Right Now!</h1>
            <p>
                <i class="bi bi-check2-square"></i> Organize your to-do lists with just a few clicks.<br>
                <i class="bi bi-check2-square"></i> Track your progress and hit every deadline.<br>
                <i class="bi bi-check2-square"></i> Stay Focused on what matters the most.
            </p>
            <a href="register.php" class="btn-join">Join Now</a>
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
