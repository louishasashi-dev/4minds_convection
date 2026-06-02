<?php
session_start();
require_once '../config/db.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $alamat   = trim($_POST['alamat']);
    $no_hp    = trim($_POST['no_hp']);

    // Cek email duplikat
    $cek = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE email = ?");
    $cek->bind_param("s", $email);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        $error = "Email sudah terdaftar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pelanggan (name, email, password, alamat, no_hp) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $name, $email, $password, $alamat, $no_hp);
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Terjadi kesalahan, coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - Sistem Konveksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center" style="min-height:100vh">
    <div class="container" style="max-width:480px">
        <div class="card shadow mt-4">
            <div class="card-body p-4">
                <h4 class="text-center mb-4">🧵 Daftar Pelanggan</h4>
                <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="name" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control"></textarea>
                    </div>
                    <div class="mb-3"><label>No HP</label><input type="text" name="no_hp" class="form-control"></div>
                    <button type="submit" class="btn btn-success w-100">Daftar</button>
                </form>
                <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>