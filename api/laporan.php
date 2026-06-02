<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); exit; }

$action       = $_GET['action'] ?? '';
$jenis        = $_GET['jenis'] ?? 'penjualan';
$tgl_awal     = $conn->real_escape_string($_GET['tgl_awal'] ?? date('Y-m-01'));
$tgl_akhir    = $conn->real_escape_string($_GET['tgl_akhir'] ?? date('Y-m-d'));

switch ($action) {
    case 'generate':
        if ($jenis === 'penjualan') {
            $sql = "SELECT t.id_transaksi, p.name as pelanggan, t.jenis_transaksi, t.total_harga, t.diskon_total, t.status, t.tanggal_transaksi
                    FROM transaksi t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    WHERE DATE(t.tanggal_transaksi) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    ORDER BY t.tanggal_transaksi";
        } elseif ($jenis === 'keuangan') {
            $sql = "SELECT pb.id_pembayaran, p.name as pelanggan, pb.metode, pb.jumlah_bayar, pb.status, pb.tanggal_pembayaran
                    FROM pembayaran pb JOIN transaksi t ON pb.id_transaksi = t.id_transaksi
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    WHERE DATE(pb.tanggal_pembayaran) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    ORDER BY pb.tanggal_pembayaran";
        } else { // aktivitas
            $sql = "SELECT pg.id_pengiriman, p.name as pelanggan, pg.kurir, pg.no_resi, pg.status, pg.tanggal_kirim, pg.tanggal_tiba
                    FROM pengiriman pg JOIN transaksi t ON pg.id_transaksi = t.id_transaksi
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                    WHERE DATE(pg.tanggal_kirim) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    ORDER BY pg.tanggal_kirim";
        }
        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        // Simpan ke tabel laporan
        $id_admin  = $_SESSION['user_id'];
        $data_json = $conn->real_escape_string(json_encode($data));
        $conn->query("INSERT INTO laporan (id_admin, jenis_laporan, periode_awal, periode_akhir, data)
                      VALUES ($id_admin, '$jenis', '$tgl_awal', '$tgl_akhir', '$data_json')");
        echo json_encode(['success' => true, 'rows' => $data]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}