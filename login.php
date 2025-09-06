<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Ambil user dari tabel "users"
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Cek password plain text
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } elseif ($user['role'] === 'owner') {
                header("Location: owner.php");
            } else {
                header("Location: customer.php");
            }
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.location='login.html';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location='login.html';</script>";
    }
} else {
    header("Location: login.html");
    exit;
}
?>
