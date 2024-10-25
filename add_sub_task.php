<?php
session_start();
require 'config.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'];
    $description = htmlspecialchars($_POST['description']);
    
    // Insert sub-task ke dalam database
    $stmt = $conn->prepare("INSERT INTO sub_tasks (todo_list_id, description) VALUES (?, ?)");
    $stmt->bind_param("is", $list_id, $description);
    
    if ($stmt->execute()) {
        header("Location: view_list.php?list_id=$list_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
