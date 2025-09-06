<?php
session_start();
include 'koneksi.php';

// Validasi login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];
$name = $_SESSION['name'] ?? 'Pemilik Toko';

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Toko Handphone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2980b9;
            --dark: #2c3e50;
            --light: #f4f6f9;
            --danger: #e74c3c;
            --success: #27ae60;
            --warning: #f39c12;
            --radius: 12px;
            --transition: all 0.3s ease;
        }
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif;}
        body {background: var(--light); color: var(--dark);}

        /* Layout */
        .dashboard {display: grid; grid-template-columns: 260px 1fr; min-height: 100vh;}
        .sidebar {
            background: var(--dark); color: white; display: flex; flex-direction: column;
            justify-content: space-between; padding: 20px 0; position: sticky; top: 0;
        }
        .logo {text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);}
        .logo h2 {font-weight: 700; color: white;}
        .logo span {color: var(--primary);}
        .nav-menu {padding: 0 15px; flex: 1;}
        .nav-item {margin-bottom: 8px;}
        .nav-link {
            display: flex; align-items: center; padding: 12px 15px;
            border-radius: var(--radius); color: rgba(255,255,255,0.8);
            text-decoration: none; transition: var(--transition);
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.12); color: white;
        }
        .nav-link i {margin-right: 12px; font-size: 18px;}

        .user-profile {
            padding: 15px 20px; display: flex; align-items: center; border-top: 1px solid rgba(255,255,255,0.1);
        }
        .user-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--primary); display: flex; align-items: center; justify-content: center;
            font-weight: bold; color: white; margin-right: 10px;
        }
        .logout-btn {
            background: var(--danger); border: none; color: white;
            padding: 8px 14px; border-radius: var(--radius); cursor: pointer;
            width: 100%; margin-top: 10px; transition: var(--transition);
        }
        .logout-btn:hover {background: #c0392b;}

        /* Main Content */
        .main-content {padding: 30px;}
        .header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap;}
        .header h1 {font-size: 26px;}
        .search-bar {
            display: flex; align-items: center; background: white; border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 6px 15px;
        }
        .search-bar input {
            border: none; outline: none; padding: 6px; min-width: 200px;
        }
        .search-bar i {color: #777; margin-right: 8px;}

        /* Action Bar */
        .action-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; flex-wrap: wrap; gap: 15px;
        }
        .btn {
            padding: 10px 20px; border-radius: var(--radius); cursor: pointer;
            transition: var(--transition); font-weight: 500; border: none;
            display: inline-flex; align-items: center;
        }
        .btn i {margin-right: 8px;}
        .btn-primary {
            background: var(--primary); color: white;
        }
        .btn-primary:hover {
            background: var(--secondary);
        }
        .btn-success {
            background: var(--success); color: white;
        }
        .btn-success:hover {
            background: #219653;
        }
        .btn-danger {
            background: var(--danger); color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .filter-group {
            display: flex; align-items: center; gap: 10px;
        }
        .filter-group select {
            padding: 8px 12px; border: 1px solid #ddd; border-radius: var(--radius);
            outline: none; transition: var(--transition);
        }
        .filter-group select:focus {
            border-color: var(--primary);
        }

        /* Product Table */
        .product-table {
            width: 100%; background: white; border-radius: var(--radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-collapse: collapse;
            margin-top: 20px;
        }
        .product-table th, .product-table td {
            padding: 15px; text-align: left; border-bottom: 1px solid #eee;
        }
        .product-table th {
            background: var(--primary); color: white; font-weight: 500;
        }
        .product-table tr:hover {background: rgba(52, 152, 219, 0.05);}
        .product-table .stock {font-weight: bold;}
        .product-table .in-stock {color: var(--success);}
        .product-table .low-stock {color: var(--warning);}
        .product-table .out-stock {color: var(--danger);}
        .product-table .actions {display: flex; gap: 8px;}
        .product-table .actions .btn {
            padding: 6px 12px; font-size: 13px;
        }
        .product-img {
            width: 50px; height: 50px; object-fit: cover; border-radius: 5px;
        }

        /* Pagination */
        .pagination {
            display: flex; justify-content: center; margin-top: 25px;
            gap: 5px;
        }
        .page-item {
            width: 35px; height: 35px; display: flex;
            align-items: center; justify-content: center;
            border-radius: 5px; cursor: pointer;
        }
        .page-item:hover {
            background: #eee;
        }
        .page-item.active {
            background: var(--primary); color: white;
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
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            border-radius: var(--radius);
            width: 500px;
            max-width: 90%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s;
        }
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            font-weight: 600;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #777;
        }
        .modal-body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: var(--radius);
            transition: var(--transition);
        }
        .form-control:focus {
            border-color: var(--primary);
            outline: none;
        }
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Responsive */
        @media(max-width: 768px) {
            .dashboard{grid-template-columns: 1fr;} 
            .sidebar{display: none;}
            .action-bar {flex-direction: column; align-items: flex-start;}
            .product-table {font-size: 14px;}
            .product-table .actions {flex-direction: column; gap: 5px;}
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <div class="logo"><h2>Toko <span>Handphone</span></h2></div>
                <div class="nav-menu">
                    <div class="nav-item"><a href="owner.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></div>
                    <div class="nav-item"><a href="produkowner.php" class="nav-link active"><i class="fas fa-mobile-alt"></i> Produk</a></div>
                    <div class="nav-item"><a href="pesanan.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Pesanan</a></div>
                    <div class="nav-item"><a href="pelanggan.php" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a></div>
                    <div class="nav-item"><a href="laporan.php" class="nav-link"><i class="fas fa-chart-pie"></i> Laporan</a></div>
                    <div class="nav-item"><a href="pengaturan.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></div>
                </div>
            </div>
            <div>
                <div class="user-profile">
                    <div class="user-avatar">PT</div>
                    <div>
                        <h4>Pemilik Toko</h4>
                        <p>Owner</p>
                    </div>
                </div>
                <form action="logout.php" method="post" style="padding: 0 20px;">
                    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>

        <!-- Main -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Manajemen Produk</h1>
                    <p>Kelola produk handphone di toko Anda</p>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari produk..." id="searchInput">
                </div>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div>
                    <button class="btn btn-primary" id="addProductBtn"><i class="fas fa-plus"></i> Tambah Produk</button>
                </div>
                <div class="filter-group">
                    <span>Filter:</span>
                    <select id="categoryFilter">
                        <option value="all">Semua Kategori</option>
                        <option value="Smartphone">Smartphone</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Sparepart">Sparepart</option>
                    </select>
                    <select id="stockFilter">
                        <option value="all">Semua Stok</option>
                        <option value="in-stock">Tersedia</option>
                        <option value="low-stock">Hampir Habis</option>
                        <option value="out-stock">Habis</option>
                    </select>
                </div>
            </div>

            <!-- Product Table -->
            <table class="product-table" id="productTable">
                <thead>
                    <tr>
                        <th width="50px">#</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Terjual</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <!-- Data produk akan diisi oleh JavaScript -->
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Produk -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Produk Baru</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="productId">
                    <div class="form-group">
                        <label for="productName">Nama Produk</label>
                        <input type="text" id="productName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productSku">SKU</label>
                        <input type="text" id="productSku" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Kategori</label>
                        <select id="productCategory" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Smartphone">Smartphone</option>
                            <option value="Aksesoris">Aksesoris</option>
                            <option value="Sparepart">Sparepart</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Harga (Rp)</label>
                        <input type="number" id="productPrice" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productStock">Stok</label>
                        <input type="number" id="productStock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productSold">Terjual</label>
                        <input type="number" id="productSold" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="productImage">Gambar Produk</label>
                        <input type="file" id="productImage" class="form-control">
                        <small class="text-muted">Format: JPG, PNG (Maks. 2MB)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="cancelBtn">Batal</button>
                <button class="btn btn-primary" id="saveProductBtn">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal" id="confirmModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Konfirmasi Hapus</h3>
                <button class="modal-close" id="closeConfirmModal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus produk ini?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="cancelDeleteBtn">Batal</button>
                <button class="btn btn-primary" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        // Data Produk
        let products = [
            {
                id: 1,
                name: "iPhone 13 128GB",
                sku: "IP13-128-BLK",
                category: "Smartphone",
                price: 12500000,
                stock: 25,
                sold: 48,
                image: "https://via.placeholder.com/50"
            },
            {
                id: 2,
                name: "Samsung Galaxy S22 Ultra",
                sku: "SGS22U-256-GRY",
                category: "Smartphone",
                price: 18999000,
                stock: 5,
                sold: 32,
                image: "https://via.placeholder.com/50"
            },
            {
                id: 3,
                name: "Xiaomi 11T Pro 5G",
                sku: "X11TP-128-BLU",
                category: "Smartphone",
                price: 8750000,
                stock: 18,
                sold: 27,
                image: "https://via.placeholder.com/50"
            },
            {
                id: 4,
                name: "OPPO Reno 7 5G",
                sku: "OR7-256-SLV",
                category: "Smartphone",
                price: 6999000,
                stock: 0,
                sold: 15,
                image: "https://via.placeholder.com/50"
            },
            {
                id: 5,
                name: "Vivo V23 5G",
                sku: "VV23-128-GLD",
                category: "Smartphone",
                price: 5499000,
                stock: 12,
                sold: 9,
                image: "https://via.placeholder.com/50"
            },
            {
                id: 6,
                name: "Case iPhone 13 Pro",
                sku: "CIP13P-SIL",
                category: "Aksesoris",
                price: 250000,
                stock: 45,
                sold: 23,
                image: "https://via.placeholder.com/50"
            },
            {
                id: 7,
                name: "LCD Samsung S21",
                sku: "LCD-SS21-ORG",
                category: "Sparepart",
                price: 1200000,
                stock: 3,
                sold: 8,
                image: "https://via.placeholder.com/50"
            }
        ];

        // DOM Elements
        const productTableBody = document.getElementById('productTableBody');
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const stockFilter = document.getElementById('stockFilter');
        const addProductBtn = document.getElementById('addProductBtn');
        const productModal = document.getElementById('productModal');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const saveProductBtn = document.getElementById('saveProductBtn');
        const productForm = document.getElementById('productForm');
        const confirmModal = document.getElementById('confirmModal');
        const closeConfirmModal = document.getElementById('closeConfirmModal');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const modalTitle = document.getElementById('modalTitle');
        
        // Variabel untuk manajemen state
        let currentPage = 1;
        const productsPerPage = 5;
        let productToDelete = null;
        let isEditing = false;

        // Fungsi untuk menampilkan produk
        function displayProducts(productsToDisplay, page = 1) {
            productTableBody.innerHTML = '';
            
            const startIndex = (page - 1) * productsPerPage;
            const endIndex = startIndex + productsPerPage;
            const paginatedProducts = productsToDisplay.slice(startIndex, endIndex);
            
            if (paginatedProducts.length === 0) {
                productTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">
                            Tidak ada produk yang ditemukan
                        </td>
                    </tr>
                `;
                return;
            }
            
            paginatedProducts.forEach((product, index) => {
                const row = document.createElement('tr');
                
                // Tentukan kelas stok berdasarkan jumlah
                let stockClass = 'in-stock';
                if (product.stock === 0) {
                    stockClass = 'out-stock';
                } else if (product.stock <= 5) {
                    stockClass = 'low-stock';
                }
                
                row.innerHTML = `
                    <td>${startIndex + index + 1}</td>
                    <td style="display: flex; align-items: center; gap: 15px;">
                        <img src="${product.image}" alt="${product.name}" class="product-img">
                        <div>
                            <div style="font-weight: 500;">${product.name}</div>
                            <small style="color: #777;">SKU: ${product.sku}</small>
                        </div>
                    </td>
                    <td>${product.category}</td>
                    <td>Rp ${formatNumber(product.price)}</td>
                    <td class="stock ${stockClass}">${product.stock}</td>
                    <td>${product.sold}</td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-primary edit-btn" data-id="${product.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-btn" data-id="${product.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                productTableBody.appendChild(row);
            });
            
            // Setup event listeners untuk tombol edit dan delete
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const productId = parseInt(e.currentTarget.getAttribute('data-id'));
                    editProduct(productId);
                });
            });
            
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const productId = parseInt(e.currentTarget.getAttribute('data-id'));
                    showDeleteConfirmation(productId);
                });
            });
            
            // Update pagination
            updatePagination(productsToDisplay.length, page);
        }

        // Fungsi untuk memformat angka (harga)
        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        }

        // Fungsi untuk update pagination
        function updatePagination(totalProducts, currentPage) {
            const totalPages = Math.ceil(totalProducts / productsPerPage);
            const paginationContainer = document.getElementById('pagination');
            
            paginationContainer.innerHTML = '';
            
            if (totalPages <= 1) return;
            
            // Tombol Previous
            const prevBtn = document.createElement('div');
            prevBtn.className = 'page-item';
            prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            if (currentPage === 1) {
                prevBtn.style.opacity = '0.5';
                prevBtn.style.cursor = 'not-allowed';
            } else {
                prevBtn.addEventListener('click', () => {
                    displayProducts(filterProducts(), currentPage - 1);
                });
            }
            paginationContainer.appendChild(prevBtn);
            
            // Nomor halaman
            for (let i = 1; i <= totalPages; i++) {
                const pageItem = document.createElement('div');
                pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageItem.textContent = i;
                pageItem.addEventListener('click', () => {
                    if (i !== currentPage) {
                        displayProducts(filterProducts(), i);
                    }
                });
                paginationContainer.appendChild(pageItem);
            }
            
            // Tombol Next
            const nextBtn = document.createElement('div');
            nextBtn.className = 'page-item';
            nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            if (currentPage === totalPages) {
                nextBtn.style.opacity = '0.5';
                nextBtn.style.cursor = 'not-allowed';
            } else {
                nextBtn.addEventListener('click', () => {
                    displayProducts(filterProducts(), currentPage + 1);
                });
            }
            paginationContainer.appendChild(nextBtn);
        }

        // Fungsi untuk memfilter produk berdasarkan pencarian dan filter
        function filterProducts() {
            const searchTerm = searchInput.value.toLowerCase();
            const category = categoryFilter.value;
            const stock = stockFilter.value;
            
            return products.filter(product => {
                // Filter berdasarkan pencarian
                const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                     product.sku.toLowerCase().includes(searchTerm);
                
                // Filter berdasarkan kategori
                const matchesCategory = category === 'all' || product.category === category;
                
                // Filter berdasarkan stok
                let matchesStock = true;
                if (stock === 'in-stock') {
                    matchesStock = product.stock > 5;
                } else if (stock === 'low-stock') {
                    matchesStock = product.stock > 0 && product.stock <= 5;
                } else if (stock === 'out-stock') {
                    matchesStock = product.stock === 0;
                }
                
                return matchesSearch && matchesCategory && matchesStock;
            });
        }

        // Fungsi untuk membuka modal tambah produk
        function openAddProductModal() {
            isEditing = false;
            modalTitle.textContent = 'Tambah Produk Baru';
            productForm.reset();
            document.getElementById('productId').value = '';
            productModal.style.display = 'flex';
        }

        // Fungsi untuk membuka modal edit produk
        function editProduct(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            
            isEditing = true;
            modalTitle.textContent = 'Edit Produk';
            
            document.getElementById('productId').value = product.id;
            document.getElementById('productName').value = product.name;
            document.getElementById('productSku').value = product.sku;
            document.getElementById('productCategory').value = product.category;
            document.getElementById('productPrice').value = product.price;
            document.getElementById('productStock').value = product.stock;
            document.getElementById('productSold').value = product.sold;
            
            productModal.style.display = 'flex';
        }

        // Fungsi untuk menyimpan produk (tambah/edit)
        function saveProduct() {
            const id = document.getElementById('productId').value;
            const name = document.getElementById('productName').value;
            const sku = document.getElementById('productSku').value;
            const category = document.getElementById('productCategory').value;
            const price = parseInt(document.getElementById('productPrice').value);
            const stock = parseInt(document.getElementById('productStock').value);
            const sold = parseInt(document.getElementById('productSold').value);
            
            if (isEditing) {
                // Edit produk yang ada
                const index = products.findIndex(p => p.id === parseInt(id));
                if (index !== -1) {
                    products[index] = {
                        ...products[index],
                        name,
                        sku,
                        category,
                        price,
                        stock,
                        sold
                    };
                }
            } else {
                // Tambah produk baru
                const newId = products.length > 0 ? Math.max(...products.map(p => p.id)) + 1 : 1;
                products.push({
                    id: newId,
                    name,
                    sku,
                    category,
                    price,
                    stock,
                    sold,
                    image: "https://via.placeholder.com/50"
                });
            }
            
            // Tutup modal dan refresh tampilan
            productModal.style.display = 'none';
            displayProducts(filterProducts(), currentPage);
        }

        // Fungsi untuk menampilkan konfirmasi hapus
        function showDeleteConfirmation(productId) {
            productToDelete = productId;
            confirmModal.style.display = 'flex';
        }

        // Fungsi untuk menghapus produk
        function deleteProduct() {
            products = products.filter(p => p.id !== productToDelete);
            confirmModal.style.display = 'none';
            displayProducts(filterProducts(), currentPage);
        }

        // Event Listeners
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            displayProducts(filterProducts(), currentPage);
        });

        categoryFilter.addEventListener('change', () => {
            currentPage = 1;
            displayProducts(filterProducts(), currentPage);
        });

        stockFilter.addEventListener('change', () => {
            currentPage = 1;
            displayProducts(filterProducts(), currentPage);
        });

        addProductBtn.addEventListener('click', openAddProductModal);

        closeModal.addEventListener('click', () => {
            productModal.style.display = 'none';
        });

        cancelBtn.addEventListener('click', () => {
            productModal.style.display = 'none';
        });

        saveProductBtn.addEventListener('click', saveProduct);

        closeConfirmModal.addEventListener('click', () => {
            confirmModal.style.display = 'none';
        });

        cancelDeleteBtn.addEventListener('click', () => {
            confirmModal.style.display = 'none';
        });

        confirmDeleteBtn.addEventListener('click', deleteProduct);

        // Tutup modal saat klik di luar modal
        window.addEventListener('click', (e) => {
            if (e.target === productModal) {
                productModal.style.display = 'none';
            }
            if (e.target === confirmModal) {
                confirmModal.style.display = 'none';
            }
        });

        // Inisialisasi tampilan
        displayProducts(products, currentPage);
    </script>
</body>
</html>