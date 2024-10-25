<?php
session_start();
require 'config.php'; // Pastikan file ini berisi koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Cek apakah ada file yang diunggah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_photo'])) {
    $file_name = basename($_FILES['profile_photo']['name']);
    $target_file = 'uploads/' . $file_name;

    // Cek apakah direktori uploads ada
    if (!file_exists('uploads')) {
        mkdir('uploads', 0755, true); // Buat direktori uploads jika tidak ada
    }

    // Pindahkan file yang diunggah ke direktori uploads
    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
        // Simpan nama file ke database
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
        $stmt->bind_param("si", $file_name, $user_id);
        $stmt->execute();

        // Redirect ke profile.php setelah berhasil mengunggah
        header("Location: profile.php");
        exit();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
