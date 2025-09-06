<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login dan role adalah admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}
// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];}

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Pengaturan Profil Admin (HTML Only)</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0f172a; /* slate-900 */
      --panel:#111827; /* gray-900 */
      --muted:#94a3b8; /* slate-400 */
      --txt:#e5e7eb; /* gray-200 */
      --accent:#22c55e; /* green-500 */
      --accent-2:#3b82f6; /* blue-500 */
      --danger:#ef4444; /* red-500 */
      --warn:#f59e0b; /* amber-500 */
      --shadow: 0 10px 30px rgba(0,0,0,.25);
      --radius: 18px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; background:linear-gradient(180deg,#0b1220,#0f172a 40%); color:var(--txt);
      font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Helvetica,Arial,sans-serif;
      letter-spacing:.1px;
    }
    .container{max-width:1100px;margin:40px auto;padding:0 16px}
    .app{
      background:rgba(17,24,39,.75);
      backdrop-filter:saturate(140%) blur(12px);
      border:1px solid rgba(148,163,184,.15);
      border-radius:var(--radius);
      box-shadow:var(--shadow);
      overflow:hidden;
    }
    header{
      display:flex;align-items:center;justify-content:space-between;
      padding:18px 22px;border-bottom:1px solid rgba(148,163,184,.15);
      background:linear-gradient(180deg,rgba(255,255,255,.02),transparent);
    }
    .brand{display:flex;gap:12px;align-items:center}
    .logo{
      width:42px;height:42px;border-radius:12px;display:grid;place-items:center;
      background:linear-gradient(135deg,var(--accent),#10b981);
      box-shadow:0 8px 24px rgba(34,197,94,.35);
      font-weight:800;color:#052e16;letter-spacing:.5px
    }
    .title small{display:block;color:var(--muted);font-weight:500;font-size:.8rem;margin-top:2px}
    .user-chip{display:flex;align-items:center;gap:10px;background:rgba(148,163,184,.08);padding:8px 12px;border-radius:14px}
    .user-chip img{width:28px;height:28px;border-radius:50%;object-fit:cover}
    .wrap{display:grid;grid-template-columns: 340px 1fr}
    @media (max-width: 920px){.wrap{grid-template-columns:1fr}}
    aside{padding:18px;border-right:1px solid rgba(148,163,184,.12)}
    main{padding:20px}

    .card{background:rgba(2,6,23,.55);border:1px solid rgba(148,163,184,.12);border-radius:16px;padding:18px}
    .card + .card{margin-top:16px}
    .group{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .row{display:flex;flex-direction:column;gap:8px}
    label{font-weight:600;font-size:.92rem}
    input,select,textarea{
      background:rgba(148,163,184,.08);
      border:1px solid rgba(148,163,184,.2);
      color:var(--txt);
      padding:12px 14px;border-radius:12px;outline:none;font-size:.95rem
    }
    input:focus,select:focus,textarea:focus{border-color:var(--accent-2);box-shadow:0 0 0 4px rgba(59,130,246,.15)}
    .actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:8px}
    .btn{border:none;cursor:pointer;padding:10px 14px;border-radius:12px;font-weight:700}
    .btn-primary{background:var(--accent-2);color:white}
    .btn-success{background:var(--accent);color:#052e16}
    .btn-danger{background:var(--danger);color:white}
    .btn-ghost{background:rgba(148,163,184,.12);color:var(--txt)}

    .avatar{
      width:120px;height:120px;border-radius:16px;object-fit:cover;display:block
    }
    .avatar-wrap{display:flex;align-items:center;gap:16px}

    .switch{position:relative;width:56px;height:30px;background:rgba(148,163,184,.25);border-radius:999px;cursor:pointer;border:1px solid rgba(148,163,184,.25)}
    .switch input{display:none}
    .knob{position:absolute;top:2px;left:2px;width:26px;height:26px;border-radius:50%;background:#fff;transition:all .25s}
    .switch input:checked + .knob{left:28px;background:var(--accent)}

    .hint{color:var(--muted);font-size:.86rem}

    .toast{position:fixed;right:18px;bottom:18px;background:#0b1220;border:1px solid rgba(148,163,184,.2);color:var(--txt);padding:12px 14px;border-radius:14px;box-shadow:var(--shadow);opacity:0;transform:translateY(14px);transition:all .25s}
    .toast.show{opacity:1;transform:translateY(0)}

    .section-title{font-size:1.05rem;font-weight:800;margin:0 0 12px}
    .small{font-size:.86rem;color:var(--muted)}

    .danger-zone{border:1px dashed rgba(239,68,68,.45);padding:14px;border-radius:14px}
     /* TAMBAHKAN INI UNTUK NOTIFIKASI */
    .alert {
      padding: 12px;
      margin: 0 20px 20px;
      border-radius: 8px;
      background: rgba(16, 185, 129, 0.15);
      border-left: 4px solid #10b981;
      color: white;
    }
    .alert-error {
      background: rgba(239, 68, 68, 0.15);
      border-left-color: #ef4444;
    }
  </style>
</head>
<div class="admin-container">
        <!-- Side Navigation -->
        <nav class="side-nav">
            <div class="nav-header">
                <h2>Admin <span>Panel</span></h2>
            </div>

            <div class="nav-menu">
                <div class="nav-item">
                    <a href="admin.php" class="nav-link active" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolauser.php" class="nav-link" data-section="users">
                        <i class="fas fa-users-cog"></i>
                        <span>Kelola User</span>
                        <span class="nav-badge" id="user-badge">3</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="kelolatoko.php" class="nav-link" data-section="store">
                        <i class="fas fa-store"></i>
                        <span>Kelola Toko</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuaproduk.php" class="nav-link" data-section="products">
                        <i class="fas fa-boxes"></i>
                        <span>Semua Produk</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="semuapesanan.php" class="nav-link" data-section="orders">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Semua Pesanan</span>
                        <span class="nav-badge" id="order-badge">5</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="laporanadmin.php" class="nav-link" data-section="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                </div>
            </div>

            <div class="user-panel">
                <div class="user-info">
                    <div class="user-avatar" id="user-avatar">AD</div>
                    <div>
                        <div class="user-name" id="admin-name">Admin Dashboard</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                <a href="pengaturanadmin.php" class="settings-link" id="settings-btn">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan</span>
                </a>
                <a href="logout.php" class="logout-link" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="alert-success" id="successMessage">
                Perubahan berhasil disimpan!
            </div>

            <!-- Dashboard Section -->
            <div class="page-content active" id="dashboard-section">
                <div class="page-header">
                    <div class="page-title">
                        <h1>Dashboard</h1>
                        <p>Selamat datang kembali, Admin! Berikut ringkasan aktivitas toko Anda.</p>
                    </div>
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari..." id="search-input">
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="card">
                        <h3 style="color: var(--dark); margin-bottom: 15px;">Total Pendapatan</h3>
                        <p style="font-size: 1.8rem; font-weight: bold; color: var(--primary);">Rp <span id="total-income">12.450.000</span></p>
                        <p style="color: var(--success); font-size: 0.9rem;">
                            <i class="fas fa-arrow-up"></i> <span id="income-change">15%</span> dari bulan lalu
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="color: var(--dark); margin-bottom: 15px;">Pesanan Baru</h3>
                        <p style="font-size: 1.8rem; font-weight: bold; color: var(--primary);"><span id="new-orders">18</span></p>
                        <p style="color: var(--warning); font-size: 0.9rem;">
                            <i class="fas fa-arrow-up"></i> <span id="orders-today">5</span> pesanan hari ini
                        </p>
                    </div>
                    
                    <div class="card">
                        <h3 style="color: var(--dark); margin-bottom: 15px;">Total Produk</h3>
                        <p style="font-size: 1.8rem; font-weight: bold; color: var(--primary);"><span id="total-products">42</span></p>
                        <p style="color: var(--danger); font-size: 0.9rem;">
                            <i class="fas fa-exclamation-circle"></i> <span id="low-stock">3</span> stok hampir habis
                        </p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <h2 style="color: var(--dark); margin-bottom: 20px;">Aktivitas Terkini</h2>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-details">
                            <p>Pesanan baru #ORD-<span class="order-id">1256</span> dari <span class="customer-name">Budi Santoso</span></p>
                            <p class="activity-time">10 menit yang lalu</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-details">
                            <p>Pelanggan baru: <span class="customer-name">Ani Fitriani</span></p>
                            <p class="activity-time">1 jam yang lalu</p>
                        </div>
                    </div>
                    <div class="activity-item" style="border-bottom: none;">
                        <div class="activity-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="activity-details">
                            <p>Stok produk <span class="product-name">Samsung Galaxy S23</span> hampir habis</p>
                            <p class="activity-time">3 jam yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Modal -->
            <div class="modal" id="settings-modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <h2><i class="fas fa-cog"></i> Pengaturan Admin</h2>
                    
                    <form id="admin-settings-form">
                        <div class="form-group">
                            <label>Nama Admin</label>
                            <input type="text" class="form-control" id="admin-name-input" value="Admin Dashboard" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="admin-email" value="admin@tokohp.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Avatar Initials</label>
                            <input type="text" class="form-control" id="admin-avatar" maxlength="2" value="AD" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

 <script>
    // DOM Elements
    const navLinks = document.querySelectorAll('.nav-link');
    const successMessage = document.getElementById('successMessage');
    const adminName = document.getElementById('admin-name');
    const userAvatar = document.getElementById('user-avatar');
    const logoutBtn = document.getElementById('logout-btn');
    
    // Navigation System - Biarkan link bekerja normal
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Remove active class from all links
            navLinks.forEach(nav => nav.classList.remove('active'));
            
            // Add active to clicked link
            this.classList.add('active');
            
            // Simulate page change (in real app, this would be server-side)
            document.querySelector('.page-title h1').textContent = 
                this.querySelector('span').textContent;
        });
    });
    
    // Logout Button
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if(confirm('Apakah Anda yakin ingin logout?')) {
            // In a real app, this would redirect to logout page
            window.location.href = 'logout.php';
        }
    });
    
    // Search Functionality
    document.getElementById('search-input').addEventListener('keyup', function(e) {
        if(e.key === 'Enter') {
            alert(`Mencari: ${this.value}`);
            this.value = '';
        }
    });
    
    // Helper function to show success message
    function showSuccessMessage(message) {
        successMessage.textContent = message || 'Perubahan berhasil disimpan!';
        successMessage.style.display = 'block';
        
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);
    }
    
    // Simulate dynamic data updates
    setInterval(() => {
        // Randomize some stats for demo purposes
        document.getElementById('new-orders').textContent = 
            Math.floor(15 + Math.random() * 10);
        document.getElementById('orders-today').textContent = 
            Math.floor(3 + Math.random() * 5);
    }, 5000);
</script>
</body>
</html<main>
          <?php if (isset($success)): ?>
            <div class="alert"><?php echo $success; ?></div>
          <?php elseif (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
          <?php endif; ?>

          <div class="card">
            <div class="section-title">Profil</div>
            <form method="POST" action="">
              <input type="hidden" name="update_profile" value="1">
              
              <div class="avatar-wrap">
                <img class="avatar" alt="Avatar" 
                  src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name'] ?? 'Admin'); ?>&background=3b82f6&color=fff"/>
              </div>
              
              <div class="group">
                <div class="row">
                  <label for="nama">Nama Lengkap</label>
                  <input id="nama" name="nama" type="text" 
                    value="<?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?>" required />
                </div>
                <div class="row">
                  <label for="username">Username</label>
                  <input id="username" type="text" 
                    value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>" disabled />
                </div>
              </div>
              
              <div class="group" style="margin-top:12px">
                <div class="row">
                  <label for="email">Email</label>
                  <input id="email" name="email" type="email" 
                    value="<?php echo htmlspecialchars($user_data['email'] ?? 'admin@example.com'); ?>" required />
                </div>
                <div class="row">
                  <label for="telepon">Telepon</label>
                  <input id="telepon" name="telepon" type="tel" 
                    value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" />
                </div>
              </div>
              
              <div class="actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              </div>
            </form>
          </div>
      </div>
    </div>
  </div>

  <div id="toast" class="toast">Tersimpan!</div>

  <script>
    const DEFAULT_PROFILE = {
      nama: "Admin",
      username: "admin",
      email: "admin@contoh.com",
      telepon: "",
      avatar: "",
      theme: "dark",
      password: "admin123" // DEMO
    };

    const els = {
      nama: document.getElementById('nama'),
      username: document.getElementById('username'),
      email: document.getElementById('email'),
      telepon: document.getElementById('telepon'),
      avatar: document.getElementById('avatar'),
      avatarInput: document.getElementById('avatarInput'),
      chipName: document.getElementById('chipName'),
      chipAvatar: document.getElementById('chipAvatar'),
      darkToggle: document.getElementById('darkToggle'),
      toast: document.getElementById('toast'),
      btnSave: document.getElementById('btnSave'),
      btnCopy: document.getElementById('btnCopy'),
      btnChangePass: document.getElementById('btnChangePass'),
      oldPass: document.getElementById('oldPass'),
      newPass: document.getElementById('newPass'),
      confirmPass: document.getElementById('confirmPass'),
      btnExport: document.getElementById('btnExport'),
      importFile: document.getElementById('importFile'),
      btnReset: document.getElementById('btnReset')
    };

    function getProfile(){
      const raw = localStorage.getItem('admin_profile');
      if(!raw){
        localStorage.setItem('admin_profile', JSON.stringify(DEFAULT_PROFILE));
        return {...DEFAULT_PROFILE};
      }
      try { return JSON.parse(raw); } catch(e){
        console.warn('Corrupt profile. Reset to default.');
        localStorage.setItem('admin_profile', JSON.stringify(DEFAULT_PROFILE));
        return {...DEFAULT_PROFILE};
      }
    }
    function setProfile(data){
      localStorage.setItem('admin_profile', JSON.stringify(data));
    }

    function showToast(msg='Tersimpan!'){
      els.toast.textContent = msg;
      els.toast.classList.add('show');
      setTimeout(()=>els.toast.classList.remove('show'), 1600);
    }

    function applyTheme(theme){
      if(theme === 'dark'){
        document.documentElement.style.colorScheme='dark';
      } else {
        document.documentElement.style.colorScheme='light';
      }
    }

    function render(profile){
      els.nama.value = profile.nama || '';
      els.username.value = profile.username || '';
      els.email.value = profile.email || '';
      els.telepon.value = profile.telepon || '';
      const avatarSrc = profile.avatar || 'data:image/svg+xml;base64,'+btoa(`<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="100%" height="100%" rx="16" fill="%23e5e7eb"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="36" font-family="Arial" fill="%234b5563">${(profile.nama||'A').slice(0,1).toUpperCase()}</text></svg>`);
      els.avatar.src = avatarSrc;
      els.chipAvatar.src = avatarSrc;
      els.chipName.textContent = profile.nama || 'Admin';
      els.darkToggle.checked = (profile.theme||'dark') === 'dark';
      applyTheme(profile.theme||'dark');
    }

    function collect(){
      return {
        ...getProfile(),
        nama: els.nama.value.trim(),
        username: els.username.value.trim(),
        email: els.email.value.trim(),
        telepon: els.telepon.value.trim(),
        theme: els.darkToggle.checked ? 'dark' : 'light'
      };
    }

    function save(){
      const data = collect();
      setProfile(data);
      render(data);
      showToast('Profil disimpan.');
    }

    function debounce(fn,ms){
      let t; return (...args)=>{clearTimeout(t); t=setTimeout(()=>fn(...args), ms)}
    }

    // Auto-save NAMA saat mengetik (langsung tersimpan)
    const autoSaveName = debounce(()=>{
      const p = getProfile();
      p.nama = els.nama.value.trim();
      setProfile(p);
      render(p);
    }, 300);

    // Avatar file -> base64
    function fileToBase64(file){
      return new Promise((resolve,reject)=>{
        const reader = new FileReader();
        reader.onload = ()=>resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
      });
    }

    // Events
    window.addEventListener('DOMContentLoaded', ()=>{
      render(getProfile());

      els.darkToggle.addEventListener('change', ()=>{
        const p = collect(); setProfile(p); applyTheme(p.theme); showToast('Tema diperbarui.');
      });

      els.avatarInput.addEventListener('change', async (e)=>{
        const file = e.target.files?.[0];
        if(!file) return;
        if(file.size > 1.2*1024*1024){
          showToast('Ukuran foto terlalu besar (>1.2MB)'); return;
        }
        const base64 = await fileToBase64(file);
        const p = getProfile(); p.avatar = base64; setProfile(p); render(p); showToast('Foto diperbarui.');
      });

      els.nama.addEventListener('input', autoSaveName);

      els.btnSave.addEventListener('click', save);

      els.btnCopy.addEventListener('click', async ()=>{
        const p = collect();
        const text = `Nama: ${p.nama}\nUsername: ${p.username}\nEmail: ${p.email}\nTelepon: ${p.telepon}`;
        try{ await navigator.clipboard.writeText(text); showToast('Disalin ke clipboard.'); }
        catch{ showToast('Gagal menyalin.'); }
      });

      els.btnChangePass.addEventListener('click', ()=>{
        const p = getProfile();
        const oldOk = els.oldPass.value === p.password;
        if(!oldOk){ showToast('Kata sandi saat ini salah.'); return; }
        if(els.newPass.value.length < 6){ showToast('Minimal 6 karakter.'); return; }
        if(els.newPass.value !== els.confirmPass.value){ showToast('Konfirmasi tidak cocok.'); return; }
        p.password = els.newPass.value; setProfile(p);
        els.oldPass.value = els.newPass.value = els.confirmPass.value = '';
        showToast('Kata sandi disimpan.');
      });

      // Export JSON
      els.btnExport.addEventListener('click', ()=>{
        const blob = new Blob([localStorage.getItem('admin_profile')||'{}'], {type:'application/json'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'profil_admin.json';
        a.click();
        URL.revokeObjectURL(a.href);
      });

      // Import JSON
      els.importFile.addEventListener('change', async (e)=>{
        const file = e.target.files?.[0]; if(!file) return;
        try{
          const text = await file.text();
          const data = JSON.parse(text);
          // Sanitasi sederhana
          const merged = { ...DEFAULT_PROFILE, ...data };
          setProfile(merged); render(merged); showToast('Profil di-import.');
        }catch{ showToast('File tidak valid.'); }
        e.target.value = '';
      });

      // Reset
      els.btnReset.addEventListener('click', ()=>{
        if(confirm('Yakin reset semua pengaturan?')){
          setProfile(DEFAULT_PROFILE); render(DEFAULT_PROFILE); showToast('Sudah direset.');
        }
      });
    });
  </script>
</body>
</html>