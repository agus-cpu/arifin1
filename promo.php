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
    <title>Promo Spesial - Lhokseumawe Mobile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --text: #333;
            --text-light: #7f8c8d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: var(--text);
            line-height: 1.6;
        }
        
        /* Header Styles */
        header {
            background: linear-gradient(135deg, var(--secondary), #1a252f);
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .logo-text .white {
            color: white;
        }
        
        .logo-text .blue {
            color: var(--primary);
        }
        
        /* Navigation */
        nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        nav ul li a:hover {
            color: var(--primary);
        }
        
        .cart-count {
            background-color: var(--accent);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            margin-left: 5px;
        }
        
        /* Info Bar */
        .info-bar {
            background-color: rgba(0,0,0,0.2);
            padding: 10px 0;
            text-align: center;
            font-size: 0.9rem;
        }
        
        /* Main Content */
        .main-content {
            padding: 30px 0 50px;
        }
        
        /* Promo Banner */
        .promo-banner {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        /* Section Header */
        .section-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .section-header h2 {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .section-header h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary);
        }
        
        .section-header p {
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        /* Product Card */
        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: var(--accent);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .product-content {
            padding: 20px;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .product-description {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 15px;
            min-height: 40px;
        }
        
        .price-container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .current-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent);
        }
        
        .original-price {
            font-size: 0.9rem;
            color: var(--text-light);
            text-decoration: line-through;
        }
        
        .discount {
            background-color: var(--accent);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .product-features {
            margin: 15px 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            margin-bottom: 5px;
            color: var(--text-light);
        }
        
        .feature-item i {
            color: var(--primary);
            font-size: 0.9rem;
        }
        
        .btn {
            display: inline-block;
            width: 100%;
            padding: 10px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 10px;
        }
        
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Flash Sale Timer */
        .flash-sale-timer {
            background-color: var(--accent);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .timer-countdown {
            display: flex;
            gap: 10px;
        }
        
        .timer-segment {
            background-color: rgba(0,0,0,0.2);
            padding: 5px 10px;
            border-radius: 4px;
            min-width: 40px;
        }
        
        /* Payment Methods */
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .payment-method {
            background-color: white;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        /* Footer */
        footer {
            background-color: var(--secondary);
            color: white;
            padding: 40px 0 20px;
        }
        
        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-column h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-column ul li a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            color: white;
            background-color: rgba(255,255,255,0.1);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            color: #bdc3c7;
        }
        
        /* Notification */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--success);
            color: white;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(150%);
            transition: transform 0.3s ease-out;
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
            
            nav ul {
                gap: 15px;
            }
            
            .flash-sale-timer {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        @media (max-width: 768px) {
            .logo-text {
                font-size: 1.3rem;
            }
            
            nav ul {
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            nav ul li a {
                font-size: 0.9rem;
            }
            
            .section-header h2 {
                font-size: 1.6rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 576px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
                <img alt="Lhokseumawe Mobile Logo" src="https://via.placeholder.com/45x45?text=LM"/>
                <div class="logo-text">
                    <span class="white">Lhokseumawe</span> <span class="blue">Mobile</span>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="customer.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="produk.php"><i class="fas fa-mobile-alt"></i> Produk</a></li>
                    <li><a class="promo.php active" href="promo.php"><i class="fas fa-tags"></i> Promo</a></li>
                    <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
                    <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang <span class="cart-count">0</span></a></li>
                </ul>
            </nav>
        </div>
        <div class="info-bar">
            <div class="container">
                <i class="fas fa-truck"></i> Gratis ongkir wilayah Kota Lhokseumawe | 
                <i class="fas fa-shield-alt"></i> Garansi resmi 1 tahun | 
                <i class="fas fa-phone-alt"></i> Hubungi: 0812-3456-7890
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <!-- Flash Sale Banner -->
            <div class="flash-sale-timer">
                <i class="fas fa-bolt"></i>
                <span>FLASH SALE AKHIR TAHUN - Berakhir dalam:</span>
                <div class="timer-countdown">
                    <span class="timer-segment" id="hours">12</span>
                    <span>:</span>
                    <span class="timer-segment" id="minutes">45</span>
                    <span>:</span>
                    <span class="timer-segment" id="seconds">30</span>
                </div>
            </div>

            <!-- Promo Banner -->
            <div class="promo-banner">
                <i class="fas fa-gift"></i> PROMO SPESIAL! Dapatkan voucher Rp 150.000 untuk pembelian di atas Rp 3.500.000 dan gratis ongkir seluruh Aceh!
            </div>

            <!-- Featured Promo Products -->
            <div class="section-header">
                <h2>Promo Spesial Bulan Ini</h2>
                <p>Dapatkan smartphone terbaru dengan harga terbaik dan bonus menarik</p>
            </div>
            
            <div class="product-grid">
                <!-- iPhone 15 Pro Max -->
                <div class="product-card">
                    <div class="product-badge">Terlaris</div>
                    <img alt="iPhone 15 Pro Max" class="product-image" src="https://via.placeholder.com/300x300?text=iPhone+15+Pro+Max"/>
                    <div class="product-content">
                        <h3 class="product-title">iPhone 15 Pro Max</h3>
                        <p class="product-description">Layar Super Retina XDR 6.7", Chip A17 Pro, Kamera 48MP</p>
                        <div class="price-container">
                            <span class="current-price">Rp 19.999.000</span>
                            <span class="original-price">Rp 22.999.000</span>
                            <span class="discount">13%</span>
                        </div>
                        <div class="product-features">
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Gratis AirPods Pro 2</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Garansi resmi 1 tahun</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Bebas biaya kirim</div>
                        </div>
                        <button class="btn add-to-cart" data-id="iphone-15-pro-max" data-name="iPhone 15 Pro Max" data-price="19999000" data-image="https://via.placeholder.com/300x300?text=iPhone+15+Pro+Max">
                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                        </button>
                        <button class="btn btn-outline"><i class="fas fa-info-circle"></i> Detail</button>
                    </div>
                </div>

                <!-- Samsung Galaxy S24 Ultra -->
                <div class="product-card">
                    <div class="product-badge">Baru</div>
                    <img alt="Samsung Galaxy S24 Ultra" class="product-image" src="https://via.placeholder.com/300x300?text=S24+Ultra"/>
                    <div class="product-content">
                        <h3 class="product-title">Samsung Galaxy S24 Ultra</h3>
                        <p class="product-description">Layar Dynamic AMOLED 2X 6.8", Snapdragon 8 Gen 3, Kamera 200MP</p>
                        <div class="price-container">
                            <span class="current-price">Rp 18.499.000</span>
                            <span class="original-price">Rp 21.999.000</span>
                            <span class="discount">16%</span>
                        </div>
                        <div class="product-features">
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Gratis Galaxy Watch6</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Garansi resmi 2 tahun</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Bonus tempered glass</div>
                        </div>
                        <button class="btn add-to-cart" data-id="s24-ultra" data-name="Samsung Galaxy S24 Ultra" data-price="18499000" data-image="https://via.placeholder.com/300x300?text=S24+Ultra">
                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                        </button>
                        <button class="btn btn-outline"><i class="fas fa-info-circle"></i> Detail</button>
                    </div>
                </div>

                <!-- Xiaomi 14 Pro -->
                <div class="product-card">
                    <div class="product-badge">Diskon</div>
                    <img alt="Xiaomi 14 Pro" class="product-image" src="https://via.placeholder.com/300x300?text=Xiaomi+14+Pro"/>
                    <div class="product-content">
                        <h3 class="product-title">Xiaomi 14 Pro</h3>
                        <p class="product-description">Layar LTPO AMOLED 6.73", Snapdragon 8 Gen 3, Kamera 50MP</p>
                        <div class="price-container">
                            <span class="current-price">Rp 12.999.000</span>
                            <span class="original-price">Rp 14.999.000</span>
                            <span class="discount">13%</span>
                        </div>
                        <div class="product-features">
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Gratis Mi Band 8 Pro</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Garansi resmi 1 tahun</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Bonus casing premium</div>
                        </div>
                        <button class="btn add-to-cart" data-id="xiaomi-14-pro" data-name="Xiaomi 14 Pro" data-price="12999000" data-image="https://via.placeholder.com/300x300?text=Xiaomi+14+Pro">
                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                        </button>
                        <button class="btn btn-outline"><i class="fas fa-info-circle"></i> Detail</button>
                    </div>
                </div>

                <!-- Oppo Find X6 Pro -->
                <div class="product-card">
                    <div class="product-badge">Limited</div>
                    <img alt="Oppo Find X6 Pro" class="product-image" src="https://via.placeholder.com/300x300?text=Oppo+Find+X6"/>
                    <div class="product-content">
                        <h3 class="product-title">Oppo Find X6 Pro</h3>
                        <p class="product-description">Layar AMOLED 6.82", Snapdragon 8 Gen 2, Kamera 50MP</p>
                        <div class="price-container">
                            <span class="current-price">Rp 11.499.000</span>
                            <span class="original-price">Rp 13.999.000</span>
                            <span class="discount">18%</span>
                        </div>
                        <div class="product-features">
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Gratis Enco X3</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Garansi resmi 1 tahun</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Bonus charger 100W</div>
                        </div>
                        <button class="btn add-to-cart" data-id="oppo-find-x6" data-name="Oppo Find X6 Pro" data-price="11499000" data-image="https://via.placeholder.com/300x300?text=Oppo+Find+X6">
                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                        </button>
                        <button class="btn btn-outline"><i class="fas fa-info-circle"></i> Detail</button>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="section-header">
                <h2>Metode Pembayaran</h2>
                <p>Kami menerima berbagai metode pembayaran untuk kenyamanan Anda</p>
            </div>
            
            <div class="payment-methods">
                <div class="payment-method">
                    <i class="fab fa-cc-visa"></i> Visa
                </div>
                <div class="payment-method">
                    <i class="fab fa-cc-mastercard"></i> Mastercard
                </div>
                <div class="payment-method">
                    <i class="fas fa-money-bill-wave"></i> Tunai
                </div>
                <div class="payment-method">
                    <i class="fas fa-qrcode"></i> QRIS
                </div>
                <div class="payment-method">
                    <i class="fab fa-cc-paypal"></i> PayPal
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-column">
                    <h3>Tentang Kami</h3>
                    <p>Lhokseumawe Mobile menyediakan smartphone berkualitas dengan harga terbaik dan garansi resmi di Lhokseumawe.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Layanan</h3>
                    <ul>
                        <li><a href="#">Garansi Produk</a></li>
                        <li><a href="#">Pengiriman</a></li>
                        <li><a href="#">Pembayaran</a></li>
                        <li><a href="#">Trade-In HP Lama</a></li>
                        <li><a href="#">Servis HP</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Kontak</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Lhokseumawe, Gp.teungoh</li>
                        <li><i class="fas fa-phone"></i> 0823-74358161</li>
                        <li><i class="fas fa-envelope"></i> arifinrahman1102@gmail.com</li>
                        <li><i class="fas fa-clock"></i> Buka setiap hari 09.00 - 21.00 WIB</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                © 2025 Lhokseumawe Mobile. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Notification Element -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message">Produk telah ditambahkan ke keranjang</span>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize cart count
            updateCartCount();
            
            // Flash sale countdown timer
            function updateCountdown() {
                const now = new Date();
                const endTime = new Date();
                endTime.setHours(23, 59, 59, 0); // Set to end of day
                
                const diff = endTime - now;
                
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
                document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            }
            
            setInterval(updateCountdown, 1000);
            updateCountdown();
            
            // Add to cart functionality
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const product = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        price: parseInt(this.getAttribute('data-price')),
                        image: this.getAttribute('data-image'),
                        quantity: 1
                    };
                    
                    addToCart(product);
                });
            });
            
            function addToCart(product) {
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Check if product already in cart
                const existingItem = cart.find(item => item.id === product.id);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    cart.push(product);
                }
                
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                showNotification(`${product.name} telah ditambahkan ke keranjang`);
            }
            
            function updateCartCount() {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                
                document.querySelectorAll('.cart-count').forEach(element => {
                    element.textContent = totalItems;
                });
            }
            
            function showNotification(message) {
                const notification = document.getElementById('notification');
                const messageElement = document.getElementById('notification-message');
                
                messageElement.textContent = message;
                notification.classList.add('show');
                
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            }
        });
    </script>
</body>
</html>