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
    <title>Keranjang Belanja - Lhokseumawe Mobile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2980b9;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --danger: #e74c3c;
            --success: #27ae60;
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
        
        .cart-count {
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .page-title {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary);
        }
        
        /* Cart Section */
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .cart-items {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .cart-item-details {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .cart-item-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .cart-item-brand {
            color: var(--primary);
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .cart-item-price {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--danger);
        }
        
        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background-color: #f5f5f5;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .quantity-input {
            width: 40px;
            height: 30px;
            text-align: center;
            border: none;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }
        
        .remove-item {
            background: none;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .remove-item i {
            margin-right: 5px;
        }
        
        /* Cart Summary */
        .cart-summary {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        
        .summary-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .summary-total {
            font-weight: bold;
            font-size: 1.2rem;
            margin: 20px 0;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--success);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: #219653;
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 15px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .continue-shopping:hover {
            text-decoration: underline;
        }
        
        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart-icon {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-cart-message {
            font-size: 1.2rem;
            margin-bottom: 30px;
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
            
            .cart-container {
                grid-template-columns: 1fr;
            }
            
            .cart-item {
                grid-template-columns: 80px 1fr;
                grid-template-rows: auto auto;
            }
            
            .cart-item-actions {
                grid-column: span 2;
                flex-direction: row;
                align-items: center;
                margin-top: 15px;
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
                    <li><a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang <span class="cart-count">3</span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container">
        <h1 class="page-title">Keranjang Belanja</h1>
        
        <div class="cart-container">
            <!-- Cart Items -->
            <div class="cart-items">
                <!-- Product 1 -->
                <div class="cart-item">
                    <img src="https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Samsung Galaxy S23 Ultra" class="cart-item-image">
                    <div class="cart-item-details">
                        <div>
                            <h3 class="cart-item-title">Samsung Galaxy S23 Ultra 5G</h3>
                            <p class="cart-item-brand">Samsung</p>
                            <p class="cart-item-price">Rp 18.999.000</p>
                        </div>
                        <p><i class="fas fa-check-circle" style="color: var(--success);"></i> Stok Tersedia</p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="remove-item"><i class="fas fa-trash"></i> Hapus</button>
                        <div class="quantity-control">
                            <button class="quantity-btn minus">-</button>
                            <input type="text" class="quantity-input" value="1" readonly>
                            <button class="quantity-btn plus">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="cart-item">
                    <img src="https://images.unsplash.com/photo-1510878933023-e2e2e3942fb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="iPhone 14 Pro Max" class="cart-item-image">
                    <div class="cart-item-details">
                        <div>
                            <h3 class="cart-item-title">iPhone 14 Pro Max 256GB</h3>
                            <p class="cart-item-brand">Apple</p>
                            <p class="cart-item-price">Rp 22.499.000</p>
                        </div>
                        <p><i class="fas fa-check-circle" style="color: var(--success);"></i> Stok Tersedia</p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="remove-item"><i class="fas fa-trash"></i> Hapus</button>
                        <div class="quantity-control">
                            <button class="quantity-btn minus">-</button>
                            <input type="text" class="quantity-input" value="1" readonly>
                            <button class="quantity-btn plus">+</button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="cart-item">
                    <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Xiaomi Redmi Note 12" class="cart-item-image">
                    <div class="cart-item-details">
                        <div>
                            <h3 class="cart-item-title">Xiaomi Redmi Note 12 Pro 5G</h3>
                            <p class="cart-item-brand">Xiaomi</p>
                            <p class="cart-item-price">Rp 4.999.000</p>
                        </div>
                        <p><i class="fas fa-check-circle" style="color: var(--success);"></i> Stok Tersedia</p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="remove-item"><i class="fas fa-trash"></i> Hapus</button>
                        <div class="quantity-control">
                            <button class="quantity-btn minus">-</button>
                            <input type="text" class="quantity-input" value="1" readonly>
                            <button class="quantity-btn plus">+</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="cart-summary">
                <h2 class="summary-title">Ringkasan Belanja</h2>
                
                <div class="summary-row">
                    <span>Subtotal (3 produk)</span>
                    <span>Rp 46.497.000</span>
                </div>
                
                <div class="summary-row">
                    <span>Ongkos Kirim</span>
                    <span>Gratis</span>
                </div>
                
                <div class="summary-row">
                    <span>Voucher Diskon</span>
                    <span>-Rp 150.000</span>
                </div>
                
                <div class="summary-row summary-total">
                    <span>Total Pembayaran</span>
                    <span>Rp 46.347.000</span>
                </div>
                
                <button class="checkout-btn">Lanjut ke Pembayaran</button>
                <a href="produk.php" class="continue-shopping">Lanjutkan Belanja</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer style="background-color: var(--dark); color: white; padding: 40px 0; margin-top: 50px;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
                <div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 20px;">Tentang Kami</h3>
                    <p style="margin-bottom: 15px;">Lhokseumawe Mobile adalah toko ponsel terpercaya di Kota Lhokseumawe yang menyediakan berbagai smartphone berkualitas dengan harga kompetitif.</p>
                </div>
                
                <div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 20px;">Kontak Kami</h3>
                    <p style="margin-bottom: 10px;"><i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i> Lhokseumawe,Gp.teungoh</p>
                    <p style="margin-bottom: 10px;"><i class="fas fa-phone" style="margin-right: 10px;"></i> 0823-74358161</p>
                    <p style="margin-bottom: 10px;"><i class="fas fa-envelope" style="margin-right: 10px;"></i> arifinrahman1102@gmail.com</p>
                </div>
                
                <div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 20px;">Pembayaran</h3>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <i class="fab fa-cc-visa" style="font-size: 2rem;"></i>
                        <i class="fab fa-cc-mastercard" style="font-size: 2rem;"></i>
                        <i class="fas fa-money-bill-wave" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; padding-top: 30px; margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1);">
                <p>&copy; 2025 Lhokseumawe Mobile. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

  <script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil data keranjang dari localStorage
    let keranjang = JSON.parse(localStorage.getItem("keranjang")) || [];
    const cartItemsContainer = document.querySelector(".cart-items");

    // Tampilkan isi keranjang
    if (keranjang.length > 0) {
        cartItemsContainer.innerHTML = "";
        keranjang.forEach((item, index) => {
            cartItemsContainer.innerHTML += `
                <div class="cart-item">
                    <img src="${item.gambar}" alt="${item.nama}" class="cart-item-image">
                    <div class="cart-item-details">
                        <div>
                            <h3 class="cart-item-title">${item.nama}</h3>
                            <p class="cart-item-price">Rp ${item.harga.toLocaleString('id-ID')}</p>
                        </div>
                        <p><i class="fas fa-check-circle" style="color: var(--success);"></i> Stok Tersedia</p>
                    </div>
                    <div class="cart-item-actions">
                        <button class="remove-item" data-index="${index}"><i class="fas fa-trash"></i> Hapus</button>
                        <div class="quantity-control">
                            <button class="quantity-btn minus" data-index="${index}">-</button>
                            <input type="text" class="quantity-input" value="${item.jumlah}" readonly>
                            <button class="quantity-btn plus" data-index="${index}">+</button>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    // Event untuk tambah/kurangi jumlah
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.dataset.index;
            const input = this.parentElement.querySelector('.quantity-input');
            let value = parseInt(input.value);

            if (this.classList.contains('minus') && value > 1) {
                input.value = value - 1;
                keranjang[index].jumlah--;
            } else if (this.classList.contains('plus')) {
                input.value = value + 1;
                keranjang[index].jumlah++;
            }

            localStorage.setItem("keranjang", JSON.stringify(keranjang));
            updateCartSummary();
        });
    });

    // Event untuk hapus item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = this.dataset.index;
            const cartItem = this.closest('.cart-item');
            cartItem.style.animation = 'fadeOut 0.3s ease forwards';

            setTimeout(() => {
                // Hapus dari array dan localStorage
                keranjang.splice(index, 1);
                localStorage.setItem("keranjang", JSON.stringify(keranjang));

                // Hapus dari tampilan
                cartItem.remove();

                updateCartCount();
                updateCartSummary();

                if (document.querySelectorAll('.cart-item').length === 0) {
                    showEmptyCart();
                }
            }, 300);
        });
    });

    // Update jumlah item di ikon keranjang
    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        cartCount.textContent = keranjang.length;
    }

    // Update ringkasan belanja
    function updateCartSummary() {
        let subtotal = 0;
        keranjang.forEach(item => {
            subtotal += item.harga * item.jumlah;
        });

        const discount = 150000;
        const total = subtotal - discount;

        document.querySelector('.summary-row:nth-child(1) span:last-child').textContent =
            `Rp ${subtotal.toLocaleString('id-ID')}`;
        document.querySelector('.summary-row:nth-child(3) span:last-child').textContent =
            `-Rp ${discount.toLocaleString('id-ID')}`;
        document.querySelector('.summary-total span:last-child').textContent =
            `Rp ${total.toLocaleString('id-ID')}`;
        document.querySelector('.summary-row:nth-child(1) span:first-child').textContent =
            `Subtotal (${keranjang.length} produk)`;
    }

    // Tampilkan pesan keranjang kosong
    function showEmptyCart() {
        const cartItems = document.querySelector('.cart-items');
        cartItems.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="empty-cart-message">Keranjang belanja Anda kosong</h3>
                <a href="produk.php" class="btn" style="background-color: var(--primary); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Mulai Belanja</a>
            </div>
        `;
        document.querySelector('.cart-summary').style.display = 'none';
    }

    // Inisialisasi awal
    updateCartCount();
    updateCartSummary();
});

// Fungsi ke pembayaran
function proceedToPayment() {
    const total = document.querySelector('.summary-total span:last-child').textContent;
    localStorage.setItem("totalPembayaran", total);
    window.location.href = "pembayaran.php";
}

document.querySelector('.checkout-btn').addEventListener('click', proceedToPayment);
</script>
