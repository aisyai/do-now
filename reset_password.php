<?php
require 'config.php';

$message = ''; // Initialize a variable for message display

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $resetRequest = $result->fetch_assoc();

    if ($resetRequest) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            
            // Update the user's password in the database
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?");
            $stmt->bind_param("ss", $new_password, $resetRequest['email']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = "<div class='alert alert-success'>Password has been reset successfully! You can now log in with your new password.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Failed to reset password.</div>";
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Invalid or expired token.</div>";
    }
} else {
    $message = "<div class='alert alert-danger'>No token provided.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fdfbfb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Adjusted for better appearance */
        }

        h2 {
            color: #ff6f61;
            text-align: center;
        }

        .btn {
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 14px;
            width: 100%; /* Make buttons full width */
        }

        .btn-primary {
            background-color: #f78ca0;
            border: none;
        }

        .btn-primary:hover {
            background-color: #ff9a9e;
        }

        .form-label {
            color: #ff6f61;
        }

        .btn-container {
            display: flex;
            justify-content: space-between; /* Space between buttons */
            margin-top: 20px; /* Space from the form */
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mt-4">Reset Password</h2>
    
    <!-- Display the notification message -->
    <?php if (!empty($message)): ?>
        <div class="mb-3"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($resetRequest) && $resetRequest): ?>
        <form action="" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password:</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn btn-primary">Reset Password</button>
                <a href="login.php" class="btn btn-primary">Back to Login</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
