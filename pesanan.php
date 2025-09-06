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
    <title>Pesanan - Toko Handphone</title>
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
            cursor: pointer;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.12); color: white;
        }
        .nav-link i {margin-right: 12px; font-size: 18px;}

        .user-profile {
            padding: 15px 20px; display: flex; align-items: center; border-top: 1px solid rgba(255,255,255,0.1);
            cursor: pointer;
        }
        .user-profile:hover {background: rgba(255,255,255,0.05);}
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
        .logout-btn:hover {background: #c0392b; transform: translateY(-2px);}
        .logout-btn:active {transform: translateY(0);}

        /* Main Content */
        .main-content {padding: 30px;}
        .header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap;}
        .header h1 {font-size: 26px;}
        .search-bar {
            display: flex; align-items: center; background: white; border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 6px 15px;
            transition: var(--transition);
        }
        .search-bar:hover {box-shadow: 0 2px 15px rgba(0,0,0,0.1);}
        .search-bar input {
            border: none; outline: none; padding: 6px; min-width: 200px;
        }
        .search-bar i {color: #777; margin-right: 8px;}

        /* Cards */
        .stats-container {display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); margin-bottom: 30px;}
        .stat-card {
            background: white; border-radius: var(--radius); padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: var(--transition);
            cursor: pointer;
        }
        .stat-card:hover {transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1);}
        .stat-header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;}
        .stat-title {font-size: 14px; color: #777;}
        .stat-icon {
            width: 45px; height: 45px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;
        }
        .icon-1 {background: var(--primary);} .icon-2 {background: var(--warning);}
        .icon-3 {background: var(--success);} .icon-4 {background: var(--danger);}
        .stat-value {font-size: 26px; font-weight: 700; margin-bottom: 5px;}
        .stat-change {font-size: 13px; font-weight: 500; display: flex; align-items: center;}
        .stat-change i {margin-right: 4px;}

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--radius); padding: 25px; color: white;
            margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;
            cursor: pointer;
        }
        .welcome-card:hover {transform: translateY(-3px); box-shadow: 0 10px 20px rgba(52,152,219,0.3);}
        .welcome-text h2 {font-size: 22px;}
        .welcome-image i {font-size: 90px; opacity: 0.15;}

        /* Order Table */
        .order-table {
            width: 100%; background: white; border-radius: var(--radius);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            padding: 15px; text-align: left; border-bottom: 1px solid #eee;
        }
        .order-table th {background: #f8f9fa; font-weight: 600;}
        .order-table tr {transition: var(--transition);}
        .order-table tr:hover {background: #f8f9fa; transform: scale(1.005);}
        .status-badge {
            padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;
            cursor: pointer; transition: var(--transition);
        }
        .status-badge:hover {opacity: 0.8; transform: scale(1.05);}
        .status-pending {background: rgba(243,156,18,0.1); color: var(--warning);}
        .status-processing {background: rgba(52,152,219,0.1); color: var(--primary);}
        .status-completed {background: rgba(39,174,96,0.1); color: var(--success);}
        .status-cancelled {background: rgba(231,76,60,0.1); color: var(--danger);}
        .action-btn {
            padding: 6px 12px; border-radius: var(--radius); border: none;
            cursor: pointer; transition: var(--transition); margin-right: 5px;
        }
        .action-btn:hover {opacity: 0.9; transform: translateY(-2px);}
        .action-btn:active {transform: translateY(0);}
        .btn-view {background: var(--primary); color: white;}
        .btn-edit {background: var(--warning); color: white;}
        .btn-delete {background: var(--danger); color: white;}
        .btn-complete {background: var(--success); color: white;}
        .btn-add {
            background: var(--success); color: white; padding: 10px 20px;
            border-radius: var(--radius); border: none; cursor: pointer;
            display: inline-flex; align-items: center; margin-bottom: 20px;
        }
        .btn-add i {margin-right: 8px;}

        /* Filter Section */
        .filter-section {
            display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;
        }
        .filter-item {
            background: white; padding: 10px 15px; border-radius: var(--radius);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center;
        }
        .filter-item:hover {box-shadow: 0 2px 10px rgba(0,0,0,0.1);}
        .filter-item label {margin-right: 10px; font-size: 14px;}
        .filter-item select, .filter-item input {
            border: 1px solid #ddd; border-radius: var(--radius); padding: 8px 12px;
            outline: none; min-width: 150px; transition: var(--transition);
        }
        .filter-item select:hover, .filter-item input:hover {
            border-color: var(--primary);
        }
        .filter-btn {
            background: var(--primary); color: white; border: none;
            padding: 8px 15px; border-radius: var(--radius); cursor: pointer;
            transition: var(--transition);
        }
        .filter-btn:hover {background: var(--secondary); transform: translateY(-2px);}
        .filter-btn:active {transform: translateY(0);}

        /* Pagination */
        .pagination-container {
            display: flex; justify-content: space-between; align-items: center; 
            margin-top: 20px;
        }
        .pagination-info {color: #777; font-size: 14px;}
        .pagination-buttons {
            display: flex; gap: 10px;
        }
        .page-btn {
            padding: 8px 12px; border-radius: var(--radius); cursor: pointer;
            transition: var(--transition); border: none;
        }
        .page-btn:hover:not(:disabled) {
            background: var(--primary); color: white;
            transform: translateY(-2px);
        }
        .page-btn:active:not(:disabled) {transform: translateY(0);}
        .page-btn:disabled {background: #ddd; color: #333; cursor: not-allowed;}

        /* Responsive */
        @media(max-width: 768px) {
            .dashboard {grid-template-columns: 1fr;} 
            .sidebar {display: none;}
            .filter-section {flex-direction: column;}
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
                    <div class="nav-item"><a href="produkowner.php" class="nav-link"><i class="fas fa-mobile-alt"></i> Produk</a></div>
                    <div class="nav-item"><a href="pesanan.php" class="nav-link active"><i class="fas fa-shopping-cart"></i> Pesanan</a></div>
                    <div class="nav-item"><a href="pelanggan.php" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a></div>
                    <div class="nav-item"><a href="laporan.php" class="nav-link"><i class="fas fa-chart-pie"></i> Laporan</a></div>
                    <div class="nav-item"><a href="pengaturan.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></div>
                </div>
            </div>
            <div>
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar">PT</div>
                    <div>
                        <h4>Pemilik Toko</h4>
                        <p>Owner</p>
                    </div>
                </div>
                <form style="padding: 0 20px;">
                    <button type="button" class="logout-btn" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Manajemen Pesanan</h1>
                    <p>Kelola semua pesanan pelanggan Anda</p>
                </div>
                <div class="search-bar" id="searchBar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari pesanan...">
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card" id="totalOrdersCard">
                    <div class="stat-header">
                        <span class="stat-title">Total Pesanan</span>
                        <div class="stat-icon icon-1"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                    <div class="stat-value">24</div>
                    <div class="stat-change" style="color: var(--success);"><i class="fas fa-arrow-up"></i> +5 hari ini</div>
                </div>
                <div class="stat-card" id="pendingOrdersCard">
                    <div class="stat-header">
                        <span class="stat-title">Menunggu</span>
                        <div class="stat-icon icon-2"><i class="fas fa-clock"></i></div>
                    </div>
                    <div class="stat-value">8</div>
                    <div class="stat-change" style="color: var(--warning);"><i class="fas fa-exclamation-circle"></i> Perlu tindakan</div>
                </div>
                <div class="stat-card" id="processingOrdersCard">
                    <div class="stat-header">
                        <span class="stat-title">Diproses</span>
                        <div class="stat-icon icon-3"><i class="fas fa-truck"></i></div>
                    </div>
                    <div class="stat-value">6</div>
                    <div class="stat-change" style="color: var(--primary);"><i class="fas fa-sync-alt"></i> Sedang berjalan</div>
                </div>
                <div class="stat-card" id="revenueCard">
                    <div class="stat-header">
                        <span class="stat-title">Total Pendapatan</span>
                        <div class="stat-icon icon-4"><i class="fas fa-coins"></i></div>
                    </div>
                    <div class="stat-value">Rp 12.4jt</div>
                    <div class="stat-change" style="color: var(--success);"><i class="fas fa-arrow-up"></i> +15% bulan ini</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-item">
                    <label for="status-filter">Status:</label>
                    <select id="status-filter">
                        <option value="all">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="processing">Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="date-filter">Tanggal:</label>
                    <input type="date" id="date-filter">
                </div>
                <div class="filter-item">
                    <label for="customer-filter">Pelanggan:</label>
                    <input type="text" id="customer-filter" placeholder="Nama pelanggan">
                </div>
                <button class="filter-btn" id="filterBtn"><i class="fas fa-filter"></i> Filter</button>
            </div>

            <!-- Orders Table -->
            <table class="order-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Jumlah Item</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="order-row" data-id="ORD-2023-001">
                        <td>#ORD-2023-001</td>
                        <td>12 Nov 2023</td>
                        <td>Andi Wijaya</td>
                        <td>3</td>
                        <td>Rp 5.250.000</td>
                        <td><span class="status-badge status-completed">Selesai</span></td>
                        <td>
                            <button class="action-btn btn-view"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    <tr class="order-row" data-id="ORD-2023-002">
                        <td>#ORD-2023-002</td>
                        <td>13 Nov 2023</td>
                        <td>Budi Santoso</td>
                        <td>1</td>
                        <td>Rp 3.999.000</td>
                        <td><span class="status-badge status-processing">Diproses</span></td>
                        <td>
                            <button class="action-btn btn-view"><i class="fas fa-eye"></i></button>
                            <button class="action-btn btn-complete"><i class="fas fa-check"></i></button>
                        </td>
                    </tr>
                    <tr class="order-row" data-id="ORD-2023-003">
                        <td>#ORD-2023-003</td>
                        <td>14 Nov 2023</td>
                        <td>Citra Dewi</td>
                        <td>2</td>
                        <td>Rp 7.150.000</td>
                        <td><span class="status-badge status-pending">Menunggu</span></td>
                        <td>
                            <button class="action-btn btn-view"><i class="fas fa-eye"></i></button>
                            <button class="action-btn btn-edit"><i class="fas fa-edit"></i></button>
                            <button class="action-btn btn-delete"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr class="order-row" data-id="ORD-2023-004">
                        <td>#ORD-2023-004</td>
                        <td>15 Nov 2023</td>
                        <td>Dian Pratama</td>
                        <td>1</td>
                        <td>Rp 2.750.000</td>
                        <td><span class="status-badge status-cancelled">Dibatalkan</span></td>
                        <td>
                            <button class="action-btn btn-view"><i class="fas fa-eye"></i></button>
                        </td>
                    </tr>
                    <tr class="order-row" data-id="ORD-2023-005">
                        <td>#ORD-2023-005</td>
                        <td>16 Nov 2023</td>
                        <td>Eka Putri</td>
                        <td>4</td>
                        <td>Rp 9.800.000</td>
                        <td><span class="status-badge status-processing">Diproses</span></td>
                        <td>
                            <button class="action-btn btn-view"><i class="fas fa-eye"></i></button>
                            <button class="action-btn btn-complete"><i class="fas fa-check"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">Menampilkan 1-5 dari 24 pesanan</div>
                <div class="pagination-buttons">
                    <button class="page-btn" id="prevPage" disabled><i class="fas fa-chevron-left"></i> Sebelumnya</button>
                    <button class="page-btn active-page" disabled>1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <button class="page-btn" id="nextPage">Selanjutnya <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk memberikan feedback klik
        function animateClick(element) {
            element.style.transform = 'scale(0.95)';
            setTimeout(() => {
                element.style.transform = '';
            }, 200);
        }

        // Sidebar Navigation
         document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            animateClick(this);
            
            // Hapus class active dari semua menu
            document.querySelectorAll('.nav-link').forEach(item => {
                item.classList.remove('active');
            });
            
            // Tambahkan class active ke menu yang diklik
            this.classList.add('active');
            
            // Simpan menu aktif di localStorage
            localStorage.setItem('activeMenu', this.getAttribute('href'));
        });
    });
        // User Profile
        document.getElementById('userProfile').addEventListener('click', function() {
            animateClick(this);
            alert('Membuka profil pengguna');
            // Buka modal atau halaman profil pengguna
        });

        // Logout Button
        document.getElementById('logoutBtn').addEventListener('click', function() {
            animateClick(this);
            if(confirm('Anda yakin ingin logout?')) {
                alert('Anda akan keluar dari sistem');
                window.location.href = 'login.html';
            }
        });

        // Search Bar
        document.getElementById('searchBar').addEventListener('click', function() {
            document.getElementById('searchInput').focus();
        });

        // Stat Cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('click', function() {
                animateClick(this);
                const cardType = this.id;
                console.log('Membuka detail:', cardType);
                // Buka halaman/laporan sesuai jenis card
            });
        });

        // Filter Button
        document.getElementById('filterBtn').addEventListener('click', function() {
            animateClick(this);
            const status = document.getElementById('status-filter').value;
            const date = document.getElementById('date-filter').value;
            const customer = document.getElementById('customer-filter').value;
            
            console.log('Filter diterapkan:', {status, date, customer});
            alert(`Filter diterapkan:\nStatus: ${status}\nTanggal: ${date || 'Semua'}\nPelanggan: ${customer || 'Semua'}`);
            
            // Di sini bisa ditambahkan logika filter aktual
        });

        // Order Rows
        document.querySelectorAll('.order-row').forEach(row => {
            row.addEventListener('click', function(e) {
                // Jangan trigger jika yang diklik adalah tombol aksi
                if(!e.target.closest('.action-btn')) {
                    animateClick(this);
                    const orderId = this.getAttribute('data-id');
                    console.log('Membuka detail pesanan:', orderId);
                    // Buka modal/halaman detail pesanan
                }
            });
        });

        // Action Buttons
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah event bubbling ke row
                animateClick(this);
                
                const action = this.querySelector('i').className;
                const orderId = this.closest('.order-row').getAttribute('data-id');
                
                switch(action) {
                    case 'fas fa-eye':
                        console.log('Melihat pesanan:', orderId);
                        alert(`Membuka detail pesanan ${orderId}`);
                        break;
                    case 'fas fa-edit':
                        console.log('Mengedit pesanan:', orderId);
                        alert(`Mengedit pesanan ${orderId}`);
                        break;
                    case 'fas fa-trash':
                        if(confirm(`Hapus pesanan ${orderId}?`)) {
                            console.log('Menghapus pesanan:', orderId);
                            alert(`Pesanan ${orderId} dihapus`);
                        }
                        break;
                    case 'fas fa-check':
                        console.log('Menyelesaikan pesanan:', orderId);
                        alert(`Pesanan ${orderId} ditandai selesai`);
                        break;
                }
            });
        });

        // Status Badges
        document.querySelectorAll('.status-badge').forEach(badge => {
            badge.addEventListener('click', function(e) {
                e.stopPropagation(); // Mencegah event bubbling ke row
                animateClick(this);
                
                const status = this.textContent;
                const orderId = this.closest('.order-row').getAttribute('data-id');
                console.log('Mengubah status pesanan:', orderId, 'ke', status);
                // Logika untuk mengubah status pesanan
            });
        });

        // Pagination
        document.querySelectorAll('.page-btn:not(.active-page)').forEach(btn => {
            btn.addEventListener('click', function() {
                if(!this.disabled) {
                    animateClick(this);
                    const pageText = this.textContent.trim();
                    console.log('Pindah ke halaman:', pageText);
                    // Logika untuk pindah halaman
                }
            });
        });
    </script>
</body>
</html>