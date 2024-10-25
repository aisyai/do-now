<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data list dan sub-task dari database berdasarkan list_id
$list_id = $_GET['list_id'];
$user_id = $_SESSION['user_id'];

// Ambil judul to-do list
$stmt = $conn->prepare("SELECT * FROM todo_lists WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $list_id, $user_id);
$stmt->execute();
$list = $stmt->get_result()->fetch_assoc();

// Ambil sub-task
$stmt = $conn->prepare("SELECT * FROM sub_tasks WHERE todo_list_id = ?");
$stmt->bind_param("i", $list_id);
$stmt->execute();
$sub_tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* General Body Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
            margin: 0;
        }

        /* Container styling */
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            font-size: 24px;
        }

        /* Button styling */
        .btn-success {
            background-color: #5dcf76;
            border-color: #5dcf76;
            border-radius: 25px;
            padding: 10px 20px;
        }

        .btn-primary, .btn-danger, .btn-warning {
            border-radius: 20px;
            padding: 8px 16px;
        }

        /* Form for adding sub-tasks */
        form {
            margin-bottom: 30px;
        }

        form input.form-control {
            padding: 12px 20px;
            border-radius: 20px;
        }

        /* Table styling */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Badge Styling for Status */
        .badge-success {
            background-color: #28a745;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h2>Detail To-Do List: <?php echo htmlspecialchars($list['title']); ?></h2>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a> <!-- Tombol Kembali -->

    <form action="add_sub_task.php" method="POST">
        <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">
        <div class="mb-3">
            <label for="description" class="form-label">Sub-Task</label>
            <input type="text" name="description" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Tambah Sub-Task</button>
    </form>

    <!-- Tampilkan daftar sub-task -->
    <form action="view_list.php?list_id=<?php echo $list_id; ?>" method="POST">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Sub-Task</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = $sub_tasks->fetch_assoc()): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="status" value="completed" <?php echo $task['status'] == 'completed' ? 'checked' : ''; ?> onchange="this.form.submit()">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        <input type="hidden" name="is_subtask" value="1">
                    </td>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
