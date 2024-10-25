<?php
// navbar.php
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
<style>
.wrapper {
    display: flex;
    transition: all 0.3s ease-in-out;
}

.sidebar {
    width: 250px;
    height: 100vh;
    background-color: rgba(226, 235, 255);
    padding: 20px;
    padding-top: 40px;
    padding-bottom: 10px;
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transform: translateX(0); /* Default tampil */
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.sidebar.active {
    transform: translateX(-100%); /* Sidebar sembunyi ketika aktif */
}

.content {
    flex-grow: 1;
    transition: margin-left 0.3s ease-in-out;
    padding: 30px;
    margin-left: 250px; /* Default margin ketika sidebar terbuka */
    background-color: #ffffff;
}

.sidebar.active ~ .content {
    margin-left: 0;
}

.toggle-btn {
    position: fixed; /* Pastikan posisi fixed */
    top: 20px;
    left: 20px;
    font-size: 1.5rem;
    cursor: pointer;
    z-index: 3000; /* Biar toggle button tetap terlihat di depan */
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%); /* Tersembunyi secara default di layar kecil */
    }

    .sidebar.active {
        transform: translateX(0); /* Muncul ketika 'active' di layar kecil */
    }

    .content {
        margin-left: 0; /* Supaya konten tetap full di layar kecil */
    }
}

.profile-card {
    text-align: center;
    margin-bottom: 20px;
    font-size: 0.85rem;
}

.profile-card img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 8px;
    object-fit: cover;
}

.nav-links {
    flex-grow: 1;
}

.nav-link {
    font-size: 16px;
    margin: 8px 0;
    display: flex;
    align-items: center;
    padding: 8px 12px;
    gap: 10px;
    text-decoration: none;
    color: #333;
    transition: background 0.3s ease;
}

.nav-link:hover {
    background-color: #F0F0F0;
}

.logout-form {
    padding-top: 20px;
}

.btn-danger {
    width: 100%;
    border-radius: 20px;
    background-color: #000;
    border: none;
    padding: 8px 10px;
    color: white;
    font-size: 0.85rem;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background-color: #575757;
    border-radius: 10px;
}

</style>

<div class="wrapper">
    <!-- Sidebar Toggle Button -->
    <i class="bi bi-list toggle-btn" id="sidebarToggleBtn"></i>

    <div class="sidebar" id="sidebar">
        <!-- Profile Section -->
        <div class="profile-card">
            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo">
            <h4><?php echo htmlspecialchars($user['username']); ?></h4>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <!-- Navbar Links -->
        <nav class="nav-links">
            <a href="profile.php" class="nav-link">
                <i class="bi bi-person-fill"></i>Profile</a>
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-card-checklist"></i>Dashboard</a>
            <a href="about_us.php" class="nav-link">
                <i class="bi bi-info-circle"></i>About Us</a>
        </nav>

        <!-- Logout Button -->
        <form action="logout.php" method="POST" class="logout-form">
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
</div>

<script>
    // JavaScript untuk toggle sidebar
    document.querySelector("#sidebarToggleBtn").addEventListener("click", function() {
        const sidebar = document.querySelector("#sidebar");
        const content = document.querySelector(".content");

        sidebar.classList.toggle("active");

        if (sidebar.classList.contains("active")) {
            content.style.marginLeft = "0";
        } else {
            content.style.marginLeft = "250px";
        }
    });
</script>