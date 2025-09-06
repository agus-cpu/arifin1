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
  <title>Toko Ponsel Lhokseumawe - Produk Unggulan</title>
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
    
    /* Page Header */
    .page-header {
      text-align: center;
      margin-bottom: 40px;
      padding: 30px 0;
      background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(231, 76, 60, 0.1));
      border-radius: 10px;
    }
    
    .page-header h1 {
      font-size: 2.5rem;
      color: var(--secondary);
      margin-bottom: 15px;
    }
    
    .page-header p {
      color: var(--text-light);
      max-width: 700px;
      margin: 0 auto;
      font-size: 1.1rem;
    }
    
    /* Filter Section */
    .filter-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      flex-wrap: wrap;
      gap: 20px;
    }
    
    .search-container {
      position: relative;
      width: 350px;
    }
    
    .search-container input {
      width: 100%;
      padding: 12px 20px 12px 45px;
      border: 1px solid #ddd;
      border-radius: 30px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .search-container input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
      outline: none;
    }
    
    .search-container i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
    }
    
    .filter-options {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .filter-dropdown {
      position: relative;
    }
    
    .filter-btn {
      padding: 12px 20px;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 30px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
      border-color: var(--primary);
    }
    
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 200px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      z-index: 1;
      border-radius: 8px;
      padding: 10px 0;
      margin-top: 5px;
    }
    
    .dropdown-content a {
      color: var(--text);
      padding: 10px 20px;
      text-decoration: none;
      display: block;
      transition: all 0.3s ease;
    }
    
    .dropdown-content a:hover {
      background-color: var(--light);
      color: var(--primary);
    }
    
    .filter-dropdown:hover .dropdown-content {
      display: block;
    }
    
    /* Weather Info */
    .weather-info {
      display: flex;
      align-items: center;
      gap: 15px;
      background-color: white;
      padding: 10px 20px;
      border-radius: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .weather-item {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 0.9rem;
      color: var(--text-light);
    }
    
    .weather-item i {
      color: var(--primary);
    }
    
    /* Products Grid */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 25px;
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
    
    .product-brand {
      font-size: 0.9rem;
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 5px;
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
    
    .product-meta {
      display: flex;
      justify-content: space-between;
      margin: 15px 0;
    }
    
    .product-rating {
      color: var(--warning);
      font-size: 0.9rem;
    }
    
    .product-stock {
      font-size: 0.85rem;
      color: var(--success);
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
    }
    
    .btn:hover {
      background-color: #2980b9;
      transform: translateY(-2px);
    }
    
    .btn-outline {
      background-color: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
      margin-top: 10px;
    }
    
    .btn-outline:hover {
      background-color: var(--primary);
      color: white;
    }
    
    /* Pagination */
    .pagination {
      display: flex;
      justify-content: center;
      margin-top: 40px;
      gap: 10px;
    }
    
    .page-item {
      list-style: none;
    }
    
    .page-link {
      display: block;
      padding: 8px 15px;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 5px;
      color: var(--text);
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .page-link:hover, .page-link.active {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
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
    
    /* Responsive Styles */
    @media (max-width: 992px) {
      .header-container {
        flex-direction: column;
        gap: 15px;
      }
      
      nav ul {
        gap: 15px;
      }
      
      .filter-section {
        flex-direction: column;
        align-items: stretch;
      }
      
      .search-container {
        width: 100%;
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
      
      .page-header h1 {
        font-size: 2rem;
      }
      
      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      }
    }
    
    @media (max-width: 576px) {
      .products-grid {
        grid-template-columns: 1fr;
      }
      
      .footer-container {
        grid-template-columns: 1fr;
      }
    }
    .product-image-div {
    width: 100%;
    height: 220px;
    background-size: cover;
    background-position: center;
    border-radius: 8px;
}

  </style>
</head>
<body>
  <header>
    <div class="container header-container">
      <div class="logo">
        <img src="https://via.placeholder.com/45x45?text=LM" alt="Lhokseumawe Mobile Logo">
        <div class="logo-text">
          <span class="white">Lhokseumawe</span> <span class="blue">Mobile</span>
        </div>
      </div>
      
      <nav>
        <ul>
          <li><a href="customer.php"><i class="fas fa-home"></i> Beranda</a></li>
          <li><a href="produk.php" class="active"><i class="fas fa-mobile-alt"></i> Produk</a></li>
          <li><a href="promo.php"><i class="fas fa-tags"></i> Promo</a></li>
          <li><a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a></li>
          <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang <span class="cart-count">3</span></a></li>
        </ul>
      </nav>
    </div>
    
    <div class="info-bar">
      <div class="container">
        <i class="fas fa-truck"></i> Gratis ongkir wilayah Kota Lhokseumawe | <i class="fas fa-shield-alt"></i> Garansi resmi 1 tahun | <i class="fas fa-phone-alt"></i> Hubungi: 0812-3456-7890
      </div>
    </div>
  </header>
  
  <main class="main-content">
    <div class="container">
      <div class="page-header">
        <h1>Produk Unggulan Kami</h1>
        <p>Temukan smartphone terbaik dengan harga kompetitif dan garansi resmi. Gratis ongkir untuk wilayah Lhokseumawe.</p>
      </div>
      
      <div class="filter-section">
        <div class="search-container">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Cari produk...">
        </div>
        
        <div class="filter-options">
          <div class="filter-dropdown">
            <button class="filter-btn">
              <i class="fas fa-filter"></i> Filter
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-content">
              <a href="#">Harga Terendah</a>
              <a href="#">Harga Tertinggi</a>
              <a href="#">Rating Tertinggi</a>
              <a href="#">Produk Terbaru</a>
            </div>
          </div>
          
          <div class="filter-dropdown">
            <button class="filter-btn">
              <i class="fas fa-list"></i> Kategori
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="dropdown-content">
              <a href="#">Semua Kategori</a>
              <a href="#">Flagship</a>
              <a href="#">Mid Range</a>
              <a href="#">Budget</a>
              <a href="#">Gaming</a>
            </div>
          </div>
          
          <div class="weather-info">
            <div class="weather-item">
              <i class="fas fa-temperature-high"></i>
              <span>31°C</span>
            </div>
            <div class="weather-item">
              <i class="fas fa-calendar-alt"></i>
              <span>06/08/2025</span>
            </div>
            <div class="weather-item">
              <i class="fas fa-clock"></i>
              <span>15:04 WIB</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="products-grid">
        <!-- Samsung Galaxy S23 Ultra -->
        <div class="product-card">
          <div class="product-badge">Terlaris</div>
          <div class="product-image-div" style="background-image: url('galaxys23.jpg');"></div>
          <div class="product-content">
            <div class="product-brand">SAMSUNG</div>
            <h3 class="product-title">Galaxy S23 Ultra 5G 256GB</h3>
            <p class="product-description">Layar Dynamic AMOLED 6.8", Snapdragon 8 Gen 2, Kamera 200MP</p>
            
            <div class="price-container">
              <span class="current-price">Rp 18.999.000</span>
              <span class="original-price">Rp 21.999.000</span>
              <span class="discount">14%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.9 (128)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- iPhone 14 Pro Max -->
        <div class="product-card">
          <div class="product-badge">Baru</div>
          <div class="product-image-div" style="background-image: url('iPhone14.jpg');"></div>
          <div class="product-content">
            <div class="product-brand">APPLE</div>
            <h3 class="product-title">iPhone 14 Pro Max 256GB</h3>
            <p class="product-description">Layar Super Retina XDR 6.7", Chip A16 Bionic, Kamera 48MP</p>
            
            <div class="price-container">
              <span class="current-price">Rp 22.499.000</span>
              <span class="original-price">Rp 24.999.000</span>
              <span class="discount">10%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.8 (95)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- Xiaomi Redmi Note 12 Pro -->
        <div class="product-card">
          <div class="product-badge">Diskon</div>
          <div class="product-image-div" style="background-image: url('redminote12.jpeg');"></div>
          <div class="product-content">
            <div class="product-brand">XIAOMI</div>
            <h3 class="product-title">Redmi Note 12 Pro 5G</h3>
            <p class="product-description">Layar AMOLED 6.67", Dimensity 1080, Kamera 50MP</p>
            
            <div class="price-container">
              <span class="current-price">Rp 4.999.000</span>
              <span class="original-price">Rp 5.499.000</span>
              <span class="discount">9%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.7 (86)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- Oppo Reno 8 Pro -->
        <div class="product-card">
          <div class="product-image-div" style="background-image: url('Reno8.jpg');"></div>
          <div class="product-content">
            <div class="product-brand">OPPO</div>
            <h3 class="product-title">Reno 8 Pro 5G 256GB</h3>
            <p class="product-description">Layar AMOLED 6.7", Dimensity 8100, Kamera 50MP</p>
            
            <div class="price-container">
              <span class="current-price">Rp 8.999.000</span>
              <span class="original-price">Rp 9.999.000</span>
              <span class="discount">10%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.6 (72)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- Vivo V27 Pro -->
        <div class="product-card">
          <div class="product-badge">Baru</div>
          <div class="product-image-div" style="background-image: url('V27Pro5.png');"></div>
          <div class="product-content">
            <div class="product-brand">VIVO</div>
            <h3 class="product-title">V27 Pro 5G 256GB</h3>
            <p class="product-description">Layar AMOLED 6.78", Dimensity 8200, Kamera 50MP</p>
            
            <div class="price-container">
              <span class="current-price">Rp 7.999.000</span>
              <span class="original-price">Rp 8.999.000</span>
              <span class="discount">11%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.5 (68)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- Realme GT Neo 5 -->
        <div class="product-card">
          <div class="product-badge">Diskon</div>
          <div class="product-image-div" style="background-image: url('realme.png');"></div>
          <div class="product-content">
            <div class="product-brand">REALME</div>
            <h3 class="product-title">GT Neo 5 5G 256GB</h3>
            <p class="product-description">Layar AMOLED 6.74", Snapdragon 8+, Kamera 50MP</p>
            
            <div class="price-container">
              <span class="current-price">Rp 6.499.000</span>
              <span class="original-price">Rp 7.499.000</span>
              <span class="discount">13%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.7 (59)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- Asus ROG Phone 7 -->
        <div class="product-card">
          <div class="product-badge">Gaming</div>
          <div class="product-image-div" style="background-image: url('rog.jpg');"></div>
          <div class="product-content">
            <div class="product-brand">ASUS</div>
            <h3 class="product-title">ROG Phone 7 5G 256GB</h3>
            <p class="product-description">Layar AMOLED 6.78" 165Hz, Snapdragon 8 Gen 2</p>
            
            <div class="price-container">
              <span class="current-price">Rp 14.999.000</span>
              <span class="original-price">Rp 16.999.000</span>
              <span class="discount">12%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.9 (47)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
        </div>
        
        <!-- OnePlus 11 -->
        <div class="product-card">
          <div class="product-image-div" style="background-image: url('oneplus.jpg');"></div>
          <div class="product-content">
            <div class="product-brand">ONEPLUS</div>
            <h3 class="product-title">OnePlus 11 5G 256GB</h3>
            <p class="product-description">Layar AMOLED 6.7" 120Hz, Snapdragon 8 Gen 2</p>
            
            <div class="price-container">
              <span class="current-price">Rp 12.499.000</span>
              <span class="original-price">Rp 13.999.000</span>
              <span class="discount">11%</span>
            </div>
            
            <div class="product-meta">
              <span class="product-rating">★ 4.8 (53)</span>
              <span class="product-stock">Tersedia</span>
            </div>
            
            <button class="btn"><i class="fas fa-shopping-cart"></i> Tambah ke Keranjang</button>
          </div>
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
          <h3>Kategori</h3>
          <ul>
            <li><a href="#">Smartphone Flagship</a></li>
            <li><a href="#">Smartphone Mid Range</a></li>
            <li><a href="#">Smartphone Budget</a></li>
            <li><a href="#">Smartphone Gaming</a></li>
            <li><a href="#">Aksesoris HP</a></li>
          </ul>
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
        &copy; 2025 Lhokseumawe Mobile. All Rights Reserved.
      </div>
    </div>
  </footer>

  <script>
    // Simple JavaScript for cart functionality
    document.querySelectorAll('.btn:not(.btn-outline)').forEach(button => {
      button.addEventListener('click', function() {
        const productTitle = this.closest('.product-card').querySelector('.product-title').textContent;
        const productPrice = this.closest('.product-card').querySelector('.current-price').textContent;
        
        // Update cart count
        const cartCount = document.querySelector('.cart-count');
        let count = parseInt(cartCount.textContent);
        cartCount.textContent = count + 1;
        
        // Show notification
        alert(`${productTitle} (${productPrice}) telah ditambahkan ke keranjang!`);
      });
    });
  </script>
</body>
</html>