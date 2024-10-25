<?php
require 'config.php';
require 'vendor/autoload.php'; // Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize a variable for message display
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    
    // Check if email is registered
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+12 hour")); // Set token expiration

        // Store token and expiry in password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expiry);
        $stmt->execute();

        // Prepare reset link
        $url = "http://localhost/tugas/UTS_LAB/reset_password.php?token=$token";

        // Send email with reset link
        $mail = new PHPMailer(true);
        try {
            // Pengaturan SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'donow.supprot@gmail.com';
            $mail->Password = 'pyrq yrwv prqp eenm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'To-Do List App');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $mail->Body = "We received a request to reset the password for your Eventify account.
            Please use the link below to reset your password: <a href='$url'>$url</a>";

            $mail->send();
            $message = "<div class='alert alert-success'>Reset link has been sent to your email.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Failed to send email. Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Email not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
            padding: 10px 15px;
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
            justify-content: flex-start; /* Space between buttons */
            margin-top: 20px; /* Space from the form */
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mt-4">Forgot Password</h2>
    
    <!-- Display the notification message above the form -->
    <?php if ($message): ?>
        <div class="mb-3"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <form action="forgot_password.php" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="email" class="form-label">Enter Your Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
            <a href="login.php" class="btn btn-primary">Back to Login</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
