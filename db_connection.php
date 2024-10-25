<?php
// db_connection.php

$host = 'localhost';  // Host database, biasanya 'localhost'
$dbname = 'todo_app'; // Nama database
$user = 'root';  // Username MySQL
$password = '';  // Password MySQL, biasanya kosong untuk XAMPP

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
