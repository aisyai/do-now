<?php
session_start();
require 'config.php'; // Panggil koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil list_id dari URL
$list_id = $_GET['list_id'];

// Hapus list dari database
$stmt = $conn->prepare("DELETE FROM todo_lists WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $list_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: dashboard.php"); // Redirect ke dashboard
} else {
    echo "Terjadi kesalahan saat menghapus list.";
}
?>
