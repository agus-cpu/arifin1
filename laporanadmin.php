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
    <title>Laporan - Toko Handphone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
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

        .report-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border-radius: var(--radius);
            border: 1px solid #e2e8f0;
            background: white;
        }

        .filter-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            align-self: flex-end;
        }

        .filter-btn:hover {
            background: var(--primary-hover);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }

        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .summary-card h3 {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 10px;
        }

        .summary-card p {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .summary-card .trend {
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .trend.up {
            color: var(--success);
        }

        .trend.down {
            color: var(--danger);
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .report-table th {
            text-align: left;
            padding: 12px 15px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 500;
            border-bottom: 1px solid #e2e8f0;
        }

        .report-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .report-table tr:last-child td {
            border-bottom: none;
        }

        .report-table tr:hover {
            background: #f8fafc;
        }

        .download-btn {
            background: var(--success);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .download-btn:hover {
            background: #0da271;
        }

        .download-btn i {
            margin-right: 5px;
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

        .action-btn:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-completed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .status-processing {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary-light);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
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

            .report-table {
                display: block;
                overflow-x: auto;
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
                    <a href="semuapesanan.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Semua Pesanan</span>
                        <span class="nav-badge">5</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="laporanadmin.php" class="nav-link active">
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
                    <h1>Laporan Toko</h1>
                    <p>Analisis dan laporan penjualan toko handphone Anda</p>
                </div>
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchReport" placeholder="Cari laporan..." onkeyup="searchReports()">
                </div>
            </div>

            <div class="card">
                <h2 style="color: var(--dark); margin-bottom: 20px;">Filter Laporan</h2>
                <div class="report-filter">
                    <div class="filter-group">
                        <label for="report-type">Jenis Laporan</label>
                        <select id="report-type">
                            <option value="sales">Laporan Penjualan</option>
                            <option value="products">Laporan Produk</option>
                            <option value="customers">Laporan Pelanggan</option>
                            <option value="stock">Laporan Stok</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="time-period">Periode Waktu</label>
                        <select id="time-period" onchange="updateDateRange()">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                            <option value="year">Tahun Ini</option>
                            <option value="custom">Kustom</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="start-date">Tanggal Mulai</label>
                        <input type="date" id="start-date">
                    </div>
                    <div class="filter-group">
                        <label for="end-date">Tanggal Akhir</label>
                        <input type="date" id="end-date">
                    </div>
                    <button class="filter-btn" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </div>
            </div>

            <div class="card">
                <h2 style="color: var(--dark); margin-bottom: 20px;">Statistik Penjualan</h2>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
                
                <div class="report-summary" id="summaryCards">
                    <!-- Summary cards will be populated by JavaScript -->
                </div>
            </div>

            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: var(--dark);">Detail Laporan</h2>
                    <button class="download-btn" onclick="exportReport()">
                        <i class="fas fa-file-export"></i> Ekspor Laporan
                    </button>
                </div>

                <table class="report-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <!-- Report data will be populated by JavaScript -->
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <div id="paginationInfo" style="color: #64748b; font-size: 0.9rem;">
                        Menampilkan 1-5 dari 15 pesanan
                    </div>
                    <div>
                        <button id="prevBtn" class="action-btn" style="margin-right: 10px;" onclick="changePage(-1)">
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        <button id="nextBtn" class="action-btn" onclick="changePage(1)">
                            Selanjutnya <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Sample report data
        let reports = [
            { id: "ORD-1256", date: "2023-08-12", customer: "Budi Santoso", product: "Samsung S23", quantity: 1, total: 5250000, status: "completed" },
            { id: "ORD-1255", date: "2023-08-11", customer: "Ani Fitriani", product: "iPhone 14", quantity: 1, total: 3750000, status: "processing" },
            { id: "ORD-1254", date: "2023-08-10", customer: "Dewi Kurnia", product: "iPhone 14 Pro Max", quantity: 1, total: 8990000, status: "completed" },
            { id: "ORD-1253", date: "2023-08-09", customer: "Rudi Hermawan", product: "Xiaomi 13 Pro", quantity: 2, total: 6450000, status: "cancelled" },
            { id: "ORD-1252", date: "2023-08-08", customer: "Siti Rahayu", product: "Oppo Reno 8", quantity: 1, total: 4200000, status: "completed" },
            { id: "ORD-1251", date: "2023-08-07", customer: "Joko Widodo", product: "Samsung S23 Ultra", quantity: 1, total: 7800000, status: "completed" },
            { id: "ORD-1250", date: "2023-08-06", customer: "Mega Putri", product: "iPhone 13", quantity: 1, total: 5500000, status: "completed" },
            { id: "ORD-1249", date: "2023-08-05", customer: "Agus Suparman", product: "Xiaomi 12 Pro", quantity: 1, total: 3200000, status: "completed" },
            { id: "ORD-1248", date: "2023-08-04", customer: "Linda Sari", product: "iPhone 14 Pro", quantity: 1, total: 9750000, status: "completed" },
            { id: "ORD-1247", date: "2023-08-03", customer: "Bambang Pamungkas", product: "Samsung Z Flip", quantity: 1, total: 6300000, status: "cancelled" },
            { id: "ORD-1246", date: "2023-08-02", customer: "Dian Novita", product: "Oppo Reno 7", quantity: 1, total: 4800000, status: "completed" },
            { id: "ORD-1245", date: "2023-08-01", customer: "Eko Pratama", product: "iPhone 12", quantity: 1, total: 5600000, status: "completed" },
            { id: "ORD-1244", date: "2023-07-31", customer: "Fitri Handayani", product: "Vivo V25", quantity: 1, total: 3900000, status: "completed" },
            { id: "ORD-1243", date: "2023-07-30", customer: "Gunawan Setiawan", product: "Samsung S22", quantity: 1, total: 7200000, status: "completed" },
            { id: "ORD-1242", date: "2023-07-29", customer: "Hesti Wulandari", product: "iPhone 11", quantity: 1, total: 6100000, status: "completed" }
        ];

        // Monthly sales data for chart
        const monthlySales = {
            revenue: [8.2, 7.5, 9.1, 8.7, 10.5, 11.2, 10.8, 12.4, 0, 0, 0, 0],
            orders: [32, 28, 35, 31, 38, 42, 40, 42, 0, 0, 0, 0]
        };

        // Pagination variables
        let currentPage = 1;
        const rowsPerPage = 5;
        let filteredReports = [...reports];
        let salesChart;

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Set default dates
            setDefaultDates();
            
            // Initialize chart
            initChart();
            
            // Load data
            updateSummaryCards();
            renderReports();
            updatePaginationInfo();
        });

        // Set default date range
        function setDefaultDates() {
            const today = new Date();
            const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            
            document.getElementById('start-date').valueAsDate = startOfMonth;
            document.getElementById('end-date').valueAsDate = today;
        }

        // Update date range based on period selection
        function updateDateRange() {
            const period = document.getElementById('time-period').value;
            const today = new Date();
            const startDateInput = document.getElementById('start-date');
            const endDateInput = document.getElementById('end-date');
            
            let startDate = new Date();
            
            switch(period) {
                case 'today':
                    startDate = today;
                    break;
                case 'week':
                    startDate.setDate(today.getDate() - 6);
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    break;
                case 'custom':
                    return; // Don't change dates for custom range
            }
            
            startDateInput.valueAsDate = startDate;
            endDateInput.valueAsDate = today;
        }

        // Initialize chart
        function initChart() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Pendapatan (juta Rp)',
                        data: monthlySales.revenue,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Jumlah Pesanan',
                        data: monthlySales.orders,
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Pendapatan (juta Rp)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Pesanan'
                            }
                        }
                    }
                }
            });
        }

        // Update summary cards
        function updateSummaryCards() {
            const totalRevenue = reports.reduce((sum, report) => sum + report.total, 0);
            const totalOrders = reports.length;
            
            // Find best selling product
            const productSales = {};
            reports.forEach(report => {
                if (report.status === 'completed') {
                    productSales[report.product] = (productSales[report.product] || 0) + report.quantity;
                }
            });
            
            let bestSeller = '';
            let maxSold = 0;
            for (const product in productSales) {
                if (productSales[product] > maxSold) {
                    bestSeller = product;
                    maxSold = productSales[product];
                }
            }
            
            const avgOrder = totalRevenue / totalOrders;
            
            document.getElementById('summaryCards').innerHTML = `
                <div class="summary-card">
                    <h3>Total Pendapatan</h3>
                    <p>Rp ${formatCurrency(totalRevenue)}</p>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> 15% dari bulan lalu
                    </div>
                </div>
                <div class="summary-card">
                    <h3>Total Pesanan</h3>
                    <p>${totalOrders}</p>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> 8% dari bulan lalu
                    </div>
                </div>
                <div class="summary-card">
                    <h3>Produk Terlaris</h3>
                    <p>${bestSeller}</p>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i> ${maxSold} unit terjual
                    </div>
                </div>
                <div class="summary-card">
                    <h3>Rata-rata Pesanan</h3>
                    <p>Rp ${formatCurrency(avgOrder)}</p>
                    <div class="trend down">
                        <i class="fas fa-arrow-down"></i> 5% dari bulan lalu
                    </div>
                </div>
            `;
        }

        // Format currency
        function formatCurrency(amount) {
            return amount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Apply filters
        function applyFilters() {
            const reportType = document.getElementById('report-type').value;
            const startDate = new Date(document.getElementById('start-date').value);
            const endDate = new Date(document.getElementById('end-date').value);
            
            // Filter by date range
            filteredReports = reports.filter(report => {
                const reportDate = new Date(report.date);
                return reportDate >= startDate && reportDate <= endDate;
            });
            
            // Additional filtering based on report type
            if (reportType === 'products') {
                // In a real app, this would show product statistics
                alert('Laporan Produk akan menampilkan statistik produk');
            } else if (reportType === 'customers') {
                // In a real app, this would show customer statistics
                alert('Laporan Pelanggan akan menampilkan statistik pelanggan');
            } else if (reportType === 'stock') {
                // In a real app, this would show stock statistics
                alert('Laporan Stok akan menampilkan statistik stok');
            }
            
            // Reset to first page
            currentPage = 1;
            
            // Update UI
            renderReports();
            updatePaginationInfo();
            
            // Update chart with filtered data
            updateChartWithFilteredData();
        }

        // Update chart with filtered data
        function updateChartWithFilteredData() {
            // In a real app, this would update the chart based on filtered data
            // For this example, we'll just show an alert
            alert('Chart akan diperbarui dengan data terfilter');
        }

        // Render reports table
        function renderReports() {
            const tableBody = document.getElementById('reportTableBody');
            tableBody.innerHTML = '';

            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const reportsToShow = filteredReports.slice(startIndex, endIndex);

            reportsToShow.forEach(report => {
                const row = document.createElement('tr');
                
                // Format date
                const date = new Date(report.date);
                const formattedDate = date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
                
                // Determine status class and text
                let statusClass, statusText;
                switch(report.status) {
                    case 'completed':
                        statusClass = 'status-completed';
                        statusText = 'Selesai';
                        break;
                    case 'processing':
                        statusClass = 'status-processing';
                        statusText = 'Diproses';
                        break;
                    case 'cancelled':
                        statusClass = 'status-cancelled';
                        statusText = 'Dibatalkan';
                        break;
                }

                row.innerHTML = `
                    <td>${formattedDate}</td>
                    <td>#${report.id}</td>
                    <td>${report.customer}</td>
                    <td>${report.product}</td>
                    <td>${report.quantity}</td>
                    <td>Rp ${formatCurrency(report.total)}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                `;
                tableBody.appendChild(row);
            });

            // Update pagination buttons
            document.getElementById('prevBtn').disabled = currentPage === 1;
            document.getElementById('nextBtn').disabled = endIndex >= filteredReports.length;
        }

        // Update pagination information
        function updatePaginationInfo() {
            const startIndex = (currentPage - 1) * rowsPerPage + 1;
            const endIndex = Math.min(currentPage * rowsPerPage, filteredReports.length);
            document.getElementById('paginationInfo').textContent = 
                `Menampilkan ${startIndex}-${endIndex} dari ${filteredReports.length} pesanan`;
        }

        // Change page
        function changePage(direction) {
            const newPage = currentPage + direction;
            const totalPages = Math.ceil(filteredReports.length / rowsPerPage);
            
            if (newPage > 0 && newPage <= totalPages) {
                currentPage = newPage;
                renderReports();
                updatePaginationInfo();
            }
        }

        // Search reports
        function searchReports() {
            const searchTerm = document.getElementById('searchReport').value.toLowerCase();
            
            if (searchTerm === '') {
                filteredReports = [...reports];
            } else {
                filteredReports = reports.filter(report => 
                    report.id.toLowerCase().includes(searchTerm) || 
                    report.customer.toLowerCase().includes(searchTerm) ||
                    report.product.toLowerCase().includes(searchTerm) ||
                    report.status.toLowerCase().includes(searchTerm)
                );
            }
            
            currentPage = 1;
            renderReports();
            updatePaginationInfo();
        }

        // Export report
        function exportReport() {
            // In a real app, this would generate a PDF or Excel file
            alert('Laporan akan diekspor ke format PDF/Excel');
        }
    </script>
</body>
</html>