<?php
session_start();
require 'config.php'; // Koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Aktifkan error display untuk cek error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fdfbfb 100%, #f6d365 100%, #fda085 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }
        .content {
            flex-grow: 1;
            padding: 30px;
            background-color: #f9f9f9;
            margin-left: 270px;
            max-width: calc(100% - 300px);
            display: flex;
            justify-content: center;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }

        h2 {
            color: #ff6f61;
        }

        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }

        .team-member img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .team-member h5 {
            margin-top: 15px;
            color: #007bff;
            font-size: 1.2rem;
        }

        .team-member p {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- About Us content -->
<div class="content">
    <div class="container">
        <h2>Tentang Kami</h2>
        <p>Ini adalah aplikasi To-Do List yang dikembangkan untuk membantu mengelola tugas sehari-hari.</p>

        <div class="row">
            <!-- Team Member 1 -->
            <div class="col-md-6 team-member">
                <img src="image/nadhs.png" alt="Nadhirah Danish Ammara">
                <h5>Nadhirah Danish Ammara</h5>
                <p>Deskripsi tentang Nama 1.</p>
            </div>

            <!-- Team Member 2 -->
            <div class="col-md-6 team-member">
                <img src="image/aisyaa.png" alt="Aisya Adiyan">
                <h5>Aisya Adiyan</h5>
                <p>Deskripsi tentang Nama 2.</p>
            </div>

            <!-- Team Member 3 -->
            <div class="col-md-6 team-member">
                <img src="image/liaa.png" alt="Vanessa Audrelia Christianto">
                <h5>Vanessa Audrelia Christianto</h5>
                <p>Deskripsi tentang Nama 3.</p>
            </div>

            <!-- Team Member 4 -->
            <div class="col-md-6 team-member">
                <img src="image/aryaa.png" alt="Aryabell Boston Tjugito">
                <h5>Aryabell Boston Tjugito</h5>
                <p>Deskripsi tentang Nama 4.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
