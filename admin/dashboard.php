<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') { header('Location: /konveksi/auth/login.php'); exit; }
require_once '../config/db.php';

$total_pelanggan  = $conn->query("SELECT COUNT(*) as c FROM pelanggan")->fetch_assoc()['c'];
$total_transaksi  = $conn->query("SELECT COUNT(*) as c FROM transaksi")->fetch_assoc()['c'];
$total_produk     = $conn->query("SELECT COUNT(*) as c FROM produk")->fetch_assoc()['c'];
$pending          = $conn->query("SELECT COUNT(*) as c FROM transaksi WHERE status='pending'")->fetch_assoc()['c'];
$pendapatan       = $conn->query("SELECT SUM(jumlah_bayar) as c FROM pembayaran WHERE status='terkonfirmasi'")->fetch_assoc()['c'] ?? 0;
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Dashboard Admin</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6>Total Pelanggan</h6>
                        <h3><?= $total_pelanggan ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6>Total Transaksi</h6>
                        <h3><?= $total_transaksi ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6>Transaksi Pending</h6>
                        <h3><?= $pending ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6>Total Pendapatan</h6>
                        <h5>Rp <?= number_format($pendapatan,0,',','.') ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <h6>Transaksi Terbaru</h6>
        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Jenis</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
            $rows = $conn->query("SELECT t.*, p.name FROM transaksi t JOIN pelanggan p ON t.id_pelanggan=p.id_pelanggan ORDER BY t.tanggal_transaksi DESC LIMIT 10");
            while ($r = $rows->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $r['id_transaksi'] ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= $r['jenis_transaksi'] ?></td>
                    <td>Rp <?= number_format($r['total_harga'],0,',','.') ?></td>
                    <td><span
                            class="badge bg-<?= $r['status']==='pending'?'warning':($r['status']==='lunas'?'success':'info') ?>"><?= $r['status'] ?></span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($r['tanggal_transaksi'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>