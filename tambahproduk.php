<?php
include 'koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = $_POST['kategori'];
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $status = $_POST['status'];

    // Validasi sederhana
    if (!$nama || !$kategori || !$harga || $stok < 0 || !$status) {
        $error = "Semua field wajib diisi dengan benar.";
    } else {
        $sql = "INSERT INTO produk (nama, kategori, harga, stok, status) VALUES 
                ('$nama', '$kategori', $harga, $stok, '$status')";
        if (mysqli_query($conn, $sql)) {
            $success = "Produk berhasil ditambahkan.";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Tambah Produk - Lhokseumawe Mobile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">

<div class="container" style="max-width:600px;">
    <h3>Tambah Produk Baru</h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="nama" name="nama" required />
        </div>
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select id="kategori" name="kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="smartphone">Smartphone</option>
                <option value="aksesoris">Aksesoris</option>
                <option value="sparepart">Sparepart</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga (Rp)</label>
            <input type="number" class="form-control" id="harga" name="harga" min="0" required />
        </div>
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" min="0" value="0" required />
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="tersedia">Tersedia</option>
                <option value="hampir_habis">Hampir Habis</option>
                <option value="habis">Habis</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Produk</button>
        <a href="kelolaproduk.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
