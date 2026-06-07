<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /konveksi/auth/login.php'); exit;
}
require_once '../config/db.php';
$id = $_SESSION['user_id'];
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Transaksi Saya</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <!-- Filter status -->
        <div class="row mb-3 g-2 align-items-center">
            <div class="col-2">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="selesai">Selesai</option>
                    <option value="lunas">Lunas</option>
                    <option value="batal">Batal</option>
                </select>
            </div>
            <div class="col-2">
                <select id="filterJenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="jahit_satuan">Jahit Satuan</option>
                    <option value="pakaian_jadi">Pakaian Jadi</option>
                    <option value="konveksi">Konveksi</option>
                </select>
            </div>
        </div>

        <!-- Tabel transaksi -->
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0" id="tabelTrx">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Jenis</th>
                            <th>Ukuran</th>
                            <th>Total</th>
                            <th>Diskon</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTrx">
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="isiDetail">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success d-none" id="btnBayar">
                    <i class="bi bi-cash-coin"></i> Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Bukti Bayar -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bayar_id_transaksi">
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
                    <label class="form-label">Bukti Pembayaran <small class="text-muted">(opsional,
                            jpg/png/pdf)</small></label>
                    <input type="file" id="bayar_bukti" class="form-control" accept="image/*,.pdf">
                </div>
                <div id="alertBayar"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKirimBayar">
                    <span id="loadingBayar" class="spinner-border spinner-border-sm d-none"></span>
                    Kirim Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let allTrx = [];

$(document).ready(function() {
    loadTrx();

    $('#filterStatus, #filterJenis').on('change', renderTabel);

    $('#btnKirimBayar').click(function() {
        let id = $('#bayar_id_transaksi').val();
        let jumlah = $('#bayar_jumlah').val();
        let metode = $('#bayar_metode').val();

        if (!jumlah || jumlah <= 0) {
            $('#alertBayar').html('<div class="alert alert-warning">Masukkan jumlah pembayaran.</div>');
            return;
        }

        $('#loadingBayar').removeClass('d-none');
        $('#btnKirimBayar').prop('disabled', true);

        let fd = new FormData();
        fd.append('action', 'bayar');
        fd.append('id_transaksi', id);
        fd.append('metode', metode);
        fd.append('jumlah', jumlah);
        if ($('#bayar_bukti')[0].files[0]) {
            fd.append('bukti_bayar', $('#bayar_bukti')[0].files[0]);
        }

        $.ajax({
            url: '/konveksi/api/pelunasan.php',
            type: 'POST',
            data: fd,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#loadingBayar').addClass('d-none');
                $('#btnKirimBayar').prop('disabled', false);
                if (res.success) {
                    $('#modalBayar').modal('hide');
                    alert('Pembayaran berhasil dikirim! Menunggu konfirmasi admin.');
                    loadTrx();
                } else {
                    $('#alertBayar').html('<div class="alert alert-danger">' + res.error +
                        '</div>');
                }
            },
            dataType: 'json'
        });
    });
});

function loadTrx() {
    $.get('/konveksi/api/transaksi.php?action=list', function(data) {
        allTrx = data;
        renderTabel();
    }, 'json');
}

function renderTabel() {
    let st = $('#filterStatus').val();
    let jn = $('#filterJenis').val();
    let filtered = allTrx.filter(t => {
        if (st && t.status !== st) return false;
        if (jn && t.jenis_transaksi !== jn) return false;
        return true;
    });

    let tbody = $('#bodyTrx');
    tbody.empty();

    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada transaksi.</td></tr>');
        return;
    }

    filtered.forEach(t => {
        let badge = {
            lunas: 'success',
            pending: 'warning',
            dikirim: 'info',
            batal: 'danger',
            diproses: 'primary',
            selesai: 'secondary'
        } [t.status] || 'secondary';

        let diskon = parseFloat(t.diskon_total) > 0 ?
            '<span class="text-danger">-Rp ' + parseInt(t.diskon_total).toLocaleString('id-ID') + '</span>' :
            '<span class="text-muted">-</span>';

        let ukuran = t.ukuran ?
            `<span class="badge bg-secondary">${t.ukuran}</span>` :
            '<span class="text-muted">-</span>';

        let btnBayar = (t.status === 'pending' || t.status === 'diproses') ?
            `<button class="btn btn-sm btn-success" onclick="bukaBayar(${t.id_transaksi}, ${t.total_harga})">
                <i class="bi bi-cash"></i>
             </button>` : '';

        tbody.append(`
            <tr>
                <td>${t.id_transaksi}</td>
                <td>${t.jenis_transaksi.replace(/_/g, ' ')}</td>
                <td>${ukuran}</td>
                <td>Rp ${parseInt(t.total_harga).toLocaleString('id-ID')}</td>
                <td>${diskon}</td>
                <td>${t.jenis_pembayaran}</td>
                <td><span class="badge bg-${badge}">${t.status}</span></td>
                <td>${t.tanggal_transaksi.substring(0, 10)}</td>
                <td class="d-flex gap-1">
                    <button class="btn btn-sm btn-info" onclick="lihatDetail(${t.id_transaksi})">
                        <i class="bi bi-eye"></i>
                    </button>
                    ${btnBayar}
                </td>
            </tr>
        `);
    });
}

function lihatDetail(id) {
    $('#isiDetail').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
    $('#btnBayar').addClass('d-none');
    new bootstrap.Modal(document.getElementById('modalDetail')).show();

    $.get('/konveksi/api/transaksi.php?action=detail&id=' + id, function(data) {
        if (!data || data.error) {
            $('#isiDetail').html('<p class="text-danger">Gagal memuat detail.</p>');
            return;
        }

        let html = '';

        // Jika jahit satuan — tampilkan info kustom
        if (data.info) {
            html += `<div class="alert alert-info mb-3">
            <strong>✂️ Pesanan Jahit Satuan</strong><br>
            <strong>Jenis Pakaian:</strong> ${data.info.jenis_pakaian || '-'}<br>
            <strong>Ukuran:</strong> ${data.info.ukuran || '-'}<br>
            <strong>Catatan:</strong> ${data.info.catatan || '-'}<br>
            <strong>Estimasi Selesai:</strong> ${data.info.tanggal_selesai ? data.info.tanggal_selesai.substring(0,10) : '-'}<br>
            <strong>Jumlah:</strong> ${data.info.jumlah} pcs<br>
            <span class="text-warning fw-bold">⏳ Harga akan dikonfirmasi oleh admin.</span>
        </div>`;
        }

        // Tabel produk (jika ada produk nyata)
        if (data.items && data.items.length > 0 && data.items[0].nama_produk) {
            html += '<table class="table table-sm table-bordered">' +
                '<thead class="table-light"><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
            data.items.forEach(d => {
                html += `<tr>
                <td>${d.nama_produk}</td>
                <td>${d.jumlah}</td>
                <td>Rp ${parseInt(d.harga_satuan).toLocaleString('id-ID')}</td>
                <td>Rp ${parseInt(d.subtotal).toLocaleString('id-ID')}</td>
            </tr>`;
            });
            html += '</tbody></table>';
        }

        if (!html) html = '<p class="text-muted">Tidak ada detail tersedia.</p>';
        $('#isiDetail').html(html);
    }, 'json');
}

function bukaBayar(id, total) {
    $('#bayar_id_transaksi').val(id);
    $('#bayar_jumlah').val(total);
    $('#alertBayar').html('');
    $('#bayar_bukti').val('');
    bootstrap.Modal.getInstance(document.getElementById('modalDetail'))?.hide();
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
}
</script>