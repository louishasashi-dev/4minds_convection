<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Status Pengiriman</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Transaksi</th>
                            <th>Kurir</th>
                            <th>No Resi</th>
                            <th>Tgl Kirim</th>
                            <th>Estimasi Tiba</th>
                            <th>Tgl Tiba</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPengiriman">
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    $.get('/konveksi/api/pengiriman.php?action=list', function(data) {
        let tbody = $('#bodyPengiriman');
        tbody.empty();
        if (data.length === 0) {
            tbody.html(
                '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pengiriman.</td></tr>'
                );
            return;
        }
        data.forEach((p, i) => {
            let badge = p.status === 'sampai' ? 'success' : p.status === 'dikirim' ? 'info' :
                'secondary';
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>#${p.id_transaksi} <small class="text-muted">(${p.jenis_transaksi.replace(/_/g,' ')})</small></td>
                    <td>${p.kurir || '-'}</td>
                    <td>${p.no_resi
                        ? `<code>${p.no_resi}</code>`
                        : '<span class="text-muted">-</span>'}</td>
                    <td>${p.tanggal_kirim   || '-'}</td>
                    <td>${p.estimasi_sampai || '-'}</td>
                    <td>${p.tanggal_tiba    || '-'}</td>
                    <td><span class="badge bg-${badge}">${p.status}</span></td>
                </tr>
            `);
        });
    }, 'json');
});
</script>