<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Dokumen Transaksi</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="row mb-3 g-2">
            <div class="col-auto">
                <select id="filterJenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="kwitansi">Kwitansi</option>
                    <option value="invoice">Invoice</option>
                    <option value="nota">Nota</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="text" id="cariPelanggan" class="form-control form-control-sm"
                    placeholder="Cari nama pelanggan...">
            </div>
        </div>

        <!-- Tabel daftar dokumen yang sudah pernah dicetak -->
        <div class="card mb-4">
            <div class="card-header fw-bold">Riwayat Dokumen Dicetak</div>
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Transaksi</th>
                            <th>Jenis Dokumen</th>
                            <th>Jenis Transaksi</th>
                            <th>Total</th>
                            <th>Tanggal Cetak</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyDokumen">
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Generate Dokumen Baru -->
        <div class="card">
            <div class="card-header fw-bold"><i class="bi bi-plus-circle"></i> Buat / Cetak Dokumen Baru</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">ID Transaksi</label>
                        <input type="number" id="gen_id_transaksi" class="form-control"
                            placeholder="Masukkan ID transaksi">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Dokumen</label>
                        <select id="gen_jenis" class="form-select">
                            <option value="kwitansi">Kwitansi</option>
                            <option value="invoice">Invoice</option>
                            <option value="nota">Nota Pesanan</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="generateDokumen()">
                            <i class="bi bi-file-earmark-plus"></i> Generate & Cetak
                        </button>
                    </div>
                </div>
                <div id="alertGen" class="mt-3"></div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Preview Dokumen -->
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="cetakDokumen()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let allDokumen = [];

$(document).ready(function() {
    loadDokumen();
    $('#filterJenis, #cariPelanggan').on('input change', renderDokumen);
});

function loadDokumen() {
    $.get('/konveksi/api/dokumen.php?action=list', function(data) {
        allDokumen = data;
        renderDokumen();
    }, 'json');
}

function renderDokumen() {
    let jenis = $('#filterJenis').val();
    let cari = $('#cariPelanggan').val().toLowerCase();
    let tbody = $('#bodyDokumen');
    tbody.empty();

    let filtered = allDokumen.filter(d =>
        (!jenis || d.jenis_dokumen === jenis) &&
        (!cari || d.nama_pelanggan.toLowerCase().includes(cari))
    );

    if (filtered.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center text-muted py-4">Tidak ada dokumen.</td></tr>');
        return;
    }

    filtered.forEach((d, i) => {
        let badgeMap = {
            kwitansi: 'primary',
            invoice: 'info',
            nota: 'secondary'
        };
        let b = badgeMap[d.jenis_dokumen] || 'secondary';
        tbody.append(`
            <tr>
                <td>${i+1}</td>
                <td>${d.nama_pelanggan}</td>
                <td>#${d.id_transaksi}</td>
                <td><span class="badge bg-${b}">${d.jenis_dokumen}</span></td>
                <td>${d.jenis_transaksi.replace(/_/g,' ')}</td>
                <td>Rp ${parseInt(d.total_harga).toLocaleString('id-ID')}</td>
                <td>${d.tanggal_cetak.substring(0,10)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="previewDokumen(${d.id_dokumen}, '${d.jenis_dokumen}')">
                        <i class="bi bi-eye"></i> Preview
                    </button>
                </td>
            </tr>
        `);
    });
}

function generateDokumen() {
    let id_trx = $('#gen_id_transaksi').val();
    let jenis = $('#gen_jenis').val();
    if (!id_trx) {
        $('#alertGen').html('<div class="alert alert-warning">Masukkan ID transaksi.</div>');
        return;
    }

    $.post('/konveksi/api/dokumen.php', {
        action: 'generate',
        id_transaksi: id_trx,
        jenis_dokumen: jenis
    }, function(res) {
        if (res.success) {
            $('#alertGen').html('<div class="alert alert-success">Dokumen berhasil dibuat!</div>');
            loadDokumen();
            previewDokumen(res.id_dokumen, res.jenis);
        } else {
            $('#alertGen').html('<div class="alert alert-danger">' + (res.error || 'Gagal') + '</div>');
        }
    }, 'json');
}

function previewDokumen(id_dokumen, jenis) {
    $('#previewTitle').text('Preview ' + jenis.charAt(0).toUpperCase() + jenis.slice(1));
    $('#previewBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
    new bootstrap.Modal(document.getElementById('modalPreview')).show();

    $.get('/konveksi/api/dokumen.php?action=get&id=' + id_dokumen, function(d) {
        if (d.error) {
            $('#previewBody').html('<div class="alert alert-danger">' + d.error + '</div>');
            return;
        }
        $('#previewBody').html(buildDokumenHTML(d));
    }, 'json');
}

function buildDokumenHTML(d) {
    let judulMap = {
        kwitansi: 'KWITANSI PEMBAYARAN',
        invoice: 'INVOICE',
        nota: 'NOTA PESANAN'
    };
    let judul = judulMap[d.jenis_dokumen] || 'DOKUMEN';

    let rows = '';
    (d.detail || []).forEach(item => {
        rows += `<tr>
            <td>${item.nama_produk}</td>
            <td class="text-center">${item.jumlah}</td>
            <td class="text-end">Rp ${parseInt(item.harga_satuan).toLocaleString('id-ID')}</td>
            <td class="text-end">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
        </tr>`;
    });

    let bayarRows = '';
    (d.pembayaran || []).forEach(b => {
        bayarRows += `<tr>
            <td>${b.tanggal_pembayaran.substring(0,10)}</td>
            <td>${b.metode}</td>
            <td class="text-end">Rp ${parseInt(b.jumlah_bayar).toLocaleString('id-ID')}</td>
        </tr>`;
    });

    let diskon = parseFloat(d.diskon_total || 0);
    let diskonRow = diskon > 0 ?
        `<tr><td colspan="3" class="text-end text-success">Diskon</td><td class="text-end text-success">- Rp ${diskon.toLocaleString('id-ID')}</td></tr>` :
        '';

    let tglSelesai = d.tanggal_selesai ? `<p><strong>Estimasi Selesai:</strong> ${d.tanggal_selesai}</p>` : '';

    return `
    <div id="areaPreview" style="font-family: sans-serif; font-size: 14px; padding: 10px;">
        <div class="text-center mb-3">
            <h4 class="fw-bold mb-0">🧵 KONVEKSI</h4>
            <small class="text-muted">Sistem Informasi Konveksi</small>
            <hr>
            <h5 class="fw-bold">${judul}</h5>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <p class="mb-1"><strong>No. Dokumen:</strong> DOC-${String(d.id_dokumen).padStart(4,'0')}</p>
                <p class="mb-1"><strong>No. Transaksi:</strong> #${d.id_transaksi}</p>
                <p class="mb-1"><strong>Tanggal Cetak:</strong> ${d.tanggal_cetak.substring(0,10)}</p>
                ${tglSelesai}
            </div>
            <div class="col-6 text-end">
                <p class="mb-1"><strong>Pelanggan:</strong> ${d.nama_pelanggan}</p>
                <p class="mb-1"><strong>Alamat:</strong> ${d.alamat || '-'}</p>
                <p class="mb-1"><strong>No. HP:</strong> ${d.no_hp || '-'}</p>
            </div>
        </div>
        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Harga Satuan</th><th class="text-end">Subtotal</th></tr>
            </thead>
            <tbody>
                ${rows}
                ${diskonRow}
                <tr class="table-warning fw-bold">
                    <td colspan="3" class="text-end">TOTAL</td>
                    <td class="text-end">Rp ${parseInt(d.total_harga).toLocaleString('id-ID')}</td>
                </tr>
            </tbody>
        </table>
        ${bayarRows ? `
        <h6 class="fw-bold mt-3">Riwayat Pembayaran</h6>
        <table class="table table-sm table-bordered">
            <thead class="table-secondary"><tr><th>Tanggal</th><th>Metode</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>${bayarRows}</tbody>
        </table>` : ''}
        <div class="mt-4 text-center text-muted" style="font-size:12px;">
            Dicetak pada ${d.tanggal_cetak.substring(0,10)} — Sistem Informasi Konveksi
        </div>
    </div>`;
}

function cetakDokumen() {
    let isi = document.getElementById('areaPreview').innerHTML;
    let w = window.open('', '_blank');
    w.document.write(`<!DOCTYPE html><html><head>
        <title>Cetak Dokumen</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>body{padding:30px} @media print{body{padding:0}}</style>
    </head><body>${isi}<script>window.onload=function(){window.print();}<\/script></body></html>`);
    w.document.close();
}
</script>