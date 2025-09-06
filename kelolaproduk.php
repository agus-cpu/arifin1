<?php 
session_start();
require_once 'koneksi.php';

// =========== PROSES HAPUS ===========
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM produk WHERE id_produk = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Produk berhasil dihapus";
        } else {
            $_SESSION['error'] = "Gagal menghapus produk: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: kelolaproduk.php");
    exit;
}

// =========== PROSES SIMPAN (TAMBAH/EDIT) ===========
$error = '';
$success = '';
$editMode = false;
$editData = null;

if (isset($_POST['simpan'])) {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $kategori = $_POST['kategori'];
    // Bersihkan input harga dari format titik ribuan
    $harga = intval(str_replace('.', '', $_POST['harga']));
    $stok = intval($_POST['stok']);
    $status = $_POST['status'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($nama) || empty($kategori) || $harga <= 0 || $stok < 0 || empty($status)) {
        $error = "Semua field wajib diisi dengan benar!";
    } else {
        if ($id > 0) {
            // Edit produk
            $stmt = $conn->prepare("UPDATE produk SET nama_produk=?, kategori=?, harga=?, stok=?, status_stok=? WHERE id_produk=?");
            $stmt->bind_param("ssiisi", $nama, $kategori, $harga, $stok, $status, $id);
        } else {
            // Tambah produk baru
            $stmt = $conn->prepare("INSERT INTO produk (nama_produk, kategori, harga, stok, status_stok) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiis", $nama, $kategori, $harga, $stok, $status);
        }

        if ($stmt->execute()) {
            $success = $id > 0 ? "Produk berhasil diperbarui" : "Produk berhasil ditambahkan";
            if ($id == 0) {
                // Reset form setelah tambah
                $_POST = array();
            }
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// =========== PROSES EDIT ===========
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $editMode = true;
            $editData = $result->fetch_assoc();
        } else {
            $_SESSION['error'] = "Produk tidak ditemukan";
            header("Location: kelolaproduk.php");
            exit;
        }
        $stmt->close();
    }
}

// =========== AMBIL DATA PRODUK ===========
// Filter cari, kategori, status menggunakan GET
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT * FROM produk WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND nama_produk LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}
if (!empty($kategori_filter)) {
    $query .= " AND kategori = ?";
    $params[] = $kategori_filter;
    $types .= "s";
}
if (!empty($status_filter)) {
    $query .= " AND status_stok = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$query .= " ORDER BY id_produk DESC";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$produk = [];
while ($row = $result->fetch_assoc()) {
    $produk[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kelola Produk - Lhokseumawe Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        :root {
            --primary: #1f3b57;
            --secondary: #ffb300;
            --light: #f4f6f9;
        }
        body { background-color: var(--light); font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            height: 100vh; background-color: var(--primary); color: white; 
            position: fixed; width: 240px; padding-top: 20px;
        }
        .sidebar a {
            color: white; display: block; padding: 12px 20px; 
            text-decoration: none; transition: 0.3s;
        }
        .sidebar a:hover { background-color: var(--secondary); color: var(--primary); }
        .sidebar .active { background-color: var(--secondary); color: var(--primary); }
        .content { margin-left: 240px; padding: 20px; }
        .badge { font-size: 0.9rem; }
        .table-responsive { overflow-x: auto; }
        .form-container { max-width: 700px; margin: 0 auto; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .content { margin-left: 0; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center mb-4">📱 Lhokseumawe Mobile</h4>
    <a href="owner.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="kelolaproduk.php" class="active"><i class="fas fa-box"></i> Kelola Produk</a>
    <a href="laporanpenjualan.php"><i class="fas fa-chart-line"></i> Laporan Penjualan</a>
    <a href="promo.php"><i class="fas fa-tags"></i> Promo</a>
    <a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a>
    <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<div class="content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box me-2"></i> Kelola Produk</h2>
        <?php if ($editMode): ?>
            <a href="kelolaproduk.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        <?php else: ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#produkModal">
                <i class="fas fa-plus me-1"></i> Tambah Produk
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!$editMode): ?>
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="kategori" class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="smartphone" <?= $kategori_filter == 'smartphone' ? 'selected' : '' ?>>Smartphone</option>
                                <option value="aksesoris" <?= $kategori_filter == 'aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
                                <option value="sparepart" <?= $kategori_filter == 'sparepart' ? 'selected' : '' ?>>Sparepart</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="tersedia" <?= $status_filter == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                <option value="hampir_habis" <?= $status_filter == 'hampir_habis' ? 'selected' : '' ?>>Hampir Habis</option>
                                <option value="habis" <?= $status_filter == 'habis' ? 'selected' : '' ?>>Habis</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Produk -->
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produk)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Tidak ada data produk</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($produk as $index => $item): ?>
                                <?php
                                    if ($item['stok'] > 10) {
                                        $status_class = 'bg-success';
                                        $status_text = 'Tersedia';
                                    } elseif ($item['stok'] > 0) {
                                        $status_class = 'bg-warning text-dark';
                                        $status_text = 'Hampir Habis';
                                    } else {
                                        $status_class = 'bg-danger';
                                        $status_text = 'Habis';
                                    }
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                    <td><?= ucfirst($item['kategori']) ?></td>
                                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                    <td><?= $item['stok'] ?></td>
                                    <td><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                    <td>
                                        <a href="kelolaproduk.php?edit=<?= $item['id_produk'] ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="kelolaproduk.php?hapus=<?= $item['id_produk'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Form Edit Produk -->
        <div class="card form-container">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Edit Produk</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= $editData['id_produk'] ?>">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($editData['nama_produk']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="smartphone" <?= $editData['kategori'] == 'smartphone' ? 'selected' : '' ?>>Smartphone</option>
                            <option value="aksesoris" <?= $editData['kategori'] == 'aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
                            <option value="sparepart" <?= $editData['kategori'] == 'sparepart' ? 'selected' : '' ?>>Sparepart</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga (Rp)</label>
                        <input type="text" class="form-control" id="harga" name="harga" value="<?= number_format($editData['harga'], 0, ',', '.') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" value="<?= $editData['stok'] ?>" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="tersedia" <?= $editData['status_stok'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="hampir_habis" <?= $editData['status_stok'] == 'hampir_habis' ? 'selected' : '' ?>>Hampir Habis</option>
                            <option value="habis" <?= $editData['status_stok'] == 'habis' ? 'selected' : '' ?>>Habis</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" name="simpan" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                        <a href="kelolaproduk.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="produkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i> Tambah Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="modal_nama" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="modal_nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_kategori" class="form-label">Kategori</label>
                        <select class="form-select" id="modal_kategori" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="smartphone">Smartphone</option>
                            <option value="aksesoris">Aksesoris</option>
                            <option value="sparepart">Sparepart</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_harga" class="form-label">Harga (Rp)</label>
                        <input type="text" class="form-control" id="modal_harga" name="harga" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="modal_stok" name="stok" min="0" value="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_status" class="form-label">Status</label>
                        <select class="form-select" id="modal_status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="tersedia">Tersedia</option>
                            <option value="hampir_habis">Hampir Habis</option>
                            <option value="habis">Habis</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" name="simpan" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JS Bootstrap & JQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Format harga input (tambah dan edit)
    $('#harga, #modal_harga').on('keyup', function() {
        var value = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(formatRupiah(value));
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        var number_string = angka.toString();
        var sisa = number_string.length % 3;
        var rupiah = number_string.substr(0, sisa);
        var ribuan = number_string.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    // Auto close alert
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});
</script>

</body>
</html>

<?php
$conn->close();
?>
