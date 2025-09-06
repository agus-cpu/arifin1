<?php
include 'koneksi.php';

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header('Location: kelolaproduk.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data produk sesuai id
$query = "SELECT * FROM produk WHERE id = $id LIMIT 1";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) === 0) {
    header('Location: kelolaproduk.php');
    exit;
}
$produk = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = $_POST['kategori'];
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $status = $_POST['status'];

    if (!$nama || !$kategori || !$harga || $stok < 0 || !$status) {
        $error = "Semua field wajib diisi dengan benar.";
    } else {
        $sql = "UPDATE produk SET
                nama = '$nama',
                kategori = '$kategori',
                harga = $harga,
                stok = $stok,
                status = '$status'
                WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            $success = "Data produk berhasil diperbarui.";
            // Refresh data produk setelah update
            $produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM produk WHERE id = $id"));
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
<title>Edit Produk - Lhokseumawe Mobile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="p-4">

<div class="container" style="max-width:600px;">
    <h3>Edit Produk</h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="nama" name="nama" required value="<?= htmlspecialchars($produk['nama']) ?>" />
        </div>
        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select id="kategori" name="kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="smartphone" <?= $produk['kategori'] === 'smartphone' ? 'selected' : '' ?>>Smartphone</option>
                <option value="aksesoris" <?= $produk['kategori'] === 'aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
                <option value="sparepart" <?= $produk['kategori'] === 'sparepart' ? 'selected' : '' ?>>Sparepart</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga (Rp)</label>
            <input type="number" class="form-control" id="harga" name="harga" min="0" required value="<?= (int)$produk['harga'] ?>" />
        </div>
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="number" class="form-control" id="stok" name="stok" min="0" required value="<?= (int)$produk['stok'] ?>" />
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="">-- Pilih Status --</option>
                <option value="tersedia" <?= $produk['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                <option value="hampir_habis" <?= $produk['status'] === 'hampir_habis' ? 'selected' : '' ?>>Hampir Habis</option>
                <option value="habis" <?= $produk['status'] === 'habis' ? 'selected' : '' ?>>Habis</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="kelolaproduk.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
