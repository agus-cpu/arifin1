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
    <title>Pembayaran - Lhokseumawe Mobile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --danger: #f72585;
            --success: #4cc9f0;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .payment-header h1 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .payment-header .steps {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin: 0 15px;
        }
        
        .step-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 8px;
            z-index: 2;
        }
        
        .step.active .step-number {
            background-color: var(--primary);
            color: white;
        }
        
        .step.completed .step-number {
            background-color: var(--success);
            color: white;
        }
        
        .step-text {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .step.active .step-text {
            color: var(--primary);
            font-weight: 600;
        }
        
        .step.completed .step-text {
            color: var(--success);
        }
        
        .step-line {
            position: absolute;
            top: 18px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }
        
        .step:last-child .step-line {
            display: none;
        }
        
        /* Payment Content */
        .payment-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 25px;
        }
        
        /* Order Summary */
        .order-summary {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: var(--primary);
        }
        
        .product-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .product-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .product-details {
            flex: 1;
        }
        
        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .product-brand {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .product-price {
            font-weight: 600;
            color: var(--primary);
        }
        
        .product-qty {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Payment Methods */
        .payment-methods {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
        }
        
        .method-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        
        .method-tab {
            padding: 10px 15px;
            font-weight: 500;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: var(--transition);
        }
        
        .method-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        
        .method-tab:hover:not(.active) {
            color: var(--accent);
        }
        
        .method-options {
            margin-top: 15px;
        }
        
        .method-option {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .method-option:hover {
            border-color: var(--accent);
        }
        
        .method-option.selected {
            border-color: var(--primary);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .method-icon {
            width: 40px;
            height: 40px;
            margin-right: 15px;
            object-fit: contain;
        }
        
        .method-info {
            flex: 1;
        }
        
        .method-name {
            font-weight: 500;
            margin-bottom: 3px;
        }
        
        .method-desc {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .method-radio {
            width: 18px;
            height: 18px;
            border: 2px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            transition: var(--transition);
        }
        
        .method-option.selected .method-radio {
            border-color: var(--primary);
            background-color: var(--primary);
        }
        
        .method-radio::after {
            content: '';
            width: 8px;
            height: 8px;
            background-color: white;
            border-radius: 50%;
            opacity: 0;
            transition: var(--transition);
        }
        
        .method-option.selected .method-radio::after {
            opacity: 1;
        }
        
        /* Payment Summary */
        .payment-summary {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            position: sticky;
            top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        
        .summary-label {
            color: #6c757d;
        }
        
        .summary-value {
            font-weight: 500;
        }
        
        .summary-total {
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .summary-total .summary-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .summary-total .summary-value {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
        }
        
        /* Payment Action */
        .payment-actions {
            margin-top: 25px;
        }
        
        .btn-pay {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-pay:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-pay i {
            margin-right: 8px;
        }
        
        .secure-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .secure-payment i {
            color: var(--success);
            margin-right: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .payment-content {
                grid-template-columns: 1fr;
            }
            
            .payment-summary {
                position: static;
                margin-top: 25px;
            }
            
            .steps {
                flex-wrap: wrap;
            }
            
            .step {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-header">
            <h1>Pembayaran</h1>
            <p>Lengkapi pembayaran untuk menyelesaikan pesanan Anda</p>
            
            <div class="steps">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <div class="step-line"></div>
                    <div class="step-text">Keranjang</div>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <div class="step-line"></div>
                    <div class="step-text">Pembayaran</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text">Selesai</div>
                </div>
            </div>
        </div>
        
        <div class="payment-content">
            <div class="payment-main">
                <div class="order-summary">
                    <h2 class="section-title">Ringkasan Pesanan</h2>
                    
                    <div class="product-item">
                        <img src="https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Samsung Galaxy S23 Ultra" class="product-image">
                        <div class="product-details">
                            <div class="product-name">Samsung Galaxy S23 Ultra 5G</div>
                            <div class="product-brand">Samsung</div>
                            <div class="product-price">Rp 18.999.000</div>
                            <div class="product-qty">1 barang</div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <img src="https://images.unsplash.com/photo-1510878933023-e2e2e3942fb0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="iPhone 14 Pro Max" class="product-image">
                        <div class="product-details">
                            <div class="product-name">iPhone 14 Pro Max 256GB</div>
                            <div class="product-brand">Apple</div>
                            <div class="product-price">Rp 22.499.000</div>
                            <div class="product-qty">1 barang</div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <img src="https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Xiaomi Redmi Note 12" class="product-image">
                        <div class="product-details">
                            <div class="product-name">Xiaomi Redmi Note 12 Pro 5G</div>
                            <div class="product-brand">Xiaomi</div>
                            <div class="product-price">Rp 4.999.000</div>
                            <div class="product-qty">1 barang</div>
                        </div>
                    </div>
                </div>
                
                <div class="payment-methods">
                    <h2 class="section-title">Metode Pembayaran</h2>
                    
                    <div class="method-tabs">
                        <div class="method-tab active">Transfer Bank</div>
                        <div class="method-tab">E-Wallet</div>
                        <div class="method-tab">Kartu Kredit</div>
                    </div>
                    
                    <div class="method-options">
                        <div class="method-option selected">
                            <img src="bankbca.png" alt="BCA" class="method-icon">
                            <div class="method-info">
                                <div class="method-name">Bank BCA</div>
                                <div class="method-desc">Transfer Virtual Account</div>
                            </div>
                            <div class="method-radio"></div>
                        </div>
                        
                        <div class="method-option">
                            <img src="bankmandiri.png" alt="Mandiri" class="method-icon">
                            <div class="method-info">
                                <div class="method-name">Bank Mandiri</div>
                                <div class="method-desc">Transfer Virtual Account</div>
                            </div>
                            <div class="method-radio"></div>
                        </div>
                        
                        <div class="method-option">
                            <img src="bankbni.png" alt="BNI" class="method-icon">
                            <div class="method-info">
                                <div class="method-name">Bank BNI</div>
                                <div class="method-desc">Transfer Virtual Account</div>
                            </div>
                            <div class="method-radio"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="payment-summary">
                <h2 class="section-title">Ringkasan Pembayaran</h2>
                
                <div class="summary-row">
                    <span class="summary-label">Subtotal (3 produk)</span>
                    <span class="summary-value">Rp 46.497.000</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Ongkos Kirim</span>
                    <span class="summary-value">Gratis</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Voucher Diskon</span>
                    <span class="summary-value">-Rp 150.000</span>
                </div>
                
                <div class="summary-row summary-total">
                    <span class="summary-label">Total Pembayaran</span>
                    <span class="summary-value">Rp 46.347.000</span>
                </div>
                
                <div class="payment-actions">
                    <button class="btn-pay">
                        <i class="fas fa-lock"></i> Bayar Sekarang
                    </button>
                    <div class="secure-payment">
                        <i class="fas fa-shield-alt"></i> Pembayaran Aman & Terenkripsi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pilih metode pembayaran
        document.querySelectorAll('.method-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.method-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
        
        // Tombol bayar
        document.querySelector('.btn-pay').addEventListener('click', function() {
            // Validasi pembayaran
            const selectedMethod = document.querySelector('.method-option.selected .method-name').textContent;
            
            // Simulasi proses pembayaran
            alert(`Anda akan melakukan pembayaran sebesar Rp 46.347.000 menggunakan ${selectedMethod}`);
            
            // Redirect ke halaman konfirmasi (ganti dengan URL yang sesuai)
            // window.location.href = "konfirmasi.html";
        });
        
        // Tab metode pembayaran
        document.querySelectorAll('.method-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.method-tab').forEach(t => {
                    t.classList.remove('active');
                });
                this.classList.add('active');
                
                // Di sini bisa menambahkan logika untuk menampilkan metode pembayaran yang sesuai
                // Contoh: jika tab E-Wallet diklik, tampilkan opsi GoPay, OVO, dll.
            });
        });
    </script>
</body>
</html>