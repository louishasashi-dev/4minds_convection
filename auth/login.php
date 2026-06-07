<?php
session_start();
require_once '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];
    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE email = ?");
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $role === 'admin' ? $user['id_admin'] : $user['id_pelanggan'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $role;
        $redirect = $_GET['redirect'] ?? '';
        if ($role === 'pelanggan' && $redirect === 'jahit') {
            header('Location: ../pelanggan/produk.php');
        } else {
            header('Location: ../' . $role . '/dashboard.php');
        }
        exit;
    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - 4MINDS Convection</title>
    <link rel="icon" type="image/png" href="/konveksi/assets/logo/logofavicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: #f5f0e8;
        font-family: Arial, sans-serif;
    }

    /* NAVBAR — sama persis dengan index.php */
    .navbar-logo {
        height: 50px;
        width: auto;
        display: block;
        transition: transform .3s ease;
    }

    .navbar-brand:hover .navbar-logo {
        transform: scale(1.03);
    }

    .btn-daftar {
        color: #FFD700;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 8px 14px;
        border: none;
        background: transparent;
        position: relative;
        transition: all .3s ease;
        text-decoration: none;
    }

    .btn-daftar::after {
        content: '';
        position: absolute;
        left: 14px;
        bottom: 4px;
        width: 0;
        height: 2px;
        background: #FFD700;
        transition: width .3s ease;
    }

    .btn-daftar:hover {
        color: #fff;
    }

    .btn-daftar:hover::after {
        width: calc(100% - 28px);
    }

    /* CONTENT AREA */
    .auth-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
    }

    .auth-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        width: 100%;
        max-width: 420px;
        overflow: hidden;
    }

    .auth-card-header {
        background: #3B3208;
        padding: 28px 32px 20px;
        text-align: center;
    }

    .auth-card-header img {
        height: 56px;
        object-fit: contain;
        margin-bottom: 10px;
    }

    .auth-card-header h5 {
        color: #FFD700;
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
        letter-spacing: 1px;
    }

    .auth-card-header small {
        color: #c8b97a;
        font-size: 0.78rem;
    }

    .auth-card-body {
        padding: 28px 32px 32px;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #3B3208;
        margin-bottom: 4px;
    }

    .form-control,
    .form-select {
        border: 1.5px solid #ddd;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: border-color .2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #D4AF37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15);
    }

    .btn-login {
        background: linear-gradient(135deg, #D4AF37, #B8860B);
        border: none;
        color: #2c1a0f;
        font-weight: 700;
        padding: 11px;
        border-radius: 8px;
        width: 100%;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        transition: all .3s;
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #B8860B, #8B6914);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(180, 134, 11, 0.35);
    }

    .auth-link {
        color: #D4AF37;
        font-weight: 600;
        text-decoration: none;
    }

    .auth-link:hover {
        color: #B8860B;
        text-decoration: underline;
    }

    .divider-text {
        text-align: center;
        font-size: 0.8rem;
        color: #999;
        margin: 18px 0 0;
    }

    /* FOOTER */
    footer {
        background: #2c3e50;
        color: #ecf0f1;
        padding: 18px 0;
        text-align: center;
        font-size: 0.83rem;
    }

    footer strong {
        color: #FFD700;
    }
    </style>
</head>

<body>
    <!-- AUTH CARD -->
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-header">
                <img src="/konveksi/assets/logo/logofavicon.png" alt="Logo">
                <h5>4MINDS CONVECTION</h5>
                <small>Masuk ke akun Anda</small>
            </div>
            <div class="auth-card-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3" style="font-size:0.88rem">
                    <i class="bi bi-exclamation-circle me-1"></i><?= $error ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="login.php?redirect=<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="pelanggan">Pelanggan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-login">LOGIN</button>
                </form>

                <p class="divider-text">
                    Belum punya akun? <a href="register.php" class="auth-link">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <strong>4MINDS CONVECTION</strong> &nbsp;·&nbsp; Melayani dengan sepenuh hati
    </footer>

</body>

</html>