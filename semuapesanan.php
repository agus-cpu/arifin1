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
    <title>Semua Pesanan - Toko Handphone</title>
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

        .settings-link {
            display: block;
            margin-top: 15px;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .settings-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .settings-link i {
            margin-right: 8px;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            background: #f1f5f9;
        }

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

        .card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .orders-table th {
            text-align: left;
            padding: 12px 15px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
        }

        .orders-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .orders-table tr:hover {
            background: #f8fafc;
        }

        .order-id {
            color: var(--primary);
            font-weight: 500;
        }

        .customer-name {
            font-weight: 500;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .status-processing {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-light);
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: var(--radius);
            border: none;
            background: var(--primary);
            color: white;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .action-btn:hover {
            background: var(--primary-hover);
        }

        .action-btn.view {
            background: var(--success);
        }

        .action-btn.view:hover {
            background: #0da271;
        }

        .action-btn.delete {
            background: var(--danger);
        }

        .action-btn.delete:hover {
            background: #dc2626;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: var(--radius);
            width: 50%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        @media (max-width: 992px) {
            .side-nav {
                width: 80px;
                padding: 15px 0;
            }

            .nav-header h2, .nav-link span, .user-name, .user-role, .settings-link span {
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

            .settings-link {
                text-align: center;
                padding: 10px 5px;
            }

            .settings-link i {
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

            .orders-table {
                display: block;
                overflow-x: auto;
            }

            .modal-content {
                width: 90%;
                margin: 20% auto;
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
                    <a href="semuaproduk.php" class="nav-link">
                        <i class="fas fa-boxes"></i>
                        <span>Semua Produk</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuapesanan.php" class="nav-link active">
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
                    <h1>Semua Pesanan</h1>
                    <p>Kelola semua pesanan pelanggan Anda di sini.</p>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari pesanan..." onkeyup="searchOrders()">
                </div>
            </div>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: var(--dark);">Daftar Pesanan</h2>
                    <div>
                        <select id="statusFilter" style="padding: 8px 12px; border-radius: var(--radius); border: 1px solid #e2e8f0; margin-right: 10px;">
                            <option value="all">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Diproses</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                        <button class="action-btn" onclick="filterOrders()" style="padding: 8px 15px;">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>

                <table class="orders-table" id="ordersTable">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <!-- Orders will be populated by JavaScript -->
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <div id="paginationInfo" style="color: #64748b; font-size: 0.9rem;">
                        Menampilkan 1-5 dari 15 pesanan
                    </div>
                    <div>
                        <button id="prevBtn" class="action-btn" style="margin-right: 10px;" onclick="changePage(-1)"><i class="fas fa-chevron-left"></i> Sebelumnya</button>
                        <button id="nextBtn" class="action-btn" onclick="changePage(1)">Selanjutnya <i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Detail Pesanan #ORD-1256</h2>
            <div id="modalContent">
                <!-- Modal content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Edit Status Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2 id="editModalTitle">Edit Status Pesanan</h2>
            <div id="editModalContent">
                <p>Pesanan: <span id="editOrderId"></span></p>
                <p>Pelanggan: <span id="editCustomerName"></span></p>
                <div style="margin: 20px 0;">
                    <label for="statusSelect">Status:</label>
                    <select id="statusSelect" style="padding: 8px 12px; border-radius: var(--radius); border: 1px solid #e2e8f0; margin-left: 10px; width: 150px;">
                        <option value="pending">Pending</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <button class="action-btn" onclick="saveStatus()" style="padding: 8px 15px;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <script>
        // Sample order data (changed from const to let)
        let orders = [
            { id: "ORD-1256", customer: "Budi Santoso", date: "12 Agu 2023", total: "Rp 5.250.000", status: "pending" },
            { id: "ORD-1255", customer: "Ani Fitriani", date: "11 Agu 2023", total: "Rp 3.750.000", status: "processing" },
            { id: "ORD-1254", customer: "Dewi Kurnia", date: "10 Agu 2023", total: "Rp 8.990.000", status: "completed" },
            { id: "ORD-1253", customer: "Rudi Hermawan", date: "9 Agu 2023", total: "Rp 6.450.000", status: "cancelled" },
            { id: "ORD-1252", customer: "Siti Rahayu", date: "8 Agu 2023", total: "Rp 4.200.000", status: "completed" },
            { id: "ORD-1251", customer: "Joko Widodo", date: "7 Agu 2023", total: "Rp 7.800.000", status: "completed" },
            { id: "ORD-1250", customer: "Mega Putri", date: "6 Agu 2023", total: "Rp 5.500.000", status: "processing" },
            { id: "ORD-1249", customer: "Agus Suparman", date: "5 Agu 2023", total: "Rp 3.200.000", status: "pending" },
            { id: "ORD-1248", customer: "Linda Sari", date: "4 Agu 2023", total: "Rp 9.750.000", status: "completed" },
            { id: "ORD-1247", customer: "Bambang Pamungkas", date: "3 Agu 2023", total: "Rp 6.300.000", status: "cancelled" },
            { id: "ORD-1246", customer: "Dian Novita", date: "2 Agu 2023", total: "Rp 4.800.000", status: "completed" },
            { id: "ORD-1245", customer: "Eko Pratama", date: "1 Agu 2023", total: "Rp 5.600.000", status: "processing" },
            { id: "ORD-1244", customer: "Fitri Handayani", date: "31 Jul 2023", total: "Rp 3.900.000", status: "completed" },
            { id: "ORD-1243", customer: "Gunawan Setiawan", date: "30 Jul 2023", total: "Rp 7.200.000", status: "pending" },
            { id: "ORD-1242", customer: "Hesti Wulandari", date: "29 Jul 2023", total: "Rp 6.100.000", status: "completed" }
        ];

        // Pagination variables
        let currentPage = 1;
        const rowsPerPage = 5;
        let filteredOrders = [...orders];
        let currentEditingOrder = null;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            renderOrders();
            updatePaginationInfo();
        });

        // Render orders based on current page and filters
        function renderOrders() {
            const tableBody = document.getElementById('ordersTableBody');
            tableBody.innerHTML = '';

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const ordersToShow = filteredOrders.slice(startIndex, endIndex);

            ordersToShow.forEach(order => {
                const row = document.createElement('tr');
                
                // Determine status class and text
                let statusClass, statusText;
                switch(order.status) {
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = 'Pending';
                        break;
                    case 'processing':
                        statusClass = 'status-processing';
                        statusText = 'Diproses';
                        break;
                    case 'completed':
                        statusClass = 'status-completed';
                        statusText = 'Selesai';
                        break;
                    case 'cancelled':
                        statusClass = 'status-cancelled';
                        statusText = 'Dibatalkan';
                        break;
                }

                row.innerHTML = `
                    <td class="order-id">#${order.id}</td>
                    <td class="customer-name">${order.customer}</td>
                    <td>${order.date}</td>
                    <td>${order.total}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="action-btn view" onclick="viewOrder('${order.id}')"><i class="fas fa-eye"></i></button>
                        <button class="action-btn" onclick="editOrder('${order.id}')"><i class="fas fa-edit"></i></button>
                        <button class="action-btn delete" onclick="deleteOrder('${order.id}')"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Update pagination buttons
            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = endIndex >= filteredOrders.length;
            
            // Update badge count
            document.querySelector('.nav-badge').textContent = filteredOrders.length;
        }

        // Update pagination information
        function updatePaginationInfo() {
            const startIndex = (currentPage - 1) * rowsPerPage + 1;
            const endIndex = Math.min(currentPage * rowsPerPage, filteredOrders.length);
            document.getElementById('paginationInfo').textContent = 
                `Menampilkan ${startIndex}-${endIndex} dari ${filteredOrders.length} pesanan`;
        }

        // Change page
        function changePage(direction) {
            const newPage = currentPage + direction;
            const totalPages = Math.ceil(filteredOrders.length / rowsPerPage);
            
            if (newPage > 0 && newPage <= totalPages) {
                currentPage = newPage;
                renderOrders();
                updatePaginationInfo();
            }
        }

        // Filter orders by status
        function filterOrders() {
            const statusFilter = document.getElementById('statusFilter').value;
            
            if (statusFilter === 'all') {
                filteredOrders = [...orders];
            } else {
                filteredOrders = orders.filter(order => order.status === statusFilter);
            }
            
            currentPage = 1;
            renderOrders();
            updatePaginationInfo();
        }

        // Search orders
        function searchOrders() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            if (searchTerm === '') {
                filteredOrders = [...orders];
            } else {
                filteredOrders = orders.filter(order => 
                    order.id.toLowerCase().includes(searchTerm) || 
                    order.customer.toLowerCase().includes(searchTerm) ||
                    order.date.toLowerCase().includes(searchTerm) ||
                    order.total.toLowerCase().includes(searchTerm) ||
                    order.status.toLowerCase().includes(searchTerm)
                );
            }
            
            currentPage = 1;
            renderOrders();
            updatePaginationInfo();
        }

        // View order details
        function viewOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (!order) return;

            document.getElementById('modalTitle').textContent = `Detail Pesanan #${order.id}`;
            
            // Create detailed content (in a real app, this would come from a database)
            let statusText;
            switch(order.status) {
                case 'pending': statusText = 'Pending'; break;
                case 'processing': statusText = 'Diproses'; break;
                case 'completed': statusText = 'Selesai'; break;
                case 'cancelled': statusText = 'Dibatalkan'; break;
            }
            
            const modalContent = `
                <div style="margin-bottom: 20px;">
                    <p><strong>Pelanggan:</strong> ${order.customer}</p>
                    <p><strong>Tanggal:</strong> ${order.date}</p>
                    <p><strong>Status:</strong> ${statusText}</p>
                    <p><strong>Total:</strong> ${order.total}</p>
                </div>
                <h3 style="margin-bottom: 10px;">Produk:</h3>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 8px; text-align: left;">Produk</th>
                            <th style="padding: 8px; text-align: right;">Harga</th>
                            <th style="padding: 8px; text-align: center;">Qty</th>
                            <th style="padding: 8px; text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 8px;">Samsung Galaxy S23</td>
                            <td style="padding: 8px; text-align: right;">Rp 12.999.000</td>
                            <td style="padding: 8px; text-align: center;">1</td>
                            <td style="padding: 8px; text-align: right;">Rp 12.999.000</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 8px;">Case Samsung Galaxy S23</td>
                            <td style="padding: 8px; text-align: right;">Rp 350.000</td>
                            <td style="padding: 8px; text-align: center;">1</td>
                            <td style="padding: 8px; text-align: right;">Rp 350.000</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="padding: 8px; text-align: right;"><strong>Total:</strong></td>
                            <td style="padding: 8px; text-align: right;"><strong>Rp 13.349.000</strong></td>
                        </tr>
                    </tfoot>
                </table>
                <div style="margin-top: 20px;">
                    <h3 style="margin-bottom: 10px;">Informasi Pengiriman:</h3>
                    <p><strong>Alamat:</strong> Jl. Merdeka No. 123, Jakarta Selatan</p>
                    <p><strong>Kurir:</strong> JNE Reguler</p>
                    <p><strong>No. Resi:</strong> JNE1234567890</p>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('orderModal').style.display = 'block';
        }

        // Edit order status
        function editOrder(orderId) {
            const order = orders.find(o => o.id === orderId);
            if (!order) return;

            currentEditingOrder = order;
            document.getElementById('editOrderId').textContent = `#${order.id}`;
            document.getElementById('editCustomerName').textContent = order.customer;
            document.getElementById('statusSelect').value = order.status;
            document.getElementById('editModal').style.display = 'block';
        }

        // Save edited status
        function saveStatus() {
            if (!currentEditingOrder) return;

            const newStatus = document.getElementById('statusSelect').value;
            currentEditingOrder.status = newStatus;
            
            // Update filtered orders if needed
            const statusFilter = document.getElementById('statusFilter').value;
            if (statusFilter !== 'all' && statusFilter !== newStatus) {
                filteredOrders = filteredOrders.filter(order => order.id !== currentEditingOrder.id);
            }
            
            closeEditModal();
            renderOrders();
            alert('Status pesanan berhasil diperbarui!');
        }

        // Delete order - FUNCTION THAT WAS FIXED
        function deleteOrder(orderId) {
            if (confirm('Apakah Anda yakin ingin menghapus pesanan ini?')) {
                // Find index in orders array
                const index = orders.findIndex(o => o.id === orderId);
                
                if (index !== -1) {
                    // Remove from orders array
                    orders.splice(index, 1);
                    
                    // Update filteredOrders based on current filter
                    const statusFilter = document.getElementById('statusFilter').value;
                    if (statusFilter === 'all') {
                        filteredOrders = [...orders];
                    } else {
                        filteredOrders = orders.filter(order => order.status === statusFilter);
                    }
                    
                    // Adjust current page if needed
                    const totalPages = Math.ceil(filteredOrders.length / rowsPerPage);
                    if (currentPage > totalPages) {
                        currentPage = Math.max(1, totalPages);
                    }
                    
                    // Re-render the table
                    renderOrders();
                    updatePaginationInfo();
                    alert('Pesanan berhasil dihapus!');
                }
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        // Close edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            currentEditingOrder = null;
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                closeModal();
                closeEditModal();
            }
        }
    </script>
</body>
</html>