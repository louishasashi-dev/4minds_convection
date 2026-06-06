<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') { header('Location: /konveksi/auth/login.php'); exit; }
require_once '../config/db.php';
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Kelola Transaksi</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">
        <div class="d-flex justify-content-between mb-3">
            <h5>Daftar Transaksi</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah
                Transaksi</button>
        </div>

        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="selesai">Selesai</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="lunas">Lunas</option>
                    <option value="batal">Batal</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterJenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="jahit_satuan">Jahit Satuan</option>
                    <option value="pakaian_jadi">Pakaian Jadi</option>
                    <option value="konveksi">Konveksi</option>
                </select>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="tabelTransaksi">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Jenis</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Pelanggan</label>
                        <select id="pilihPelanggan" class="form-select">
                            <?php
                            $rows = $conn->query("SELECT id_pelanggan, name FROM pelanggan ORDER BY name");
                            while ($r = $rows->fetch_assoc()):
                            ?>
                            <option value="<?= $r['id_pelanggan'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Jenis Transaksi</label>
                        <select id="pilihJenis" class="form-select">
                            <option value="jahit_satuan">Jahit Satuan (DP 50%)</option>
                            <option value="pakaian_jadi">Pakaian Jadi (Lunas/COD)</option>
                            <option value="konveksi">Konveksi (100%)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Pembayaran</label>
                        <select id="pilihPembayaran" class="form-select">
                            <option value="dp">DP 50%</option>
                            <option value="lunas">Lunas</option>
                            <option value="cod">COD</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" id="tglSelesai" class="form-control">
                </div>
                <hr>
                <h6>Item Produk</h6>
                <div id="itemContainer">
                    <div class="row item-row mb-2">
                        <div class="col-md-6">
                            <select class="form-select produk-select">
                                <?php
                                $prows = $conn->query("SELECT id_produk, nama_produk, harga FROM produk ORDER BY nama_produk");
                                while ($pr = $prows->fetch_assoc()):
                                ?>
                                <option value="<?= $pr['id_produk'] ?>" data-harga="<?= $pr['harga'] ?>">
                                    <?= htmlspecialchars($pr['nama_produk']) ?> -
                                    Rp<?= number_format($pr['harga'],0,',','.') ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control jumlah-input" placeholder="Jumlah" min="1"
                                value="1">
                        </div>
                        <div class="col-md-3">
                            <span class="subtotal-text pt-2 d-block text-end fw-bold"></span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnTambahItem">+ Tambah Item</button>
                <div class="mt-3 text-end">
                    <strong>Subtotal: <span id="grandTotal">Rp 0</span></strong><br>
                    <span class="text-success" id="infoDiskon"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanTransaksi">Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    loadTransaksi();

    function loadTransaksi() {
        $.get('/konveksi/api/transaksi.php?action=list', function(data) {
            let tbody = $('#tabelTransaksi tbody');
            tbody.empty();
            let filterStatus = $('#filterStatus').val();
            let filterJenis = $('#filterJenis').val();
            data.forEach((t, i) => {
                if (filterStatus && t.status !== filterStatus) return;
                if (filterJenis && t.jenis_transaksi !== filterJenis) return;
                let badgeClass = t.status === 'lunas' ? 'success' : t.status === 'pending' ?
                    'warning' : 'info';
                tbody.append(`<tr>
                    <td>${t.id_transaksi}</td>
                    <td>${t.nama_pelanggan}</td>
                    <td>${t.jenis_transaksi}</td>
                    <td>Rp ${parseInt(t.total_harga).toLocaleString('id-ID')}</td>
                    <td>${t.jenis_pembayaran}</td>
                    <td><span class="badge bg-${badgeClass}">${t.status}</span></td>
                    <td>${t.tanggal_transaksi.substring(0,10)}</td>
                    <td>${t.deskripsi ? '<span title="'+t.deskripsi+'">'+t.deskripsi.substring(0,30)+(t.deskripsi.length>30?'...':'')+'</span>' : '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="lihatDetail(${t.id_transaksi})"><i class="bi bi-eye"></i></button>
                        ${t.jenis_transaksi === 'kustom' && t.total_harga == 0 ? `<button class="btn btn-sm btn-warning" onclick="setHargaKustom(${t.id_transaksi})" title="Set Harga"><i class="bi bi-tag"></i></button>` : ''}
                        <button class="btn btn-sm btn-success" onclick="updateStatus(${t.id_transaksi})"><i class="bi bi-check2-circle"></i></button>
                    </td>
                </tr>`);
            });
        }, 'json');
    }

    $('#filterStatus, #filterJenis').on('change', loadTransaksi);

    // Hitung total otomatis
    function hitungTotal() {
        let total = 0;
        $('.item-row').each(function() {
            let harga = parseFloat($(this).find('.produk-select option:selected').data('harga')) || 0;
            let qty = parseInt($(this).find('.jumlah-input').val()) || 0;
            let sub = harga * qty;
            $(this).find('.subtotal-text').text('Rp ' + sub.toLocaleString('id-ID'));
            total += sub;
        });
        $('#grandTotal').text('Rp ' + total.toLocaleString('id-ID'));
    }

    $(document).on('change', '.produk-select, .jumlah-input', hitungTotal);

    $('#btnTambahItem').click(function() {
        let clone = $('.item-row').first().clone();
        clone.find('.jumlah-input').val(1);
        clone.find('.subtotal-text').text('');
        $('#itemContainer').append(clone);
    });

    $('#btnSimpanTransaksi').click(function() {
        let items = [];
        $('.item-row').each(function() {
            items.push({
                id_produk: $(this).find('.produk-select').val(),
                jumlah: $(this).find('.jumlah-input').val()
            });
        });
        $.post('/konveksi/api/transaksi.php', {
            action: 'create',
            id_pelanggan: $('#pilihPelanggan').val(),
            jenis_transaksi: $('#pilihJenis').val(),
            jenis_pembayaran: $('#pilihPembayaran').val(),
            tanggal_selesai: $('#tglSelesai').val(),
            items: JSON.stringify(items)
        }, function(res) {
            if (res.success) {
                alert('Transaksi berhasil dibuat! ID: ' + res.id_transaksi);
                $('#modalTambah').modal('hide');
                loadTransaksi();
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });
});

function lihatDetail(id) {
    $.get('/konveksi/api/transaksi.php?action=detail&id=' + id, function(data) {
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

        // Tabel produk
        let items = data.items || data;
        let adaProduk = items.length > 0 && items[0].nama_produk;
        if (adaProduk) {
            html +=
                '<table class="table table-sm"><thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
            items.forEach(d => {
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

        $('#detailContent').html(html);
        $('#modalDetail').modal('show');
    }, 'json');
}

function updateStatus(id) {
    let status = prompt('Masukkan status baru (diproses/selesai/dikirim/lunas/batal):');
    if (!status) return;
    $.post('/konveksi/api/transaksi.php', {
        action: 'update_status',
        id: id,
        status: status
    }, function(res) {
        if (res.success) {
            alert('Status diperbarui');
            location.reload();
        }
    }, 'json');
}

function setHargaKustom(id) {
    let harga = prompt('Masukkan total harga untuk pesanan kustom ini (angka tanpa titik/koma):');
    if (!harga || isNaN(harga)) return;
    let jp = prompt('Jenis pembayaran? Ketik: dp / lunas / cod');
    if (!jp) return;
    $.post('/konveksi/api/kustom.php', {
        action: 'set_harga',
        id_transaksi: id,
        total_harga: harga,
        jenis_pembayaran: jp
    }, function(res) {
        if (res.success) {
            alert('Harga berhasil ditetapkan. Pelanggan kini bisa melakukan pelunasan.');
            location.reload();
        } else {
            alert('Error: ' + res.error);
        }
    }, 'json');
}
</script>