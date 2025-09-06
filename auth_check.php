<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Jika perlu pengecekan role khusus
/*
if ($_SESSION['role'] !== 'customer') {
    header("Location: ../unauthorized.php");
    exit();
}
*/
?>