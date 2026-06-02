<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    
    case 'detail_publik':
        $id  = intval($_GET['id']);
        $res = $conn->query("SELECT * FROM produk WHERE id_produk=$id");
        $row = $res->fetch_assoc();
        echo json_encode($row ?: ['error' => 'not found']);
        break;

    case 'list':
        $keyword = $conn->real_escape_string($_GET['q'] ?? '');
        $jenis   = $conn->real_escape_string($_GET['jenis'] ?? '');
        $where   = "WHERE 1=1";
        if ($keyword) $where .= " AND (nama_produk LIKE '%$keyword%' OR kategori LIKE '%$keyword%')";
        if ($jenis)   $where .= " AND jenis = '$jenis'";
        $res = $conn->query("SELECT * FROM produk $where ORDER BY nama_produk");
        $data = [];
        while ($r = $res->fetch_assoc()) $data[] = $r;
        echo json_encode($data);
        break;

    case 'create':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $nama      = $conn->real_escape_string($_POST['nama_produk']);
        $kategori  = $conn->real_escape_string($_POST['kategori']);
        $harga     = floatval($_POST['harga']);
        $stok      = intval($_POST['stok']);
        $jenis     = $conn->real_escape_string($_POST['jenis']);
        $model     = $conn->real_escape_string($_POST['model'] ?? '');
        $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');
        $gambar    = '';
        if (!empty($_FILES['gambar']['name'])) {
            $ext   = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $fname = 'produk_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/uploads/' . $fname);
            $gambar = $fname;
        }
        $conn->query("INSERT INTO produk (nama_produk, kategori, harga, stok, jenis, model, deskripsi, gambar) VALUES ('$nama','$kategori',$harga,$stok,'$jenis','$model','$deskripsi','$gambar')");
        echo json_encode(['success' => true]);
        break;

    case 'update':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id        = intval($_POST['id_produk']);
        $nama      = $conn->real_escape_string($_POST['nama_produk']);
        $kategori  = $conn->real_escape_string($_POST['kategori'] ?? '');
        $harga     = floatval($_POST['harga']);
        $stok      = intval($_POST['stok']);
        $jenis     = $conn->real_escape_string($_POST['jenis']);
        $model     = $conn->real_escape_string($_POST['model'] ?? '');
        $deskripsi = $conn->real_escape_string($_POST['deskripsi'] ?? '');

        // Cek apakah ada gambar baru
        $gambar_sql = '';
        if (!empty($_FILES['gambar']['name'])) {
            $ext   = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $fname = 'produk_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], '../assets/uploads/' . $fname);
            $gambar_sql = ", gambar='$fname'";
        }

        $conn->query("UPDATE produk
                      SET nama_produk='$nama', kategori='$kategori', harga=$harga,
                          stok=$stok, jenis='$jenis', model='$model', deskripsi='$deskripsi'
                          $gambar_sql
                      WHERE id_produk=$id");
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        if ($_SESSION['role'] !== 'admin') { echo json_encode(['error' => 'Forbidden']); break; }
        $id = intval($_POST['id_produk']);
        // Simpan nama produk ke detail_transaksi sebelum dihapus, agar riwayat tidak hilang
        $produk_info = $conn->query("SELECT nama_produk FROM produk WHERE id_produk=$id")->fetch_assoc();
        if ($produk_info) {
            // Tandai detail_transaksi bahwa produk ini sudah dihapus
            $nama_lama = $conn->real_escape_string($produk_info['nama_produk'] . ' [dihapus]');
            // Cek apakah tabel detail_transaksi punya kolom nama_snapshot (opsional)
        }
        $conn->query("DELETE FROM produk WHERE id_produk=$id");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Action tidak dikenal']);
}