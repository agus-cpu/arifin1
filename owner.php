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

// Ambil data statistik
$stats = [
    'total_produk' => 6,
    'rating_toko' => 4.8,
    'pesanan_baru' => 2,
    'total_user' => 3,
    'pendapatan' => 23998000
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Toko Handphone</title>
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
            --shadow: 0 5px 15px rgba(0,0,0,0.05);
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
        .user-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--primary); display: flex; align-items: center; justify-content: center;
            font-weight: bold; color: white; margin-right: 10px;
        }
        .logout-btn {
            background: var(--danger); border: none; color: white;
            padding: 8px 14px; border-radius: var(--radius); cursor: pointer;
            width: 100%; margin-top: 10px; transition: var(--transition);
            display: flex; align-items: center; justify-content: center;
            gap: 8px;
        }
        .logout-btn:hover {background: #c0392b;}

        /* Main Content */
        .main-content {padding: 30px;}
        .header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap;}
        .header h1 {font-size: 26px;}
        .search-bar {
            display: flex; align-items: center; background: white; border-radius: 30px;
            box-shadow: var(--shadow); padding: 6px 15px; transition: var(--transition);
        }
        .search-bar:hover {box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
        .search-bar input {
            border: none; outline: none; padding: 6px; min-width: 200px;
        }
        .search-bar i {color: #777; margin-right: 8px;}

        /* Cards */
        .stats-container {display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); margin-bottom: 30px;}
        .stat-card {
            background: white; border-radius: var(--radius); padding: 20px;
            box-shadow: var(--shadow); transition: var(--transition); cursor: pointer;
            border-left: 3px solid transparent;
        }
        .stat-card:hover {
            transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-left-color: var(--primary);
        }
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
            cursor: pointer; transition: var(--transition);
        }
        .welcome-card:hover {transform: translateY(-3px); box-shadow: 0 10px 20px rgba(41,128,185,0.3);}
        .welcome-text h2 {font-size: 22px;}
        .welcome-image i {font-size: 90px; opacity: 0.15; transition: var(--transition);}
        .welcome-card:hover .welcome-image i {opacity: 0.25;}

        /* Revenue Card */
        .revenue-card {
            background: white; border-radius: var(--radius); padding: 20px;
            border-left: 4px solid var(--primary); box-shadow: var(--shadow);
            cursor: pointer; transition: var(--transition);
        }
        .revenue-card:hover {
            transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .revenue-header {display: flex; justify-content: space-between; align-items: center;}
        .revenue-value {font-size: 30px; font-weight: bold; color: var(--primary);}
        .revenue-change {
            display: inline-flex; align-items: center; color: var(--success);
            background: rgba(39,174,96,0.1); padding: 4px 10px; border-radius: 20px; font-size: 14px;
        }

        /* Responsive */
        @media(max-width: 768px) {
            .dashboard{grid-template-columns: 1fr;} 
            .sidebar{display: none;}
            .header{flex-direction: column; align-items: flex-start; gap: 15px;}
            .search-bar{width: 100%;}
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
                    <a href="owner.php" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="produkowner.php" class="nav-link"><i class="fas fa-mobile-alt"></i> Produk</a>
                    <a href="pesanan.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                    <a href="pelanggan.php" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a>
                    <a href="laporan.php" class="nav-link"><i class="fas fa-chart-pie"></i> Laporan</a>
                    <a href="pengaturan.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a>
                </div>
            </div>
            <div>
                <div class="user-profile" onclick="window.location.href='pengaturan.php'">
                    <div class="user-avatar"><?= strtoupper(substr($name,0,2)); ?></div>
                    <div>
                        <h4><?= htmlspecialchars($name); ?></h4>
                        <p>Owner</p>
                    </div>
                </div>
                <form action="logout.php" method="post" style="padding: 0 20px;">
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Dashboard Toko</h1>
                    <p>Kelola toko & produk handphone Anda</p>
                </div>
                <div class="search-bar" onclick="document.querySelector('.search-bar input').focus()">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari produk, pelanggan...">
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-container">
                <a href="produkowner.php" class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Produk Saya</span>
                        <div class="stat-icon icon-1"><i class="fas fa-boxes"></i></div>
                    </div>
                    <div class="stat-value"><?= $stats['total_produk']; ?></div>
                    <div class="stat-change" style="color: var(--success);">
                        <i class="fas fa-arrow-up"></i> +3 produk baru
                    </div>
                </a>
                
                <a href="pelanggan.php?filter=rating" class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Rating Toko</span>
                        <div class="stat-icon icon-2"><i class="fas fa-star"></i></div>
                    </div>
                    <div class="stat-value"><?= $stats['rating_toko']; ?></div>
                    <div class="stat-change" style="color: var(--warning);">
                        <i class="fas fa-thumbs-up"></i> Excellent
                    </div>
                </a>
                
                <a href="pesanan.php?status=new" class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">Pesanan Baru</span>
                        <div class="stat-icon icon-3"><i class="fas fa-shopping-bag"></i></div>
                    </div>
                    <div class="stat-value"><?= $stats['pesanan_baru']; ?></div>
                    <div class="stat-change" style="color: var(--success);">
                        <i class="fas fa-arrow-up"></i> +5 pesanan hari ini
                    </div>
                </a>
                
                <a href="pelanggan.php" class="stat-card">
                    <div class="stat-header">
                        <span class="stat-title">User</span>
                        <div class="stat-icon icon-4"><i class="fas fa-user-tie"></i></div>
                    </div>
                    <div class="stat-value"><?= $stats['total_user']; ?></div>
                    <div class="stat-change" style="color: var(--danger);">
                        <i class="fas fa-exclamation-triangle"></i> Limited
                    </div>
                </a>
            </div>

            <!-- Welcome -->
            <div class="welcome-card" onclick="window.location.href='pengaturan.php'">
                <div class="welcome-text">
                    <h2>Selamat datang, <?= htmlspecialchars($name); ?>!</h2>
                    <p>Semoga hari Anda menyenangkan. Berikut ringkasan aktivitas toko hari ini.</p>
                </div>
                <div class="welcome-image"><i class="fas fa-store"></i></div>
            </div>

            <!-- Revenue -->
            <div class="revenue-card" onclick="window.location.href='laporan.php?filter=revenue'">
                <div class="revenue-header">
                    <span class="revenue-title">Pendapatan</span>
                    <div class="stat-icon" style="background: var(--primary);"><i class="fas fa-coins"></i></div>
                </div>
                <div class="revenue-value">Rp <?= number_format($stats['pendapatan'], 0, ',', '.'); ?></div>
                <div class="revenue-change"><i class="fas fa-arrow-up"></i> +12% dari bulan lalu</div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk semua elemen yang bisa diklik
        document.addEventListener('DOMContentLoaded', function() {
            // Animasi saat hover
            const clickableElements = document.querySelectorAll('[onclick], a, button, .stat-card, .welcome-card, .revenue-card');
            
            clickableElements.forEach(el => {
                el.style.transition = 'all 0.3s ease';
                el.addEventListener('click', function(e) {
                    if (this.tagName === 'A' || this.tagName === 'BUTTON') return;
                    
                    // Efek tekan saat diklik
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });

            // Fokus input search saat diklik
            document.querySelector('.search-bar').addEventListener('click', function() {
                this.querySelector('input').focus();
            });

            // Format angka dengan titik
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
        });
    </script>
</body>
</html>