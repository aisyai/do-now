<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user udah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah task_id ada di URL
if (!isset($_GET['task_id'])) {
    die("Error: task_id tidak ditemukan.");
}

$task_id = $_GET['task_id'];

// Ambil data sub-task berdasarkan task_id
$stmt = $conn->prepare("SELECT * FROM sub_tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

// Jika form disubmit, lakukan update data sub-task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = htmlspecialchars($_POST['description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $due_time = $_POST['due_time'];

    // Update sub-task di database
    $stmt = $conn->prepare("UPDATE sub_tasks SET description = ?, status = ?, due_date = ?, due_time = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $description, $status, $due_date, $due_time, $task_id);

    if ($stmt->execute()) {
        // Redirect ke halaman detail to-do list
        header("Location: view_list.php?list_id=" . $task['todo_list_id']);
        exit();
    } else {
        echo "Terjadi kesalahan saat mengedit sub-task: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sub-Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h2 {
            color: #333;
            font-size: 24px;
        }

        .btn-success {
            background-color: #5dcf76;
            border-color: #5dcf76;
            border-radius: 20px;
            padding: 10px 20px;
        }

        .form-control {
            border-radius: 20px;
            padding: 10px 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Sub-Task</h2>

    <!-- Form untuk mengedit sub-task -->
    <form action="edit_sub_task.php?task_id=<?php echo $task_id; ?>" method="POST">
        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi Sub-Task</label>
            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($task['description']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="incomplete" <?php echo $task['status'] == 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo $task['due_date']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="due_time" class="form-label">Due Time</label>
            <input type="time" name="due_time" class="form-control" value="<?php echo $task['due_time']; ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Update Sub-Task</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
