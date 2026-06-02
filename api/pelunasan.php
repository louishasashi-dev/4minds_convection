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
            $sql = "SELECT pb.*, t.jenis_transaksi, t.total_harga, p.name as nama_pelanggan 
                    FROM pembayaran pb 
                    JOIN transaksi t ON pb.id_transaksi = t.id_transaksi 
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                    ORDER BY pb.tanggal_pembayaran DESC";
        } else {
            $id  = $_SESSION['user_id'];
            $sql = "SELECT pb.*, t.jenis_transaksi, t.total_harga, p.name as nama_pelanggan 
                    FROM pembayaran pb 
                    JOIN transaksi t ON pb.id_transaksi = t.id_transaksi 
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                    WHERE t.id_pelanggan = $id AND pb.status != 'terkonfirmasi'
                    ORDER BY pb.tanggal_pembayaran DESC";
        }
        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'bayar':
        // Pelanggan melakukan pembayaran sisa / DP
        $id_transaksi = intval($_POST['id_transaksi']);
        $metode       = $conn->real_escape_string($_POST['metode']);
        $jumlah       = floatval($_POST['jumlah']);

        // Upload bukti bayar (opsional)
        $bukti = '';
        if (!empty($_FILES['bukti_bayar']['name'])) {
            $ext    = pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION);
            $fname  = 'bukti_' . time() . '.' . $ext;
            $target = '../assets/uploads/' . $fname;
            move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $target);
            $bukti = $fname;
        }

        $conn->query("INSERT INTO pembayaran (id_transaksi, metode, jumlah_bayar, bukti_bayar, status)
                      VALUES ($id_transaksi, '$metode', $jumlah, '$bukti', 'menunggu')");
        // Ubah status transaksi jadi diproses saat pelanggan kirim pembayaran
        $conn->query("UPDATE transaksi SET status='diproses' WHERE id_transaksi=$id_transaksi AND status='pending'");
        echo json_encode(['success' => true]);
        break;

    case 'konfirmasi':
        // Admin konfirmasi atau tolak pembayaran
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_bayar  = intval($_POST['id_pembayaran']);
        $id_admin  = $_SESSION['user_id'];
        $status_baru = $_POST['status'] === 'ditolak' ? 'ditolak' : 'terkonfirmasi';

        $conn->query("UPDATE pembayaran SET status='$status_baru', id_admin=$id_admin WHERE id_pembayaran=$id_bayar");

        // Cek otomatis lunas hanya jika dikonfirmasi, bukan ditolak
        if ($status_baru === 'terkonfirmasi') {
            $pb  = $conn->query("SELECT * FROM pembayaran WHERE id_pembayaran=$id_bayar")->fetch_assoc();
            $trx = $conn->query("SELECT * FROM transaksi WHERE id_transaksi={$pb['id_transaksi']}")->fetch_assoc();
            $total_bayar = $conn->query("SELECT SUM(jumlah_bayar) as s FROM pembayaran WHERE id_transaksi={$pb['id_transaksi']} AND status='terkonfirmasi'")->fetch_assoc()['s'];
            if ($total_bayar >= $trx['total_harga']) {
                // Sudah lunas penuh
                $conn->query("UPDATE transaksi SET status='lunas' WHERE id_transaksi={$pb['id_transaksi']}");
            } else {
                // Baru DP / bayar sebagian — set diproses
                $conn->query("UPDATE transaksi SET status='diproses' WHERE id_transaksi={$pb['id_transaksi']}");
            }
        } else {
            // Ditolak — kembalikan transaksi ke pending agar pelanggan bisa upload ulang
            $pb = $conn->query("SELECT * FROM pembayaran WHERE id_pembayaran=$id_bayar")->fetch_assoc();
            $conn->query("UPDATE transaksi SET status='pending' WHERE id_transaksi={$pb['id_transaksi']}");
        }// Cek otomatis lunas hanya jika dikonfirmasi, bukan ditolak
        if ($status_baru === 'terkonfirmasi') {
            $pb  = $conn->query("SELECT * FROM pembayaran WHERE id_pembayaran=$id_bayar")->fetch_assoc();
            $trx = $conn->query("SELECT * FROM transaksi WHERE id_transaksi={$pb['id_transaksi']}")->fetch_assoc();
            $total_bayar = $conn->query("SELECT SUM(jumlah_bayar) as s FROM pembayaran WHERE id_transaksi={$pb['id_transaksi']} AND status='terkonfirmasi'")->fetch_assoc()['s'];
            if ($total_bayar >= $trx['total_harga']) {
                // Sudah lunas penuh
                $conn->query("UPDATE transaksi SET status='lunas' WHERE id_transaksi={$pb['id_transaksi']}");
            } else {
                // Baru DP / bayar sebagian — set diproses
                $conn->query("UPDATE transaksi SET status='diproses' WHERE id_transaksi={$pb['id_transaksi']}");
            }
        } else {
            // Ditolak — kembalikan transaksi ke pending agar pelanggan bisa upload ulang
            $pb = $conn->query("SELECT * FROM pembayaran WHERE id_pembayaran=$id_bayar")->fetch_assoc();
            $conn->query("UPDATE transaksi SET status='pending' WHERE id_transaksi={$pb['id_transaksi']}");
        }

        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}