<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'list':
        // Admin bisa lihat semua, pelanggan hanya miliknya
        if ($_SESSION['role'] === 'admin') {
            $sql = "SELECT uk.*, p.name as nama_pelanggan FROM ukuran_pelanggan uk
                    JOIN pelanggan p ON uk.id_pelanggan = p.id_pelanggan
                    ORDER BY p.name, uk.ukuran";
        } else {
            $id = intval($_SESSION['user_id']);
            $sql = "SELECT * FROM ukuran_pelanggan WHERE id_pelanggan = $id ORDER BY ukuran";
        }
        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'save':
        // Pelanggan simpan/update ukuran tubuhnya
        $id_pelanggan = ($_SESSION['role'] === 'admin')
            ? intval($_POST['id_pelanggan'])
            : intval($_SESSION['user_id']);
        $ukuran  = $conn->real_escape_string(trim($_POST['ukuran']));
        $catatan = $conn->real_escape_string(trim($_POST['catatan'] ?? ''));

        if (!$ukuran) { echo json_encode(['error' => 'Ukuran tidak boleh kosong']); break; }

        // Cek apakah sudah ada untuk pelanggan ini
        $cek = $conn->query("SELECT id_ukuran FROM ukuran_pelanggan WHERE id_pelanggan=$id_pelanggan")->fetch_assoc();
        if ($cek) {
            $conn->query("UPDATE ukuran_pelanggan SET ukuran='$ukuran', catatan='$catatan' WHERE id_pelanggan=$id_pelanggan");
        } else {
            $conn->query("INSERT INTO ukuran_pelanggan (id_pelanggan, ukuran, catatan) VALUES ($id_pelanggan, '$ukuran', '$catatan')");
        }
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id = intval($_POST['id_ukuran']);
        $conn->query("DELETE FROM ukuran_pelanggan WHERE id_ukuran=$id");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}