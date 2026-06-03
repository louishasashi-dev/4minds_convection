<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Cek apakah halaman ini butuh login (admin & pelanggan area)
$path = $_SERVER['PHP_SELF'];
$butuh_login = str_contains($path, '/admin/') || str_contains($path, '/pelanggan/');

if ($butuh_login && !isset($_SESSION['user_id'])) {
    header('Location: /konveksi/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Konveksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .sidebar {
        min-height: 100vh;
        background: #2c3e50;
    }

    .sidebar a {
        color: #ecf0f1;
        text-decoration: none;
        display: block;
        padding: 10px 20px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #1abc9c;
        color: #fff;
    }

    .sidebar .brand {
        padding: 20px;
        font-size: 1.2rem;
        font-weight: bold;
        color: #fff;
        border-bottom: 1px solid #34495e;
    }

    .main-content {
        padding: 20px;
    }

    .topbar {
        background: #fff;
        padding: 10px 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <div class="d-flex">