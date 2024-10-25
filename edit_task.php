<?php
session_start();
require 'config.php'; // Koneksi ke database

// Jika user belum login, redirect ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah task_id, deskripsi, prioritas, tanggal, dan waktu ada
if (!isset($_POST['task_id']) || !isset($_POST['description']) || !isset($_POST['priority']) || !isset($_POST['due_date']) || !isset($_POST['due_time'])) {
    die("Error: Task ID, deskripsi, prioritas, tanggal, atau waktu tidak ditemukan.");
}

$task_id = $_POST['task_id'];
$description = $_POST['description'];
$priority = $_POST['priority'];
$list_id = $_POST['list_id'];
$due_date = $_POST['due_date'];
$due_time = $_POST['due_time'];

// Validasi priority agar sesuai dengan nilai ENUM
$valid_priorities = ['high', 'medium', 'low'];
if (!in_array($priority, $valid_priorities)) {
    die("Error: Prioritas tidak valid.");
}

// Validasi format tanggal dan waktu
$date_regex = '/\d{4}-\d{2}-\d{2}/'; // Regex untuk memvalidasi format tanggal (YYYY-MM-DD)
$time_regex = '/\d{2}:\d{2}/'; // Regex untuk memvalidasi format waktu (HH:MM)

if (!preg_match($date_regex, $due_date)) {
    die("Error: Format tanggal tidak valid.");
}

if (!preg_match($time_regex, $due_time)) {
    die("Error: Format waktu tidak valid.");
}

// Update task ke database dengan due date dan due time
$stmt = $conn->prepare("UPDATE tasks SET description = ?, priority = ?, due_date = ?, due_time = ? WHERE id = ? AND todo_list_id = ?");
$stmt->bind_param("ssssii", $description, $priority, $due_date, $due_time, $task_id, $list_id);

if ($stmt->execute()) {
    header("Location: view_list.php?list_id=" . $list_id);
    exit();
} else {
    echo "Error: Gagal mengedit task.";
}
?>
