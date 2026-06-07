<?php
if (session_status() === PHP_SESSION_NONE) session_start();
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    /* ── GLOBAL ─────────────────────────────────── */
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f0f2f5;
        color: #1a1a2e;
        margin: 0;
    }

    /* ── SIDEBAR ────────────────────────────────── */
    .sidebar {
        min-height: 100vh;
        background: #2c3e50;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar .brand {
        padding: 22px 20px 18px;
        font-size: 1.15rem;
        font-weight: 700;
        color: #D4AF37;
        border-bottom: 1px solid #34495e;
        letter-spacing: 0.5px;
    }

    .sidebar .nav-section {
        padding: 12px 16px 4px;
        font-size: .68rem;
        font-weight: 600;
        color: #7f8c8d;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .sidebar a {
        color: #bdc3c7;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 20px;
        font-size: .875rem;
        font-weight: 500;
        transition: all .2s ease;
        border-left: 3px solid transparent;
    }

    .sidebar a:hover {
        background: rgba(212, 175, 55, .1);
        color: #D4AF37;
        border-left-color: #D4AF37;
    }

    .sidebar a.active {
        background: rgba(212, 175, 55, .15);
        color: #D4AF37;
        border-left-color: #D4AF37;
        font-weight: 600;
    }

    .sidebar a i {
        font-size: 1rem;
        width: 18px;
        text-align: center;
    }

    /* ── TOPBAR ─────────────────────────────────── */
    .topbar {
        background: #ffffff;
        padding: 14px 24px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        font-size: .95rem;
        color: #2c3e50;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .topbar span:last-child {
        font-size: .85rem;
        color: #7f8c8d;
        font-weight: 500;
    }

    /* ── MAIN CONTENT ───────────────────────────── */
    .main-content {
        padding: 24px;
    }

    /* ── CARDS ──────────────────────────────────── */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
    }

    .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
        padding: 14px 20px;
    }

    /* ── TABLES ─────────────────────────────────── */
    .table thead.table-dark th {
        background: #2c3e50;
        color: #D4AF37;
        font-weight: 600;
        font-size: .8rem;
        letter-spacing: .5px;
        text-transform: uppercase;
        border: none;
        padding: 12px 14px;
    }

    .table tbody tr {
        transition: background .15s;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .table td {
        vertical-align: middle;
        font-size: .875rem;
        padding: 10px 14px;
        border-color: #f0f2f5;
    }

    /* ── BUTTONS ────────────────────────────────── */
    .btn-primary {
        background: #2c3e50;
        border-color: #2c3e50;
    }

    .btn-primary:hover {
        background: #1a252f;
        border-color: #1a252f;
    }

    .btn-gold {
        background: #D4AF37;
        border: none;
        color: #2c1a0f;
        font-weight: 600;
    }

    .btn-gold:hover {
        background: #B8860B;
        color: #fff;
    }

    /* ── BADGES ─────────────────────────────────── */
    .badge {
        font-weight: 500;
        font-size: .75rem;
        padding: 5px 10px;
        border-radius: 20px;
    }

    /* ── FORMS ──────────────────────────────────── */
    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: .875rem;
        padding: 8px 12px;
        transition: border-color .2s, box-shadow .2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #D4AF37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, .15);
    }

    .form-label {
        font-weight: 500;
        font-size: .85rem;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    /* ── MODALS ─────────────────────────────────── */
    .modal-header {
        background: #2c3e50;
        color: #D4AF37;
        border-radius: 12px 12px 0 0;
        padding: 16px 20px;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, .15);
    }

    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 14px 20px;
    }

    /* ── ALERTS ─────────────────────────────────── */
    .alert {
        border-radius: 10px;
        border: none;
        font-size: .875rem;
    }

    .alert-info {
        background: #e8f4fd;
        color: #1a6fa3;
    }

    .alert-success {
        background: #e8f8f0;
        color: #1a7a45;
    }

    .alert-warning {
        background: #fef9e7;
        color: #9a6f00;
    }

    .alert-danger {
        background: #fdecea;
        color: #c0392b;
    }

    /* ── PAGE TITLE ─────────────────────────────── */
    .page-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    /* ── SCROLLBAR ──────────────────────────────── */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: #34495e;
        border-radius: 4px;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="icon" type="image/png" href="/konveksi/assets/logo/logofavicon.png">
</head>

<body>
    <div class="d-flex">