<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role adalah admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
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
    <title>Kelola User - Toko Handphone</title>
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

        /* Admin Container */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Side Navigation */
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

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #0d9f6e;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        /* User Table */
        .user-table-container {
            background: white;
            border-radius: var(--radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-table th {
            background: #f8fafc;
            text-align: left;
            padding: 12px 16px;
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        .user-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .user-table tr:last-child td {
            border-bottom: none;
        }

        .user-table tr:hover {
            background: #f8fafc;
        }

        .user-avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-light);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 8px;
        }

        .role-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .role-admin {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }

        .role-staff {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .role-customer {
            background: rgba(100, 116, 139, 0.1);
            color: var(--secondary);
        }

        .action-btns {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .action-btn.edit {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }

        .action-btn.edit:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .action-btn.delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: white;
            border-top: 1px solid #e2e8f0;
        }

        .pagination-info {
            color: #64748b;
            font-size: 0.85rem;
        }

        .pagination-controls {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #f1f5f9;
            color: #334155;
            transition: var(--transition);
            text-decoration: none;
        }

        .page-btn.active {
            background: var(--primary);
            color: white;
        }

        .page-btn:hover:not(.active) {
            background: #e2e8f0;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            width: 500px;
            max-width: 90%;
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            font-size: 1.3rem;
            color: var(--dark);
        }

        .close-modal {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--secondary);
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-cancel {
            padding: 8px 16px;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            background: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #f1f5f9;
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

            .user-table {
                display: block;
                overflow-x: auto;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-bar {
                margin-top: 15px;
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
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
                    <a href="admin.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolauser.php" class="nav-link active">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola User</span>
                        <span class="nav-badge">3</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolatoko.php" class="nav-link">
                        <i class="fas fa-store"></i>
                        <span>Kelola Toko</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuaproduk.php" class="nav-link">
                        <i class="fas fa-boxes"></i>
                        <span>Semua Produk</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuapesanan.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Semua Pesanan</span>
                        <span class="nav-badge">5</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="laporanadmin.php" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </div>
            </div>

            <div class="user-panel">
                <div class="user-info">
                    <div class="user-avatar">AD</div>
                    <div>
                        <div class="user-name">Admin Toko</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                <a href="pengaturanadmin.php" class="settings-link">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Kelola User</h1>
                    <p>Kelola semua user dan akses mereka ke sistem</p>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari user..." id="searchInput">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-primary" id="addUserBtn">
                    <i class="fas fa-plus"></i>
                    Tambah User
                </button>
                <button class="btn btn-success" id="exportBtn">
                    <i class="fas fa-file-export"></i>
                    Export Data
                </button>
                <button class="btn btn-danger" id="deleteSelectedBtn">
                    <i class="fas fa-trash-alt"></i>
                    Hapus Terpilih
                </button>
            </div>

            <!-- User Table -->
            <div class="user-table-container">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar-small">AD</div>
                                    <div>
                                        <div style="font-weight: 500;">Admin Toko</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">admin</div>
                                    </div>
                                </div>
                            </td>
                            <td>admin@tokohp.com</td>
                            <td><span class="role-badge role-admin">Admin</span></td>
                            <td>12 Jan 2023</td>
                            <td><span style="color: var(--success);"><i class="fas fa-circle" style="font-size: 0.7rem;"></i> Aktif</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="#edit-user" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#delete-user" class="action-btn delete" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar-small">BS</div>
                                    <div>
                                        <div style="font-weight: 500;">Budi Santoso</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">budi.s</div>
                                    </div>
                                </div>
                            </td>
                            <td>budi@example.com</td>
                            <td><span class="role-badge role-staff">Staff</span></td>
                            <td>15 Mar 2023</td>
                            <td><span style="color: var(--success);"><i class="fas fa-circle" style="font-size: 0.7rem;"></i> Aktif</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="#edit-user" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#delete-user" class="action-btn delete" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar-small">AF</div>
                                    <div>
                                        <div style="font-weight: 500;">Ani Fitriani</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">ani.f</div>
                                    </div>
                                </div>
                            </td>
                            <td>ani@example.com</td>
                            <td><span class="role-badge role-customer">Customer</span></td>
                            <td>22 Apr 2023</td>
                            <td><span style="color: var(--success);"><i class="fas fa-circle" style="font-size: 0.7rem;"></i> Aktif</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="#edit-user" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#delete-user" class="action-btn delete" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar-small">RH</div>
                                    <div>
                                        <div style="font-weight: 500;">Rudi Hermawan</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">rudi.h</div>
                                    </div>
                                </div>
                            </td>
                            <td>rudi@example.com</td>
                            <td><span class="role-badge role-customer">Customer</span></td>
                            <td>05 Mei 2023</td>
                            <td><span style="color: var(--danger);"><i class="fas fa-circle" style="font-size: 0.7rem;"></i> Nonaktif</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="#edit-user" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#delete-user" class="action-btn delete" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" class="user-checkbox"></td>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="user-avatar-small">SR</div>
                                    <div>
                                        <div style="font-weight: 500;">Siti Rahayu</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">siti.r</div>
                                    </div>
                                </div>
                            </td>
                            <td>siti@example.com</td>
                            <td><span class="role-badge role-customer">Customer</span></td>
                            <td>18 Mei 2023</td>
                            <td><span style="color: var(--success);"><i class="fas fa-circle" style="font-size: 0.7rem;"></i> Aktif</span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="#edit-user" class="action-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#delete-user" class="action-btn delete" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <div class="pagination-info">
                        Menampilkan 1-5 dari 12 user
                    </div>
                    <div class="pagination-controls">
                        <a href="#" class="page-btn">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" class="page-btn active">1</a>
                        <a href="#" class="page-btn">2</a>
                        <a href="#" class="page-btn">3</a>
                        <a href="#" class="page-btn">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal" id="addUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Tambah User Baru</h2>
                <span class="close-modal">&times;</span>
            </div>
            <form id="addUserForm">
                <div class="form-group">
                    <label for="fullName">Nama Lengkap</label>
                    <input type="text" id="fullName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" class="form-control" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal" id="editUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit User</h2>
                <span class="close-modal">&times;</span>
            </div>
            <form id="editUserForm">
                <div class="form-group">
                    <label for="editFullName">Nama Lengkap</label>
                    <input type="text" id="editFullName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" id="editUsername" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editRole">Role</label>
                    <select id="editRole" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editStatus">Status</label>
                    <select id="editStatus" class="form-control" required>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i> Konfirmasi Hapus</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div style="padding: 20px;">
                <p>Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const addUserBtn = document.getElementById('addUserBtn');
        const exportBtn = document.getElementById('exportBtn');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const searchInput = document.getElementById('searchInput');
        
        // Modal Elements
        const addUserModal = document.getElementById('addUserModal');
        const editUserModal = document.getElementById('editUserModal');
        const deleteModal = document.getElementById('deleteModal');
        const closeModalBtns = document.querySelectorAll('.close-modal, .btn-cancel');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        
        // Form Elements
        const addUserForm = document.getElementById('addUserForm');
        const editUserForm = document.getElementById('editUserForm');
        
        // Event Listeners
        addUserBtn.addEventListener('click', () => {
            addUserModal.style.display = 'flex';
        });
        
        exportBtn.addEventListener('click', () => {
            alert('Fitur export data akan membuka dialog download file Excel');
        });
        
        deleteSelectedBtn.addEventListener('click', () => {
            const selectedUsers = Array.from(userCheckboxes).filter(cb => cb.checked);
            if (selectedUsers.length === 0) {
                alert('Pilih setidaknya satu user untuk dihapus');
                return;
            }
            
            if (confirm(`Anda yakin ingin menghapus ${selectedUsers.length} user terpilih?`)) {
                alert(`${selectedUsers.length} user berhasil dihapus`);
                // In real app, would send request to server
                selectedUsers.forEach(cb => {
                    cb.closest('tr').remove();
                });
            }
        });
        
        selectAll.addEventListener('change', (e) => {
            userCheckboxes.forEach(cb => {
                cb.checked = e.target.checked;
            });
        });
        
        // Search functionality
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('.user-table tbody tr');
                
                rows.forEach(row => {
                    const name = row.querySelector('td:nth-child(2) div:nth-child(1)').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
        
        // Modal handling
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addUserModal.style.display = 'none';
                editUserModal.style.display = 'none';
                deleteModal.style.display = 'none';
            });
        });
        
        // Edit buttons
        document.querySelectorAll('.action-btn.edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                editUserModal.style.display = 'flex';
                
                // In real app, would populate form with user data
                const row = e.target.closest('tr');
                const name = row.querySelector('td:nth-child(2) div:nth-child(1)').textContent;
                const username = row.querySelector('td:nth-child(2) div:nth-child(2)').textContent;
                const email = row.querySelector('td:nth-child(3)').textContent;
                const role = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
                const status = row.querySelector('td:nth-child(6) span').textContent.includes('Aktif') ? 'active' : 'inactive';
                
                document.getElementById('editFullName').value = name;
                document.getElementById('editUsername').value = username;
                document.getElementById('editEmail').value = email;
                document.getElementById('editRole').value = role;
                document.getElementById('editStatus').value = status;
            });
        });
        
        // Delete buttons
        document.querySelectorAll('.action-btn.delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                deleteModal.style.display = 'flex';
                
                // Store reference to the row to be deleted
                const row = e.target.closest('tr');
                confirmDeleteBtn.onclick = () => {
                    row.remove();
                    deleteModal.style.display = 'none';
                    alert('User berhasil dihapus');
                };
            });
        });
        
        // Form submissions
        addUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('User baru berhasil ditambahkan');
            addUserModal.style.display = 'none';
            addUserForm.reset();
        });
        
        editUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Perubahan user berhasil disimpan');
            editUserModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === addUserModal) addUserModal.style.display = 'none';
            if (e.target === editUserModal) editUserModal.style.display = 'none';
            if (e.target === deleteModal) deleteModal.style.display = 'none';
        });
    </script>
</body>
</html>