<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php'); exit;
}
require_once '../config/db.php';
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Kelola Pengiriman</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <!--
            PERBAIKAN UTAMA: col-auto diganti col-12 col-sm-auto,
            dan select diberi min-width: 160px.
            Tanpa min-width, "Semua Status" tidak muat karena form-select-sm
            punya padding kecil dan browser menghitung lebar dari option terpendek.
        -->
        <div class="row mb-3 g-2">
            <div class="col-12 col-sm-auto">
                <select id="filterStatus" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">Semua Status</option>
                    <option value="belum_dikirim">Belum Dikirim</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="sampai">Sampai</option>
                </select>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <!-- PERBAIKAN: Tambah table-responsive wrapper -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Pelanggan</th>
                                <th>Transaksi</th>
                                <th>Ekspedisi</th>
                                <th>No Resi</th>
                                <th>Tgl Kirim</th>
                                <th>Estimasi</th>
                                <th>Tgl Tiba</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyPengiriman">
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
</div>

<!-- Modal Proses Kirim -->
<div class="modal fade" id="modalKirim" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Data Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kirim_id_transaksi">
                <p class="text-muted">Transaksi: <strong id="kirim_label"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Kurir</label>
                    <input type="text" id="kirim_kurir" class="form-control" placeholder="Contoh: JNE, J&T, SiCepat">
                </div>
                <div class="mb-3">
                    <label class="form-label">No Resi</label>
                    <input type="text" id="kirim_resi" class="form-control" placeholder="Masukkan nomor resi">
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Tanggal Kirim</label>
                        <input type="date" id="kirim_tgl" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Estimasi Sampai</label>
                        <input type="date" id="kirim_estimasi" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanKirim">
                    <i class="bi bi-truck"></i> Proses Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let allData = [];

$(document).ready(function() {
    let today = new Date().toISOString().split('T')[0];
    $('#kirim_tgl').val(today);

    load();
    $('#filterStatus').on('change', render);

    $('#btnSimpanKirim').click(function() {
        let id = $('#kirim_id_transaksi').val();
        let kurir = $('#kirim_kurir').val().trim();
        let resi = $('#kirim_resi').val().trim();
        let tgl = $('#kirim_tgl').val();
        let estimasi = $('#kirim_estimasi').val();

        if (!kurir || !tgl) {
            alert('Kurir dan tanggal kirim wajib diisi!');
            return;
        }

        $.post('/konveksi/api/pengiriman.php', {
            action: 'proses',
            id_transaksi: id,
            kurir,
            no_resi: resi,
            tanggal_kirim: tgl,
            estimasi_sampai: estimasi
        }, function(res) {
            if (res.success) {
                $('#modalKirim').modal('hide');
                alert('Pengiriman berhasil diproses!');
                load();
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });
});

function load() {
    $.get('/konveksi/api/pengiriman.php?action=list', function(data) {
        allData = data;
        render();
    }, 'json');
}

function render() {
    let st = $('#filterStatus').val();
    let tbody = $('#bodyPengiriman');
    tbody.empty();

    let filtered = allData.filter(d => !st || d.status === st);
    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="10" class="text-center text-muted py-4">Tidak ada data pengiriman.</td></tr>');
        return;
    }

    filtered.forEach((p, i) => {
        let badge = p.status === 'sampai' ? 'success' : p.status === 'dikirim' ? 'info' : 'secondary';
        let aksi = '';
        if (p.status === 'belum_dikirim' || !p.status) {
            aksi = `<button class="btn btn-sm btn-primary" onclick="bukaKirim(${p.id_transaksi}, '#${p.id_transaksi}')">
                        <i class="bi bi-truck"></i> Kirim
                    </button>`;
        } else if (p.status === 'dikirim') {
            aksi = `<button class="btn btn-sm btn-success" onclick="tandaiSampai(${p.id_transaksi})">
                        <i class="bi bi-check2-all"></i> Sampai
                    </button>`;
        }

        tbody.append(`
            <tr>
                <td>${i + 1}</td>
                <td>${p.nama_pelanggan}</td>
                <td>#${p.id_transaksi} <small>(${p.jenis_transaksi.replace(/_/g,' ')})</small></td>
                <td>${p.kurir || '-'}</td>
                <td>${p.no_resi ? `<code>${p.no_resi}</code>` : '-'}</td>
                <td>${p.tanggal_kirim   || '-'}</td>
                <td>${p.estimasi_sampai || '-'}</td>
                <td>${p.tanggal_tiba    || '-'}</td>
                <td><span class="badge bg-${badge}">${p.status || 'belum_dikirim'}</span></td>
                <td>${aksi}</td>
            </tr>
        `);
    });
}

function bukaKirim(id, label) {
    $('#kirim_id_transaksi').val(id);
    $('#kirim_label').text(label);
    $('#kirim_kurir, #kirim_resi, #kirim_estimasi').val('');
    new bootstrap.Modal(document.getElementById('modalKirim')).show();
}

function tandaiSampai(id) {
    if (!confirm('Tandai paket ini sudah sampai ke pelanggan?')) return;
    $.post('/konveksi/api/pengiriman.php', {
        action: 'sampai',
        id_transaksi: id
    }, function(res) {
        if (res.success) load();
        else alert('Error: ' + res.error);
    }, 'json');
}
</script>