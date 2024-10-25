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
    // Ambil dan sanitasi input dari form
    $description = htmlspecialchars($_POST['description']);
    $priority = $_POST['priority']; // Ambil nilai priority dari form
    $list_id = $_POST['list_id'];
    $due_date = $_POST['due_date']; // Ambil nilai due date dari form
    $due_time = $_POST['due_time']; // Ambil nilai due time dari form

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

    // Tambah task ke database dengan due date dan due time
    $stmt = $conn->prepare("INSERT INTO tasks (todo_list_id, description, priority, due_date, due_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $list_id, $description, $priority, $due_date, $due_time); // Bind due date dan due time

    if ($stmt->execute()) {
        header("Location: view_list.php?list_id=$list_id"); // Redirect ke halaman list
        exit();
    } else {
        echo "Terjadi kesalahan saat menambahkan task: " . $stmt->error; // Tampilkan error jika ada
    }
}
?>
