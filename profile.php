<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT username, email, profile_photo FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Proses upload foto profil baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_photo'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profile_photo"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_photo"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            // Update database with new profile photo path
            $stmt_update = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
            $stmt_update->bind_param("si", $target_file, $user_id);
            $stmt_update->execute();

            // Refresh user data
            $user['profile_photo'] = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #E2EBFF;
            color: #333;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            margin: 0;
        }

        .wrapper {
            display: flex;
            transition: all 0.3s ease-in-out;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: rgba(226, 235, 255);
            padding: 20px;
            padding-top: 40px;
            padding-bottom: 10px;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transform: translateX(0); /* Default tampil */
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar.active {
            transform: translateX(-100%); /* Sidebar sembunyi ketika aktif */
        }

        .content {
            flex-grow: 1;
            transition: margin-left 0.3s ease-in-out;
            padding: 30px;
            margin-left: 250px; /* Default margin ketika sidebar terbuka */
            background-color: #ffffff;
        }

        .sidebar.active ~ .content {
            margin-left: 0;
        }

        .toggle-btn {
            position: fixed; /* Pastikan posisi fixed */
            top: 20px;
            left: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 3000; /* Biar toggle button tetap terlihat di depan */
        }

        .profile-card {
            text-align: center;
            margin-top: 100px;
            font-size: 1rem;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .profile-card input[type="file"] {
            display: none;
        }

        .profile-card label {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%); /* Tersembunyi secara default di layar kecil */
            }

            .sidebar.active {
                transform: translateX(0); /* Muncul ketika 'active' di layar kecil */
            }

            .content {
                margin-left: 0; /* Supaya konten tetap full di layar kecil */
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Include Sidebar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="content" id="content">
        <i class="bi bi-list toggle-btn" id="sidebarToggleBtn"></i>
        <div class="container">
            <div class="profile-card">
                <form action="" method="POST" enctype="multipart/form-data">
                    <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
                    <label for="profile_photo">Edit profile picture</label>
                    <input type="file" name="profile_photo" id="profile_photo" onchange="this.form.submit()">
                </form>
                <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript untuk toggle sidebar
    document.getElementById('sidebarToggleBtn').addEventListener('click', function () {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');

        // Toggle class 'active' buat sidebar
        sidebar.classList.toggle('active');

        // Adjust margin pada konten
        if (sidebar.classList.contains('active')) {
            content.style.marginLeft = '0';
        } else {
            content.style.marginLeft = '250px';
        }
    });
</script>

</body>
</html>
