<?php
require 'config.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'];

    // Update status di database
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $task_id);
    $stmt->execute();

    // Balas sebagai JSON supaya bisa diketahui sukses atau tidak
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit();
}
