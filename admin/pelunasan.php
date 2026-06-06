<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Konfirmasi Pelunasan</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="row mb-3 g-2">
            <div class="col-auto">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="terkonfirmasi">Terkonfirmasi</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Transaksi</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Total Transaksi</th>
                            <th>Jumlah Bayar</th>
                            <th>Metode</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPelunasan">
                        <tr>
                            <td colspan="12" class="text-center py-4">
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
let allData = [];

$(document).ready(function() {
    load();
    $('#filterStatus').on('change', render);
});

function load() {
    $.get('/konveksi/api/pelunasan.php?action=list', function(data) {
        allData = data;
        render();
    }, 'json');
}

function getKeterangan(p) {
    if (p.jenis_pembayaran !== 'dp') return '-';
    if (parseInt(p.urutan_bayar) === 1) {
        return '<span class="badge bg-info text-dark">Pembayaran DP 50%</span>';
    } else {
        return '<span class="badge bg-success">Pelunasan sisa DP 50%</span>';
    }
}

function render() {
    let st = $('#filterStatus').val();
    let tbody = $('#bodyPelunasan');
    tbody.empty();

    let filtered = allData.filter(d => !st || d.status === st);
    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="12" class="text-center text-muted py-4">Tidak ada data.</td></tr>');
        return;
    }

    filtered.forEach((p, i) => {
        let badge = p.status === 'terkonfirmasi' ? 'success' : p.status === 'ditolak' ? 'danger' : 'warning';
        let bukti = p.bukti_bayar ?
            `<a href="/konveksi/assets/uploads/${p.bukti_bayar}" target="_blank" class="btn btn-sm btn-outline-info">
                   <i class="bi bi-file-earmark-image"></i> Lihat
               </a>` :
            '<span class="text-muted">-</span>';

        let aksi = p.status === 'menunggu' ?
            `<button class="btn btn-sm btn-success" onclick="konfirmasi(${p.id_pembayaran}, 'terkonfirmasi')">
                   <i class="bi bi-check-circle"></i>
               </button>
               <button class="btn btn-sm btn-danger ms-1" onclick="konfirmasi(${p.id_pembayaran}, 'ditolak')">
                   <i class="bi bi-x-circle"></i>
               </button>` :
            '<span class="text-muted">-</span>';

        tbody.append(`
            <tr>
                <td>${i + 1}</td>
                <td>${p.nama_pelanggan}</td>
                <td>#${p.id_transaksi}</td>
                <td>${p.jenis_transaksi.replace(/_/g,' ')}</td>
                <td>${getKeterangan(p)}</td>
                <td>Rp ${parseInt(p.total_harga).toLocaleString('id-ID')}</td>
                <td>Rp ${parseInt(p.jumlah_bayar).toLocaleString('id-ID')}</td>
                <td>${p.metode}</td>
                <td>${bukti}</td>
                <td><span class="badge bg-${badge}">${p.status}</span></td>
                <td>${p.tanggal_pembayaran.substring(0,10)}</td>
                <td>${aksi}</td>
            </tr>
        `);
    });
}

function konfirmasi(id, status) {
    let pesan = status === 'terkonfirmasi' ? 'Konfirmasi pembayaran ini?' : 'Tolak pembayaran ini?';
    if (!confirm(pesan)) return;
    $.post('/konveksi/api/pelunasan.php', {
        action: 'konfirmasi',
        id_pembayaran: id,
        status: status
    }, function(res) {
        if (res.success) {
            load();
        } else alert('Error: ' + res.error);
    }, 'json');
}
</script>