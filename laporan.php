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
    <title>Laporan Toko</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Laporan Page Specific Styles */
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            flex-wrap: wrap;
        }
        select, input[type="date"], button {
            padding: 8px 12px;
            border-radius: var(--radius);
            border: 1px solid #ddd;
            font-size: 14px;
        }
        button {
            background: var(--primary);
            color: #fff;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            min-width: 80px;
        }
        button:hover {
            background: var(--secondary);
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: var(--shadow);
        }
        .stat-box h3 {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }
        .stat-box p {
            font-size: 18px;
            font-weight: 600;
        }
        .chart-container {
            background: white;
            border-radius: var(--radius);
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            height: 300px; /* Ukuran lebih kecil */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .chart-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
        }
        .table-container {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th {
            background: var(--dark);
            color: #fff;
            padding: 12px;
            text-align: left;
            font-weight: 500;
        }
        td {
            padding: 12px;
            border-top: 1px solid #eee;
        }
        .status-lunas {
            color: var(--success);
            font-weight: 600;
        }
        .status-pending {
            color: var(--danger);
            font-weight: 600;
        }

        /* Responsive */
        @media(max-width: 768px) {
            .dashboard{grid-template-columns: 1fr;} 
            .sidebar{display: none;}
            .header{flex-direction: column; align-items: flex-start; gap: 15px;}
            .search-bar{width: 100%;}
            .stats{grid-template-columns: 1fr 1fr;}
            .chart-container {
                height: 250px;
            }
        }
        @media(max-width: 480px) {
            .filters{flex-direction: column;}
            .stats{grid-template-columns: 1fr;}
            .chart-container {
                height: 220px;
                padding: 10px;
            }
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
                    <a href="owner.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="produkowner.php" class="nav-link"><i class="fas fa-mobile-alt"></i> Produk</a>
                    <a href="pesanan.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                    <a href="pelanggan.php" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a>
                    <a href="laporan.php" class="nav-link active"><i class="fas fa-chart-pie"></i> Laporan</a>
                    <a href="pengaturan.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a>
                </div>
            </div>
            <div>
                <div class="user-profile" onclick="window.location.href='pengaturan.php'">
                    <div class="user-avatar">PT</div>
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

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Laporan Toko</h1>
                    <p>Analisis penjualan dan kinerja toko</p>
                </div>
                <div class="search-bar" onclick="document.querySelector('.search-bar input').focus()">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari laporan...">
                </div>
            </div>

            <div class="filters">
                <select>
                    <option>Penjualan</option>
                    <option>Produk</option>
                    <option>Pelanggan</option>
                </select>
                <input type="date">
                <input type="date">
                <button>Filter</button>
            </div>

            <div class="stats">
                <div class="stat-box">
                    <h3>Total Penjualan</h3>
                    <p>127</p>
                </div>
                <div class="stat-box">
                    <h3>Total Pendapatan</h3>
                    <p>Rp 48.750.000</p>
                </div>
                <div class="stat-box">
                    <h3>Produk Terlaris</h3>
                    <p>iPhone 13</p>
                </div>
                <div class="stat-box">
                    <h3>Pelanggan Baru</h3>
                    <p>28</p>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-wrapper">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>12/08/2023</td>
                            <td>iPhone 13 128GB</td>
                            <td>2</td>
                            <td>Rp 12.500.000</td>
                            <td>Rp 25.000.000</td>
                            <td class="status-lunas">Lunas</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>11/08/2023</td>
                            <td>Samsung Galaxy S22</td>
                            <td>1</td>
                            <td>Rp 11.200.000</td>
                            <td>Rp 11.200.000</td>
                            <td class="status-lunas">Lunas</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>10/08/2023</td>
                            <td>Xiaomi Redmi Note 11</td>
                            <td>3</td>
                            <td>Rp 3.250.000</td>
                            <td>Rp 9.750.000</td>
                            <td class="status-lunas">Lunas</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>09/08/2023</td>
                            <td>OPPO Reno 7</td>
                            <td>1</td>
                            <td>Rp 4.800.000</td>
                            <td>Rp 4.800.000</td>
                            <td class="status-pending">Pending</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>08/08/2023</td>
                            <td>Vivo V23 5G</td>
                            <td>2</td>
                            <td>Rp 5.500.000</td>
                            <td>Rp 11.000.000</td>
                            <td class="status-lunas">Lunas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Chart initialization with smaller size
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu'],
                    datasets: [{
                        label: 'Penjualan',
                        data: [10, 12, 8, 14, 9, 15, 11, 13],
                        borderColor: 'var(--primary)',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'var(--primary)',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    size: 13
                                }
                            }
                        } 
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });

            // Click effects for interactive elements
            const clickableElements = document.querySelectorAll('.stat-box, .nav-link, .user-profile, button');
            
            clickableElements.forEach(el => {
                el.style.transition = 'var(--transition)';
                el.addEventListener('click', function(e) {
                    if (this.tagName === 'A' || this.tagName === 'BUTTON') return;
                    
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
        });
    </script>
</body>
</html>