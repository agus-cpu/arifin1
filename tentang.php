<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role adalah customer
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.html");
    exit;
}

// Ambil data customer dari session
$username = $_SESSION['username'];
$name = $_SESSION['name'];

// Contoh query untuk menampilkan data tambahan (jika perlu)
$sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - Lhokseumawe Mobile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2980b9;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --danger: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        header {
            background-color: var(--dark);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .logo img {
            height: 50px;
            margin-right: 10px;
        }
        
        .logo h1 {
            font-size: 1.5rem;
            color: white;
        }
        
        .logo span {
            color: var(--primary);
        }
        
        /* Navigation */
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            padding: 5px 0;
            position: relative;
        }
        
        nav ul li a:hover {
            color: var(--primary);
        }
        
        nav ul li a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s;
        }
        
        nav ul li a:hover::after {
            width: 100%;
        }
        
        /* Main Content */
        .about-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            align-items: center;
            text-align: center;
            color: white;
        }
        
        .about-hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }
        
        .about-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        /* About Section */
        .about-section {
            padding: 60px 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .section-title h2 {
            font-size: 2rem;
            color: var(--dark);
            display: inline-block;
            padding-bottom: 10px;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background-color: var(--primary);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .about-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            align-items: center;
            margin-bottom: 60px;
        }
        
        .about-text {
            flex: 1;
            min-width: 300px;
        }
        
        .about-text p {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .about-image {
            flex: 1;
            min-width: 300px;
            height: 400px;
            background: url('https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80') center/cover;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Visi Misi */
        .vision-mission {
            background-color: var(--light);
            padding: 60px 0;
        }
        
        .vm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .vm-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .vm-card h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .vm-card h3 i {
            margin-right: 10px;
        }
        
        .vm-card ul {
            list-style-position: inside;
            padding-left: 5px;
        }
        
        .vm-card li {
            margin-bottom: 10px;
        }
        
        /* Team Section */
        .team-section {
            padding: 60px 0;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .team-member {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .member-image {
            height: 250px;
            background-size: cover;
            background-position: center;
        }
        
        .member-info {
            padding: 20px;
        }
        
        .member-info h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .member-info p {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background-color: var(--light);
            border-radius: 50%;
            color: var(--dark);
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .footer-col h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
            color: white;
        }
        
        .footer-col h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-col p {
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #ecf0f1;
            opacity: 0.8;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            opacity: 1;
            color: var(--primary);
        }
        
        .contact-info {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        
        .contact-info i {
            margin-right: 10px;
            margin-top: 3px;
            color: var(--primary);
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 20px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            nav ul li {
                margin: 5px 10px;
            }
            
            .about-hero h1 {
                font-size: 2rem;
            }
            
            .about-content {
                flex-direction: column;
            }
            
            .about-image {
                order: -1;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-container">
            <a href="customer.html" class="logo">
                <img src="https://via.placeholder.com/50" alt="Lhokseumawe Mobile Logo">
                <h1>Lhokseumawe <span>Mobile</span></h1>
            </a>
            <nav>
                <ul>
                    <li><a href="customer.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="produk.php"><i class="fas fa-mobile-alt"></i> Produk</a></li>
                    <li><a href="promo.php"><i class="fas fa-tags"></i> Promo</a></li>
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang <span class="cart-count"></span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- About Hero Section -->
    <section class="about-hero">
        <div class="about-hero-content">
            <h1>Tentang Lhokseumawe Mobile</h1>
            <p>Menghadirkan solusi teknologi terbaik untuk masyarakat Lhokseumawe sejak 2010</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="section-title">
                <h2>Siapa Kami</h2>
            </div>
            
            <div class="about-content">
                <div class="about-text">
                    <p>Lhokseumawe Mobile didirikan pada tahun 2010 dengan visi menjadi penyedia perangkat mobile terdepan di wilayah Aceh Utara. Kami memulai dengan sebuah toko kecil di pusat kota Lhokseumawe dan kini telah berkembang menjadi salah satu destinasi utama untuk kebutuhan teknologi di daerah ini.</p>
                    
                    <p>Dengan komitmen untuk menyediakan produk berkualitas, layanan purna jual yang terjamin, dan harga yang kompetitif, kami telah melayani ribuan pelanggan dengan berbagai kebutuhan perangkat mobile mereka.</p>
                    
                    <p>Kami bukan sekadar menjual produk, tetapi juga memberikan solusi teknologi yang sesuai dengan kebutuhan dan anggaran setiap pelanggan. Tim ahli kami siap memberikan rekomendasi terbaik untuk Anda.</p>
                </div>
                
                <div class="about-image"></div>
            </div>
        </div>
    </section>

    <!-- Vision Mission Section -->
    <section class="vision-mission">
        <div class="container">
            <div class="section-title">
                <h2>Visi & Misi Kami</h2>
            </div>
            
            <div class="vm-grid">
                <div class="vm-card">
                    <h3><i class="fas fa-eye"></i> Visi</h3>
                    <ul>
                        <li>Menjadi pusat teknologi mobile terdepan di wilayah Aceh Utara</li>
                        <li>Memberikan akses teknologi terkini dengan harga terjangkau</li>
                        <li>Meningkatkan literasi digital masyarakat melalui produk dan layanan kami</li>
                    </ul>
                </div>
                
                <div class="vm-card">
                    <h3><i class="fas fa-bullseye"></i> Misi</h3>
                    <ul>
                        <li>Menyediakan produk original dengan garansi resmi</li>
                        <li>Memberikan pelayanan terbaik sebelum, selama, dan setelah pembelian</li>
                        <li>Mengutamakan kepuasan pelanggan dalam setiap transaksi</li>
                        <li>Berkontribusi pada pengembangan UMKM lokal melalui kemitraan</li>
                    </ul>
                </div>
                
                <div class="vm-card">
                    <h3><i class="fas fa-handshake"></i> Nilai Kami</h3>
                    <ul>
                        <li>Integritas dalam setiap pelayanan</li>
                        <li>Profesionalisme tim yang terlatih</li>
                        <li>Inovasi terus menerus dalam layanan</li>
                        <li>Tanggung jawab sosial terhadap masyarakat</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3>Tentang Kami</h3>
                <p>Lhokseumawe Mobile adalah toko ponsel terpercaya di Kota Lhokseumawe yang menyediakan berbagai smartphone berkualitas dengan harga kompetitif.</p>
                <div class="social-links" style="margin-top: 20px;">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
         <div class="footer-col">
                <h3>Kontak Kami</h3>
                <div class="contact-info">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Lhokseumawe,Gp.teungoh</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-phone"></i>
                    <p>0823-74358161</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <p>arifinrahman1102@gmail.com</p>
                </div>
                <div class="contact-info">
                    <i class="fas fa-clock"></i>
                    <p>Buka setiap hari 09:00 - 21:00 WIB</p>
                </div>
            </div>
        
        <div class="copyright">
            <p>&copy; 2025 Lhokseumawe Mobile. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>