<?php
// Config.php untuk koneksi database dan pembuatan database otomatis

$host = 'localhost';
$user = 'root'; // Ubah sesuai dengan username MySQL Anda
$password = ''; // Ubah sesuai dengan password MySQL Anda
$dbname = 'todo_app'; // Nama database

// Koneksi ke MySQL
$conn = new mysqli($host, $user, $password);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat database jika belum ada
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Gagal membuat database: " . $conn->error);
}

// Pilih database yang sudah dibuat
$conn->select_db($dbname);

// Fungsi untuk cek apakah kolom sudah ada di tabel
function check_column_exists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Buat tabel users jika belum ada
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255) DEFAULT 'default.png'
)";
if ($conn->query($sql_users) !== TRUE) {
    die("Gagal membuat tabel users: " . $conn->error);
}

// Cek apakah kolom 'reset_token' sudah ada di tabel users
if (!check_column_exists($conn, 'users', 'reset_token')) {
    $sql_add_reset_token = "ALTER TABLE users ADD COLUMN reset_token VARCHAR(255) DEFAULT NULL";
    if (!$conn->query($sql_add_reset_token)) {
        die("Gagal menambahkan kolom reset_token: " . $conn->error);
    }
}

// Cek apakah kolom 'reset_expiry' sudah ada di tabel users
if (!check_column_exists($conn, 'users', 'reset_expiry')) {
    $sql_add_reset_expiry = "ALTER TABLE users ADD COLUMN reset_expiry DATETIME DEFAULT NULL";
    if (!$conn->query($sql_add_reset_expiry)) {
        die("Gagal menambahkan kolom reset_expiry: " . $conn->error);
    }
}

// Buat tabel todo_lists jika belum ada
$sql_todo_lists = "CREATE TABLE IF NOT EXISTS todo_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    priority TINYINT(1) DEFAULT 0,
    status ENUM('completed', 'incomplete') DEFAULT 'incomplete',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
if ($conn->query($sql_todo_lists) !== TRUE) {
    die("Gagal membuat tabel todo_lists: " . $conn->error);
}

// Buat tabel tasks jika belum ada
$sql_tasks = "CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    todo_list_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    status ENUM('completed', 'incomplete') DEFAULT 'incomplete',
    priority ENUM('high', 'medium', 'low') NOT NULL DEFAULT 'medium',
    due_date DATE,
    due_time TIME,
    FOREIGN KEY (todo_list_id) REFERENCES todo_lists(id) ON DELETE CASCADE
)";
if ($conn->query($sql_tasks) !== TRUE) {
    die("Gagal membuat tabel tasks: " . $conn->error);
}

// Buat tabel sub_tasks jika belum ada
$sql_sub_tasks = "CREATE TABLE IF NOT EXISTS sub_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    todo_list_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    status ENUM('completed', 'incomplete') DEFAULT 'incomplete',
    due_date DATE,
    due_time TIME,
    FOREIGN KEY (todo_list_id) REFERENCES todo_lists(id) ON DELETE CASCADE
)";
if ($conn->query($sql_sub_tasks) !== TRUE) {
    die("Gagal membuat tabel sub_tasks: " . $conn->error);
}

// Cek apakah kolom 'profile_photo' sudah ada di tabel users
if (!check_column_exists($conn, 'users', 'profile_photo')) {
    $sql_add_profile_photo = "ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT 'default.png'";
    if (!$conn->query($sql_add_profile_photo)) {
        die("Gagal menambahkan kolom profile_photo: " . $conn->error);
    }
}

// Cek apakah kolom 'color' sudah ada di tabel todo_lists
if (!check_column_exists($conn, 'todo_lists', 'color')) {
    $sql_add_color_column = "ALTER TABLE todo_lists ADD COLUMN color VARCHAR(7) DEFAULT NULL";
    if (!$conn->query($sql_add_color_column)) {
        die("Gagal menambahkan kolom color: " . $conn->error);
    }
}

// Cek apakah kolom 'description' sudah ada di tabel todo_lists
if (!check_column_exists($conn, 'todo_lists', 'description')) {
    $sql_add_description_column = "ALTER TABLE todo_lists ADD COLUMN description VARCHAR(255) DEFAULT NULL";
    if (!$conn->query($sql_add_description_column)) {
        die("Gagal menambahkan kolom description: " . $conn->error);
    }
}

// Cek apakah kolom 'user_id' sudah ada di tabel tasks
if (!check_column_exists($conn, 'tasks', 'user_id')) {
    $sql_add_user_id_column = "ALTER TABLE tasks ADD COLUMN user_id INT NOT NULL AFTER id";
    if (!$conn->query($sql_add_user_id_column)) {
        die("Gagal menambahkan kolom user_id: " . $conn->error);
    } else {
        $sql_add_user_id_fk = "ALTER TABLE tasks ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE";
        if (!$conn->query($sql_add_user_id_fk)) {
            die("Gagal menambahkan foreign key user_id: " . $conn->error);
        }
    }
}

// Buat tabel password_resets jika belum ada
$sql_password_resets = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE
)";
if ($conn->query($sql_password_resets) !== TRUE) {
    die("Gagal membuat tabel password_resets: " . $conn->error);
}

// Insert user data untuk testing (opsional)
$username = "user123";
$email = "user@example.com";
$password = password_hash("yourpassword", PASSWORD_DEFAULT); // hashing password
$profile_photo = "path/to/photo.jpg";

// Cek apakah email sudah ada
$stmt_check = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows == 0) {
    // Jika email belum ada, lakukan insert
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_photo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $profile_photo);

    if ($stmt->execute()) {
        // Pesan ini dihapus agar tidak mengganggu UI
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Selesai
?>
