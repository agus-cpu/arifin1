<?php
require_once 'includes/config.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = bersihkan_input($_POST['name']);
    $username = bersihkan_input($_POST['username']);
    $password = bersihkan_input($_POST['password']);
    $role = 'customer'; // Default role untuk registrasi

    // Cek username sudah ada atau belum
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        // NOTE: Dalam produksi, gunakan password_hash()
        $insert = mysqli_query($conn, "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', '$role')");
        
        if ($insert) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Registrasi gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Toko Ponsel Lhokseumawe</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h1>Registrasi Akun</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
    </div>
</body>
</html>