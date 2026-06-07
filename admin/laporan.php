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
                    <div class="fw-bold" id="ringkasanJumlah">0</div>
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
    let jenis = $('#jenisLaporan').val();
    let awal = $('#tglAwal').val();
    let akhir = $('#tglAkhir').val();
    let judulMap = {
        penjualan: 'LAPORAN PENJUALAN',
        keuangan: 'LAPORAN KEUANGAN / PEMBAYARAN',
        aktivitas: 'LAPORAN AKTIVITAS PENGIRIMAN'
    };
    let judul = judulMap[jenis] || 'LAPORAN';

    // Ambil data ringkasan dari kartu yang sudah tampil
    let totalData = $('#ringkasanJumlah').text();
    let totalNominal = $('#ringkasanNominal').text();
    let labelNominal = $('#ringkasanLabel').text();

    // Bangun isi tabel cetak yang lebih rinci
    let rows = [];
    let totalHarga = 0,
        totalDiskon = 0,
        totalBayar = 0;
    let tabelHTML = '';

    if (jenis === 'penjualan') {
        // Kumpulkan data dari tabel yang sudah di-render
        $('#isiTabel table tbody tr').each(function() {
            let cols = $(this).find('td');
            rows.push({
                no: cols.eq(0).text(),
                pelanggan: cols.eq(1).text(),
                jenis: cols.eq(2).text(),
                total: cols.eq(3).text(),
                diskon: cols.eq(4).text(),
                status: cols.eq(5).text().trim(),
                tanggal: cols.eq(6).text()
            });
            // Parse angka untuk total
            let t = cols.eq(3).text().replace(/[^0-9]/g, '');
            let dk = cols.eq(4).text().replace(/[^0-9]/g, '');
            totalHarga += parseInt(t || 0);
            totalDiskon += parseInt(dk || 0);
        });

        tabelHTML = `
        <table>
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th>Pelanggan</th>
                    <th>Jenis Transaksi</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Net</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                ${rows.map(r => {
                    let total = parseInt(r.total.replace(/[^0-9]/g,'') || 0);
                    let diskon = parseInt(r.diskon.replace(/[^0-9]/g,'') || 0);
                    let net = total - diskon;
                    return `<tr>
                        <td>${r.no}</td>
                        <td>${r.pelanggan}</td>
                        <td>${r.jenis}</td>
                        <td class="text-end">${r.total}</td>
                        <td class="text-end">${r.diskon}</td>
                        <td class="text-end">Rp ${net.toLocaleString('id-ID')}</td>
                        <td><span class="badge-status">${r.status}</span></td>
                        <td>${r.tanggal}</td>
                    </tr>`;
                }).join('')}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">TOTAL KESELURUHAN</td>
                    <td class="text-end fw-bold">Rp ${totalHarga.toLocaleString('id-ID')}</td>
                    <td class="text-end fw-bold">Rp ${totalDiskon.toLocaleString('id-ID')}</td>
                    <td class="text-end fw-bold">Rp ${(totalHarga - totalDiskon).toLocaleString('id-ID')}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>`;

    } else if (jenis === 'keuangan') {
        $('#isiTabel table tbody tr').each(function() {
            let cols = $(this).find('td');
            rows.push({
                no: cols.eq(0).text(),
                pelanggan: cols.eq(1).text(),
                metode: cols.eq(2).text(),
                jumlah: cols.eq(3).text(),
                status: cols.eq(4).text().trim(),
                tanggal: cols.eq(5).text()
            });
            let j = cols.eq(3).text().replace(/[^0-9]/g, '');
            totalBayar += parseInt(j || 0);
        });

        let terkonfirmasi = rows.filter(r => r.status === 'terkonfirmasi').reduce((s, r) => s + parseInt(r.jumlah
            .replace(/[^0-9]/g, '') || 0), 0);
        let menunggu = rows.filter(r => r.status === 'menunggu').reduce((s, r) => s + parseInt(r.jumlah.replace(
            /[^0-9]/g, '') || 0), 0);
        let ditolak = rows.filter(r => r.status === 'ditolak').reduce((s, r) => s + parseInt(r.jumlah.replace(/[^0-9]/g,
            '') || 0), 0);

        tabelHTML = `
        <table>
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th class="text-end">Jumlah Bayar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                ${rows.map(r => `<tr>
                    <td>${r.no}</td>
                    <td>${r.pelanggan}</td>
                    <td>${r.metode}</td>
                    <td class="text-end">${r.jumlah}</td>
                    <td><span class="badge-status">${r.status}</span></td>
                    <td>${r.tanggal}</td>
                </tr>`).join('')}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-bold">TOTAL SEMUA PEMBAYARAN</td>
                    <td class="text-end fw-bold">Rp ${totalBayar.toLocaleString('id-ID')}</td>
                    <td colspan="2"></td>
                </tr>
                <tr class="tfoot-sub">
                    <td colspan="3" class="text-end">↳ Terkonfirmasi</td>
                    <td class="text-end">Rp ${terkonfirmasi.toLocaleString('id-ID')}</td>
                    <td colspan="2"></td>
                </tr>
                <tr class="tfoot-sub">
                    <td colspan="3" class="text-end">↳ Menunggu</td>
                    <td class="text-end">Rp ${menunggu.toLocaleString('id-ID')}</td>
                    <td colspan="2"></td>
                </tr>
                <tr class="tfoot-sub">
                    <td colspan="3" class="text-end">↳ Ditolak</td>
                    <td class="text-end">Rp ${ditolak.toLocaleString('id-ID')}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>`;

    } else {
        $('#isiTabel table tbody tr').each(function() {
            let cols = $(this).find('td');
            rows.push({
                no: cols.eq(0).text(),
                pelanggan: cols.eq(1).text(),
                kurir: cols.eq(2).text(),
                resi: cols.eq(3).text().trim(),
                status: cols.eq(4).text().trim(),
                tglKirim: cols.eq(5).text(),
                tglTiba: cols.eq(6).text()
            });
        });

        let sampai = rows.filter(r => r.status === 'sampai').length;
        let dikirim = rows.filter(r => r.status === 'dikirim').length;
        let belum = rows.filter(r => r.status === 'belum_dikirim' || r.status === '-').length;

        tabelHTML = `
        <table>
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th>Pelanggan</th>
                    <th>Kurir</th>
                    <th>No Resi</th>
                    <th>Status</th>
                    <th>Tgl Kirim</th>
                    <th>Tgl Tiba</th>
                </tr>
            </thead>
            <tbody>
                ${rows.map(r => `<tr>
                    <td>${r.no}</td>
                    <td>${r.pelanggan}</td>
                    <td>${r.kurir}</td>
                    <td>${r.resi}</td>
                    <td><span class="badge-status">${r.status}</span></td>
                    <td>${r.tglKirim}</td>
                    <td>${r.tglTiba}</td>
                </tr>`).join('')}
            </tbody>
        </table>`;
    }

    let w = window.open('', '_blank');
    w.document.open();
    w.document.write(`<!DOCTYPE html><html><head>
        <title>${judul}</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; font-size: 13px; padding: 30px; color: #222; }
            @media print { body { padding: 15px; } }

            /* HEADER */
            .header-cetak { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
            .header-cetak img { height: 64px; object-fit: contain; }
            .header-cetak .info h3 { font-size: 18px; font-weight: bold; margin-bottom: 2px; }
            .header-cetak .info small { color: #666; font-size: 12px; }
            .divider { border: none; border-top: 2px solid #222; margin: 10px 0 4px; }
            .divider-thin { border: none; border-top: 1px solid #ccc; margin: 4px 0 14px; }
            .judul-laporan { text-align: center; font-size: 15px; font-weight: bold; letter-spacing: 1px; margin-bottom: 4px; }
            .periode { text-align: center; font-size: 12px; color: #555; margin-bottom: 20px; }

            /* RINGKASAN */
            .ringkasan { display: flex; gap: 12px; margin-bottom: 20px; }
            .ringkasan-box { flex: 1; border: 1px solid #ddd; border-radius: 6px; padding: 10px 14px; }
            .ringkasan-box .nilai { font-size: 16px; font-weight: bold; color: #1a56db; }
            .ringkasan-box .label { font-size: 11px; color: #666; margin-top: 2px; }

            /* TABLE */
            table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
            thead tr { background-color: #1e293b; color: #fff; }
            thead th { padding: 8px 10px; text-align: left; font-size: 12px; font-weight: 600; }
            tbody tr { border-bottom: 1px solid #e5e7eb; }
            tbody tr:nth-child(even) { background-color: #f8fafc; }
            tbody td { padding: 7px 10px; font-size: 12px; vertical-align: middle; }
            tfoot tr { background-color: #fef9c3; }
            tfoot td { padding: 7px 10px; font-size: 12px; font-weight: 600; border-top: 2px solid #d97706; }
            tfoot tr.tfoot-sub { background-color: #fffbeb; }
            tfoot tr.tfoot-sub td { font-weight: normal; font-size: 11px; color: #555; border-top: none; }
            .text-end { text-align: right; }
            .fw-bold { font-weight: bold; }
            

            /* BADGE STATUS */
            .badge-status { display: inline-block; padding: 2px 8px; border-radius: 20px;
                font-size: 11px; font-weight: 600; background: #e5e7eb; color: #333; }

            /* TTD */
            .ttd { display: flex; justify-content: space-between; margin-top: 30px; }
            .ttd--1 { display: flex; justify-content: center; }
            .ttd div { text-align: center; width: 38%; }
            .ttd div .label-ttd { font-size: 12px; display: block; margin-bottom: 60px; }
            .ttd div .garis-ttd { border-top: 1px solid #333; padding-top: 6px; font-size: 12px; }

            /* FOOTER */
            .footer-cetak { text-align: center; font-size: 11px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 10px; }
        </style>
    </head><body>

        <div class="header-cetak">
            <img src="/konveksi/assets/logo/logofavicon.png">
            <div class="info">
                <h3>4MINDS CONVECTION</h3>
                <small>Solusi Konveksi Terpercaya</small>
            </div>
        </div>
        <hr class="divider">
        <hr class="divider-thin">
        <div class="judul-laporan">${judul}</div>
        <div class="periode">Periode: ${awal} s/d ${akhir}</div>

        <div class="ringkasan">
            <div class="ringkasan-box">
                <div class="nilai">${totalData}</div>
                <div class="label">Total Data</div>
            </div>
            <div class="ringkasan-box">
                <div class="nilai">${totalNominal}</div>
                <div class="label">${labelNominal}</div>
            </div>
            <div class="ringkasan-box">
                <div class="nilai">${awal} s/d ${akhir}</div>
                <div class="label">Periode</div>
            </div>
        </div>

        ${tabelHTML}

        <div class="ttd--1">
            <span class="label-ttd">Mengetahui,</span>
        </div>

        <div class="ttd">
            <div>
                <span class="label-ttd">Owner/Pemilik,</span>
                <span class="label-ttd">(________________________)</span>
            </div>
            <div>
                <span class="label-ttd">Direktur Keuangan,</span>
                <span class="label-ttd">(________________________)</span>
            </div>
            <div>
                <span class="label-ttd">Manajer Keuangan,</span>
                <span class="label-ttd">(________________________)</span>
            </div>
            <div>
                <span class="label-ttd">Admin / Penanggung Jawab,</span>
                <span class="label-ttd">(________________________)</span>
            </div>
        </div>

        <div class="footer-cetak">
            Dicetak pada ${new Date().toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})}
            &mdash; 4MINDS CONVECTION
        </div>

    <script>window.onload=function(){window.print();}<\/script>
    </body></html>`);
    w.document.close();
}
</script>