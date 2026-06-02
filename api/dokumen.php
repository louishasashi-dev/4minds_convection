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
            $sql = "SELECT dk.*, t.jenis_transaksi, t.total_harga, t.status as status_transaksi,
                           p.name as nama_pelanggan
                    FROM dokumen_transaksi dk
                    JOIN transaksi t ON dk.id_transaksi = t.id_transaksi
                    JOIN pelanggan p ON dk.id_pelanggan = p.id_pelanggan
                    ORDER BY dk.tanggal_cetak DESC";
        } else {
            $id = intval($_SESSION['user_id']);
            $sql = "SELECT dk.*, t.jenis_transaksi, t.total_harga, t.status as status_transaksi,
                           p.name as nama_pelanggan
                    FROM dokumen_transaksi dk
                    JOIN transaksi t ON dk.id_transaksi = t.id_transaksi
                    JOIN pelanggan p ON dk.id_pelanggan = p.id_pelanggan
                    WHERE dk.id_pelanggan = $id
                    ORDER BY dk.tanggal_cetak DESC";
        }
        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'generate':
        // Admin membuat/cetak dokumen untuk sebuah transaksi
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_transaksi  = intval($_POST['id_transaksi']);
        $jenis_dokumen = $conn->real_escape_string($_POST['jenis_dokumen']); // kwitansi / invoice / nota
        $id_admin      = intval($_SESSION['user_id']);

        // Ambil data transaksi
        $trx = $conn->query("SELECT t.*, p.id_pelanggan, p.name as nama_pelanggan, p.alamat, p.no_hp
                              FROM transaksi t
                              JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                              WHERE t.id_transaksi = $id_transaksi")->fetch_assoc();
        if (!$trx) { echo json_encode(['error' => 'Transaksi tidak ditemukan']); break; }

        // Ambil pembayaran terkonfirmasi terakhir
        $bayar = $conn->query("SELECT * FROM pembayaran WHERE id_transaksi=$id_transaksi AND status='terkonfirmasi' ORDER BY tanggal_pembayaran DESC LIMIT 1")->fetch_assoc();
        $id_pembayaran = $bayar ? intval($bayar['id_pembayaran']) : 'NULL';

        // Cek apakah dokumen jenis ini sudah pernah dibuat untuk transaksi ini
        $cek = $conn->query("SELECT id_dokumen FROM dokumen_transaksi WHERE id_transaksi=$id_transaksi AND jenis_dokumen='$jenis_dokumen'")->fetch_assoc();
        if ($cek) {
            // Update tanggal cetak saja
            $conn->query("UPDATE dokumen_transaksi SET tanggal_cetak=NOW(), id_pembayaran=$id_pembayaran WHERE id_dokumen={$cek['id_dokumen']}");
            $id_dokumen = $cek['id_dokumen'];
        } else {
            $conn->query("INSERT INTO dokumen_transaksi (id_transaksi, id_pelanggan, id_pembayaran, jenis_dokumen, tanggal_cetak)
                          VALUES ($id_transaksi, {$trx['id_pelanggan']}, $id_pembayaran, '$jenis_dokumen', NOW())");
            $id_dokumen = $conn->insert_id;
        }

        // Ambil detail transaksi untuk data dokumen
        $detail = [];
        $res_d = $conn->query("SELECT dt.*, pr.nama_produk FROM detail_transaksi dt JOIN produk pr ON dt.id_produk = pr.id_produk WHERE dt.id_transaksi=$id_transaksi");
        while ($r = $res_d->fetch_assoc()) $detail[] = $r;

        echo json_encode([
            'success'      => true,
            'id_dokumen'   => $id_dokumen,
            'jenis'        => $jenis_dokumen,
            'transaksi'    => $trx,
            'detail'       => $detail,
            'pembayaran'   => $bayar
        ]);
        break;

    case 'get':
        // Ambil data 1 dokumen untuk ditampilkan / download pelanggan
        $id_dokumen = intval($_GET['id']);
        $role = $_SESSION['role'];
        $sql = "SELECT dk.*, t.jenis_transaksi, t.total_harga, t.diskon_total, t.jenis_pembayaran,
                       t.tanggal_transaksi, t.tanggal_selesai, t.status as status_transaksi,
                       p.name as nama_pelanggan, p.alamat, p.no_hp
                FROM dokumen_transaksi dk
                JOIN transaksi t ON dk.id_transaksi = t.id_transaksi
                JOIN pelanggan p ON dk.id_pelanggan = p.id_pelanggan
                WHERE dk.id_dokumen = $id_dokumen";
        if ($role !== 'admin') {
            $id_user = intval($_SESSION['user_id']);
            $sql .= " AND dk.id_pelanggan = $id_user";
        }
        $dok = $conn->query($sql)->fetch_assoc();
        if (!$dok) { echo json_encode(['error' => 'Dokumen tidak ditemukan']); break; }

        $detail = [];
        $res_d = $conn->query("SELECT dt.*, pr.nama_produk FROM detail_transaksi dt JOIN produk pr ON dt.id_produk = pr.id_produk WHERE dt.id_transaksi={$dok['id_transaksi']}");
        while ($r = $res_d->fetch_assoc()) $detail[] = $r;

        $bayar = [];
        $res_b = $conn->query("SELECT * FROM pembayaran WHERE id_transaksi={$dok['id_transaksi']} AND status='terkonfirmasi' ORDER BY tanggal_pembayaran");
        while ($r = $res_b->fetch_assoc()) $bayar[] = $r;

        $dok['detail']     = $detail;
        $dok['pembayaran'] = $bayar;
        echo json_encode($dok);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}