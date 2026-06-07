<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'list_ukuran') {
    // Ambil ukuran unik saja, diurutkan dari kecil ke besar
    $urutanUkuran = ['XS','S','M','L','XL','XXL','XXXL','3XL','4XL','5XL'];
    $res  = $conn->query("SELECT DISTINCT ukuran FROM ukuran_model ORDER BY id_ukuran_model ASC");
    $data = [];
    while ($r = $res->fetch_assoc()) {
        $data[] = ['ukuran' => $r['ukuran']];
    }
    // Sort sesuai urutan standar
    usort($data, function($a, $b) use ($urutanUkuran) {
        $ai = array_search(strtoupper($a['ukuran']), $urutanUkuran);
        $bi = array_search(strtoupper($b['ukuran']), $urutanUkuran);
        $ai = $ai === false ? 99 : $ai;
        $bi = $bi === false ? 99 : $bi;
        return $ai - $bi;
    });
    echo json_encode($data);
    exit;
}

echo json_encode(['error' => 'Action tidak dikenal']);