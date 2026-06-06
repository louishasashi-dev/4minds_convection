<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role']; // 'admin' atau 'pelanggan'

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
    <title>Login - Sistem Konveksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center" style="min-height:100vh">
    <div class="container" style="max-width:420px">
        <div class="card shadow mt-5">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">🧵 Sistem Konveksi</h4>
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php?redirect=<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="pelanggan">Pelanggan</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar</a></p>
            </div>
        </div>
    </div>
</body>

</html>