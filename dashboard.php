<?php
session_start();
require 'config.php'; // Koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT username, email, profile_photo FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Retrieve tasks
$search_query = "";
$tasks_stmt = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = htmlspecialchars(trim($_GET['search']));
    $tasks_stmt = $conn->prepare("SELECT * FROM todo_lists WHERE user_id = ? AND title LIKE ?");
    $like_query = "%" . $search_query . "%";
    $tasks_stmt->bind_param("is", $user_id, $like_query);
} else {
    $tasks_stmt = $conn->prepare("SELECT * FROM todo_lists WHERE user_id = ?");
    $tasks_stmt->bind_param("i", $user_id);
}

$tasks_stmt->execute();
$lists = $tasks_stmt->get_result();

// Ambil data to-do list dengan sorting prioritas (prioritas di atas)
$stmt = $conn->prepare("SELECT * FROM todo_lists WHERE user_id = ? ORDER BY priority DESC, status ASC, id ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lists = $stmt->get_result();

// Aktifkan error display untuk cek error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Array warna pastel untuk sticky notes
$colors = ['#E1CBFF', '#FFD3D3', '#B2FFD0', '#FDFCE5', '#B9C9FF', '#e8dff5',
           '#fce1e4', '#daeaf6', '#FFF7AA', '#F4D19B', '#FFC7EA', '#A1EEBD', '#EEEDED'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #E2EBFF;
            color: #333;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            margin: 0;
        }

        .sidebar-toggle {
            display: block; /* Pastikan tampil */
            position: fixed; 
            top: 20px; 
            left: 20px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 3000; /* Biar tetap di atas elemen lain */
        }

        .container {
            padding: 30px;
        }

        .sticky-note-container {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 20px;
            margin-top: 20px;
            justify-content: start;
        }

        .sticky-note {
            padding: 20px;
            width: 200px;
            height: 200px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            min-height: 150px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease;
            transition: all 0.3s ease-in-out;
        }

        .sticky-note:hover {
            transform: scale(1.05);
        }

        .sticky-note-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sticky-note-actions {
            display: flex;
            justify-content: space-between;
        }

        .add-task-note {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 50px;
            background-color: #ddd;
        }

        .add-task-link {
            text-decoration: none;
            color: #333;
        }

        .btn-custom {
            border: none;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            transition: background-color 0.3s ease;
        }

        .priority-icon, .view-icon, .delete-icon {
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .priority-icon.inactive {
            color: #ccc;
        }

        .priority-icon.active {
            color: #f4c150;
        }

        .priority-icon:hover {
            transform: rotate(20deg) scale(1.2);
        }

        .btn-custom.btn-sm:hover {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        #backToDashboard {
            display: block;                /* Menampilkan elemen dengan blok */
            margin-bottom: 10px;                /* Memberikan margin di atas dan bawah */
            text-decoration: none;         /* Menghilangkan garis bawah pada link */
            color: #666;                   /* Warna teks abu-abu yang lembut */
            font-size: 1rem;               /* Ukuran font */
            font-weight: 300;              /* Membuat teks menjadi lebih tipis */
            font-style: italic;            /* Membuat teks menjadi italic */
            margin-top: 15px;
        }

        #backToDashboard:hover {
            color: #B7B7B7;
            transition: all 0.3s ease;
        }

        /* Container untuk search bar dan tombol */
        .search-container {
            display: flex;
            align-items: center;
            gap: 20px; /* Beri jarak antara search bar dan tombol */
            width: 100%;
        }

        /* Search Bar Styling */
        .search-bar {
            display: flex;
            align-items: center;
            background-color: #ffffff;
            border: 1px solid #d1d1d1; /* Border sesuai dengan tampilan gambar */
            border-radius: 30px; /* Membuat sudut melengkung */
            padding: 5px 15px; /* Padding dalam search bar */
            width: 100%;
            max-width: 500px; /* Batas maksimal lebar */
            box-sizing: border-box;
        }

        .search-bar input[type="text"] {
            border: none;
            outline: none;
            width: 100%;
            font-size: 1rem;
            padding: 5px;
            background: transparent;
        }

        .search-bar i {
            color: #a1a1a1; /* Warna ikon search sesuai gambar */
            font-size: 1.2rem;
            margin-left: 10px; /* Tambahkan jarak antara input dan ikon */
        }

        /* Tombol Priority dan Checked Styling */
        .btn-group {
            display: flex;
            align-items: center;
            gap: 15px; /* Jarak antara tombol */
            padding: 10px 25px; /* Padding lebih untuk membuat ukuran tombol besar dan bulat */
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }

        /* Tombol Priority */
        .btn-priority {
            background-color: #f4b400 !important; /* Warna kuning priority */
            color: #ffffff !important; /* Warna teks putih */
            border-radius: 50px !important;
        }

        /* Tombol Checked */
        .btn-checked {
            background-color: #34a853 !important; /* Warna hijau checked */
            color: #ffffff !important; /* Warna teks putih */
            border-radius: 50px !important;
        }

        /* Hover Effects */
        .btn-checked:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Bayangan lebih besar saat di-hover */
            opacity: 0.9; /* Sedikit transparan saat di-hover */
            transition: all 0.3s ease;
            transform: scale(1.05);
        }

        .btn-priority:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Bayangan lebih besar saat di-hover */
            opacity: 0.9; /* Sedikit transparan saat di-hover */
            transition: all 0.3s ease;
        }

        /* Responsive untuk Tombol dan Search */
        @media (max-width: 768px) {
            .search-container {
                flex-direction: column; /* Susun elemen vertikal di layar kecil */
                align-items: stretch; /* Membuat elemen mengambil lebar penuh */
            }

            .search-bar {
                width: 100%; /* Buat search bar mengambil seluruh lebar */
                margin-bottom: 15px; /* Beri jarak antara search bar dan tombol */
            }

            .btn-group {
                flex-direction: row; /* Tetap dalam satu baris di layar kecil */
                gap: 15px; /* Jarak yang tetap antara tombol */
            }
        }

        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="content" id="content">
        <div class="container">
            <h2>What do you want to <strong>DoNow</strong>?</h2>
            
            <!-- Filter and Search Bar -->
            <div class="search-container">
                <div class="search-bar">
                    <input type="text" id="searchNotes" placeholder="Search notes...">
                    <i class="bi bi-search"></i>
                </div>
                <div class="btn-group ms-3">
                    <button class="btn btn-priority" id="filterPriority">priority</button>
                    <button class="btn btn-checked" id="filterChecked">checked</button>
                </div>
            </div>

            <!-- Back to Dashboard Link -->
            <div id="backToDashboard" class="back-to-dashboard" style="display: none;">
                <a id="backToDashboard" href="dashboard.php">← back to dashboard</a>
                </a>
            </div>

            <!-- Sticky Notes Grid -->
            <div class="sticky-note-container">
                <!-- Tambahkan Sticky Note Baru -->
                <div id="addTaskNote" class="sticky-note add-task-note">
                    <a href="create_list.php" class="add-task-link">+</a>
                </div>

                <!-- Loop list to-do dari database -->
                <?php if ($lists->num_rows > 0): ?>
                    <?php while ($list = $lists->fetch_assoc()): 
                        if (empty($list['color'])) {
                            $random_color = $colors[array_rand($colors)];
                            $stmt_update_color = $conn->prepare("UPDATE todo_lists SET color = ? WHERE id = ?");
                            $stmt_update_color->bind_param("si", $random_color, $list['id']);
                            $stmt_update_color->execute();
                        } else {
                            $random_color = $list['color'];
                        }
                        $isChecked = $list['status'] == 'completed' ? 'true' : 'false';
                        $isPriority = isset($list['priority']) && $list['priority'] == 1 ? 'true' : 'false';
                    ?>
                    <div class="sticky-note" style="background-color: <?php echo $random_color; ?>;"
                        data-priority="<?php echo $isPriority; ?>">
                        <div class="sticky-note-title <?php echo $list['status'] == 'completed' ? 'text-decoration-line-through' : ''; ?>">
                            <?php echo htmlspecialchars($list['title']); ?>
                        </div>
                        <p><?php echo !empty($list['description']) ? htmlspecialchars($list['description']) : 'No description'; ?></p>
                        <div class="sticky-note-actions">
                            <a href="toggle_priority.php?list_id=<?php echo $list['id']; ?>">
                                <i class="bi <?php echo isset($list['priority']) && $list['priority'] == 1 ? 'bi-star-fill priority-icon active' : 'bi-star priority-icon inactive'; ?>"></i>
                            </a>
                            <div>
                                <a href="view_list.php?list_id=<?php echo $list['id']; ?>" class="btn btn-custom btn-sm" title="View List">
                                    <i class="bi bi-list-check view-icon"></i>
                                </a>
                                <a href="delete_list.php?list_id=<?php echo $list['id']; ?>" class="btn btn-custom btn-sm" title="Delete List">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No tasks found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('searchNotes').addEventListener('input', function () {
    const searchText = this.value.toLowerCase();
    const stickyNotes = document.querySelectorAll('.sticky-note');
    const addTaskNote = document.getElementById('addTaskNote');

    stickyNotes.forEach(function (note) {
        // Abaikan sticky note "Add Task"
        if (note === addTaskNote) {
            if (searchText === '') {
                // Jika tidak ada pencarian, pastikan "Add Task" muncul
                note.style.display = 'flex';
            } else {
                // Jika ada pencarian, sembunyikan "Add Task"
                note.style.display = 'none';
            }
            return;
        }

        // Tampilkan atau sembunyikan notes lain berdasarkan teks pencarian
        const title = note.querySelector('.sticky-note-title').innerText.toLowerCase();
        if (title.includes(searchText)) {
            note.style.display = 'flex';
        } else {
            note.style.display = 'none';
        }
    });
});

document.getElementById('filterPriority').addEventListener('click', function () {
    const stickyNotes = document.querySelectorAll('.sticky-note');
    const addTaskNote = document.getElementById('addTaskNote');

    stickyNotes.forEach(function (note) {
        // Abaikan sticky note "Add Task"
        if (note === addTaskNote) {
            // Sembunyikan sticky note "Add Task" saat tombol priority ditekan
            note.style.display = 'none';
            return;
        }

        // Cek apakah sticky note ini memiliki priority
        const isPriority = note.getAttribute('data-priority') === 'true';

        if (isPriority) {
            note.style.display = 'flex'; // Tampilkan jika memiliki priority
        } else {
            note.style.display = 'none'; // Sembunyikan jika tidak memiliki priority
        }
    });

    // Panggil fungsi untuk menampilkan link "back to dashboard"
    showBackToDashboard();
});

document.getElementById('filterChecked').addEventListener('click', function () {
    const stickyNotes = document.querySelectorAll('.sticky-note');
    const addTaskNote = document.getElementById('addTaskNote');

    stickyNotes.forEach(function (note) {
        // Abaikan sticky note "Add Task"
        if (note === addTaskNote) {
            // Sembunyikan sticky note "Add Task" saat tombol checked ditekan
            note.style.display = 'none';
            return;
        }

        // Cek apakah sticky note ini sudah selesai (done)
        const isChecked = note.getAttribute('data-status') === 'true';

        if (isChecked) {
            note.style.display = 'flex'; // Tampilkan jika sudah selesai
        } else {
            note.style.display = 'none'; // Sembunyikan jika belum selesai
        }
    });

    // Panggil fungsi untuk menampilkan link "back to dashboard"
    showBackToDashboard();
});

// Tambahkan JavaScript baru untuk "back to dashboard"
function showBackToDashboard() {
    // Menampilkan link "back to dashboard"
    let backToDashboardLink = document.getElementById('backToDashboard');
    if (!backToDashboardLink) {
        backToDashboardLink = document.createElement('a');
        backToDashboardLink.id = 'backToDashboard';
        backToDashboardLink.href = '#';
        backToDashboardLink.innerHTML = '&larr; back to dashboard';
        
        // Tambahkan style untuk back to dashboard agar sesuai gambar
        backToDashboardLink.style.display = 'block';
        backToDashboardLink.style.margin = '20px 0';
        backToDashboardLink.style.textDecoration = 'none';
        backToDashboardLink.style.color = '#666'; // Warna teks abu-abu
        backToDashboardLink.style.fontSize = '1rem';
        backToDashboardLink.style.fontWeight = '300'; // Buat teks tipis
        backToDashboardLink.style.fontStyle = 'italic'; // Buat teks italic

        backToDashboardLink.addEventListener('click', function (e) {
            e.preventDefault();
            goBackToDashboard();
        });

        // Sisipkan link setelah search bar
        const searchContainer = document.querySelector('.search-container');
        searchContainer.insertAdjacentElement('afterend', backToDashboardLink);
    } else {
        // Jika sudah ada, pastikan tampil
        backToDashboardLink.style.display = 'block';
    }
}

function goBackToDashboard() {
    // Sembunyikan link "back to dashboard"
    const backToDashboardLink = document.getElementById('backToDashboard');
    if (backToDashboardLink) {
        backToDashboardLink.style.display = 'none';
    }

    // Tampilkan kembali semua sticky notes dan "Add Task" button
    const stickyNotes = document.querySelectorAll('.sticky-note');
    const addTaskNote = document.getElementById('addTaskNote');

    stickyNotes.forEach(function (note) {
        note.style.display = 'flex';
    });

    // Pastikan "Add Task" muncul kembali
    if (addTaskNote) {
        addTaskNote.style.display = 'flex';
    }
}
</script>

</body>
</html>
