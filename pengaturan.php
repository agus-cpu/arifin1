<?php
session_start();
include 'koneksi.php';

// Validasi login dan role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.html");
    exit;
}

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Update database
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE username = ?");
    $stmt->bind_param("ssss", $name, $email, $phone, $_SESSION['username']);
    $stmt->execute();
    
    // Update session
    $_SESSION['name'] = $name;
    
    // Redirect untuk menghindari resubmit
    header("Location: pengaturan.php");
    exit;
}

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$name = $user_data['name'] ?? 'Pemilik Toko';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Toko Handphone</title>
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
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.12); color: white;
        }
        .nav-link i {margin-right: 12px; font-size: 18px;}

        .user-profile {
            padding: 15px 20px; display: flex; align-items: center; border-top: 1px solid rgba(255,255,255,0.1);
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
        }
        .logout-btn:hover {background: #c0392b;}

        /* Main Content */
        .main-content {padding: 30px;}
        .header {display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap;}
        .header h1 {font-size: 26px;}
        .search-bar {
            display: flex; align-items: center; background: white; border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 6px 15px;
        }
        .search-bar input {
            border: none; outline: none; padding: 6px; min-width: 200px;
        }
        .search-bar i {color: #777; margin-right: 8px;}

        /* Settings Card */
        .settings-card {
            background: white; border-radius: var(--radius); padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px;
        }
        .settings-card h2 {
            font-size: 20px; margin-bottom: 20px; color: var(--dark);
            display: flex; align-items: center;
        }
        .settings-card h2 i {
            margin-right: 10px; color: var(--primary);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block; margin-bottom: 8px; font-weight: 500;
        }
        .form-control {
            width: 100%; padding: 10px 15px; border: 1px solid #ddd;
            border-radius: var(--radius); transition: var(--transition);
        }
        .form-control:focus {
            border-color: var(--primary); outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .form-row {
            display: flex; gap: 20px; margin-bottom: 20px;
        }
        .form-row .form-group {
            flex: 1;
        }

        /* Button Styles */
        .btn {
            padding: 10px 20px; border-radius: var(--radius); cursor: pointer;
            transition: var(--transition); font-weight: 500; border: none;
        }
        .btn-primary {
            background: var(--primary); color: white;
        }
        .btn-primary:hover {
            background: var(--secondary);
        }
        .btn-secondary {
            background: #e0e0e0; color: var(--dark);
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }

        /* Tabs */
        .tabs {
            display: flex; border-bottom: 1px solid #ddd; margin-bottom: 25px;
        }
        .tab {
            padding: 10px 20px; cursor: pointer; position: relative;
            color: #777; font-weight: 500; transition: var(--transition);
        }
        .tab.active {
            color: var(--primary);
        }
        .tab.active:after {
            content: ''; position: absolute; bottom: -1px; left: 0;
            width: 100%; height: 2px; background: var(--primary);
        }
        .tab:hover:not(.active) {
            color: var(--dark);
        }

        /* Switch Toggle */
        .switch {
            position: relative; display: inline-block; width: 50px; height: 24px;
        }
        .switch input {
            opacity: 0; width: 0; height: 0;
        }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc; transition: var(--transition); border-radius: 34px;
        }
        .slider:before {
            position: absolute; content: ""; height: 16px; width: 16px;
            left: 4px; bottom: 4px; background-color: white;
            transition: var(--transition); border-radius: 50%;
        }
        input:checked + .slider {
            background-color: var(--success);
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .switch-label {
            margin-left: 10px; vertical-align: middle;
        }

        /* Responsive */
        @media(max-width: 768px) {
            .dashboard{grid-template-columns: 1fr;} 
            .sidebar{display: none;}
            .form-row {
                flex-direction: column; gap: 0;
            }
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        /* Success Message */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: none;
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
                    <div class="nav-item"><a href="pesanan.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Pesanan</a></div>
                    <div class="nav-item"><a href="pelanggan.php" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a></div>
                    <div class="nav-item"><a href="laporan.php" class="nav-link"><i class="fas fa-chart-pie"></i> Laporan</a></div>
                    <div class="nav-item"><a href="pengaturan.html" class="nav-link active"><i class="fas fa-cog"></i> Pengaturan</a></div>
                </div>
            </div>
            <div>
                <div class="user-profile">
    <div class="user-avatar">PT</div>
    <div>
        <h4 id="displayName"><?php echo htmlspecialchars($name); ?></h4>
        <p>Owner</p>
    </div>
</div>
                <form action="logout.php" method="post" style="padding: 0 20px;">
                    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>

        <!-- Main -->
        <div class="main-content">
            <div class="header">
                <div>
                    <h1>Pengaturan Toko</h1>
                    <p>Kelola pengaturan toko dan akun Anda</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" data-tab="umum">Umum</div>
                <div class="tab" data-tab="toko">Toko</div>
                <div class="tab" data-tab="keamanan">Keamanan</div>
                <div class="tab" data-tab="notifikasi">Notifikasi</div>
            </div>

            <!-- Success Message -->
            <div class="alert-success" id="successMessage">
                Pengaturan berhasil disimpan!
            </div>

            <!-- General Settings -->
            <div class="tab-content active" id="umum">
                <div class="settings-card">
                    <h2><i class="fas fa-user-cog"></i> Pengaturan Akun</h2>
                  <form id="accountForm" method="POST" action="pengaturan.php">
    <input type="hidden" name="update_profile" value="1">
    <div class="form-row">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" class="form-control" name="fullName" id="fullName" 
                   value="<?php echo htmlspecialchars($user_data['name'] ?? 'Pemilik Toko'); ?>" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
        </div>
    </div>
               <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="email" value="owner@tokohp.com" required>
                        </div>

                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="tel" class="form-control" id="phone" value="081234567890" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <!-- Store Settings -->
            <div class="tab-content" id="toko">
                <div class="settings-card">
                    <h2><i class="fas fa-store"></i> Pengaturan Toko</h2>
                    
                    <form id="storeForm">
                        <div class="form-group">
                            <label>Nama Toko</label>
                            <input type="text" class="form-control" id="storeName" value="Toko Handphone" required>
                        </div>

                        <div class="form-group">
                            <label>Alamat Toko</label>
                            <textarea class="form-control" id="storeAddress" rows="3" required>Jl. Raya Contoh No. 123, Kota Bandung</textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Kota</label>
                                <input type="text" class="form-control" id="storeCity" value="Bandung" required>
                            </div>
                            <div class="form-group">
                                <label>Kode Pos</label>
                                <input type="text" class="form-control" id="storePostalCode" value="40123" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Logo Toko</label>
                            <input type="file" class="form-control" id="storeLogo" accept="image/jpeg, image/png">
                            <small style="color: #777;">Ukuran maksimal 2MB, format JPG/PNG</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="tab-content" id="keamanan">
                <div class="settings-card">
                    <h2><i class="fas fa-lock"></i> Keamanan</h2>
                    
                    <form id="securityForm">
                        <div class="form-group">
                            <label>Password Saat Ini</label>
                            <input type="password" class="form-control" id="currentPassword" placeholder="Masukkan password saat ini" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" class="form-control" id="newPassword" placeholder="Masukkan password baru" required>
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Konfirmasi password baru" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Verifikasi Perubahan</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" class="form-control" id="verificationCode" style="flex: 1; max-width: 200px;" placeholder="Kode verifikasi" required>
                                <button type="button" class="btn btn-secondary" id="sendCodeBtn">Kirim Kode</button>
                            </div>
                            <small style="color: #777;">Kode verifikasi akan dikirim ke email Anda</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Perbarui Password</button>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="tab-content" id="notifikasi">
                <div class="settings-card">
                    <h2><i class="fas fa-bell"></i> Pengaturan Notifikasi</h2>
                    
                    <form id="notificationForm">
                        <div class="form-group">
                            <label>
                                <span class="switch">
                                    <input type="checkbox" id="emailNotifications" checked>
                                    <span class="slider"></span>
                                </span>
                                <span class="switch-label">Aktifkan Notifikasi Email</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <span class="switch">
                                    <input type="checkbox" id="newOrderNotifications" checked>
                                    <span class="slider"></span>
                                </span>
                                <span class="switch-label">Notifikasi Pesanan Baru</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <span class="switch">
                                    <input type="checkbox" id="paymentNotifications">
                                    <span class="slider"></span>
                                </span>
                                <span class="switch-label">Notifikasi Pembayaran</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>
                                <span class="switch">
                                    <input type="checkbox" id="stockNotifications" checked>
                                    <span class="slider"></span>
                                </span>
                                <span class="switch-label">Notifikasi Stok Habis</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab Navigation
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show the selected tab content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Form Submissions
        document.getElementById('accountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would normally send data to server
            showSuccessMessage();
        });

        document.getElementById('storeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Validate file size if uploaded
            const fileInput = document.getElementById('storeLogo');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    return;
                }
            }
            showSuccessMessage();
        });

        document.getElementById('securityForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                alert('Password baru dan konfirmasi password tidak cocok!');
                return;
            }
            
            showSuccessMessage();
        });

        document.getElementById('notificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showSuccessMessage();
        });

        // Send Verification Code
        document.getElementById('sendCodeBtn').addEventListener('click', function() {
            alert('Kode verifikasi telah dikirim ke email Anda!');
        });

        // Show success message
        function showSuccessMessage() {
            const successMessage = document.getElementById('successMessage');
            successMessage.style.display = 'block';
            
            // Hide after 3 seconds
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 3000);
        }

        // File upload preview (optional)
        document.getElementById('storeLogo').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                console.log('File selected:', fileName);
            }
        });
        document.getElementById('accountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Ambil nilai dari form
    const newName = document.getElementById('fullName').value;
    
    // Update tampilan di sidebar
    document.getElementById('displayName').textContent = newName;
    
    // Simpan ke localStorage (sementara sebelum diimplementasikan backend)
    localStorage.setItem('ownerName', newName);
    
    showSuccessMessage();
});

// Saat halaman dimuat, cek localStorage
document.addEventListener('DOMContentLoaded', function() {
    const savedName = localStorage.getItem('ownerName');
    if (savedName) {
        document.getElementById('displayName').textContent = savedName;
        document.getElementById('fullName').value = savedName;
    }
});
    </script>
</body>
</html>