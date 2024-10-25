<?php
session_start();
require 'config.php'; // Panggil koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $user_id = $_SESSION['user_id'];
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : null;

    // Tambah to-do list ke database dengan kolom description
    $stmt = $conn->prepare("INSERT INTO todo_lists (user_id, title, description) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Error saat mempersiapkan query: " . $conn->error); // Menampilkan error jika ada
    }

    if (!$stmt->bind_param("iss", $user_id, $title, $description)) {
        die("Error saat bind parameter: " . $stmt->error);
    }

    if ($stmt->execute()) {
        // Redirect ke dashboard setelah to-do list berhasil ditambahkan
        header("Location: dashboard.php");
        exit(); // Tambahkan exit() agar redirect dieksekusi dengan benar
    } else {
        echo "Terjadi kesalahan saat membuat to-do list: " . $stmt->error;
    }

    $stmt->close(); // Tutup statement setelah selesai digunakan
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
        }

        .content {
            margin-left: 270px; /* Geser konten agar tidak ketutupan sidebar */
            padding: 30px;
            width: calc(100% - 270px);
            background-color: #f9f9f9;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-control {
            padding: 10px;
            border-radius: 20px;
        }

        .btn-success {
            border-radius: 20px;
            padding: 10px 20px;
        }

        .btn-secondary {
            border-radius: 20px;
            padding: 10px 20px;
        }
    </style>
</head>

<body>

<!-- Main Content -->
<div class="content">
    <div class="container">
        <h2 class="mt-5">Buat To-Do List Baru</h2>

        <!-- Form untuk membuat to-do list baru -->
        <form action="create_list.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="title" class="form-label">Judul To-Do List</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Buat List</button>
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
