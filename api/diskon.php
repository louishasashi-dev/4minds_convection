<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $res = $conn->query("SELECT d.*, a.name as nama_admin FROM diskon d JOIN admin a ON d.id_admin = a.id_admin ORDER BY d.created_at DESC");
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'create':
        $id_admin    = $_SESSION['user_id'];
        $nama        = $conn->real_escape_string($_POST['nama_diskon']);
        $jenis       = $conn->real_escape_string($_POST['jenis_diskon']);
        $syarat      = $conn->real_escape_string($_POST['syarat'] ?? '');
        $persentase  = floatval($_POST['persentase']);
        $conn->query("INSERT INTO diskon (id_admin, nama_diskon, jenis_diskon, syarat, persentase) VALUES ($id_admin,'$nama','$jenis','$syarat',$persentase)");
        echo json_encode(['success' => true]);
        break;

    case 'update':
        $id         = intval($_POST['id_diskon']);
        $nama       = $conn->real_escape_string($_POST['nama_diskon']);
        $persentase = floatval($_POST['persentase']);
        $syarat     = $conn->real_escape_string($_POST['syarat'] ?? '');
        $conn->query("UPDATE diskon SET nama_diskon='$nama', persentase=$persentase, syarat='$syarat' WHERE id_diskon=$id");
        echo json_encode(['success' => true]);
        break;

    case 'toggle':
        $id     = intval($_POST['id_diskon']);
        $status = $conn->real_escape_string($_POST['status']);
        $conn->query("UPDATE diskon SET status='$status' WHERE id_diskon=$id");
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = intval($_POST['id_diskon']);
        $conn->query("DELETE FROM diskon WHERE id_diskon=$id");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}