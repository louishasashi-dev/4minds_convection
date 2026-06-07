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
            <div class="col-12 col-sm-auto">
                <select id="filterStatus" class="form-select form-select-sm" style="min-width: 160px;">
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
                            <th>ID Trx</th>
                            <th>Keterangan</th>
                            <th>Total Transaksi</th>
                            <th>Jumlah Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPelunasan">
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

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered mb-3">
                    <tr>
                        <th style="width:35%">Jenis Transaksi</th>
                        <td id="detail_jenis"></td>
                    </tr>
                    <tr>
                        <th>Metode</th>
                        <td id="detail_metode"></td>
                    </tr>
                    <tr>
                        <th>Tanggal & Waktu</th>
                        <td id="detail_tanggal"></td>
                    </tr>
                </table>
                <div id="detail_bukti_wrap">
                    <p class="fw-semibold mb-2">Bukti Pembayaran</p>
                    <div id="detail_bukti"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
    if (p.jenis_pembayaran === 'lunas' || p.jenis_pembayaran === 'cod') {
        return '<span class="badge bg-primary">Bayar Lunas</span>';
    }
    if (p.jenis_pembayaran !== 'dp') return '-';
    if (parseInt(p.urutan_bayar) === 1) {
        return '<span class="badge bg-info text-dark">Pembayaran<br>DP 50%</span>';
    } else {
        return '<span class="badge bg-success">Pelunasan sisa<br>DP 50%</span>';
    }
}

function render() {
    let st = $('#filterStatus').val();
    let tbody = $('#bodyPelunasan');
    tbody.empty();

    let filtered = allData.filter(d => !st || d.status === st);
    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>');
        return;
    }

    filtered.forEach((p, i) => {
        let badge = p.status === 'terkonfirmasi' ? 'success' : p.status === 'ditolak' ? 'danger' : 'warning';

        let aksi = `<button class="btn btn-sm btn-info me-1" onclick="bukaDetail(${i})">
                        <i class="bi bi-eye"></i> Detail
                    </button>`;

        if (p.status === 'menunggu') {
            aksi += `
                <button class="btn btn-sm btn-success me-1" onclick="konfirmasi(${p.id_pembayaran}, 'terkonfirmasi')">
                    <i class="bi bi-check-circle"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="konfirmasi(${p.id_pembayaran}, 'ditolak')">
                    <i class="bi bi-x-circle"></i>
                </button>`;
        }

        tbody.append(`
            <tr>
                <td>${i + 1}</td>
                <td>${p.nama_pelanggan}</td>
                <td>#${p.id_transaksi}</td>
                <td>${getKeterangan(p)}</td>
                <td>Rp ${parseInt(p.total_harga).toLocaleString('id-ID')}</td>
                <td>Rp ${parseInt(p.jumlah_bayar).toLocaleString('id-ID')}</td>
                <td><span class="badge bg-${badge}">${p.status}</span></td>
                <td style="white-space:nowrap">${aksi}</td>
            </tr>
        `);
    });
}

function bukaDetail(index) {
    let st = $('#filterStatus').val();
    let filtered = allData.filter(d => !st || d.status === st);
    let p = filtered[index];

    $('#detail_jenis').text(p.jenis_transaksi.replace(/_/g, ' '));
    $('#detail_metode').text(p.metode);
    $('#detail_tanggal').text(p.tanggal_pembayaran.substring(0, 20));

    if (p.bukti_bayar) {
        let url = '/konveksi/assets/uploads/' + p.bukti_bayar;
        $('#detail_bukti').html(`
            <a href="${url}" target="_blank">
                <img src="${url}" style="width:100%;aspect-ratio:4/5;object-fit:cover;border-radius:8px">
            </a>
        `);
    } else {
        $('#detail_bukti').html(`
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-circle"></i> Pelanggan tidak upload bukti pembayaran.
            </div>
        `);
    }

    new bootstrap.Modal(document.getElementById('modalDetail')).show();
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