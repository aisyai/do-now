<?php
session_start();
require_once 'db_connection.php'; // Pastikan file ini berisi koneksi ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: login.php');
    exit();
}

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Cek apakah form telah dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil username dan email dari form
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Validasi input
    if (empty($username) || empty($email)) {
        // Redirect atau berikan pesan error
        header('Location: profile.php?error=fields_required');
        exit();
    }

    // Update data pengguna di database
    $update_query = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('ssi', $username, $email, $user_id);

    if ($stmt->execute()) {
        // Redirect ke halaman profil setelah berhasil memperbarui
        header('Location: profile.php?success=profile_updated');
    } else {
        // Redirect atau berikan pesan error
        header('Location: profile.php?error=update_failed');
    }
    exit();
}
