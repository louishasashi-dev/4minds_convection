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
        $where   = $keyword ? "WHERE jenis LIKE '%$keyword%' OR ukuran LIKE '%$keyword%'" : '';
        $res     = $conn->query("SELECT * FROM ukuran_model $where ORDER BY jenis, ukuran");
        $data    = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'create':
        $jenis     = $conn->real_escape_string($_POST['jenis']);
        $ukuran    = $conn->real_escape_string($_POST['ukuran']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');

        // Cek duplikat
        $cek = $conn->query("SELECT id_ukuran_model FROM ukuran_model WHERE jenis='$jenis' AND ukuran='$ukuran'");
        if ($cek->num_rows > 0) {
            echo json_encode(['error' => 'Kombinasi jenis dan ukuran sudah ada']);
            break;
        }

        $conn->query("INSERT INTO ukuran_model (jenis, ukuran, deskripsi) VALUES ('$jenis','$ukuran','$deskripsi')");
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id        = intval($_POST['id']);
        $jenis     = $conn->real_escape_string($_POST['jenis']);
        $ukuran    = $conn->real_escape_string($_POST['ukuran']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');

        // Cek duplikat kecuali milik sendiri
        $cek = $conn->query("SELECT id_ukuran_model FROM ukuran_model WHERE jenis='$jenis' AND ukuran='$ukuran' AND id_ukuran_model != $id");
        if ($cek->num_rows > 0) {
            echo json_encode(['error' => 'Kombinasi jenis dan ukuran sudah ada']);
            break;
        }

        $conn->query("UPDATE ukuran_model SET jenis='$jenis', ukuran='$ukuran', deskripsi='$deskripsi' WHERE id_ukuran_model=$id");
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = intval($_POST['id']);
        // Cek apakah dipakai produk
        $cek = $conn->query("SELECT id_produk FROM produk WHERE id_ukuran_model=$id");
        if ($cek->num_rows > 0) {
            echo json_encode(['error' => 'Ukuran ini masih digunakan oleh ' . $cek->num_rows . ' produk, tidak bisa dihapus']);
            break;
        }
        $conn->query("DELETE FROM ukuran_model WHERE id_ukuran_model=$id");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}