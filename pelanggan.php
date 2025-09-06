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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Pelanggan - Toko Handphone</title>
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
        * {margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}
        body {background:var(--light);color:var(--dark)}
        .dashboard {display:grid;grid-template-columns:260px 1fr;min-height:100vh}
        .sidebar {background:var(--dark);color:#fff;display:flex;flex-direction:column;justify-content:space-between;padding:20px 0;position:sticky;top:0}
        .logo {text-align:center;padding:20px;border-bottom:1px solid rgba(255,255,255,.1)}
        .logo h2{font-weight:700;color:#fff}.logo span{color:var(--primary)}
        .nav-menu{padding:0 15px;flex:1}.nav-item{margin-bottom:8px}
        .nav-link{display:flex;align-items:center;padding:12px 15px;border-radius:var(--radius);color:rgba(255,255,255,.8);text-decoration:none;transition:var(--transition)}
        .nav-link:hover,.nav-link.active{background:rgba(255,255,255,.12);color:#fff}
        .nav-link i{margin-right:12px;font-size:18px}
        .user-profile{padding:15px 20px;display:flex;align-items:center;border-top:1px solid rgba(255,255,255,.1)}
        .user-avatar{width:42px;height:42px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;margin-right:10px}
        .logout-btn{background:var(--danger);border:none;color:#fff;padding:8px 14px;border-radius:var(--radius);cursor:pointer;width:100%;margin-top:10px;transition:var(--transition)}
        .logout-btn:hover{background:#c0392b}
        .main-content{padding:30px}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:12px}
        .header h1{font-size:26px}
        .search-bar{display:flex;align-items:center;background:#fff;border-radius:30px;box-shadow:0 2px 10px rgba(0,0,0,.05);padding:6px 15px}
        .search-bar input{border:none;outline:none;padding:6px;min-width:220px}
        .search-bar i{color:#777;margin-right:8px}
        .stats-container{display:grid;gap:20px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));margin-bottom:30px}
        .stat-card{background:#fff;border-radius:var(--radius);padding:20px;box-shadow:0 5px 15px rgba(0,0,0,.05);transition:var(--transition)}
        .stat-card:hover{transform:translateY(-5px)}
        .stat-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
        .stat-title{font-size:14px;color:#777}
        .stat-icon{width:45px;height:45px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px}
        .icon-1{background:var(--primary)}.icon-2{background:var(--warning)}.icon-3{background:var(--success)}.icon-4{background:var(--danger)}
        .stat-value{font-size:26px;font-weight:700;margin-bottom:5px}
        .stat-change{font-size:13px;font-weight:500;display:flex;align-items:center}
        .stat-change i{margin-right:4px}
        .filter-section{display:flex;gap:15px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
        .filter-item{background:#fff;padding:10px 15px;border-radius:var(--radius);box-shadow:0 2px 5px rgba(0,0,0,.05);display:flex;align-items:center;gap:10px}
        .filter-item label{font-size:14px}
        .filter-item select,.filter-item input{border:1px solid #ddd;border-radius:var(--radius);padding:8px 12px;outline:none;min-width:150px}
        .filter-btn{background:var(--primary);color:#fff;border:none;padding:8px 15px;border-radius:var(--radius);cursor:pointer}
        .btn-add{background:var(--success);color:#fff;padding:10px 20px;border-radius:var(--radius);border:none;cursor:pointer;display:inline-flex;align-items:center}
        .btn-add i{margin-right:8px}
        .customer-table{width:100%;background:#fff;border-radius:var(--radius);box-shadow:0 5px 15px rgba(0,0,0,.05);border-collapse:collapse;margin-top:10px;overflow:hidden}
        .customer-table th,.customer-table td{padding:15px;text-align:left;border-bottom:1px solid #eee;vertical-align:middle}
        .customer-table thead th{background:#f8f9fa;font-weight:600}
        .customer-table tr:hover{background:#f8f9fa}
        .customer-avatar{width:40px;height:40px;border-radius:50%;background:#eee;display:flex;align-items:center;justify-content:center;font-weight:700;color:#555}
        .status-badge{padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600}
        .status-active{background:rgba(39,174,96,.1);color:var(--success)}
        .status-inactive{background:rgba(231,76,60,.1);color:var(--danger)}
        .status-vip{background:rgba(241,196,15,.15);color:#9a7d0a}
        .action-btn{padding:7px 12px;border-radius:var(--radius);border:none;cursor:pointer;transition:var(--transition);margin-right:5px}
        .btn-view{background:var(--primary);color:#fff}.btn-edit{background:var(--warning);color:#fff}.btn-delete{background:var(--danger);color:#fff}
        .inline-form{display:none;background:#fff;padding:18px;border-radius:var(--radius);box-shadow:0 2px 10px rgba(0,0,0,.08);margin:12px 0}
        .inline-form h3{margin-bottom:10px}
        .form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px}
        .form-grid input,.form-grid select{padding:10px;border:1px solid #ddd;border-radius:10px}
        .form-actions{display:flex;gap:10px;margin-top:10px;flex-wrap:wrap}
        .btn{padding:10px 14px;border:none;border-radius:10px;cursor:pointer}
        .btn-primary{background:var(--primary);color:#fff}
        .btn-secondary{background:#ddd;color:#333}
        .btn-success{background:var(--success);color:#fff}
        .btn-danger{background:var(--danger);color:#fff}
        .pagination-wrap{display:flex;justify-content:space-between;align-items:center;margin-top:18px;gap:10px;flex-wrap:wrap}
        .page-btn{background:#ddd;color:#333;border:none;border-radius:8px;padding:8px 12px;cursor:pointer}
        .page-btn.active{background:var(--primary);color:#fff}
        /* Modal */
        .modal{position:fixed;inset:0;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;padding:20px;z-index:50}
        .modal.open{display:flex}
        .modal-card{background:#fff;border-radius:16px;max-width:520px;width:100%;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,.2)}
        .modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
        .modal-title{font-size:18px;font-weight:700}
        .close-modal{background:transparent;border:none;font-size:20px;cursor:pointer}
        @media(max-width:768px){.dashboard{grid-template-columns:1fr}.sidebar{display:none}}
    </style>
</head>
<body>
<div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="logo"><h2>Toko <span>Handphone</span></h2></div>
            <div class="nav-menu">
                <div class="nav-item"><a href="owner.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></div>
                <div class="nav-item"><a href="produkowner.php" class="nav-link"><i class="fas fa-mobile-alt"></i> Produk</a></div>
                <div class="nav-item"><a href="pesanan.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Pesanan</a></div>
                <div class="nav-item"><a href="prlanggan.php" class="nav-link active"><i class="fas fa-users"></i> Pelanggan</a></div>
                <div class="nav-item"><a href="laporan.php" class="nav-link"><i class="fas fa-chart-pie"></i> Laporan</a></div>
                <div class="nav-item"><a href="pengaturan.php" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></div>
            </div>
        </div>
        <div>
            <div class="user-profile">
                <div class="user-avatar">PT</div>
                <div><h4>Pemilik Toko</h4><p>Owner</p></div>
            </div>
            <form style="padding:0 20px;">
                <button type="button" class="logout-btn" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <div class="header">
            <div>
                <h1>Manajemen Pelanggan</h1>
                <p>Kelola data pelanggan toko Anda</p>
            </div>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama, telepon, email, atau lokasi...">
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-container" id="statsCards">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Pelanggan</span>
                    <div class="stat-icon icon-1"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <div class="stat-change" style="color:var(--success)"><i class="fas fa-arrow-up"></i> +0 bulan ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Pelanggan Aktif</span>
                    <div class="stat-icon icon-2"><i class="fas fa-user-check"></i></div>
                </div>
                <div class="stat-value" id="statActive">0</div>
                <div class="stat-change" style="color:var(--success)"><i class="fas fa-check-circle"></i> <span id="statActivePct">0%</span> aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Pembelian Ulang</span>
                    <div class="stat-icon icon-3"><i class="fas fa-redo"></i></div>
                </div>
                <div class="stat-value">64%</div>
                <div class="stat-change" style="color:var(--primary)"><i class="fas fa-chart-line"></i> +8% YoY</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Rating Pelanggan</span>
                    <div class="stat-icon icon-4"><i class="fas fa-star"></i></div>
                </div>
                <div class="stat-value">4.7</div>
                <div class="stat-change" style="color:var(--warning)"><i class="fas fa-thumbs-up"></i> Sangat Baik</div>
            </div>
        </div>

        <!-- Filter + Add -->
        <div class="filter-section">
            <div class="filter-item">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="vip">VIP</option>
                </select>
            </div>
            <div class="filter-item">
                <label for="joinFilter">Bergabung:</label>
                <select id="joinFilter">
                    <option value="all">Semua Waktu</option>
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>
            <div class="filter-item">
                <label for="locationFilter">Lokasi:</label>
                <input type="text" id="locationFilter" placeholder="Kota atau wilayah">
            </div>
            <button class="filter-btn" id="applyFilter"><i class="fas fa-filter"></i> Filter</button>
            <button class="btn-add" id="toggleAdd"><i class="fas fa-plus"></i> Tambah Pelanggan</button>
        </div>

        <!-- Inline Add Form -->
        <div class="inline-form" id="addForm">
            <h3>Tambah Pelanggan Baru</h3>
            <div class="form-grid">
                <input type="text" id="fNama" placeholder="Nama Pelanggan" required>
                <input type="text" id="fLokasi" placeholder="Lokasi (Kota/Wilayah)" required>
                <input type="text" id="fTelepon" placeholder="Nomor Telepon" required>
                <input type="email" id="fEmail" placeholder="Email" required>
                <input type="date" id="fBergabung" required>
                <input type="number" id="fBelanja" placeholder="Total Belanja (Rp)" required>
                <select id="fStatus" required>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="vip">VIP</option>
                </select>
            </div>
            <div class="form-actions">
                <button class="btn btn-success" id="btnSimpan">Simpan</button>
                <button class="btn btn-secondary" id="btnBatal">Batal</button>
            </div>
        </div>

        <!-- Table -->
        <table class="customer-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Pelanggan</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Bergabung</th>
                <th>Total Belanja</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination-wrap">
            <div id="pageInfo">Menampilkan 0-0 dari 0 pelanggan</div>
            <div id="pagination"></div>
        </div>
    </main>
</div>

<!-- Modal: View/Edit -->
<div class="modal" id="modal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-title" id="modalTitle">Detail Pelanggan</div>
            <button class="close-modal" id="closeModal"><i class="fas fa-times"></i></button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<script>
(function(){
    // ===== Utilities =====
    const rupiah = (n)=> new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n||0);
    const fmtDate = (d)=> new Date(d).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});
    const inisial = (nama)=> (nama||'').trim().split(/\s+/).map(s=>s[0]).join('').substring(0,2).toUpperCase();
    const $ = (sel,root=document)=> root.querySelector(sel);
    const $$ = (sel,root=document)=> Array.from(root.querySelectorAll(sel));
    const LS_KEY = 'pelangganDataV2';

    // ===== State =====
    let data = loadData();
    let page = 1;
    const pageSize = 8;
    let filters = { search:'', status:'all', join:'all', location:'' };

    function seedData(){
        return [
            {id:1,nama:'Andi Wijaya',lokasi:'Jakarta Selatan',telepon:'08123456789',email:'andi.wijaya@email.com',bergabung:'2023-01-12',belanja:8250000,status:'active'},
            {id:2,nama:'Budi Santoso',lokasi:'Bekasi',telepon:'08234567890',email:'budi.santoso@email.com',bergabung:'2023-03-05',belanja:5750000,status:'active'},
            {id:3,nama:'Citra Dewi',lokasi:'Depok',telepon:'08345678901',email:'citra.dewi@email.com',bergabung:'2023-04-18',belanja:12500000,status:'vip'},
            {id:4,nama:'Dian Pratama',lokasi:'Tangerang',telepon:'08456789012',email:'dian.pratama@email.com',bergabung:'2023-06-30',belanja:3200000,status:'inactive'},
            {id:5,nama:'Eka Putri',lokasi:'Jakarta Utara',telepon:'08567890123',email:'eka.putri@email.com',bergabung:'2023-09-15',belanja:6800000,status:'active'},
        ];
    }
    function loadData(){
        try{
            const s = localStorage.getItem(LS_KEY);
            if(s){ return JSON.parse(s); }
        }catch(e){}
        return seedData();
    }
    function saveData(){ localStorage.setItem(LS_KEY, JSON.stringify(data)); }

    // ===== Filtering/Searching =====
    function applyFilters(){
        const q = filters.search.toLowerCase();
        const now = new Date();
        let filtered = data.filter(d=>{
            const str = (d.nama+' '+d.telepon+' '+d.email+' '+d.lokasi).toLowerCase();
            const matchSearch = !q || str.includes(q);
            const matchStatus = filters.status==='all' || d.status===filters.status;
            const matchLoc = !filters.location || (d.lokasi||'').toLowerCase().includes(filters.location.toLowerCase());

            let matchJoin = true;
            if(filters.join!=='all'){
                const joined = new Date(d.bergabung);
                const diff = (now - joined) / (1000*60*60*24); // days
                if(filters.join==='week')  matchJoin = diff <= 7;
                if(filters.join==='month') matchJoin = diff <= 31;
                if(filters.join==='year')  matchJoin = diff <= 366;
            }
            return matchSearch && matchStatus && matchLoc && matchJoin;
        });
        return filtered.sort((a,b)=> new Date(b.bergabung) - new Date(a.bergabung));
    }

    // ===== Rendering =====
    function render(){
        const tbody = $('#tableBody');
        tbody.innerHTML = '';
        const filtered = applyFilters();
        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / pageSize));
        if(page > totalPages) page = totalPages;

        const start = (page-1)*pageSize;
        const end = Math.min(start + pageSize, total);
        const pageItems = filtered.slice(start, end);

        pageItems.forEach((c, idx)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><div class="customer-avatar">${inisial(c.nama)}</div></td>
                <td><strong>${c.nama}</strong><br><small>${c.lokasi||'-'}</small></td>
                <td>${c.telepon||'-'}</td>
                <td>${c.email||'-'}</td>
                <td>${fmtDate(c.bergabung)}</td>
                <td>${rupiah(c.belanja||0)}</td>
                <td>${statusBadge(c.status)}</td>
                <td>
                    <button class="action-btn btn-view" data-id="${c.id}" title="Lihat"><i class="fas fa-eye"></i></button>
                    <button class="action-btn btn-edit" data-id="${c.id}" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="action-btn btn-delete" data-id="${c.id}" title="Hapus"><i class="fas fa-trash"></i></button>
                </td>`;
            tbody.appendChild(tr);
        });

        // Page info & pagination
        $('#pageInfo').text(`Menampilkan ${total? (start+1):0}-${end} dari ${total} pelanggan`);
        renderPagination(totalPages);

        // Stats
        $('#statTotal').text(data.length);
        const aktif = data.filter(x=>x.status==='active' || x.status==='vip').length;
        $('#statActive').text(aktif);
        $('#statActivePct').text(data.length? Math.round(aktif/data.length*100)+'%':'0%');
    }

    function statusBadge(s){
        if(s==='active') return `<span class="status-badge status-active">Aktif</span>`;
        if(s==='inactive') return `<span class="status-badge status-inactive">Tidak Aktif</span>`;
        return `<span class="status-badge status-vip">VIP</span>`;
    }

    function renderPagination(totalPages){
        const wrap = $('#pagination');
        wrap.innerHTML = '';
        const prev = document.createElement('button');
        prev.className = 'page-btn';
        prev.textContent = 'Sebelumnya';
        prev.disabled = page===1;
        prev.onclick = ()=>{ page=Math.max(1,page-1); render(); };
        wrap.appendChild(prev);

        for(let i=1;i<=totalPages;i++){
            const b = document.createElement('button');
            b.className = 'page-btn'+(i===page?' active':'');
            b.textContent = i;
            b.onclick = ()=>{ page=i; render(); };
            wrap.appendChild(b);
        }

        const next = document.createElement('button');
        next.className = 'page-btn';
        next.textContent = 'Selanjutnya';
        next.disabled = page===totalPages;
        next.onclick = ()=>{ page=Math.min(totalPages,page+1); render(); };
        wrap.appendChild(next);
    }

    // ===== Add / Edit / View / Delete =====
    function clearAddForm(){
        $('#fNama').value='';
        $('#fLokasi').value='';
        $('#fTelepon').value='';
        $('#fEmail').value='';
        $('#fBergabung').value='';
        $('#fBelanja').value='';
        $('#fStatus').value='active';
    }

    function addCustomer(){
        const nama = $('#fNama').value.trim();
        const lokasi = $('#fLokasi').value.trim();
        const telepon = $('#fTelepon').value.trim();
        const email = $('#fEmail').value.trim();
        const bergabung = $('#fBergabung').value;
        const belanja = parseInt($('#fBelanja').value||'0',10);
        const status = $('#fStatus').value;

        if(!nama || !lokasi || !telepon || !email || !bergabung){
            alert('Lengkapi semua data wajib.');
            return;
        }
        const id = (data.length? Math.max(...data.map(d=>d.id)):0) + 1;
        data.unshift({id,nama,lokasi,telepon,email,bergabung,belanja,status});
        saveData();
        clearAddForm();
        $('#addForm').style.display='none';
        page = 1; // tampilkan di atas
        render();
    }

    function viewCustomer(c){
        $('#modalTitle').text('Detail Pelanggan');
        $('#modalContent').innerHTML = `
            <div style="display:flex;gap:12px;align-items:center;margin-bottom:10px">
                <div class="customer-avatar" style="width:50px;height:50px">${inisial(c.nama)}</div>
                <div><div style="font-weight:700">${c.nama}</div><div style="font-size:13px;color:#666">${c.lokasi||'-'}</div></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <div><strong>Telepon</strong><br>${c.telepon||'-'}</div>
                <div><strong>Email</strong><br>${c.email||'-'}</div>
                <div><strong>Bergabung</strong><br>${fmtDate(c.bergabung)}</div>
                <div><strong>Total Belanja</strong><br>${rupiah(c.belanja||0)}</div>
                <div><strong>Status</strong><br>${statusBadge(c.status)}</div>
            </div>`;
        openModal();
    }

    function editCustomer(c){
        $('#modalTitle').text('Edit Pelanggan');
        $('#modalContent').innerHTML = `
            <div class="form-grid">
                <input type="text" id="eNama" value="${esc(c.nama)}" placeholder="Nama Pelanggan">
                <input type="text" id="eLokasi" value="${esc(c.lokasi)}" placeholder="Lokasi">
                <input type="text" id="eTelepon" value="${esc(c.telepon)}" placeholder="Telepon">
                <input type="email" id="eEmail" value="${esc(c.email)}" placeholder="Email">
                <input type="date" id="eBergabung" value="${c.bergabung}">
                <input type="number" id="eBelanja" value="${c.belanja||0}" placeholder="Total Belanja">
                <select id="eStatus">
                    <option value="active" ${c.status==='active'?'selected':''}>Aktif</option>
                    <option value="inactive" ${c.status==='inactive'?'selected':''}>Tidak Aktif</option>
                    <option value="vip" ${c.status==='vip'?'selected':''}>VIP</option>
                </select>
            </div>
            <div class="form-actions" style="margin-top:14px">
                <button class="btn btn-primary" id="btnUpdate">Update</button>
                <button class="btn btn-secondary" id="btnCancel">Batal</button>
            </div>`;
        openModal();

        $('#btnCancel').onclick = closeModal;
        $('#btnUpdate').onclick = ()=>{
            c.nama = $('#eNama').value.trim();
            c.lokasi = $('#eLokasi').value.trim();
            c.telepon = $('#eTelepon').value.trim();
            c.email = $('#eEmail').value.trim();
            c.bergabung = $('#eBergabung').value;
            c.belanja = parseInt($('#eBelanja').value||'0',10);
            c.status = $('#eStatus').value;
            saveData();
            closeModal();
            render();
        };
    }

    function deleteCustomer(id){
        if(!confirm('Hapus pelanggan ini?')) return;
        data = data.filter(d=>d.id!==id);
        saveData();
        render();
    }

    function esc(s){ return String(s||'').replace(/"/g,'&quot;'); }

    // ===== Modal helpers =====
    const modal = $('#modal');
    const openModal = ()=> modal.classList.add('open');
    const closeModal = ()=> modal.classList.remove('open');
    $('#closeModal').onclick = closeModal;
    modal.addEventListener('click', e=>{ if(e.target===modal) closeModal(); });

    // ===== Events =====
    $('#logoutBtn').onclick = ()=>{ alert('Anda akan keluar dari sistem'); window.location.href='login.html'; };
    $('#toggleAdd').onclick = ()=>{ const f=$('#addForm'); f.style.display = (f.style.display==='none'||!f.style.display)?'block':'none'; };
    $('#btnBatal').onclick = ()=>{ clearAddForm(); $('#addForm').style.display='none'; };
    $('#btnSimpan').onclick = addCustomer;

    $('#applyFilter').onclick = ()=>{
        filters.status = $('#statusFilter').value;
        filters.join = $('#joinFilter').value;
        filters.location = $('#locationFilter').value.trim();
        page = 1;
        render();
    };
    $('#searchInput').addEventListener('input', e=>{
        filters.search = e.target.value;
        page = 1;
        render();
    });

    // Delegate action buttons (view/edit/delete)
    document.addEventListener('click', (e)=>{
        const btnView = e.target.closest('.btn-view');
        const btnEdit = e.target.closest('.btn-edit');
        const btnDelete = e.target.closest('.btn-delete');
        if(btnView){
            const id = +btnView.dataset.id;
            const c = data.find(x=>x.id===id);
            if(c) viewCustomer(c);
        }
        if(btnEdit){
            const id = +btnEdit.dataset.id;
            const c = data.find(x=>x.id===id);
            if(c) editCustomer(c);
        }
        if(btnDelete){
            const id = +btnDelete.dataset.id;
            deleteCustomer(id);
        }
    });

    // ===== Init =====
    render();
})();
</script>
</body>
</html>
