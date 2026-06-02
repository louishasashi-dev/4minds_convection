<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $role = $_SESSION['role'];
        if ($role === 'admin') {
            $sql = "SELECT pg.*, t.jenis_transaksi, t.status as status_transaksi, p.name as nama_pelanggan
                    FROM pengiriman pg
                    JOIN transaksi t ON pg.id_transaksi = t.id_transaksi
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan

                    UNION

                    SELECT NULL, t.id_transaksi, NULL,
                           NULL, NULL, NULL,
                           NULL, NULL, 'belum_dikirim',
                           t.jenis_transaksi, t.status as status_transaksi, p.name as nama_pelanggan
                    FROM transaksi t
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    WHERE t.status IN ('lunas','diproses','selesai','dikirim')
                    AND t.id_transaksi NOT IN (SELECT id_transaksi FROM pengiriman)

                    ORDER BY tanggal_kirim DESC";
        } else {
            $id = $_SESSION['user_id'];
            $sql = "SELECT pg.*, t.jenis_transaksi, t.status as status_transaksi, p.name as nama_pelanggan
                    FROM pengiriman pg
                    JOIN transaksi t ON pg.id_transaksi = t.id_transaksi
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    WHERE t.id_pelanggan = $id
                    ORDER BY pg.tanggal_kirim DESC";
        }
        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'proses':
        // Admin memproses pengiriman
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_transaksi = intval($_POST['id_transaksi']);
        $id_admin     = $_SESSION['user_id'];
        $kurir        = $conn->real_escape_string($_POST['kurir']);
        $no_resi      = $conn->real_escape_string($_POST['no_resi']);
        $tgl_kirim    = $conn->real_escape_string($_POST['tanggal_kirim']);
        $estimasi     = $conn->real_escape_string($_POST['estimasi_sampai']);

        // Cek sudah ada record pengiriman?
        $cek = $conn->query("SELECT id_pengiriman FROM pengiriman WHERE id_transaksi=$id_transaksi")->fetch_assoc();
        if ($cek) {
            $conn->query("UPDATE pengiriman SET kurir='$kurir', no_resi='$no_resi', tanggal_kirim='$tgl_kirim', estimasi_sampai='$estimasi', status='dikirim', id_admin=$id_admin WHERE id_transaksi=$id_transaksi");
        } else {
            $conn->query("INSERT INTO pengiriman (id_transaksi, id_admin, kurir, no_resi, tanggal_kirim, estimasi_sampai, status) VALUES ($id_transaksi, $id_admin, '$kurir', '$no_resi', '$tgl_kirim', '$estimasi', 'dikirim')");
        }
        $conn->query("UPDATE transaksi SET status='dikirim' WHERE id_transaksi=$id_transaksi");
        echo json_encode(['success' => true]);
        break;

    case 'sampai':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_transaksi = intval($_POST['id_transaksi']);
        $tgl_tiba     = date('Y-m-d');
        $conn->query("UPDATE pengiriman SET status='sampai', tanggal_tiba='$tgl_tiba' WHERE id_transaksi=$id_transaksi");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}