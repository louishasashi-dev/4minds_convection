<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') { header('Location: /konveksi/auth/login.php'); exit; }
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Jasa Konveksi</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <!-- Filter Status -->
        <div class="row mb-3 g-2">
            <div class="col-12 col-sm-auto">
                <select id="filterStatus" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="selesai">Selesai</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="lunas">Lunas</option>
                    <option value="batal">Batal</option>
                </select>
            </div>
            <div class="col-12 col-sm-auto">
                <input type="text" id="searchInput" class="form-control form-control-sm"
                    placeholder="🔍 Cari pelanggan...">
            </div>
            <div class="col-12 col-sm-auto ms-auto">
                <button class="btn btn-sm btn-success" onclick="exportExcel()">
                    <i class="bi bi-file-earmark-excel"></i> Export
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-building"></i> Daftar Pesanan Jasa Konveksi
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Ukuran</th>
                                <th>Total Harga</th>
                                <th>Status Pesanan</th>
                                <th>Status Bayar</th>
                                <th>Desain</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyKonveksi">
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
</div>

<!-- Modal Update Status -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_id">
                <div id="infoUpdate" class="alert alert-info mb-3"></div>
                <div class="mb-3">
                    <label class="form-label">Status Pesanan</label>
                    <select id="update_status" class="form-select">
                        <option value="pending">⏳ Pending</option>
                        <option value="diproses">⚙️ Diproses</option>
                        <option value="dikirim">📦 Dikirim</option>
                        <option value="selesai">✅ Selesai</option>
                        <option value="batal">❌ Batal</option>
                    </select>
                </div>
                <div class="mb-3" id="pengirimanBox" style="display:none;">
                    <label class="form-label">Kurir</label>
                    <input type="text" id="update_kurir" class="form-control mb-2" placeholder="JNE/JNT/SiCepat">
                    <label class="form-label">No. Resi</label>
                    <input type="text" id="update_resi" class="form-control" placeholder="Nomor Resi">
                </div>
                <div id="alertUpdate"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnUpdate">
                    <span id="loadingUpdate" class="spinner-border spinner-border-sm d-none"></span>
                    Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="modalKonfirmasiBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="bayar_id_pembayaran">
                <div id="infoBayar" class="alert alert-info mb-3"></div>
                <div class="mb-3">
                    <label class="form-label">Status Pembayaran</label>
                    <select id="bayar_status" class="form-select">
                        <option value="terkonfirmasi">✅ Terkonfirmasi</option>
                        <option value="ditolak">❌ Ditolak</option>
                    </select>
                </div>
                <div id="alertBayar"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKonfirmasiBayar">
                    <span id="loadingBayar" class="spinner-border spinner-border-sm d-none"></span>
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

<script>
let allData = [];
let searchTerm = '';

$(document).ready(function() {
    loadData();
    $('#filterStatus').on('change', renderTable);
    $('#searchInput').on('keyup', function() {
        searchTerm = $(this).val().toLowerCase();
        renderTable();
    });

    $('#btnUpdate').click(updateStatus);
    $('#btnKonfirmasiBayar').click(konfirmasiBayar);
});

function loadData() {
    $('#bodyKonveksi').html('<tr><td colspan="12" class="text-center"><div class="spinner-border"></div></td></tr>');
    $.get('/konveksi/api/transaksi.php?action=list&jenis=konveksi', function(data) {
        allData = data;
        renderTable();
    }).fail(function() {
        $('#bodyKonveksi').html(
            '<tr><td colspan="12" class="text-center text-danger">Gagal memuat data</td></tr>');
    });
}

function renderTable() {
    let statusFilter = $('#filterStatus').val();
    let filtered = allData.filter(t => {
        if (statusFilter && t.status !== statusFilter) return false;
        if (searchTerm) {
            let nama = (t.nama_pelanggan || '').toLowerCase();
            if (!nama.includes(searchTerm)) return false;
        }
        return true;
    });

    let tbody = $('#bodyKonveksi');
    tbody.empty();

    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="12" class="text-center text-muted">Tidak ada data</td></tr>');
        return;
    }

    filtered.forEach((t, i) => {
        let produkList = '';
        let totalQty = 0;
        if (t.items && t.items.length > 0) {
            produkList = t.items.map(item => item.nama_produk || 'Produk').join(', ');
            totalQty = t.items.reduce((sum, item) => sum + parseInt(item.jumlah), 0);
        } else {
            produkList = '-';
        }

        let statusBadge = getStatusBadge(t.status);
        let paymentBadge = (t.status_pembayaran === 'terkonfirmasi') ?
            '<span class="badge bg-success">Lunas</span>' :
            '<span class="badge bg-warning">Menunggu</span>';

        let desainHtml = '-';
        if (t.file_desain) {
            let url = '/konveksi/assets/uploads/' + t.file_desain;
            desainHtml = `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-info">Lihat</a>`;
        }

        let aksi = `
            <button class="btn btn-sm btn-info" onclick="lihatDetail(${t.id_transaksi})" title="Detail">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-warning" onclick="bukaUpdate(${t.id_transaksi}, '${t.nama_pelanggan}', '${t.status}')" title="Update Status">
                <i class="bi bi-pencil"></i>
            </button>
        `;

        // Tombol konfirmasi bayar (hanya jika status bayar masih menunggu dan ada bukti bayar)
        if (t.status_pembayaran === 'menunggu' && t.bukti_bayar) {
            let jumlahBayar = t.jumlah_bayar || t.total_harga;
            aksi += `<button class="btn btn-sm btn-success" onclick="bukaKonfirmasiBayar(${t.id_pembayaran}, '${t.nama_pelanggan}', ${jumlahBayar})" title="Konfirmasi Bayar">
                <i class="bi bi-credit-card"></i> Konfirmasi Bayar
            </button>`;
        }

        if (t.status === 'dikirim') {
            aksi += `<button class="btn btn-sm btn-success" onclick="updateJadiSampai(${t.id_transaksi})" title="Tandai Sampai">
                <i class="bi bi-check-circle"></i>
            </button>`;
        }

        tbody.append(`
            <tr>
                <td>${i+1}</td>
                <td>${t.tanggal_transaksi ? t.tanggal_transaksi.substring(0,10) : '-'}</td>
                <td><strong>${t.nama_pelanggan || '-'}</strong><br><small>${t.email_pelanggan || ''}</small></td>
                <td>${produkList}</td>
                <td>${totalQty} pcs</td>
                <td><span class="badge bg-secondary">${t.ukuran || '-'}</span></td>
                <td>Rp ${parseInt(t.total_harga || 0).toLocaleString('id-ID')}</td>
                <td>${statusBadge}</td>
                <td>${paymentBadge}</td>
                <td>${desainHtml}</td>
                <td>${aksi}</td>
            </tr>
        `);
    });
}

function getStatusBadge(status) {
    const map = {
        'pending': '<span class="badge bg-secondary">Pending</span>',
        'diproses': '<span class="badge bg-primary">Diproses</span>',
        'dikirim': '<span class="badge bg-info">Dikirim</span>',
        'selesai': '<span class="badge bg-success">Selesai</span>',
        'lunas': '<span class="badge bg-success">Lunas</span>',
        'batal': '<span class="badge bg-danger">Batal</span>'
    };
    return map[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function bukaUpdate(id, nama, statusSekarang) {
    $('#update_id').val(id);
    $('#update_status').val(statusSekarang);
    $('#infoUpdate').html(`<strong>Pelanggan:</strong> ${nama}<br><strong>Status saat ini:</strong> ${statusSekarang}`);
    $('#alertUpdate').html('');
    $('#pengirimanBox').toggle(statusSekarang === 'dikirim');
    $('#update_kurir').val('');
    $('#update_resi').val('');
    new bootstrap.Modal(document.getElementById('modalUpdateStatus')).show();
}

function updateStatus() {
    let id = $('#update_id').val();
    let status = $('#update_status').val();
    let kurir = $('#update_kurir').val();
    let resi = $('#update_resi').val();

    $('#loadingUpdate').removeClass('d-none');
    $('#btnUpdate').prop('disabled', true);

    let data = {
        action: 'update_status',
        id_transaksi: id,
        status: status
    };
    if (kurir) data.kurir = kurir;
    if (resi) data.no_resi = resi;

    $.post('/konveksi/api/transaksi.php', data, function(res) {
        $('#loadingUpdate').addClass('d-none');
        $('#btnUpdate').prop('disabled', false);
        if (res.success) {
            $('#modalUpdateStatus').modal('hide');
            alert('✅ Status berhasil diupdate!');
            loadData();
        } else {
            $('#alertUpdate').html(`<div class="alert alert-danger">${res.error || 'Gagal update'}</div>`);
        }
    }, 'json').fail(function() {
        $('#loadingUpdate').addClass('d-none');
        $('#btnUpdate').prop('disabled', false);
        $('#alertUpdate').html('<div class="alert alert-danger">Gagal koneksi ke server</div>');
    });
}

function bukaKonfirmasiBayar(idPembayaran, nama, jumlah) {
    $('#bayar_id_pembayaran').val(idPembayaran);
    $('#infoBayar').html(`
        <strong>Pelanggan:</strong> ${nama}<br>
        <strong>Jumlah Bayar:</strong> Rp ${parseInt(jumlah).toLocaleString('id-ID')}
    `);
    $('#bayar_status').val('terkonfirmasi');
    $('#alertBayar').html('');
    new bootstrap.Modal(document.getElementById('modalKonfirmasiBayar')).show();
}

function konfirmasiBayar() {
    let id = $('#bayar_id_pembayaran').val();
    let status = $('#bayar_status').val();

    $('#loadingBayar').removeClass('d-none');
    $('#btnKonfirmasiBayar').prop('disabled', true);

    $.post('/konveksi/api/pelunasan.php', {
        action: 'konfirmasi',
        id_pembayaran: id,
        status: status
    }, function(res) {
        $('#loadingBayar').addClass('d-none');
        $('#btnKonfirmasiBayar').prop('disabled', false);
        if (res.success) {
            $('#modalKonfirmasiBayar').modal('hide');
            alert('✅ Pembayaran berhasil dikonfirmasi!');
            loadData();
        } else {
            $('#alertBayar').html('<div class="alert alert-danger">Error: ' + (res.error ||
                'Gagal konfirmasi') + '</div>');
        }
    }, 'json').fail(function(xhr) {
        $('#loadingBayar').addClass('d-none');
        $('#btnKonfirmasiBayar').prop('disabled', false);
        $('#alertBayar').html('<div class="alert alert-danger">Gagal koneksi ke server: ' + xhr.status +
            '</div>');
    });
}

function updateJadiSampai(id) {
    if (!confirm('Konfirmasi bahwa barang sudah sampai ke pelanggan?')) return;
    $.post('/konveksi/api/transaksi.php', {
        action: 'update_status',
        id_transaksi: id,
        status: 'selesai'
    }, function(res) {
        if (res.success) {
            alert('✅ Status diubah menjadi Selesai!');
            loadData();
        } else {
            alert('Gagal update status');
        }
    }, 'json');
}

function lihatDetail(id) {
    $('#detailBody').html('<div class="text-center"><div class="spinner-border"></div></div>');
    new bootstrap.Modal(document.getElementById('modalDetail')).show();

    $.get('/konveksi/api/transaksi.php?action=detail&id=' + id, function(t) {
        let itemsHtml =
            '<table class="table table-sm"><thead class="table-light"><tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
        if (t.items && t.items.length > 0) {
            t.items.forEach(item => {
                itemsHtml += `<tr>
                    <td>${item.nama_produk || '-'}</td>
                    <td>${item.jumlah} pcs</td>
                    <td>Rp ${parseInt(item.harga_satuan || 0).toLocaleString('id-ID')}</td>
                    <td>Rp ${parseInt(item.subtotal || 0).toLocaleString('id-ID')}</td>
                </tr>`;
            });
        }
        itemsHtml += '</tbody></table>';

        $('#detailBody').html(`
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr><th width="40%">ID Transaksi</th><td>#${t.id_transaksi}</td></tr>
                        <tr><th>Pelanggan</th><td><strong>${t.nama_pelanggan}</strong><br>${t.email_pelanggan || '-'}</td></tr>
                        <tr><th>Tanggal</th><td>${t.tanggal_transaksi}</td></tr>
                        <tr><th>Ukuran</th><td>${t.ukuran || '-'}</td></tr>
                        <tr><th>Deskripsi</th><td>${t.deskripsi || '-'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr><th>Total Harga</th><td class="fw-bold text-success">Rp ${parseInt(t.total_harga || 0).toLocaleString('id-ID')}</td></tr>
                        <tr><th>Diskon</th><td class="text-danger">Rp ${parseInt(t.diskon_total || 0).toLocaleString('id-ID')}</td></tr>
                        <tr><th>Status</th><td>${getStatusBadge(t.status)}</td></tr>
                        <tr><th>Status Bayar</th><td>${t.status_pembayaran === 'terkonfirmasi' ? '✅ Terkonfirmasi' : '⏳ Menunggu'}</td></tr>
                    </table>
                </div>
                <div class="col-12 mt-3">
                    <h6>Detail Produk</h6>
                    ${itemsHtml}
                </div>
            </div>
        `);
    }, 'json');
}

function exportExcel() {
    window.open('/konveksi/api/transaksi.php?action=export_excel&jenis=konveksi', '_blank');
}
</script>