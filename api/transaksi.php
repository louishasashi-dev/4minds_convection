<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'list':
        $jenis_filter = isset($_GET['jenis']) ? $conn->real_escape_string($_GET['jenis']) : '';

        $where = [];

        // Jika pelanggan, hanya tampilkan transaksi milik sendiri
        if ($_SESSION['role'] === 'pelanggan') {
            $id_pelanggan = intval($_SESSION['user_id']);
            $where[] = "t.id_pelanggan = $id_pelanggan";
        }

        if ($jenis_filter) {
            $where[] = "t.jenis_transaksi = '$jenis_filter'";
        }

        $sql = "SELECT t.*, p.name as nama_pelanggan, p.email as email_pelanggan,
                       (SELECT status FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as status_pembayaran,
                       (SELECT id_pembayaran FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as id_pembayaran,
                       (SELECT bukti_bayar FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as bukti_bayar,
                       (SELECT jumlah_bayar FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as jumlah_bayar,
                       (SELECT metode FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as metode_bayar
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY t.tanggal_transaksi DESC";

        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) {
            $items_sql = "SELECT dt.*, pr.nama_produk 
                         FROM detail_transaksi dt 
                         LEFT JOIN produk pr ON dt.id_produk = pr.id_produk 
                         WHERE dt.id_transaksi = " . $r['id_transaksi'];
            $items_res = $conn->query($items_sql);
            $items = [];
            while ($item = $items_res->fetch_assoc()) {
                $items[] = $item;
            }
            $r['items'] = $items;
            $data[] = $r;
        }
        echo json_encode($data);
        break;

    case 'detail':
        $id = intval($_GET['id']);
        $sql = "SELECT t.*, p.name as nama_pelanggan, p.email as email_pelanggan, p.no_hp,
                       (SELECT status FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as status_pembayaran,
                       (SELECT id_pembayaran FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as id_pembayaran,
                       (SELECT bukti_bayar FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as bukti_bayar,
                       (SELECT jumlah_bayar FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as jumlah_bayar
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                WHERE t.id_transaksi = $id";
        $res = $conn->query($sql);
        $data = $res->fetch_assoc();
        
        if ($data) {
            $items_sql = "SELECT dt.*, pr.nama_produk, pr.harga as harga_produk
                         FROM detail_transaksi dt 
                         LEFT JOIN produk pr ON dt.id_produk = pr.id_produk 
                         WHERE dt.id_transaksi = $id";
            $items_res = $conn->query($items_sql);
            $items = [];
            while ($item = $items_res->fetch_assoc()) {
                $items[] = $item;
            }
            $data['items'] = $items;

            // Untuk jahit satuan, ambil data dari pesan_jahit
            if ($data['jenis_transaksi'] === 'jahit_satuan') {
                $pj = $conn->query("SELECT * FROM pesan_jahit WHERE id_transaksi = $id")->fetch_assoc();
                if ($pj) $data['pesan_jahit'] = $pj;
            }
        }
        echo json_encode($data);
        break;

    case 'create':
        $id_pelanggan = intval($_POST['id_pelanggan'] ?? $_SESSION['user_id']);
        $jenis = $conn->real_escape_string($_POST['jenis_transaksi']);
        $jenis_pembayaran = $conn->real_escape_string($_POST['jenis_pembayaran']);
        $items = json_decode($_POST['items'], true);
        $tgl_selesai = $conn->real_escape_string($_POST['tanggal_selesai'] ?? '');
        $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');
        $ukuran = $conn->real_escape_string($_POST['ukuran'] ?? '');
        
        $ukuran_multiplier = [
            'XS' => 0.00, 'S' => 0.00, 'M' => 0.02, 'L' => 0.04,
            'XL' => 0.07, 'XXL' => 0.10, 'XXXL' => 0.14, 'XXXXL' => 0.18,
            'XXXXXL' => 0.22, 'XXXXXXL' => 0.26, 'XXXXXXXL' => 0.30, 'XXXXXXXXL' => 0.35
        ];
        $pct_ukuran = $ukuran_multiplier[strtoupper($ukuran)] ?? 0.00;
        
        $file_desain_nama = '';
        if (isset($_FILES['file_desain']) && $_FILES['file_desain']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg','image/png','image/gif','image/webp','application/pdf'];
            $file_tmp = $_FILES['file_desain']['tmp_name'];
            $file_mime = mime_content_type($file_tmp);
            $file_size = $_FILES['file_desain']['size'];
            
            if (!in_array($file_mime, $allowed_types)) {
                echo json_encode(['error' => 'Format file tidak didukung']);
                exit;
            }
            if ($file_size > 5 * 1024 * 1024) {
                echo json_encode(['error' => 'Ukuran file maksimal 5 MB']);
                exit;
            }
            $ext = pathinfo($_FILES['file_desain']['name'], PATHINFO_EXTENSION);
            $file_desain_nama = 'desain_' . time() . '_' . $id_pelanggan . '.' . strtolower($ext);
            $upload_dir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($file_tmp, $upload_dir . $file_desain_nama);
        }
        
        if (empty($items)) { 
            echo json_encode(['error' => 'Item tidak boleh kosong']); 
            break; 
        }
        
        $conn->begin_transaction();
        try {
            $total = 0;
            $total_qty = 0;
            $detail_rows = [];
            
            foreach ($items as $item) {
                $pid = intval($item['id_produk']);
                $qty = intval($item['jumlah']);
                $total_qty += $qty;
                
                $produk_row = $conn->query("SELECT harga, stok FROM produk WHERE id_produk=$pid")->fetch_assoc();
                if (!$produk_row) throw new Exception("Produk tidak ditemukan");
                if ($produk_row['stok'] < $qty) {
                    throw new Exception("Stok tidak mencukupi");
                }
                
                $harga_dasar = floatval($produk_row['harga']);
                $harga_ukuran = $harga_dasar * (1 + $pct_ukuran);
                $subtotal = $harga_ukuran * $qty;
                $total += $subtotal;
                $detail_rows[] = [$pid, $qty, $harga_ukuran, $subtotal];
                
                $conn->query("UPDATE produk SET stok = stok - $qty WHERE id_produk=$pid");
            }
            
            $diskon_qty = ($total_qty > 50) ? $total * 0.02 : 0;
            $diskon_total = $diskon_qty;
            $total_akhir = $total - $diskon_total;
            
            $tgl_q = $tgl_selesai ? "'$tgl_selesai'" : "NULL";
            $desain_q = $file_desain_nama ? "'$file_desain_nama'" : "NULL";
            $ukuran_q = $ukuran ? "'$ukuran'" : "NULL";
            
            $conn->query("INSERT INTO transaksi 
                (id_pelanggan, jenis_transaksi, total_harga, diskon_total, jenis_pembayaran, tanggal_selesai, deskripsi, file_desain, ukuran, status)
                VALUES ($id_pelanggan, '$jenis', $total_akhir, $diskon_total, '$jenis_pembayaran', $tgl_q, '$deskripsi', $desain_q, $ukuran_q, 'pending')");
            $id_transaksi = $conn->insert_id;
            
            foreach ($detail_rows as $d) {
                $conn->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal)
                              VALUES ($id_transaksi, {$d[0]}, {$d[1]}, {$d[2]}, {$d[3]})");
            }
            
            if ($jenis_pembayaran === 'dp') {
                $dp_amount = $total_akhir * 0.5;
                $conn->query("INSERT INTO pembayaran (id_transaksi, metode, jumlah_bayar, status) 
                              VALUES ($id_transaksi, 'transfer', $dp_amount, 'menunggu')");
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'id_transaksi' => $id_transaksi]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'update_status':
        $id = intval($_POST['id_transaksi'] ?? $_POST['id'] ?? 0);
        $status = $conn->real_escape_string($_POST['status'] ?? '');
        $kurir = isset($_POST['kurir']) ? $conn->real_escape_string($_POST['kurir']) : '';
        $no_resi = isset($_POST['no_resi']) ? $conn->real_escape_string($_POST['no_resi']) : '';
        
        if ($id && $status) {
            $conn->query("UPDATE transaksi SET status='$status' WHERE id_transaksi=$id");
            
            // Jika status dikirim, update pengiriman
            if ($status === 'dikirim' && ($kurir || $no_resi)) {
                $cek = $conn->query("SELECT id_pengiriman FROM pengiriman WHERE id_transaksi=$id");
                if ($cek->num_rows > 0) {
                    $update_sql = "UPDATE pengiriman SET tanggal_kirim=CURDATE(), status='dikirim'";
                    if ($kurir) $update_sql .= ", kurir='$kurir'";
                    if ($no_resi) $update_sql .= ", no_resi='$no_resi'";
                    $update_sql .= " WHERE id_transaksi=$id";
                    $conn->query($update_sql);
                } else {
                    $conn->query("INSERT INTO pengiriman (id_transaksi, kurir, no_resi, tanggal_kirim, status) 
                                  VALUES ($id, '$kurir', '$no_resi', CURDATE(), 'dikirim')");
                }
            }
            
            // Jika status selesai, update pengiriman menjadi sampai
            if ($status === 'selesai') {
                $conn->query("UPDATE transaksi SET tanggal_selesai=CURDATE() WHERE id_transaksi=$id");
                $conn->query("UPDATE pengiriman SET status='sampai', tanggal_tiba=CURDATE() WHERE id_transaksi=$id");
            }
            
            // Jika status batal, kembalikan stok
            if ($status === 'batal') {
                $detail = $conn->query("SELECT id_produk, jumlah FROM detail_transaksi WHERE id_transaksi=$id");
                while ($d = $detail->fetch_assoc()) {
                    $conn->query("UPDATE produk SET stok = stok + {$d['jumlah']} WHERE id_produk = {$d['id_produk']}");
                }
            }
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Parameter tidak lengkap']);
        }
        break;

    case 'count_pending':
        $jenis = isset($_GET['jenis']) ? $conn->real_escape_string($_GET['jenis']) : '';
        $sql = "SELECT COUNT(*) as count FROM transaksi WHERE status='pending'";
        if ($jenis) {
            $sql .= " AND jenis_transaksi = '$jenis'";
        }
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        echo json_encode(['count' => intval($row['count'])]);
        break;

    case 'export_excel':
        $jenis_filter = isset($_GET['jenis']) ? $conn->real_escape_string($_GET['jenis']) : '';
        
        $sql = "SELECT t.id_transaksi, t.tanggal_transaksi, t.jenis_transaksi, t.total_harga, 
                       t.diskon_total, t.status, t.ukuran, t.jenis_pembayaran, t.tanggal_selesai, t.deskripsi,
                       p.name as nama_pelanggan, p.email as email_pelanggan, p.no_hp,
                       (SELECT status FROM pembayaran WHERE id_transaksi = t.id_transaksi ORDER BY id_pembayaran DESC LIMIT 1) as status_pembayaran
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                WHERE t.jenis_transaksi = 'konveksi'
                ORDER BY t.tanggal_transaksi DESC";
        
        $res = $conn->query($sql);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan_konveksi_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID Transaksi', 'Tanggal', 'Pelanggan', 'Email', 'No HP', 'Total', 'Diskon', 'Status Pesanan', 'Status Bayar', 'Jenis Bayar', 'Ukuran', 'Tgl Selesai', 'Catatan']);
        
        while ($row = $res->fetch_assoc()) {
            fputcsv($output, [
                $row['id_transaksi'],
                $row['tanggal_transaksi'],
                $row['nama_pelanggan'],
                $row['email_pelanggan'],
                $row['no_hp'],
                $row['total_harga'],
                $row['diskon_total'],
                $row['status'],
                $row['status_pembayaran'],
                $row['jenis_pembayaran'],
                $row['ukuran'],
                $row['tanggal_selesai'],
                $row['deskripsi']
            ]);
        }
        fclose($output);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal: ' . $action]);
}
?>