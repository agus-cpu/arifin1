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
    <title>Lhokseumawe Mobile - Toko Ponsel Terbaik di Aceh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2980b9;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --danger: #e74c3c;
            --success: #27ae60;
            --warning: #f39c12;
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
        
        /* Header Styles */
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
        
        nav ul li a i {
            margin-right: 5px;
        }
        
        .cart-count {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
  /* Hero Section */
        .hero {
            background: url('tokoponsel.png') no-repeat center center fixed;
            background-size: cover;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: fadeInDown 1s ease;
        }

        .hero h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
            animation: fadeIn 1.5s ease;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            animation: fadeIn 2s ease;
        }

        
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: 2px solid var(--primary);
            font-size: 1.1rem;
        }
        
        .btn:hover {
            background-color: transparent;
            color: white;
            border-color: white;
        }
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 40px auto;
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
        
        /* Product Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--danger);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 1;
        }
        
        .product-badge.new {
            background-color: var(--primary);
        }
        
        .product-badge.discount {
            background-color: var(--success);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-category {
            color: var(--primary);
            font-size: 0.9rem;
            margin-bottom: 5px;
            display: block;
        }
        
        .product-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--dark);
            font-weight: 600;
        }
        
        .product-price {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .current-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--danger);
            margin-right: 10px;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }
        
        .discount-percent {
            background-color: var(--light);
            color: var(--danger);
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-left: 10px;
            font-weight: bold;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 15px;
        }
        
        .product-stock {
            color: var(--success);
        }
        
        .product-rating {
            color: var(--warning);
        }
        
        .product-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        
        .add-to-cart {
            flex: 1;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .add-to-cart:hover {
            background-color: var(--secondary);
        }
        
        .add-to-cart i {
            margin-right: 5px;
        }
        
        .wishlist {
            width: 40px;
            height: 40px;
            background-color: var(--light);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: #777;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .wishlist:hover {
            color: var(--danger);
            background-color: rgba(231, 76, 60, 0.1);
        }
        
        /* Categories Section */
        .categories {
            margin: 60px 0;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .category-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .category-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .category-card:hover .category-icon {
            transform: scale(1.1);
            color: var(--secondary);
        }
        
        .category-title {
            font-size: 1.1rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
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
            color: #ecf0f1;
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
            display: inline-block;
            padding: 2px 0;
        }
        
        .footer-links a:hover {
            opacity: 1;
            color: var(--primary);
            transform: translateX(5px);
        }
        
        .footer-links a i {
            margin-right: 5px;
            width: 20px;
            text-align: center;
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
        
        .social-links {
            display: flex;
            margin-top: 20px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 10px;
            color: white;
            transition: all 0.3s;
            font-size: 1.1rem;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .payment-methods i {
            font-size: 2rem;
            color: #ecf0f1;
            transition: all 0.3s;
        }
        
        .payment-methods i:hover {
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
        
        /* Responsive Styles */
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
            
            .hero h2 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .product-actions {
                flex-direction: column;
            }
            
            .wishlist {
                width: 100%;
                margin-top: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .categories-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
        .logout-btn {
    background-color: var(--danger);
    padding: 6px 12px;
    border-radius: 4px;
    color: white !important;
}
.logout-btn:hover {
    background-color: #c0392b;
}


        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .product-card {
            animation: fadeIn 0.5s ease forwards;
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="header-container">
            <a href="customer.php" class="logo">
                <img src="https://via.placeholder.com/50" alt="Lhokseumawe Mobile Logo">
                <h1>Lhokseumawe <span>Mobile</span></h1>
            </a>
            <nav>
                <ul>
                    <li><a href="customer.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="produk.php"><i class="fas fa-mobile-alt"></i> Produk</a></li>
                    <li><a href="promo.php"><i class="fas fa-tags"></i> Promo</a></li>
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang <span class="cart-count">3</span></a></li>
                    <li><a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h2>Toko Ponsel Terbaik di Lhokseumawe</h2>
            <p>Dapatkan smartphone terbaru dengan harga terbaik dan garansi resmi. Gratis ongkir untuk wilayah Kota Lhokseumawe.</p>
            <a href="produk.php" class="btn">Belanja Sekarang</a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="container">
        <div class="section-title">
            <h2>Produk Unggulan</h2>
        </div>
        
        <div class="products-grid">
            <!-- Product 1 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Samsung Galaxy S23">
                    <span class="product-badge">HOT</span>
                </div>
                <div class="product-info">
                    <span class="product-category">Samsung</span>
                    <h3 class="product-title">Samsung Galaxy S23 Ultra 5G</h3>
                    <div class="product-price">
                        <span class="current-price">Rp 18.999.000</span>
                        <span class="original-price">Rp 21.999.000</span>
                        <span class="discount-percent">14%</span>
                    </div>
                    <div class="product-meta">
                        <span class="product-stock"><i class="fas fa-check-circle"></i> Stok Tersedia</span>
                        <span class="product-rating"><i class="fas fa-star"></i> 4.9</span>
                    </div>
                    <div class="product-actions">
                        <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Keranjang</button>
                        <button class="wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            
            <!-- Product 2 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1510878933023-e2e2e3942fb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="iPhone 14 Pro">
                    <span class="product-badge new">NEW</span>
                </div>
                <div class="product-info">
                    <span class="product-category">Apple</span>
                    <h3 class="product-title">iPhone 14 Pro Max 256GB</h3>
                    <div class="product-price">
                        <span class="current-price">Rp 22.499.000</span>
                        <span class="original-price">Rp 24.999.000</span>
                        <span class="discount-percent">10%</span>
                    </div>
                    <div class="product-meta">
                        <span class="product-stock"><i class="fas fa-check-circle"></i> Stok Tersedia</span>
                        <span class="product-rating"><i class="fas fa-star"></i> 4.8</span>
                    </div>
                    <div class="product-actions">
                        <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Keranjang</button>
                        <button class="wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            
            <!-- Product 3 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Xiaomi Redmi Note 12">
                </div>
                <div class="product-info">
                    <span class="product-category">Xiaomi</span>
                    <h3 class="product-title">Xiaomi Redmi Note 12 Pro 5G</h3>
                    <div class="product-price">
                        <span class="current-price">Rp 4.999.000</span>
                        <span class="original-price">Rp 5.499.000</span>
                        <span class="discount-percent">9%</span>
                    </div>
                    <div class="product-meta">
                        <span class="product-stock"><i class="fas fa-check-circle"></i> Stok Tersedia</span>
                        <span class="product-rating"><i class="fas fa-star"></i> 4.7</span>
                    </div>
                    <div class="product-actions">
                        <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Keranjang</button>
                        <button class="wishlist"><i class="far fa-heart"></i></button>
                    </div>
                </div>
            </div>
            
            <!-- Product 4 -->
            <div class="product-card">
                <div class="product-image">
                    <img src="https://images.unsplash.com/photo-1598327105666-5b893731aff7?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Oppo Reno 8">
                    <span class="product-badge discount">DISKON</span>
                </div>
                <div class="product-info">
                    <span class="product-category">Oppo</span>
                    <h3 class="product-title">Oppo Reno 8 Pro 5G</h3>
                    <div class="product-price">
                        <span class="current-price">Rp 8.999.000</span>
                        <span class="original-price">Rp 10.499.000</span>
                        <span class="discount-percent">14%</span>
                    </div>
                    <div class="product-meta">
                        <span class="product-stock"><i class="fas fa-check-circle"></i> Stok Tersedia</span>
                        <span class="product-rating"><i class="fas fa-star"></i> 4.6</span>
                    </div>
                    <div class="product-actions">
                        <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Keranjang</button>
                        <button class="wishlist"><i class="far fa-heart"></i></button>
                    </div>
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
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="footer-col">
                <h3>Link Cepat</h3>
                <ul class="footer-links">
                    <li><a href="costumer.html"><i class="fas fa-chevron-right"></i> Beranda</a></li>
                    <li><a href="produk.html"><i class="fas fa-chevron-right"></i> Produk</a></li>
                    <li><a href="promo.html"><i class="fas fa-chevron-right"></i> Promo</a></li>
                    <li><a href="tentang.html"><i class="fas fa-chevron-right"></i> Tentang Kami</a></li>
                </ul>
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
            
            <div class="footer-col">
                <h3>Pembayaran</h3>
                <p>Kami menerima berbagai metode pembayaran:</p>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <i class="fas fa-money-bill-wave" title="Tunai"></i>
                    <i class="fab fa-cc-paypal" title="PayPal"></i>
                    <i class="fas fa-qrcode" title="QRIS"></i>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; 2025 Lhokseumawe Mobile. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Enhanced JavaScript for e-commerce functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Cart functionality
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            const cartCount = document.querySelector('.cart-count');
            let count = parseInt(cartCount.textContent) || 0;
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Animation effect
                    this.innerHTML = '<i class="fas fa-check"></i> Ditambahkan';
                    this.style.backgroundColor = 'var(--success)';
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-cart-plus"></i> Keranjang';
                        this.style.backgroundColor = 'var(--primary)';
                    }, 1000);
                    
                    // Update cart count
                    count++;
                    cartCount.textContent = count;
                    cartCount.style.transform = 'scale(1.2)';
                    
                    setTimeout(() => {
                        cartCount.style.transform = 'scale(1)';
                    }, 300);
                    
                    // Get product info
                    const productCard = this.closest('.product-card');
                    const productName = productCard.querySelector('.product-title').textContent;
                    const productPrice = productCard.querySelector('.current-price').textContent;
                    
                    console.log(`Added to cart: ${productName} (${productPrice})`);
                });
            });
            
            // Wishlist functionality
            const wishlistButtons = document.querySelectorAll('.wishlist');
            
            wishlistButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const productCard = this.closest('.product-card');
                    const productName = productCard.querySelector('.product-title').textContent;
                    
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        icon.style.color = 'var(--danger)';
                        this.style.backgroundColor = 'rgba(231, 76, 60, 0.1)';
                        console.log(`Added to wishlist: ${productName}`);
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        icon.style.color = '';
                        this.style.backgroundColor = 'var(--light)';
                        console.log(`Removed from wishlist: ${productName}`);
                    }
                });
            });
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
