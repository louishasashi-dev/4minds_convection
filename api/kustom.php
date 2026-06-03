<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'submit':
        // Pelanggan submit pesanan kustom dari landing page
        $id_pelanggan     = intval($_SESSION['user_id']);
        $jenis_pakaian    = $conn->real_escape_string(trim($_POST['jenis'] ?? ''));
        $ukuran           = $conn->real_escape_string(trim($_POST['ukuran'] ?? ''));
        $jumlah           = max(1, intval($_POST['jumlah'] ?? 1));
        $catatan          = $conn->real_escape_string(trim($_POST['catatan'] ?? ''));
        $estimasi         = $conn->real_escape_string(trim($_POST['estimasi'] ?? ''));
        $jenis_pembayaran = $conn->real_escape_string(trim($_POST['jenis_pembayaran'] ?? 'dp'));

        if (!$jenis_pakaian || !$catatan) {
            echo json_encode(['error' => 'Jenis pakaian dan catatan wajib diisi']); break;
        }

        // Harga placeholder 0, admin akan update nanti
        $total = 0;
        $tgl_q = $estimasi ? "'$estimasi'" : "NULL";

        $conn->query("INSERT INTO transaksi 
            (id_pelanggan, jenis_transaksi, total_harga, diskon_total, jenis_pembayaran, tanggal_selesai, catatan_kustom, ukuran_kustom, status)
            VALUES 
            ($id_pelanggan, 'kustom', $total, 0, '$jenis_pembayaran', $tgl_q, '$catatan', '$ukuran', 'pending')");

        $id_transaksi = $conn->insert_id;

        // Buat detail dummy dengan id_produk NULL dan keterangan dari catatan
        $conn->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal)
                      VALUES ($id_transaksi, NULL, $jumlah, 0, 0)");

        echo json_encode(['success' => true, 'id_transaksi' => $id_transaksi]);
        break;

    case 'set_harga':
        // Admin set harga pesanan kustom
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_transaksi = intval($_POST['id_transaksi']);
        $harga        = floatval($_POST['total_harga']);
        $jp           = $conn->real_escape_string($_POST['jenis_pembayaran']);

        $conn->query("UPDATE transaksi SET total_harga=$harga, jenis_pembayaran='$jp' WHERE id_transaksi=$id_transaksi");
        // Update subtotal di detail juga
        $conn->query("UPDATE detail_transaksi SET harga_satuan=$harga, subtotal=$harga WHERE id_transaksi=$id_transaksi");

        // Jika DP: buat record pembayaran DP 50%
        if ($jp === 'dp') {
            $dp = $harga * 0.5;
            $conn->query("INSERT INTO pembayaran (id_transaksi, metode, jumlah_bayar, status) VALUES ($id_transaksi, 'transfer', $dp, 'menunggu')");
        }

        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}