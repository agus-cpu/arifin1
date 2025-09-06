<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role adalah admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}
// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
}
// Ambil data admin dari session
$username = $_SESSION['username'];
$name = $_SESSION['name'];

// Query untuk ambil data tambahan (opsional)
$sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Toko Handphone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-hover: #1e40af;
            --primary-light: #3b82f6;
            --secondary: #64748b;
            --dark: #0f172a;
            --light: #f8fafc;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --radius: 8px;
            --transition: all 0.2s ease;
            --sidebar-bg: #1e293b;
            --nav-active: #1e40af;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #e2e8f0;
            color: #1e293b;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .side-nav {
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            height: 100vh;
            position: sticky;
            top: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }

        .nav-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .nav-header h2 {
            font-size: 1.5rem;
            color: white;
        }

        .nav-header h2 span {
            color: var(--primary-light);
        }

        .nav-menu {
            flex: 1;
            overflow-y: auto;
            padding: 0 15px;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: var(--radius);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(30, 64, 175, 0.2);
            color: white;
            font-weight: 500;
        }

        .nav-link.active:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-light);
            border-radius: 0 var(--radius) var(--radius) 0;
        }

        .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-right: 12px;
            text-align: center;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--primary-light);
            color: white;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 500;
        }

        /* User Panel */
        .user-panel {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
            background: rgba(0, 0, 0, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 12px;
        }

        .user-name {
            font-size: 0.95rem;
            font-weight: 500;
            color: white;
        }

        .user-role {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .settings-link, .logout-link {
            display: block;
            margin-top: 10px;
            padding: 10px 15px;
            border-radius: var(--radius);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .settings-link {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
        }

        .settings-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .logout-link {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            text-align: center;
        }

        .logout-link:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #dc2626;
        }

        .settings-link i, .logout-link i {
            margin-right: 8px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background: #f1f5f9;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 1.8rem;
            color: var(--dark);
        }

        .page-title p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: white;
            padding: 8px 15px;
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }

        .search-bar input {
            border: none;
            outline: none;
            padding: 5px;
            min-width: 250px;
        }

        .search-bar i {
            color: #94a3b8;
            margin-right: 10px;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Activity Item */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
        }

        .activity-details p {
            font-weight: 500;
        }

        .activity-time {
            color: #64748b;
            font-size: 0.8rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .side-nav {
                width: 80px;
                padding: 15px 0;
            }

            .nav-header h2, .nav-link span, .user-name, .user-role, 
            .settings-link span, .logout-link span {
                display: none;
            }

            .nav-link {
                justify-content: center;
                padding: 15px 5px;
            }

            .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }

            .user-info {
                justify-content: center;
            }

            .user-avatar {
                margin-right: 0;
            }

            .settings-link, .logout-link {
                text-align: center;
                padding: 10px 5px;
            }

            .settings-link i, .logout-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .side-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: auto;
                flex-direction: row;
                padding: 0;
                z-index: 1000;
            }

            .nav-header, .user-panel {
                display: none;
            }

            .nav-menu {
                display: flex;
                padding: 0;
                width: 100%;
            }

            .nav-item {
                flex: 1;
                margin: 0;
            }

            .nav-link {
                flex-direction: column;
                padding: 10px 5px;
                font-size: 0.7rem;
                text-align: center;
            }

            .nav-link i {
                margin: 0 0 5px 0;
                font-size: 1.2rem;
            }

            .main-content {
                padding-bottom: 80px;
            }
        }

        /* Success Message */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: none;
        }

        /* === Modal and form helpers (non-intrusive) === */
        .modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0,0,0,0.45);
            z-index: 9999;
            padding: 20px;
        }
        .modal-content {
            position: relative;
            width: 100%;
            max-width: 520px;
            background: #fff;
            border-radius: 10px;
            padding: 20px 22px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .close-modal {
            position: absolute;
            right: 12px;
            top: 8px;
            font-size: 22px;
            cursor: pointer;
            color: #6b7280;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-sizing: border-box;
            margin-top: 6px;
        }
        .btn { display:inline-block; padding:8px 12px; border-radius:8px; border:none; cursor:pointer; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Side Navigation -->
        <nav class="side-nav">
            <div class="nav-header">
                <h2>Admin <span>Panel</span></h2>
            </div>

            <div class="nav-menu">
                <div class="nav-item">
                    <a href="admin.php" class="nav-link" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolauser.php" class="nav-link" data-section="users">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola User</span>
                        <span class="nav-badge" id="user-badge">3</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolatoko.php" class="nav-link" data-section="store">
                        <i class="fas fa-store"></i>
                        <span>Kelola Toko</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuaproduk.php" class="nav-link" data-section="products">
                        <i class="fas fa-boxes"></i>
                        <span>Semua Produk</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuapesanan.php" class="nav-link" data-section="orders">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Semua Pesanan</span>
                        <span class="nav-badge" id="order-badge">5</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="laporanadmin.php" class="nav-link" data-section="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </div>
            </div>

            <div class="user-panel">
                <div class="user-info">
                    <div class="user-avatar" id="user-avatar">AD</div>
                    <div>
                        <div class="user-name" id="admin-name">Admin Dashboard</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                <!-- NOTE: saya tidak ubah markup ini sama sekali -->
                <a href="pengaturanadmin.php" class="settings-link" id="settings-btn">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
                <a href="logout.php" class="logout-link" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="alert-success" id="successMessage">
                Perubahan berhasil disimpan!
            </div>

            <!-- Dashboard Section -->
            <div class="page-content active" id="dashboard-section">
                <div class="page-header">
                    <div class="page-title">
                        <h1>Dashboard</h1>
                        <p>Selamat datang kembali, Admin! Berikut ringkasan aktivitas toko Anda.</p>
                    </div>
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari..." id="search-input">
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="card">
                        <h3 style="color: var(--dark); margin-bottom: 15px;">Total Pendapatan</h3>
                        <p style="font-size: 1.8rem; font-weight: bold; color: var(--primary);">Rp <span id="total-income">12.450.000</span></p>
                        <p style="color: var(--success); font-size: 0.9rem;">
                            <i class="fas fa-arrow-up"></i> <span id="income-change">15%</span> dari bulan lalu
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="color: var(--dark); margin-bottom: 15px;">Pesanan Baru</h3>
                        <p style="font-size: 1.8rem; font-weight: bold; color: var(--primary);"><span id="new-orders">18</span></p>
                        <p style="color: var(--warning); font-size: 0.9rem;">
                            <i class="fas fa-arrow-up"></i> <span id="orders-today">5</span> pesanan hari ini
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="color: var(--dark); margin-bottom: 15px;">Total Produk</h3>
                        <p style="font-size: 1.8rem; font-weight: bold; color: var(--primary);"><span id="total-products">42</span></p>
                        <p style="color: var(--danger); font-size: 0.9rem;">
                            <i class="fas fa-exclamation-circle"></i> <span id="low-stock">3</span> stok hampir habis
                        </p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <h2 style="color: var(--dark); margin-bottom: 20px;">Aktivitas Terkini</h2>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-details">
                            <p>Pesanan baru #ORD-<span class="order-id">1256</span> dari <span class="customer-name">Budi Santoso</span></p>
                            <p class="activity-time">10 menit yang lalu</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-details">
                            <p>Pelanggan baru: <span class="customer-name">Ani Fitriani</span></p>
                            <p class="activity-time">1 jam yang lalu</p>
                        </div>
                    </div>
                    <div class="activity-item" style="border-bottom: none;">
                        <div class="activity-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="activity-details">
                            <p>Stok produkt <span class="product-name">Samsung Galaxy S23</span> hampir habis</p>
                            <p class="activity-time">3 jam yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Modal -->
            <div class="modal" id="settings-modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2><i class="fas fa-cog"></i> Pengaturan Admin</h2>
                    
                    <form id="admin-settings-form" style="margin-top:12px;">
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Nama Admin</label>
                            <input type="text" class="form-control" id="admin-name-input" value="Admin Dashboard" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Email</label>
                            <input type="email" class="form-control" id="admin-email" value="admin@tokohp.com" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>Avatar Initials</label>
                            <input type="text" class="form-control" id="admin-avatar" maxlength="2" value="AD" required>
                        </div>
                        
                        <div style="text-align:right;">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

 <script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Existing UI behaviors (keamanan: jangan ubah navbar/sidebar) ---
    const navLinks = document.querySelectorAll('.nav-link');
    const successMessage = document.getElementById('successMessage');
    const adminName = document.getElementById('admin-name');
    const userAvatar = document.getElementById('user-avatar');
    const logoutBtn = document.getElementById('logout-btn');

    // Navigation System - tetap seperti semula
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Remove active class from all links
            navLinks.forEach(nav => nav.classList.remove('active'));
            
            // Add active to clicked link
            this.classList.add('active');
            
            // Simulate page change (in real app, this would be server-side)
            const titleEl = document.querySelector('.page-title h1');
            if (titleEl) {
                const span = this.querySelector('span');
                if (span) titleEl.textContent = span.textContent;
            }
        });
    });

    // Logout Button (tidak diubah markupnya)
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if(confirm('Apakah Anda yakin ingin logout?')) {
            // In a real app, this would redirect to logout page
            window.location.href = 'logout.php';
        }
    });

    // Search Functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if(e.key === 'Enter') {
                alert(`Mencari: ${this.value}`);
                this.value = '';
            }
        });
    }

    // Helper: show success message
    function showSuccessMessage(message) {
        if (!successMessage) return;
        successMessage.textContent = message || 'Perubahan berhasil disimpan!';
        successMessage.style.display = 'block';
        
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);
    }

    // Simulate dynamic data updates (demo)
    setInterval(() => {
        const newOrdersEl = document.getElementById('new-orders');
        const ordersTodayEl = document.getElementById('orders-today');
        if (newOrdersEl) newOrdersEl.textContent = Math.floor(15 + Math.random() * 10);
        if (ordersTodayEl) ordersTodayEl.textContent = Math.floor(3 + Math.random() * 5);
    }, 5000);

    // --------------- SETTINGS (localStorage) ---------------
    const settingsBtn = document.getElementById('settings-btn'); // tetap ada di navbar/sidebar (tidak diubah)
    const settingsModal = document.getElementById('settings-modal');
    const closeModalBtn = settingsModal ? settingsModal.querySelector('.close-modal') : null;
    const adminForm = document.getElementById('admin-settings-form');
    const adminNameInput = document.getElementById('admin-name-input');
    const adminEmailInput = document.getElementById('admin-email');
    const adminAvatarInput = document.getElementById('admin-avatar');

    const LS_PREFIX = 'pengaturan_admin_'; // prefix storage

    // Open modal ketika klik tombol Pengaturan (tidak mengubah markup)
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function(e) {
            // cegah navigasi ke pengaturanadmin.php untuk membuka modal lokal
            e.preventDefault();
            if (settingsModal) {
                settingsModal.style.display = 'flex';
                // fokus ke input nama
                setTimeout(() => {
                    if (adminNameInput) adminNameInput.focus();
                }, 80);
            }
        });
    }

    // Close modal
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            if (settingsModal) settingsModal.style.display = 'none';
        });
    }
    // Click di luar modal-content -> close
    if (settingsModal) {
        settingsModal.addEventListener('click', function(e) {
            if (e.target === settingsModal) settingsModal.style.display = 'none';
        });
    }

    // Load saved settings dari localStorage dan apply
    function loadSettings() {
        const name = localStorage.getItem(LS_PREFIX + 'adminName');
        const email = localStorage.getItem(LS_PREFIX + 'adminEmail');
        const avatar = localStorage.getItem(LS_PREFIX + 'adminAvatar');

        if (name) {
            adminName.textContent = name;
            if (adminNameInput) adminNameInput.value = name;
        }
        if (email) {
            if (adminEmailInput) adminEmailInput.value = email;
        }
        if (avatar) {
            userAvatar.textContent = avatar;
            if (adminAvatarInput) adminAvatarInput.value = avatar;
        }
    }

    // Save settings helper
    function saveSettings(name, email, avatar) {
        try {
            localStorage.setItem(LS_PREFIX + 'adminName', name);
            localStorage.setItem(LS_PREFIX + 'adminEmail', email);
            localStorage.setItem(LS_PREFIX + 'adminAvatar', avatar);
        } catch (err) {
            console.error('Gagal menyimpan pengaturan:', err);
        }
    }

    // Submit form -> simpan & apply
    if (adminForm) {
        adminForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = (adminNameInput && adminNameInput.value.trim()) ? adminNameInput.value.trim() : 'Admin Dashboard';
            const email = (adminEmailInput && adminEmailInput.value.trim()) ? adminEmailInput.value.trim() : '';
            let avatar = (adminAvatarInput && adminAvatarInput.value.trim()) ? adminAvatarInput.value.trim().toUpperCase().slice(0,2) : '';
            if (!avatar) {
                // fallback: ambil inisial dari nama
                const parts = name.split(' ').filter(Boolean);
                avatar = parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : (name.slice(0,2).toUpperCase() || 'AD');
            }
            // apply
            if (adminName) adminName.textContent = name;
            if (userAvatar) userAvatar.textContent = avatar;
            // save
            saveSettings(name, email, avatar);
            showSuccessMessage('Perubahan profil tersimpan');
            // tutup modal
            if (settingsModal) settingsModal.style.display = 'none';
        });
    }

    // Live preview saat mengetik
    if (adminNameInput) {
        adminNameInput.addEventListener('input', function() {
            adminName.textContent = this.value || 'Admin Dashboard';
        });
    }
    if (adminAvatarInput) {
        adminAvatarInput.addEventListener('input', function() {
            // hanya 2 karakter uppercase
            this.value = (this.value || '').toUpperCase().slice(0,2);
            userAvatar.textContent = this.value || 'AD';
        });
    }

    // Inisialisasi: muat pengaturan tersimpan
    loadSettings();

});
</script>
</body>
</html>
