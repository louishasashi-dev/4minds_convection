<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Pelunasan / Pembayaran</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Halaman ini menampilkan seluruh riwayat pembayaran Anda. Upload bukti transfer untuk tagihan yang masih
            <strong>menunggu</strong> konfirmasi.
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Transaksi</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Total Transaksi</th>
                            <th>Jumlah Bayar</th>
                            <th>Metode</th>
                            <th>Status Bayar</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPelunasan">
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Upload Bukti -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bayar_id_transaksi">
                <p class="text-muted mb-3">Transaksi ID: <strong id="label_id_transaksi"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Jumlah yang Dibayar (Rp)</label>
                    <input type="number" id="bayar_jumlah" class="form-control" placeholder="Masukkan nominal">
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select id="bayar_metode" class="form-select">
                        <option value="transfer">Transfer Bank</option>
                        <option value="tunai">Tunai</option>
                        <option value="cod">COD</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bukti Pembayaran <small class="text-muted">(opsional)</small></label>
                    <input type="file" id="bayar_bukti" class="form-control" accept="image/*,.pdf">
                </div>
                <div id="alertBayar"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKirim">
                    <span id="loadingBayar" class="spinner-border spinner-border-sm d-none"></span>
                    Kirim Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    loadPelunasan();

    $('#btnKirim').click(function() {
        let id = $('#bayar_id_transaksi').val();
        let jumlah = $('#bayar_jumlah').val();
        if (!jumlah || jumlah <= 0) {
            $('#alertBayar').html('<div class="alert alert-warning">Masukkan jumlah pembayaran.</div>');
            return;
        }
        $('#loadingBayar').removeClass('d-none');
        $('#btnKirim').prop('disabled', true);

        let fd = new FormData();
        fd.append('action', 'bayar');
        fd.append('id_transaksi', id);
        fd.append('metode', $('#bayar_metode').val());
        fd.append('jumlah', jumlah);
        if ($('#bayar_bukti')[0].files[0]) fd.append('bukti_bayar', $('#bayar_bukti')[0].files[0]);

        $.ajax({
            url: '/konveksi/api/pelunasan.php',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#loadingBayar').addClass('d-none');
                $('#btnKirim').prop('disabled', false);
                if (res.success) {
                    $('#modalBayar').modal('hide');
                    alert('Pembayaran dikirim! Menunggu konfirmasi admin.');
                    loadPelunasan();
                } else {
                    $('#alertBayar').html('<div class="alert alert-danger">' + res.error +
                        '</div>');
                }
            },
            dataType: 'json'
        });
    });
});

function getKeterangan(p) {
    if (p.jenis_pembayaran !== 'dp') return '-';
    if (parseInt(p.urutan_bayar) === 1) {
        return '<span class="badge bg-info text-dark">Pembayaran DP 50%</span>';
    } else {
        return '<span class="badge bg-secondary">Pelunasan sisa DP (50% sebelumnya telah dibayarkan)</span>';
    }
}

function loadPelunasan() {
    $.get('/konveksi/api/pelunasan.php?action=list', function(data) {
        let tbody = $('#bodyPelunasan');
        tbody.empty();
        if (data.length === 0) {
            tbody.html(
                '<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada tagihan yang perlu dibayar.</td></tr>'
            );
            return;
        }
        data.forEach((p, i) => {
            let badge = p.status === 'terkonfirmasi' ? 'success' : p.status === 'ditolak' ? 'danger' :
                'warning';
            let aksi = p.status === 'menunggu' ?
                `<button class="btn btn-sm btn-primary" onclick="bukaBayar(${p.id_transaksi})">
                       <i class="bi bi-upload"></i> Upload
                   </button>` :
                '<span class="text-muted">-</span>';
            let bukti = p.bukti_bayar ?
                `<a href="/konveksi/assets/uploads/${p.bukti_bayar}" target="_blank" class="btn btn-sm btn-outline-secondary">
                       <i class="bi bi-file-earmark"></i>
                   </a>` :
                '';
            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>#${p.id_transaksi}</td>
                    <td>${p.jenis_transaksi.replace(/_/g,' ')}</td>
                    <td>${getKeterangan(p)}</td>
                    <td>Rp ${parseInt(p.total_harga).toLocaleString('id-ID')}</td>
                    <td>Rp ${parseInt(p.jumlah_bayar).toLocaleString('id-ID')}</td>
                    <td>${p.metode}</td>
                    <td><span class="badge bg-${badge}">${p.status}</span></td>
                    <td>${p.tanggal_pembayaran.substring(0,10)}</td>
                    <td class="d-flex gap-1">${aksi}${bukti}</td>
                </tr>
            `);
        });
    }, 'json');
}

function bukaBayar(id) {
    $('#bayar_id_transaksi').val(id);
    $('#label_id_transaksi').text('#' + id);
    $('#bayar_jumlah').val('');
    $('#alertBayar').html('');
    $('#bayar_bukti').val('');
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
}
</script>