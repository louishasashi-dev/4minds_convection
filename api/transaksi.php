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

    // Cek apakah ini transaksi jahit satuan
    $trx = $conn->query("SELECT jenis_transaksi, catatan_kustom, ukuran_kustom, jenis_pakaian_kustom, tanggal_selesai FROM transaksi WHERE id_transaksi=$id")->fetch_assoc();

    // Ambil items
    $res = $conn->query("SELECT dt.*, pr.nama_produk FROM detail_transaksi dt 
                         LEFT JOIN produk pr ON dt.id_produk = pr.id_produk 
                         WHERE dt.id_transaksi = $id");
    $items = [];
    while ($r = $res->fetch_assoc()) $items[] = $r;

    // Jika jahit satuan, sertakan info kustom
    if ($trx && $trx['jenis_transaksi'] === 'jahit_satuan' && $trx['catatan_kustom']) {
        $jumlah = isset($items[0]) ? $items[0]['jumlah'] : 1;
        echo json_encode([
            'info' => [
                'jenis_pakaian'   => $trx['jenis_pakaian_kustom'] ?: '-',
                'ukuran'          => $trx['ukuran_kustom'],
                'catatan'         => $trx['catatan_kustom'],
                'tanggal_selesai' => $trx['tanggal_selesai'],
                'jumlah'          => $jumlah
            ],
            'items' => $items
        ]);
    } else {
        echo json_encode(['items' => $items]);
    }
    break;

    case 'create':
    $id_pelanggan     = intval($_POST['id_pelanggan'] ?? $_SESSION['user_id']);
    $jenis            = $conn->real_escape_string($_POST['jenis_transaksi']);
    $jenis_pembayaran = $conn->real_escape_string($_POST['jenis_pembayaran']);
    $items            = json_decode($_POST['items'], true);
    $tgl_selesai      = $conn->real_escape_string($_POST['tanggal_selesai'] ?? '');
    $deskripsi        = $conn->real_escape_string($_POST['deskripsi'] ?? '');
    $ukuran           = $conn->real_escape_string($_POST['ukuran'] ?? '');

    // Persentase kenaikan harga per ukuran
    $ukuran_multiplier = [
        'XS'    => 0.00,
        'S'     => 0.00,
        'M'     => 0.02,
        'L'     => 0.04,
        'XL'    => 0.07,
        'XXL'   => 0.10,
        'XXXL'  => 0.14,
        '3XL'   => 0.14,
        '4XL'   => 0.18,
        '5XL'   => 0.22,
    ];
    $pct_ukuran = $ukuran_multiplier[strtoupper($ukuran)] ?? 0.00;

    // Upload file desain
    $file_desain_nama = '';
    if ($jenis === 'konveksi' && isset($_FILES['file_desain']) && $_FILES['file_desain']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg','image/png','image/gif','image/webp','image/bmp','image/svg+xml','application/pdf'];
        $file_tmp  = $_FILES['file_desain']['tmp_name'];
        $file_mime = mime_content_type($file_tmp);
        $file_size = $_FILES['file_desain']['size'];
        if (!in_array($file_mime, $allowed_types)) {
            echo json_encode(['error' => 'Format file tidak didukung. Gunakan gambar atau PDF.']);
            exit;
        }
        if ($file_size > 5 * 1024 * 1024) {
            echo json_encode(['error' => 'Ukuran file maksimal 5 MB.']);
            exit;
        }
        $ext              = pathinfo($_FILES['file_desain']['name'], PATHINFO_EXTENSION);
        $file_desain_nama = 'desain_' . time() . '_' . $id_pelanggan . '.' . strtolower($ext);
        $upload_dir       = __DIR__ . '/../assets/uploads/';
        move_uploaded_file($file_tmp, $upload_dir . $file_desain_nama);
    }

    if (empty($items)) { echo json_encode(['error' => 'Item tidak boleh kosong']); break; }

    $conn->begin_transaction();
    try {
        $total       = 0;
        $total_qty   = 0;
        $detail_rows = [];

        foreach ($items as $item) {
            $pid = intval($item['id_produk']);
            $qty = intval($item['jumlah']);
            $total_qty += $qty;

            $produk_row = $conn->query("SELECT harga, stok FROM produk WHERE id_produk=$pid")->fetch_assoc();
            if (!$produk_row) throw new Exception("Produk tidak ditemukan");
            if ($produk_row['stok'] < $qty) {
                throw new Exception("Stok produk tidak mencukupi (tersisa {$produk_row['stok']})");
            }

            // Harga dasar + kenaikan ukuran
            $harga_dasar   = floatval($produk_row['harga']);
            $harga_ukuran  = $harga_dasar * (1 + $pct_ukuran);
            $subtotal      = $harga_ukuran * $qty;

            // Diskon ukuran = selisih dari harga dasar
            $diskon_ukuran_item = ($harga_ukuran - $harga_dasar) * $qty * -1; // negatif = markup, simpan sebagai info saja
            // Sebenarnya ini markup bukan diskon, tapi kita simpan pct_ukuran untuk referensi
            $diskon_ukuran_item = 0; // tidak ada diskon ukuran, ukuran menaikkan harga

            $total += $subtotal;
            $detail_rows[] = [$pid, $qty, $harga_ukuran, $subtotal, 0];

            $conn->query("UPDATE produk SET stok = stok - $qty WHERE id_produk=$pid");
        }

        // Diskon qty > 50 = 2%
        $diskon_qty = 0;
        if ($total_qty > 50) {
            $diskon_qty = $total * 0.02;
        }

        // Diskon dari tabel diskon (promo aktif)
        $diskon_promo = 0;
        $diskon_row = $conn->query("SELECT * FROM diskon WHERE status='aktif' ORDER BY persentase DESC LIMIT 1")->fetch_assoc();
        if ($diskon_row) {
            $diskon_promo = $total * ($diskon_row['persentase'] / 100);
        }

        $diskon_total = $diskon_qty + $diskon_promo;
        $total_akhir  = $total - $diskon_total;

        $tgl_q    = $tgl_selesai ? "'$tgl_selesai'" : "NULL";
        $desain_q = $file_desain_nama ? "'$file_desain_nama'" : "NULL";
        $ukuran_q = $ukuran ? "'$ukuran'" : "NULL";

        $conn->query("INSERT INTO transaksi 
            (id_pelanggan, jenis_transaksi, total_harga, diskon_total, jenis_pembayaran, tanggal_selesai, deskripsi, file_desain, ukuran, status)
            VALUES ($id_pelanggan, '$jenis', $total_akhir, $diskon_total, '$jenis_pembayaran', $tgl_q, '$deskripsi', $desain_q, $ukuran_q, 'pending')");
        $id_transaksi = $conn->insert_id;

        foreach ($detail_rows as $d) {
            $conn->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal, diskon_ukuran)
                          VALUES ($id_transaksi, {$d[0]}, {$d[1]}, {$d[2]}, {$d[3]}, {$d[4]})");
        }

        if ($jenis_pembayaran === 'dp') {
            $dp_amount = $total_akhir * 0.5;
            $conn->query("INSERT INTO pembayaran (id_transaksi, metode, jumlah_bayar, status) 
                          VALUES ($id_transaksi, 'transfer', $dp_amount, 'menunggu')");
        }

        $conn->commit();
        echo json_encode([
            'success'      => true,
            'id_transaksi' => $id_transaksi,
            'total'        => $total_akhir,
            'diskon_qty'   => $diskon_qty,
            'diskon_promo' => $diskon_promo,
            'diskon_total' => $diskon_total,
            'pct_ukuran'   => $pct_ukuran * 100
        ]);
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