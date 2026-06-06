<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /konveksi/auth/login.php');
    exit;
}
require_once '../config/db.php';

$id = $_SESSION['user_id'];

$total_transaksi   = $conn->query("SELECT COUNT(*) as c FROM transaksi WHERE id_pelanggan=$id")->fetch_assoc()['c'];
$transaksi_pending = $conn->query("SELECT COUNT(*) as c FROM transaksi WHERE id_pelanggan=$id AND status='pending'")->fetch_assoc()['c'];
$transaksi_dikirim = $conn->query("SELECT COUNT(*) as c FROM transaksi WHERE id_pelanggan=$id AND status='dikirim'")->fetch_assoc()['c'];
$total_bayar       = $conn->query("SELECT SUM(pb.jumlah_bayar) as s FROM pembayaran pb JOIN transaksi t ON pb.id_transaksi=t.id_transaksi WHERE t.id_pelanggan=$id AND pb.status='terkonfirmasi'")->fetch_assoc()['s'] ?? 0;

$transaksi_terbaru = $conn->query("SELECT * FROM transaksi WHERE id_pelanggan=$id ORDER BY tanggal_transaksi DESC LIMIT 5");
$pengiriman_aktif  = $conn->query("SELECT pg.*, t.jenis_transaksi FROM pengiriman pg JOIN transaksi t ON pg.id_transaksi=t.id_transaksi WHERE t.id_pelanggan=$id AND pg.status='dikirim' LIMIT 3");
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Dashboard</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="alert alert-info mb-4">
            👋 Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!
        </div>

        <!-- Kartu Ringkasan -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card text-white bg-primary text-center p-3">
                    <div class="fs-2 fw-bold"><?= $total_transaksi ?></div>
                    <div class="small">Total Transaksi</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white bg-warning text-center p-3">
                    <div class="fs-2 fw-bold"><?= $transaksi_pending ?></div>
                    <div class="small">Transaksi Pending</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white bg-info text-center p-3">
                    <div class="fs-2 fw-bold"><?= $transaksi_dikirim ?></div>
                    <div class="small">Sedang Dikirim</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-white bg-success text-center p-3">
                    <div class="fs-2 fw-bold">Rp <?= number_format($total_bayar, 0, ',', '.') ?></div>
                    <div class="small">Total Sudah Dibayar</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Transaksi Terbaru -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>📋 Transaksi Terbaru</span>
                        <a href="/konveksi/pelanggan/transaksi.php" class="btn btn-sm btn-outline-primary">Lihat
                            Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Jenis</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($transaksi_terbaru->num_rows === 0): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Belum ada transaksi</td>
                                </tr>
                                <?php else: ?>
                                <?php while ($t = $transaksi_terbaru->fetch_assoc()): ?>
                                <?php
                                $badge = match($t['status']) {
                                    'lunas'   => 'success',
                                    'pending' => 'warning',
                                    'dikirim' => 'info',
                                    'batal'   => 'danger',
                                    default   => 'secondary'
                                };
                                ?>
                                <tr>
                                    <td><?= $t['id_transaksi'] ?></td>
                                    <td><?= str_replace('_', ' ', $t['jenis_transaksi']) ?></td>
                                    <td>Rp <?= number_format($t['total_harga'], 0, ',', '.') ?></td>
                                    <td><span class="badge bg-<?= $badge ?>"><?= $t['status'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($t['tanggal_transaksi'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Kanan: Pengiriman + Menu Cepat -->
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>🚚 Pengiriman Aktif</span>
                        <a href="/konveksi/pelanggan/pengiriman.php" class="btn btn-sm btn-outline-info">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if ($pengiriman_aktif->num_rows === 0): ?>
                        <p class="text-muted text-center py-2 mb-0">Tidak ada paket yang sedang dikirim</p>
                        <?php else: ?>
                        <?php while ($pg = $pengiriman_aktif->fetch_assoc()): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong><?= htmlspecialchars($pg['kurir']) ?></strong>
                                <span class="badge bg-info"><?= $pg['status'] ?></span>
                            </div>
                            <div class="small text-muted">
                                Resi: <?= htmlspecialchars($pg['no_resi']) ?><br>
                                Estimasi:
                                <?= $pg['estimasi_sampai'] ? date('d/m/Y', strtotime($pg['estimasi_sampai'])) : '-' ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Menu Cepat -->
                <div class="card">
                    <div class="card-header">⚡ Menu Cepat</div>
                    <div class="card-body p-2">
                        <div class="d-grid gap-2">
                            <a href="/konveksi/" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-search"></i> Lihat Produk
                            </a>
                            <a href="/konveksi/pelanggan/transaksi.php" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-cart-plus"></i> Transaksi Saya
                            </a>
                            <a href="/konveksi/pelanggan/pelunasan.php" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-cash"></i> Bayar Tagihan
                            </a>
                            <a href="/konveksi/pelanggan/dokumen.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-file-earmark-arrow-down"></i> Download Dokumen
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>