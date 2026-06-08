<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { echo json_encode(['error' => 'Unauthorized']); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'submit':
        if ($_SESSION['role'] !== 'pelanggan') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_pelanggan  = intval($_SESSION['user_id']);
        $jenis_pakaian = $conn->real_escape_string(trim($_POST['jenis_pakaian'] ?? ''));
        $ukuran        = $conn->real_escape_string(trim($_POST['ukuran'] ?? ''));
        $jumlah        = max(1, intval($_POST['jumlah'] ?? 1));
        $catatan       = $conn->real_escape_string(trim($_POST['catatan'] ?? ''));
        $estimasi      = $conn->real_escape_string(trim($_POST['estimasi'] ?? ''));

        if (!$jenis_pakaian || !$catatan) {
            echo json_encode(['error' => 'Jenis pakaian dan catatan wajib diisi']); break;
        }

        // Upload file desain (opsional)
        $file_desain_nama = '';
        if (isset($_FILES['file_desain']) && $_FILES['file_desain']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg','image/png','image/gif','image/webp','image/bmp','image/svg+xml','application/pdf'];
            $file_tmp  = $_FILES['file_desain']['tmp_name'];
            $file_mime = mime_content_type($file_tmp);
            $file_size = $_FILES['file_desain']['size'];

            if (!in_array($file_mime, $allowed_types)) {
                echo json_encode(['error' => 'Format file tidak didukung. Gunakan gambar atau PDF.']); break;
            }
            if ($file_size > 5 * 1024 * 1024) {
                echo json_encode(['error' => 'Ukuran file maksimal 5 MB.']); break;
            }

            $ext              = pathinfo($_FILES['file_desain']['name'], PATHINFO_EXTENSION);
            $file_desain_nama = 'desain_jahit_' . time() . '_' . $id_pelanggan . '.' . strtolower($ext);
            $upload_dir       = __DIR__ . '/../assets/uploads/';
            move_uploaded_file($file_tmp, $upload_dir . $file_desain_nama);
        }

        $tgl_q    = $estimasi ? "'$estimasi'" : "NULL";
        $desain_q = $file_desain_nama ? "'$file_desain_nama'" : "NULL";
        $conn->query("INSERT INTO pesan_jahit 
            (id_pelanggan, jenis_pakaian, ukuran, jumlah, catatan, estimasi_selesai, file_desain, status)
            VALUES ($id_pelanggan, '$jenis_pakaian', '$ukuran', $jumlah, '$catatan', $tgl_q, $desain_q, 'menunggu')");

        echo json_encode(['success' => true, 'id_pesan' => $conn->insert_id]);
        break;

    case 'list':
        // Admin: semua pesan, Pelanggan: pesan milik sendiri
        if ($_SESSION['role'] === 'admin') {
            $sql = "SELECT pj.*, p.name as nama_pelanggan 
                    FROM pesan_jahit pj 
                    JOIN pelanggan p ON pj.id_pelanggan = p.id_pelanggan 
                    ORDER BY pj.created_at DESC";
        } else {
            $id = intval($_SESSION['user_id']);
            $sql = "SELECT pj.*, p.name as nama_pelanggan 
                    FROM pesan_jahit pj 
                    JOIN pelanggan p ON pj.id_pelanggan = p.id_pelanggan 
                    WHERE pj.id_pelanggan = $id 
                    ORDER BY pj.created_at DESC";
        }
        $res = $conn->query($sql);
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'setujui':
        // Admin setujui pesan dan buat transaksi otomatis
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_pesan = intval($_POST['id_pesan']);
        $harga    = floatval($_POST['harga']);
        $jp       = $conn->real_escape_string($_POST['jenis_pembayaran']);

        // Ambil data pesan
        $pesan = $conn->query("SELECT * FROM pesan_jahit WHERE id_pesan=$id_pesan")->fetch_assoc();
        if (!$pesan) { echo json_encode(['error' => 'Pesan tidak ditemukan']); break; }

        $conn->begin_transaction();
        try {
            $id_pelanggan  = $pesan['id_pelanggan'];
            $estimasi      = $pesan['estimasi_selesai'] ? "'{$pesan['estimasi_selesai']}'" : "NULL";
            $catatan       = $conn->real_escape_string($pesan['catatan']);
            $ukuran        = $conn->real_escape_string($pesan['ukuran']);
            $jenis_pakaian = $conn->real_escape_string($pesan['jenis_pakaian']); // ← TAMBAHAN

            // Buat transaksi
            $conn->query("INSERT INTO transaksi 
                (id_pelanggan, jenis_transaksi, total_harga, diskon_total, jenis_pembayaran, tanggal_selesai, deskripsi, ukuran, status)
                VALUES ($id_pelanggan, 'jahit_satuan', $harga, 0, '$jp', $estimasi, '$catatan', '$ukuran', 'pending')");
            $id_transaksi = $conn->insert_id;

            // Buat detail dummy
            $conn->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, jumlah, harga_satuan, subtotal)
                          VALUES ($id_transaksi, NULL, {$pesan['jumlah']}, $harga, $harga)");

            // Jika DP: buat record pembayaran (metode mengikuti pilihan admin saat setujui)
            if ($jp === 'dp') {
                $dp = $harga * 0.5;
                $metode_dp = ($jp === 'dp') ? 'transfer' : $jp; // default transfer untuk DP
                $conn->query("INSERT INTO pembayaran (id_transaksi, metode, jumlah_bayar, status) 
                              VALUES ($id_transaksi, '$metode_dp', $dp, 'menunggu')");
            }

            // Update status pesan
            $conn->query("UPDATE pesan_jahit SET status='disetujui', harga_disetujui=$harga, 
                          jenis_pembayaran='$jp', id_transaksi=$id_transaksi 
                          WHERE id_pesan=$id_pesan");

            $conn->commit();
            echo json_encode(['success' => true, 'id_transaksi' => $id_transaksi]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;

    case 'tolak':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id_pesan = intval($_POST['id_pesan']);
        $conn->query("UPDATE pesan_jahit SET status='ditolak' WHERE id_pesan=$id_pesan");
        echo json_encode(['success' => true]);
        break;

    case 'count_menunggu':
        // Untuk badge notifikasi di header admin
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['count' => 0]); break; }
        $res = $conn->query("SELECT COUNT(*) as c FROM pesan_jahit WHERE status='menunggu'");
        echo json_encode(['count' => intval($res->fetch_assoc()['c'])]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}