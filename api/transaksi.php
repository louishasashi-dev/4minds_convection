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
            $sql = "SELECT t.*, p.name as nama_pelanggan FROM transaksi t 
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                    ORDER BY t.tanggal_transaksi DESC";
        } else {
            $id  = $_SESSION['user_id'];
            $sql = "SELECT t.*, p.name as nama_pelanggan FROM transaksi t 
                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                    WHERE t.id_pelanggan = $id ORDER BY t.tanggal_transaksi DESC";
        }
        $res  = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'detail':
        $id  = intval($_GET['id']);
        $res = $conn->query("SELECT dt.*, pr.nama_produk FROM detail_transaksi dt 
                             JOIN produk pr ON dt.id_produk = pr.id_produk 
                             WHERE dt.id_transaksi = $id");
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'create':
        $id_pelanggan     = intval($_POST['id_pelanggan'] ?? $_SESSION['user_id']);
        $jenis            = $conn->real_escape_string($_POST['jenis_transaksi']);
        $jenis_pembayaran = $conn->real_escape_string($_POST['jenis_pembayaran']);
        $items            = json_decode($_POST['items'], true); // [{id_produk, jumlah}]
        $tgl_selesai      = $conn->real_escape_string($_POST['tanggal_selesai'] ?? '');

        if (empty($items)) { echo json_encode(['error' => 'Item tidak boleh kosong']); break; }

        // Mulai transaksi DB
        $conn->begin_transaction();
        try {
            // Hitung total & cek diskon
            $total = 0;
            $detail_rows = [];
            foreach ($items as $item) {
                $pid = intval($item['id_produk']);
                $qty = intval($item['jumlah']);
                $produk_row = $conn->query("SELECT harga, stok FROM produk WHERE id_produk=$pid")->fetch_assoc();
                if (!$produk_row) throw new Exception("Produk tidak ditemukan");

                // Cek stok cukup
                if ($produk_row['stok'] < $qty) {
                    throw new Exception("Stok produk tidak mencukupi (tersisa {$produk_row['stok']})");
                }

                $subtotal = $produk_row['harga'] * $qty;
                $total   += $subtotal;
                $detail_rows[] = [$pid, $qty, $produk_row['harga'], $subtotal];

                // Kurangi stok
                $conn->query("UPDATE produk SET stok = stok - $qty WHERE id_produk=$pid");
            }

            // Cek diskon aktif
            $diskon_total = 0;
            $diskon_row = $conn->query("SELECT * FROM diskon WHERE status='aktif' ORDER BY persentase DESC LIMIT 1")->fetch_assoc();
            if ($diskon_row) {
                // Syarat: bisa dikembangkan lebih lanjut
                $diskon_total = $total * ($diskon_row['persentase'] / 100);
                $total        = $total - $diskon_total;
            }

            // Insert transaksi
            $tgl_q = $tgl_selesai ? "'$tgl_selesai'" : "NULL";
            $conn->query("INSERT INTO transaksi (id_pelanggan, jenis_transaksi, total_harga, diskon_total, jenis_pembayaran, tanggal_selesai, status)
                          VALUES ($id_pelanggan, '$jenis', $total, $diskon_total, '$jenis_pembayaran', $tgl_q, 'pending')");
            $id_transaksi = $conn->insert_id;

            // Insert detail
            foreach ($detail_rows as $d) {
                $conn->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal)
                              VALUES ($id_transaksi, {$d[0]}, {$d[1]}, {$d[2]}, {$d[3]})");
            }

            // Jika DP (jahit satuan): buat record pembayaran DP 50%
            if ($jenis_pembayaran === 'dp') {
                $dp_amount = $total * 0.5;
                $conn->query("INSERT INTO pembayaran (id_transaksi, metode, jumlah_bayar, status) VALUES ($id_transaksi, 'transfer', $dp_amount, 'menunggu')");
            }

            $conn->commit();
            echo json_encode(['success' => true, 'id_transaksi' => $id_transaksi, 'total' => $total]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'update_status':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id     = intval($_POST['id']);
        $status = $conn->real_escape_string($_POST['status']);
        $conn->query("UPDATE transaksi SET status='$status' WHERE id_transaksi=$id");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}