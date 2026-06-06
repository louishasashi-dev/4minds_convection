<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') { header('Location: /konveksi/auth/login.php'); exit; }
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Pesan Jahit Satuan</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="row mb-3 g-2">
            <div class="col-auto">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="disetujui">Disetujui</option>
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
                            <th>Jenis Pakaian</th>
                            <th>Jumlah</th>
                            <th>Ukuran</th>
                            <th>Catatan</th>
                            <th>Estimasi</th>
                            <th>Status</th>
                            <th>Harga</th>
                            <th>Tanggal</th>
                            <th>Desain</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyPesan">
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

<!-- Modal Setujui -->
<div class="modal fade" id="modalSetujui" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Setujui Permintaan Jahit Satuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="setujui_id_pesan">
                <div id="infoSetujui" class="alert alert-info mb-3"></div>
                <div class="mb-3">
                    <label class="form-label">Total Harga (Rp)</label>
                    <input type="number" id="setujui_harga" class="form-control" placeholder="Masukkan total harga">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Pembayaran</label>
                    <select id="setujui_jp" class="form-select">
                        <option value="dp">DP 50%</option>
                        <option value="lunas">Lunas</option>
                        <option value="cod">COD</option>
                    </select>
                </div>
                <div id="alertSetujui"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKirimSetujui">
                    <span id="loadingSetujui" class="spinner-border spinner-border-sm d-none"></span>
                    Setujui & Buat Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let allPesan = [];

$(document).ready(function() {
    load();
    $('#filterStatus').on('change', render);

    $('#btnKirimSetujui').click(function() {
        let id = $('#setujui_id_pesan').val();
        let harga = $('#setujui_harga').val();
        let jp = $('#setujui_jp').val();

        if (!harga || harga <= 0) {
            $('#alertSetujui').html(
                '<div class="alert alert-warning">Masukkan harga terlebih dahulu.</div>');
            return;
        }

        $('#loadingSetujui').removeClass('d-none');
        $('#btnKirimSetujui').prop('disabled', true);

        $.post('/konveksi/api/pesan_jahit.php', {
            action: 'setujui',
            id_pesan: id,
            harga: harga,
            jenis_pembayaran: jp
        }, function(res) {
            $('#loadingSetujui').addClass('d-none');
            $('#btnKirimSetujui').prop('disabled', false);
            if (res.success) {
                $('#modalSetujui').modal('hide');
                alert('✅ Permintaan disetujui! Transaksi #' + res.id_transaksi +
                    ' berhasil dibuat.');
                load();
            } else {
                $('#alertSetujui').html('<div class="alert alert-danger">Error: ' + res.error +
                    '</div>');
            }
        }, 'json');
    });
});

function load() {
    $.get('/konveksi/api/pesan_jahit.php?action=list', function(data) {
        allPesan = data;
        render();
    }, 'json');
}

function render() {
    let st = $('#filterStatus').val();
    let tbody = $('#bodyPesan');
    tbody.empty();

    let filtered = allPesan.filter(d => !st || d.status === st);
    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="12" class="text-center text-muted py-4">Tidak ada data.</td></tr>');
        return;
    }

    filtered.forEach((p, i) => {
        let badge = p.status === 'disetujui' ? 'success' : p.status === 'ditolak' ? 'danger' : 'warning';
        let harga = p.harga_disetujui > 0 ? 'Rp ' + parseInt(p.harga_disetujui).toLocaleString('id-ID') : '-';
        let estimasi = p.estimasi_selesai ? p.estimasi_selesai.substring(0, 10) : '-';
        let catatan = p.catatan ? '<span title="' + p.catatan + '">' + p.catatan.substring(0, 30) + (p.catatan
            .length > 30 ? '...' : '') + '</span>' : '-';

        let aksi = '';
        if (p.status === 'menunggu') {
            aksi = `
                <button class="btn btn-sm btn-success" onclick="bukaSetujui(${p.id_pesan}, '${p.nama_pelanggan}', '${p.jenis_pakaian}', '${p.jumlah}')">
                    <i class="bi bi-check-circle"></i> Setujui
                </button>
                <button class="btn btn-sm btn-danger ms-1" onclick="tolak(${p.id_pesan})">
                    <i class="bi bi-x-circle"></i> Tolak
                </button>`;
        } else if (p.status === 'disetujui') {
            aksi =
                '<a href="/konveksi/admin/transaksi.php" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Lihat Transaksi</a>';
        } else {
            aksi = '<span class="text-muted">-</span>';
        }

        // Tentukan tampilan file desain
        let desainHtml = '-';
        if (p.file_desain) {
            let url = '/konveksi/assets/uploads/' + p.file_desain;
            if (p.file_desain.toLowerCase().endsWith('.pdf')) {
                desainHtml = `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-danger py-0">
                    <i class="bi bi-file-earmark-pdf"></i> PDF</a>`;
            } else {
                desainHtml = `<a href="${url}" target="_blank">
                    <img src="${url}" style="height:40px;border-radius:4px;object-fit:cover"></a>`;
            }
        }

        tbody.append(`
            <tr>
                <td>${i+1}</td>
                <td>${p.nama_pelanggan}</td>
                <td>${p.jenis_pakaian}</td>
                <td>${p.jumlah} pcs</td>
                <td>${p.ukuran || '-'}</td>
                <td>${catatan}</td>
                <td>${estimasi}</td>
                <td><span class="badge bg-${badge}">${p.status}</span></td>
                <td>${harga}</td>
                <td>${p.created_at.substring(0,10)}</td>
                <td>${desainHtml}</td>
                <td>${aksi}</td>
            </tr>
        `);
    });
}

function bukaSetujui(id, nama, jenis, jumlah) {
    $('#setujui_id_pesan').val(id);
    $('#setujui_harga').val('');
    $('#alertSetujui').html('');
    $('#infoSetujui').html(`
        <strong>Pelanggan:</strong> ${nama}<br>
        <strong>Jenis:</strong> ${jenis}<br>
        <strong>Jumlah:</strong> ${jumlah} pcs
    `);
    new bootstrap.Modal(document.getElementById('modalSetujui')).show();
}

function tolak(id) {
    if (!confirm('Tolak permintaan ini?')) return;
    $.post('/konveksi/api/pesan_jahit.php', {
        action: 'tolak',
        id_pesan: id
    }, function(res) {
        if (res.success) load();
        else alert('Error: ' + res.error);
    }, 'json');
}
</script>