<?php
session_start();
require 'config.php'; // Koneksi ke database

// Aktifkan error display untuk debug (biar jelas kalau ada yang salah)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil id list dari URL
if (!isset($_GET['list_id'])) {
    die("Error: list_id tidak ditemukan.");
}

$list_id = $_GET['list_id'];
$user_id = $_SESSION['user_id'];

// Ambil informasi tentang list yang ada
$stmt = $conn->prepare("SELECT priority FROM todo_lists WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $list_id, $user_id);

// Cek apakah berhasil, kalau enggak langsung kasih error
if (!$stmt->execute()) {
    die("Gagal mengambil data: " . $stmt->error);
}

$result = $stmt->get_result();
$list = $result->fetch_assoc();

if (!$list) {
    die("Error: To-Do List tidak ditemukan atau Anda tidak memiliki akses.");
}

// Toggle nilai prioritas (dari 1 jadi 0, atau dari 0 jadi 1)
$new_priority = $list['priority'] == 1 ? 0 : 1;

// Buat query update buat mengubah prioritas
$update_stmt = $conn->prepare("UPDATE todo_lists SET priority = ? WHERE id = ? AND user_id = ?");
$update_stmt->bind_param("iii", $new_priority, $list_id, $user_id);

// Cek apakah berhasil update
if (!$update_stmt->execute()) {
    die("Gagal mengupdate data: " . $update_stmt->error);
}

// Redirect kembali ke dashboard
header("Location: dashboard.php");
exit();
?>
