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
    <title>Semua Produk - Toko Handphone</title>
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

        /* Table Styles */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .products-table th, 
        .products-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .products-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: var(--dark);
        }

        .products-table tr:hover {
            background-color: #f8fafc;
        }

        .product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status.available {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status.out-of-stock {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 0.8rem;
            transition: var(--transition);
        }

        .edit-btn {
            background-color: var(--primary-light);
            color: white;
        }

        .edit-btn:hover {
            background-color: var(--primary);
        }

        .delete-btn {
            background-color: var(--danger);
            color: white;
        }

        .delete-btn:hover {
            background-color: #dc2626;
        }

        .add-product {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background-color: var(--success);
            color: white;
            text-decoration: none;
            border-radius: var(--radius);
            margin-bottom: 20px;
            transition: var(--transition);
        }

        .add-product:hover {
            background-color: #059669;
        }

        .add-product i {
            margin-right: 8px;
        }

        /* Modal Styles */
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

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-bar {
                margin-top: 15px;
                width: 100%;
            }

            .search-bar input {
                min-width: auto;
                width: 100%;
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
                    <a href="semuaproduk.php" class="nav-link active">
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
                <a href="pengaturanadmin." class="settings-link">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="page-header">
                <div class="page-title">
                    <h1>Semua Produk</h1>
                    <p>Kelola semua produk toko handphone Anda</p>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari produk..." id="searchInput">
                </div>
            </div>

            <a href="#" class="add-product" id="addProductBtn">
                <i class="fas fa-plus"></i> Tambah Produk Baru
            </a>

            <div class="card">
                <table class="products-table" id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Merk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="product1">
                            <td>P001</td>
                            <td><img src="https://via.placeholder.com/50" alt="iPhone 13" class="product-img"></td>
                            <td>iPhone 13 Pro Max</td>
                            <td>Apple</td>
                            <td>Rp 19.999.000</td>
                            <td>8</td>
                            <td><span class="status available">Tersedia</span></td>
                            <td>
                                <button class="action-btn edit-btn" data-product-id="product1"><i class="fas fa-edit"></i> Edit</button>
                                <button class="action-btn delete-btn" data-product-id="product1"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                        <tr id="product2">
                            <td>P002</td>
                            <td><img src="https://via.placeholder.com/50" alt="Samsung S21" class="product-img"></td>
                            <td>Galaxy S21 Ultra</td>
                            <td>Samsung</td>
                            <td>Rp 15.999.000</td>
                            <td>12</td>
                            <td><span class="status available">Tersedia</span></td>
                            <td>
                                <button class="action-btn edit-btn" data-product-id="product2"><i class="fas fa-edit"></i> Edit</button>
                                <button class="action-btn delete-btn" data-product-id="product2"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                        <tr id="product3">
                            <td>P003</td>
                            <td><img src="https://via.placeholder.com/50" alt="Redmi Note 11" class="product-img"></td>
                            <td>Redmi Note 11 Pro</td>
                            <td>Xiaomi</td>
                            <td>Rp 4.499.000</td>
                            <td>0</td>
                            <td><span class="status out-of-stock">Habis</span></td>
                            <td>
                                <button class="action-btn edit-btn" data-product-id="product3"><i class="fas fa-edit"></i> Edit</button>
                                <button class="action-btn delete-btn" data-product-id="product3"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                        <tr id="product4">
                            <td>P004</td>
                            <td><img src="https://via.placeholder.com/50" alt="ROG Phone 5" class="product-img"></td>
                            <td>ROG Phone 5</td>
                            <td>ASUS</td>
                            <td>Rp 12.999.000</td>
                            <td>5</td>
                            <td><span class="status available">Tersedia</span></td>
                            <td>
                                <button class="action-btn edit-btn" data-product-id="product4"><i class="fas fa-edit"></i> Edit</button>
                                <button class="action-btn delete-btn" data-product-id="product4"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                        <tr id="product5">
                            <td>P005</td>
                            <td><img src="https://via.placeholder.com/50" alt="Poco X4" class="product-img"></td>
                            <td>Poco X4 Pro</td>
                            <td>POCO</td>
                            <td>Rp 4.299.000</td>
                            <td>15</td>
                            <td><span class="status available">Tersedia</span></td>
                            <td>
                                <button class="action-btn edit-btn" data-product-id="product5"><i class="fas fa-edit"></i> Edit</button>
                                <button class="action-btn delete-btn" data-product-id="product5"><i class="fas fa-trash"></i> Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal" id="addProductModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h2>
                <span class="close-modal">&times;</span>
            </div>
            <form id="addProductForm">
                <div class="form-group">
                    <label for="productId">ID Produk</label>
                    <input type="text" id="productId" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="productName">Nama Produk</label>
                    <input type="text" id="productName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="productBrand">Merk</label>
                    <input type="text" id="productBrand" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="productPrice">Harga</label>
                    <input type="text" id="productPrice" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="productStock">Stok</label>
                    <input type="number" id="productStock" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="productImage">Gambar (URL)</label>
                    <input type="text" id="productImage" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal" id="editProductModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Produk</h2>
                <span class="close-modal">&times;</span>
            </div>
            <form id="editProductForm">
                <div class="form-group">
                    <label for="editProductId">ID Produk</label>
                    <input type="text" id="editProductId" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="editProductName">Nama Produk</label>
                    <input type="text" id="editProductName" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editProductBrand">Merk</label>
                    <input type="text" id="editProductBrand" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editProductPrice">Harga</label>
                    <input type="text" id="editProductPrice" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editProductStock">Stok</label>
                    <input type="number" id="editProductStock" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="editProductImage">Gambar (URL)</label>
                    <input type="text" id="editProductImage" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteProductModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle" style="color: var(--danger);"></i> Konfirmasi Hapus</h2>
                <span class="close-modal">&times;</span>
            </div>
            <div style="padding: 20px;">
                <p>Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteProduct">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const addProductBtn = document.getElementById('addProductBtn');
        const searchInput = document.getElementById('searchInput');
        const productsTable = document.getElementById('productsTable');
        
        // Modal Elements
        const addProductModal = document.getElementById('addProductModal');
        const editProductModal = document.getElementById('editProductModal');
        const deleteProductModal = document.getElementById('deleteProductModal');
        const closeModalBtns = document.querySelectorAll('.close-modal, .btn-cancel');
        const confirmDeleteProductBtn = document.getElementById('confirmDeleteProduct');
        
        // Form Elements
        const addProductForm = document.getElementById('addProductForm');
        const editProductForm = document.getElementById('editProductForm');
        
        // Product ID to be deleted
        let productToDelete = null;
        
        // Event Listeners
        addProductBtn.addEventListener('click', () => {
            addProductModal.style.display = 'flex';
        });
        
        // Search functionality
        searchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = productsTable.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const productBrand = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || productBrand.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Close modals
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                addProductModal.style.display = 'none';
                editProductModal.style.display = 'none';
                deleteProductModal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === addProductModal) addProductModal.style.display = 'none';
            if (e.target === editProductModal) editProductModal.style.display = 'none';
            if (e.target === deleteProductModal) deleteProductModal.style.display = 'none';
        });
        
        // Add new product
        addProductForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Get form values
            const productId = document.getElementById('productId').value;
            const productName = document.getElementById('productName').value;
            const productBrand = document.getElementById('productBrand').value;
            const productPrice = document.getElementById('productPrice').value;
            const productStock = document.getElementById('productStock').value;
            const productImage = document.getElementById('productImage').value;
            const productStatus = productStock > 0 ? 'available' : 'out-of-stock';
            
            // Create new row
            const newRow = document.createElement('tr');
            newRow.id = 'product' + (productsTable.querySelectorAll('tbody tr').length + 1);
            newRow.innerHTML = `
                <td>${productId}</td>
                <td><img src="${productImage || 'https://via.placeholder.com/50'}" alt="${productName}" class="product-img"></td>
                <td>${productName}</td>
                <td>${productBrand}</td>
                <td>${productPrice}</td>
                <td>${productStock}</td>
                <td><span class="status ${productStatus}">${productStock > 0 ? 'Tersedia' : 'Habis'}</span></td>
                <td>
                    <button class="action-btn edit-btn" data-product-id="${newRow.id}"><i class="fas fa-edit"></i> Edit</button>
                    <button class="action-btn delete-btn" data-product-id="${newRow.id}"><i class="fas fa-trash"></i> Hapus</button>
                </td>
            `;
            
            // Add to table
            productsTable.querySelector('tbody').appendChild(newRow);
            
            // Add event listeners to new buttons
            document.querySelector(`#${newRow.id} .edit-btn`).addEventListener('click', handleEditProduct);
            document.querySelector(`#${newRow.id} .delete-btn`).addEventListener('click', handleDeleteProduct);
            
            // Reset form and close modal
            addProductForm.reset();
            addProductModal.style.display = 'none';
            alert('Produk baru berhasil ditambahkan');
        });
        
        // Edit product buttons
        function handleEditProduct(e) {
            const productId = e.target.getAttribute('data-product-id');
            const productRow = document.getElementById(productId);
            
            // Get current values
            const cells = productRow.querySelectorAll('td');
            const productImage = productRow.querySelector('img').src;
            
            // Populate form
            document.getElementById('editProductId').value = cells[0].textContent;
            document.getElementById('editProductName').value = cells[2].textContent;
            document.getElementById('editProductBrand').value = cells[3].textContent;
            document.getElementById('editProductPrice').value = cells[4].textContent;
            document.getElementById('editProductStock').value = cells[5].textContent;
            document.getElementById('editProductImage').value = productImage;
            
            // Set form submit handler
            editProductForm.onsubmit = function(e) {
                e.preventDefault();
                
                // Update product row
                cells[2].textContent = document.getElementById('editProductName').value;
                cells[3].textContent = document.getElementById('editProductBrand').value;
                cells[4].textContent = document.getElementById('editProductPrice').value;
                cells[5].textContent = document.getElementById('editProductStock').value;
                
                // Update image
                const newImage = document.getElementById('editProductImage').value;
                productRow.querySelector('img').src = newImage || 'https://via.placeholder.com/50';
                
                // Update status
                const status = cells[6].querySelector('span');
                const stock = parseInt(document.getElementById('editProductStock').value);
                if (stock > 0) {
                    status.className = 'status available';
                    status.textContent = 'Tersedia';
                } else {
                    status.className = 'status out-of-stock';
                    status.textContent = 'Habis';
                }
                
                editProductModal.style.display = 'none';
                alert('Perubahan produk berhasil disimpan');
            };
            
            editProductModal.style.display = 'flex';
        }
        
        // Delete product buttons
        function handleDeleteProduct(e) {
            productToDelete = e.target.getAttribute('data-product-id');
            deleteProductModal.style.display = 'flex';
        }
        
        // Confirm delete
        confirmDeleteProductBtn.addEventListener('click', () => {
            document.getElementById(productToDelete).remove();
            deleteProductModal.style.display = 'none';
            alert('Produk berhasil dihapus');
        });
        
        // Initialize event listeners for existing buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', handleEditProduct);
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', handleDeleteProduct);
        });
    </script>
</body>
</html>