<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Laporan</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <!-- Form filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Jenis Laporan</label>
                        <select id="jenisLaporan" class="form-select">
                            <option value="penjualan">Penjualan</option>
                            <option value="keuangan">Keuangan / Pembayaran</option>
                            <option value="aktivitas">Aktivitas Pengiriman</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Awal</label>
                        <input type="date" id="tglAwal" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" id="tglAkhir" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" id="btnGenerate">
                            <i class="bi bi-bar-chart"></i> Generate Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan angka -->
        <div class="row g-3 mb-4 d-none" id="kartuRingkasan">
            <div class="col-md-4">
                <div class="card text-white bg-primary text-center p-3">
                    <div class="fs-2 fw-bold" id="ringkasanJumlah">0</div>
                    <div class="small">Total Data</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success text-center p-3">
                    <div class="fw-bold" id="ringkasanNominal">Rp 0</div>
                    <div class="small" id="ringkasanLabel">Total Nilai</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info text-center p-3">
                    <div class="fw-bold" id="ringkasanPeriode">-</div>
                    <div class="small">Periode</div>
                </div>
            </div>
        </div>

        <!-- Tabel hasil -->
        <div class="card d-none" id="kartuHasil">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span id="judulTabel">Hasil Laporan</span>
                <button class="btn btn-sm btn-outline-secondary" onclick="cetakLaporan()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
            <div class="card-body p-0" id="isiTabel"></div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    // Default periode bulan ini
    let now = new Date();
    let y = now.getFullYear();
    let m = String(now.getMonth() + 1).padStart(2, '0');
    let today = now.toISOString().split('T')[0];
    $('#tglAwal').val(`${y}-${m}-01`);
    $('#tglAkhir').val(today);

    $('#btnGenerate').click(generate);
});

function generate() {
    let jenis = $('#jenisLaporan').val();
    let awal = $('#tglAwal').val();
    let akhir = $('#tglAkhir').val();

    if (!awal || !akhir) {
        alert('Tentukan periode laporan.');
        return;
    }
    if (awal > akhir) {
        alert('Tanggal awal tidak boleh lebih dari tanggal akhir.');
        return;
    }

    $('#btnGenerate').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm"></span> Memproses...'
    );

    $.get('/konveksi/api/laporan.php', {
        action: 'generate',
        jenis,
        tgl_awal: awal,
        tgl_akhir: akhir
    }, function(res) {
        $('#btnGenerate').prop('disabled', false).html('<i class="bi bi-bar-chart"></i> Generate Laporan');

        if (!res.success) {
            alert('Error: ' + res.error);
            return;
        }

        let rows = res.rows;
        $('#kartuRingkasan').removeClass('d-none');
        $('#kartuHasil').removeClass('d-none');
        $('#ringkasanJumlah').text(rows.length);
        $('#ringkasanPeriode').text(awal + ' s/d ' + akhir);

        let html = '';

        if (jenis === 'penjualan') {
            $('#judulTabel').text('Laporan Penjualan');
            let total = rows.reduce((s, r) => s + parseFloat(r.total_harga), 0);
            $('#ringkasanNominal').text('Rp ' + total.toLocaleString('id-ID'));
            $('#ringkasanLabel').text('Total Nilai Transaksi');

            html = '<table class="table table-sm table-bordered mb-0">' +
                '<thead class="table-dark"><tr><th>#</th><th>Pelanggan</th><th>Jenis</th>' +
                '<th>Total</th><th>Diskon</th><th>Status</th><th>Tanggal</th></tr></thead><tbody>';
            rows.forEach((r, i) => {
                let badge = r.status === 'lunas' ? 'success' : r.status === 'pending' ? 'warning' :
                    'info';
                html += `<tr>
                    <td>${i+1}</td>
                    <td>${r.pelanggan}</td>
                    <td>${r.jenis_transaksi.replace(/_/g,' ')}</td>
                    <td>Rp ${parseInt(r.total_harga).toLocaleString('id-ID')}</td>
                    <td>Rp ${parseInt(r.diskon_total).toLocaleString('id-ID')}</td>
                    <td><span class="badge bg-${badge}">${r.status}</span></td>
                    <td>${r.tanggal_transaksi.substring(0,10)}</td>
                </tr>`;
            });
            html += '</tbody></table>';

        } else if (jenis === 'keuangan') {
            $('#judulTabel').text('Laporan Keuangan');
            let total = rows.reduce((s, r) => s + parseFloat(r.jumlah_bayar), 0);
            $('#ringkasanNominal').text('Rp ' + total.toLocaleString('id-ID'));
            $('#ringkasanLabel').text('Total Pembayaran');

            html = '<table class="table table-sm table-bordered mb-0">' +
                '<thead class="table-dark"><tr><th>#</th><th>Pelanggan</th><th>Metode</th>' +
                '<th>Jumlah</th><th>Status</th><th>Tanggal</th></tr></thead><tbody>';
            rows.forEach((r, i) => {
                let badge = r.status === 'terkonfirmasi' ? 'success' : r.status === 'ditolak' ?
                    'danger' : 'warning';
                html += `<tr>
                    <td>${i+1}</td>
                    <td>${r.pelanggan}</td>
                    <td>${r.metode}</td>
                    <td>Rp ${parseInt(r.jumlah_bayar).toLocaleString('id-ID')}</td>
                    <td><span class="badge bg-${badge}">${r.status}</span></td>
                    <td>${r.tanggal_pembayaran.substring(0,10)}</td>
                </tr>`;
            });
            html += '</tbody></table>';

        } else {
            $('#judulTabel').text('Laporan Aktivitas Pengiriman');
            $('#ringkasanNominal').text(rows.filter(r => r.status === 'sampai').length + ' paket');
            $('#ringkasanLabel').text('Sudah Sampai');

            html = '<table class="table table-sm table-bordered mb-0">' +
                '<thead class="table-dark"><tr><th>#</th><th>Pelanggan</th><th>Kurir</th>' +
                '<th>No Resi</th><th>Status</th><th>Tgl Kirim</th><th>Tgl Tiba</th></tr></thead><tbody>';
            rows.forEach((r, i) => {
                let badge = r.status === 'sampai' ? 'success' : 'info';
                html += `<tr>
                    <td>${i+1}</td>
                    <td>${r.pelanggan}</td>
                    <td>${r.kurir || '-'}</td>
                    <td>${r.no_resi ? `<code>${r.no_resi}</code>` : '-'}</td>
                    <td><span class="badge bg-${badge}">${r.status}</span></td>
                    <td>${r.tanggal_kirim || '-'}</td>
                    <td>${r.tanggal_tiba  || '-'}</td>
                </tr>`;
            });
            html += '</tbody></table>';
        }

        $('#isiTabel').html(html);
    }, 'json');
}

function cetakLaporan() {
    window.print();
}
</script>