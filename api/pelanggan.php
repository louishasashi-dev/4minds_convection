<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'list':
        $keyword = $conn->real_escape_string($_GET['q'] ?? '');
        $where   = $keyword ? "WHERE name LIKE '%$keyword%' OR email LIKE '%$keyword%'" : '';
        $res     = $conn->query("
            SELECT p.*, 
                   (SELECT COUNT(*) FROM transaksi t WHERE t.id_pelanggan = p.id_pelanggan) as total_transaksi,
                   uk.ukuran, uk.catatan as catatan_ukuran
            FROM pelanggan p
            LEFT JOIN ukuran_pelanggan uk ON p.id_pelanggan = uk.id_pelanggan
            $where
            ORDER BY p.created_at DESC
        ");
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'create':
        $name     = $conn->real_escape_string($_POST['name']);
        $email    = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $no_hp    = $conn->real_escape_string($_POST['no_hp'] ?? '');
        $alamat   = $conn->real_escape_string($_POST['alamat'] ?? '');

        // Cek duplikat email
        $cek = $conn->query("SELECT id_pelanggan FROM pelanggan WHERE email='$email'");
        if ($cek->num_rows > 0) {
            echo json_encode(['error' => 'Email sudah digunakan']);
            break;
        }

        $conn->query("INSERT INTO pelanggan (name, email, password, no_hp, alamat)
                      VALUES ('$name', '$email', '$password', '$no_hp', '$alamat')");
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id     = intval($_POST['id']);
        $name   = $conn->real_escape_string($_POST['name']);
        $email  = $conn->real_escape_string($_POST['email']);
        $no_hp  = $conn->real_escape_string($_POST['no_hp'] ?? '');
        $alamat = $conn->real_escape_string($_POST['alamat'] ?? '');

        // Cek duplikat email (kecuali milik sendiri)
        $cek = $conn->query("SELECT id_pelanggan FROM pelanggan WHERE email='$email' AND id_pelanggan != $id");
        if ($cek->num_rows > 0) {
            echo json_encode(['error' => 'Email sudah digunakan pelanggan lain']);
            break;
        }

        $sql = "UPDATE pelanggan SET name='$name', email='$email', no_hp='$no_hp', alamat='$alamat'";
        if (!empty($_POST['password'])) {
            $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql .= ", password='$pass'";
        }
        $sql .= " WHERE id_pelanggan=$id";
        $conn->query($sql);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM pelanggan WHERE id_pelanggan=$id");
        echo json_encode(['success' => true]);
        break;

    case 'transaksi':
        $id  = intval($_GET['id']);
        $res = $conn->query("SELECT * FROM transaksi WHERE id_pelanggan=$id ORDER BY tanggal_transaksi DESC");
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}