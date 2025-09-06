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
    <title>Kelola Toko - Admin Panel</title>
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background: #f1f5f9;
        }

        /* Header */
        .page-header {
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .page-title p {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Store Cards */
        .store-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .store-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: var(--transition);
        }

        .store-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .store-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .store-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .store-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
        }

        .store-rating {
            display: flex;
            align-items: center;
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .store-rating i {
            margin-right: 5px;
            color: var(--warning);
            font-size: 0.8rem;
        }

        .store-details {
            padding: 20px;
        }

        .store-detail {
            display: flex;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .store-detail-label {
            min-width: 100px;
            color: #64748b;
            font-weight: 500;
        }

        .store-detail-value {
            color: #334155;
            flex: 1;
        }

        .store-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .store-actions {
            padding: 15px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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

        .btn-edit {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
        }

        .btn-edit:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Add Store Button */
        .add-store {
            margin-bottom: 20px;
            text-align: right;
        }

        .btn-add {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: var(--radius);
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }

        .btn-add:hover {
            background: var(--primary-hover);
        }

        .btn-add i {
            margin-right: 8px;
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
            display: flex;
            align-items: center;
        }

        .modal-header h2 i {
            margin-right: 10px;
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

            .store-container {
                grid-template-columns: 1fr;
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
                    <a href="kelolauser.php" class="nav-link">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola User</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolatoko.php" class="nav-link active">
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
                    <h1>Kelola Toko</h1>
                    <p>Manajemen toko dan pemilik toko</p>
                </div>
            </div>

            <div class="add-store">
                <button class="btn btn-add" id="addStoreBtn">
                    <i class="fas fa-plus"></i>
                    Tambah Toko
                </button>
            </div>

            <div class="store-container" id="storeContainer">
                <!-- Toko 1 -->
                <div class="store-card" id="store1">
                    <div class="store-header">
                        <div class="store-title">
                            <div class="store-name">Toko Handphone Lhokseumawe</div>
                            <div class="store-rating">
                                <i class="fas fa-star"></i>
                                4.8
                            </div>
                        </div>
                    </div>
                    <div class="store-details">
                        <div class="store-detail">
                            <div class="store-detail-label">Pemilik:</div>
                            <div class="store-detail-value">Pemilik Toko</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Alamat:</div>
                            <div class="store-detail-value">Jl. Medan-Banda Aceh No. 45, Lhokseumawe</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Telepon:</div>
                            <div class="store-detail-value">0645-123456</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Email:</div>
                            <div class="store-detail-value">info@hokseumawe-mobile.com</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Jam Buka:</div>
                            <div class="store-detail-value">08:00 - 21:00</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Status:</div>
                            <div class="store-detail-value">
                                <span class="store-status status-active">ACTIVE</span>
                            </div>
                        </div>
                    </div>
                    <div class="store-actions">
                        <button class="btn btn-edit edit-store-btn" data-store-id="store1">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button class="btn btn-delete delete-store-btn" data-store-id="store1">
                            <i class="fas fa-trash-alt"></i>
                            Hapus
                        </button>
                    </div>
                </div>

                <!-- Toko 2 -->
                <div class="store-card" id="store2">
                    <div class="store-header">
                        <div class="store-title">
                            <div class="store-name">Mobile Center Aceh</div>
                            <div class="store-rating">
                                <i class="fas fa-star"></i>
                                4.6
                            </div>
                        </div>
                    </div>
                    <div class="store-details">
                        <div class="store-detail">
                            <div class="store-detail-label">Pemilik:</div>
                            <div class="store-detail-value">Pemilik Toko</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Alamat:</div>
                            <div class="store-detail-value">Jl. Banda Aceh No. 78, Lhokseumawe</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Telepon:</div>
                            <div class="store-detail-value">0645-789012</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Email:</div>
                            <div class="store-detail-value">info@mobile-center.com</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Jam Buka:</div>
                            <div class="store-detail-value">09:00 - 22:00</div>
                        </div>
                        <div class="store-detail">
                            <div class="store-detail-label">Status:</div>
                            <div class="store-detail-value">
                                <span class="store-status status-active">ACTIVE</span>
                            </div>
                        </div>
                    </div>
                    <div class="store-actions">
                        <button class="btn btn-edit edit-store-btn" data-store-id="store2">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button class="btn btn-delete delete-store-btn" data-store-id="store2">
                            <i class="fas fa-trash-alt"></i>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Store Modal -->
    <div class="modal" id="addStoreModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-store"></i> Tambah Toko Baru</h2>
                <span class="close-modal">&times;</span>
            </div>
            <form id="addStoreForm">
                <div class="form-group">
                    <label for="storeName">Nama Toko</label>
                    <input type="text" id="storeName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="storeOwner">Pemilik Toko</label>
                    <input type="text" id="storeOwner" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="storeAddress">Alamat</label>
                    <textarea id="storeAddress" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="storePhone">Telepon</label>
                    <input type="tel" id="storePhone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="storeEmail">Email</label>
                    <input type="email" id="storeEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="storeHours">Jam Buka</label>
                    <input type="text" id="storeHours" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="storeStatus">Status</label>
                    <select id="storeStatus" class="form-control" required>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Store Modal -->
    <div class="modal" id="editStoreModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Toko</h2>
                <span class="close-modal">&times;</span>
            </div>
            <form id="editStoreForm">
                <div class="form-group">
                    <label for="editStoreName">Nama Toko</label>
                    <input type="text" id="editStoreName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editStoreOwner">Pemilik Toko</label>
                    <input type="text" id="editStoreOwner" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editStoreAddress">Alamat</label>
                    <textarea id="editStoreAddress" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="editStorePhone">Telepon</label>
                    <input type="tel" id="editStorePhone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editStoreEmail">Email</label>
                    <input type="email" id="editStoreEmail" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editStoreHours">Jam Buka</label>
                    <input type="text" id="editStoreHours" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editStoreStatus">Status</label>
                    <select id="editStoreStatus" class="form-control" required>
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
    <div class="modal" id="deleteStoreModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i> Konfirmasi Hapus</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div style="padding: 20px;">
                <p>Apakah Anda yakin ingin menghapus toko ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteStore">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const addStoreBtn = document.getElementById('addStoreBtn');
        const storeContainer = document.getElementById('storeContainer');
        const editStoreBtns = document.querySelectorAll('.edit-store-btn');
        const deleteStoreBtns = document.querySelectorAll('.delete-store-btn');
        
        // Modal Elements
        const addStoreModal = document.getElementById('addStoreModal');
        const editStoreModal = document.getElementById('editStoreModal');
        const deleteStoreModal = document.getElementById('deleteStoreModal');
        const closeModalBtns = document.querySelectorAll('.close-modal, .btn-cancel');
        const confirmDeleteStoreBtn = document.getElementById('confirmDeleteStore');
        
        // Form Elements
        const addStoreForm = document.getElementById('addStoreForm');
        const editStoreForm = document.getElementById('editStoreForm');
        
        // Store ID to be deleted
        let storeToDelete = null;
        
        // Event Listeners
        addStoreBtn.addEventListener('click', () => {
            addStoreModal.style.display = 'flex';
        });
        
        // Close modals
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addStoreModal.style.display = 'none';
                editStoreModal.style.display = 'none';
                deleteStoreModal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === addStoreModal) addStoreModal.style.display = 'none';
            if (e.target === editStoreModal) editStoreModal.style.display = 'none';
            if (e.target === deleteStoreModal) deleteStoreModal.style.display = 'none';
        });
        
        // Add new store
        addStoreForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Create new store card
            const storeId = 'store' + (storeContainer.children.length + 1);
            const storeName = document.getElementById('storeName').value;
            const storeOwner = document.getElementById('storeOwner').value;
            const storeAddress = document.getElementById('storeAddress').value;
            const storePhone = document.getElementById('storePhone').value;
            const storeEmail = document.getElementById('storeEmail').value;
            const storeHours = document.getElementById('storeHours').value;
            const storeStatus = document.getElementById('storeStatus').value;
            
            const newStoreCard = document.createElement('div');
            newStoreCard.className = 'store-card';
            newStoreCard.id = storeId;
            newStoreCard.innerHTML = `
                <div class="store-header">
                    <div class="store-title">
                        <div class="store-name">${storeName}</div>
                        <div class="store-rating">
                            <i class="fas fa-star"></i>
                            4.0
                        </div>
                    </div>
                </div>
                <div class="store-details">
                    <div class="store-detail">
                        <div class="store-detail-label">Pemilik:</div>
                        <div class="store-detail-value">${storeOwner}</div>
                    </div>
                    <div class="store-detail">
                        <div class="store-detail-label">Alamat:</div>
                        <div class="store-detail-value">${storeAddress}</div>
                    </div>
                    <div class="store-detail">
                        <div class="store-detail-label">Telepon:</div>
                        <div class="store-detail-value">${storePhone}</div>
                    </div>
                    <div class="store-detail">
                        <div class="store-detail-label">Email:</div>
                        <div class="store-detail-value">${storeEmail}</div>
                    </div>
                    <div class="store-detail">
                        <div class="store-detail-label">Jam Buka:</div>
                        <div class="store-detail-value">${storeHours}</div>
                    </div>
                    <div class="store-detail">
                        <div class="store-detail-label">Status:</div>
                        <div class="store-detail-value">
                            <span class="store-status ${storeStatus === 'active' ? 'status-active' : 'status-inactive'}">
                                ${storeStatus === 'active' ? 'ACTIVE' : 'INACTIVE'}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="store-actions">
                    <button class="btn btn-edit edit-store-btn" data-store-id="${storeId}">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    <button class="btn btn-delete delete-store-btn" data-store-id="${storeId}">
                        <i class="fas fa-trash-alt"></i>
                        Hapus
                    </button>
                </div>
            `;
            
            // Add to container
            storeContainer.appendChild(newStoreCard);
            
            // Add event listeners to new buttons
            document.querySelector(`#${storeId} .edit-store-btn`).addEventListener('click', handleEditStore);
            document.querySelector(`#${storeId} .delete-store-btn`).addEventListener('click', handleDeleteStore);
            
            // Reset form and close modal
            addStoreForm.reset();
            addStoreModal.style.display = 'none';
            alert('Toko baru berhasil ditambahkan');
        });
        
        // Edit store buttons
        function handleEditStore(e) {
            const storeId = e.target.getAttribute('data-store-id');
            const storeCard = document.getElementById(storeId);
            
            // Get current values
            const storeName = storeCard.querySelector('.store-name').textContent;
            const storeOwner = storeCard.querySelector('.store-detail:nth-child(1) .store-detail-value').textContent;
            const storeAddress = storeCard.querySelector('.store-detail:nth-child(2) .store-detail-value').textContent;
            const storePhone = storeCard.querySelector('.store-detail:nth-child(3) .store-detail-value').textContent;
            const storeEmail = storeCard.querySelector('.store-detail:nth-child(4) .store-detail-value').textContent;
            const storeHours = storeCard.querySelector('.store-detail:nth-child(5) .store-detail-value').textContent;
            const storeStatus = storeCard.querySelector('.store-status').classList.contains('status-active') ? 'active' : 'inactive';
            
            // Populate form
            document.getElementById('editStoreName').value = storeName;
            document.getElementById('editStoreOwner').value = storeOwner;
            document.getElementById('editStoreAddress').value = storeAddress;
            document.getElementById('editStorePhone').value = storePhone;
            document.getElementById('editStoreEmail').value = storeEmail;
            document.getElementById('editStoreHours').value = storeHours;
            document.getElementById('editStoreStatus').value = storeStatus;
            
            // Set form submit handler
            editStoreForm.onsubmit = function(e) {
                e.preventDefault();
                
                // Update store card
                storeCard.querySelector('.store-name').textContent = document.getElementById('editStoreName').value;
                storeCard.querySelector('.store-detail:nth-child(1) .store-detail-value').textContent = document.getElementById('editStoreOwner').value;
                storeCard.querySelector('.store-detail:nth-child(2) .store-detail-value').textContent = document.getElementById('editStoreAddress').value;
                storeCard.querySelector('.store-detail:nth-child(3) .store-detail-value').textContent = document.getElementById('editStorePhone').value;
                storeCard.querySelector('.store-detail:nth-child(4) .store-detail-value').textContent = document.getElementById('editStoreEmail').value;
                storeCard.querySelector('.store-detail:nth-child(5) .store-detail-value').textContent = document.getElementById('editStoreHours').value;
                
                const status = document.getElementById('editStoreStatus').value;
                const statusSpan = storeCard.querySelector('.store-status');
                statusSpan.className = `store-status ${status === 'active' ? 'status-active' : 'status-inactive'}`;
                statusSpan.textContent = status === 'active' ? 'ACTIVE' : 'INACTIVE';
                
                editStoreModal.style.display = 'none';
                alert('Perubahan toko berhasil disimpan');
            };
            
            editStoreModal.style.display = 'flex';
        }
        
        // Delete store buttons
        function handleDeleteStore(e) {
            storeToDelete = e.target.getAttribute('data-store-id');
            deleteStoreModal.style.display = 'flex';
        }
        
        // Confirm delete
        confirmDeleteStoreBtn.addEventListener('click', () => {
            document.getElementById(storeToDelete).remove();
            deleteStoreModal.style.display = 'none';
            alert('Toko berhasil dihapus');
        });
        
        // Initialize event listeners for existing buttons
        editStoreBtns.forEach(btn => {
            btn.addEventListener('click', handleEditStore);
        });
        
        deleteStoreBtns.forEach(btn => {
            btn.addEventListener('click', handleDeleteStore);
        });
    </script>
</body>
</html>