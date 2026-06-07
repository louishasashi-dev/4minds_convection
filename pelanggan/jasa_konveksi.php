<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') { header('Location: /konveksi/auth/login.php'); exit; }
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Jasa Konveksi</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="row mb-3 g-2">
            <div class="col-md-5">
                <input type="text" id="searchProduk" class="form-control" placeholder="🔍 Cari layanan konveksi...">
            </div>
        </div>

        <div id="keranjangBar"
            class="alert alert-primary d-none mb-3 d-flex justify-content-between align-items-center">
            <span>🛒 <strong id="jmlKeranjang">0</strong> item dipilih &mdash; Total: <strong id="totalKeranjang">Rp
                    0</strong></span>
            <button class="btn btn-primary btn-sm" onclick="bukaCheckout()">Lanjut Checkout &rarr;</button>
        </div>

        <div class="row g-3" id="produkGrid">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary"></div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detail Produk -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="isiDetail">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success d-none" id="btnTambahDariDetail">
                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Checkout -->
<div class="modal fade" id="modalCheckout" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🏭 Checkout Jasa Konveksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <h6 class="fw-bold mb-2">Produk yang Dipesan</h6>
                <table class="table table-sm table-bordered mb-3">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th style="width:130px">Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="keranjangTabel"></tbody>
                    <tfoot>
                        <tr class="table-warning fw-bold">
                            <td colspan="3" class="text-end">Subtotal</td>
                            <td id="totalCheckout">Rp 0</td>
                            <td></td>
                        </tr>
                        <tr class="text-success d-none" id="rowDiskonQty">
                            <td colspan="3" class="text-end">Diskon qty >50 pcs (2%)</td>
                            <td id="nominalDiskonQty">-Rp 0</td>
                            <td></td>
                        </tr>
                        <tr class="table-success fw-bold d-none" id="rowTotalAkhir">
                            <td colspan="3" class="text-end">Total Akhir</td>
                            <td id="totalAkhirCheckout">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="row g-3">
                    <!-- UKURAN -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ukuran <span class="text-danger">*</span></label>
                        <select id="co_ukuran" class="form-select"
                            onchange="renderKeranjangTabel(); hitungTotalCheckout();">
                            <option value="">-- Pilih Ukuran --</option>
                        </select>
                        <small class="text-muted">Ukuran lebih besar, harga menyesuaikan</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Pembayaran</label>
                        <select id="co_pembayaran" class="form-select">
                            <option value="lunas">Bayar Lunas</option>
                            <option value="dp">DP 50% dulu</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi / Catatan Pesanan <small
                                class="text-muted">(opsional)</small></label>
                        <textarea id="co_deskripsi" class="form-control" rows="2"
                            placeholder="Contoh: warna navy, sablon dada kiri, bahan drifit..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Estimasi Tanggal Selesai <small
                                class="text-muted">(opsional)</small></label>
                        <input type="date" id="co_tgl_selesai" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload Desain / Referensi
                            <small class="text-muted">(opsional — gambar atau PDF)</small>
                        </label>
                        <input type="file" id="co_file_desain" class="form-control"
                            accept="image/jpeg,image/png,image/gif,image/webp,image/bmp,image/svg+xml,application/pdf">
                        <div class="form-text">Format: JPG, PNG, GIF, WEBP, BMP, SVG, PDF &bull; Maks. <strong>5
                                MB</strong></div>
                        <div id="previewDesain" class="mt-2 d-none">
                            <img id="previewDesainImg" src="" alt="Preview" class="img-thumbnail"
                                style="max-height:150px;">
                        </div>
                        <div id="previewDesainPdf" class="mt-2 d-none">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                                <span id="namaPdfDesain"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info harga dinamis -->
                <div class="alert alert-light border mt-3" id="infoHargaDinamis" style="display:none">
                    <div class="d-flex justify-content-between">
                        <span>Harga satuan (ukuran dipilih):</span>
                        <strong id="infoHargaSatuan">Rp 0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total qty:</span>
                        <strong id="infoTotalQty">0 pcs</strong>
                    </div>
                    <div class="d-flex justify-content-between text-success d-none" id="infoDiskonQtyRow">
                        <span>✅ Diskon qty >50 pcs (2%):</span>
                        <strong id="infoDiskonQtyNominal">-Rp 0</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fs-6 fw-bold">
                        <span>Total yang dibayar:</span>
                        <span class="text-success" id="infoTotalAkhir">Rp 0</span>
                    </div>
                </div>

                <div id="alertCheckout" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKirimOrder">
                    <span id="loadingOrder" class="spinner-border spinner-border-sm d-none"></span>
                    <i class="bi bi-check-circle"></i> Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="toastNotif" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastPesan"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let keranjang = [];
let produkAktif = null;
const ukuranPct = {
    'XS': 0,
    'S': 0,
    'M': 0.02,
    'L': 0.04,
    'XL': 0.07,
    'XXL': 0.10,
    'XXXL': 0.14,
    '3XL': 0.14,
    '4XL': 0.18,
    '5XL': 0.22
};

$(document).ready(function() {
    // Load ukuran dari DB
    $.get('/konveksi/api/ukuran.php?action=list_ukuran', function(data) {
        let sel = $('#co_ukuran');
        data.forEach(function(u) {
            sel.append('<option value="' + u.ukuran + '">' + u.ukuran + '</option>');
        });
    }, 'json');

    loadProduk();
    $('#searchProduk').on('keyup', loadProduk);
});

function loadProduk() {
    let q = $('#searchProduk').val();
    $('#produkGrid').html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>');
    $.get('/konveksi/api/produk.php?action=list&q=' + encodeURIComponent(q) + '&jenis=konveksi', function(data) {
        let grid = $('#produkGrid');
        grid.empty();
        if (data.length === 0) {
            grid.html('<p class="text-muted col-12 mt-3">Tidak ada layanan konveksi ditemukan.</p>');
            return;
        }
        data.forEach(function(p) {
            let img = p.gambar ? '/konveksi/assets/uploads/' + p.gambar :
                'https://via.placeholder.com/200x150?text=No+Image';
            let stokBadge = parseInt(p.stok) > 0 ?
                '<span class="badge bg-success">Stok: ' + p.stok + '</span>' :
                '<span class="badge bg-danger">Stok Habis</span>';
            let btnPesan = parseInt(p.stok) > 0 ?
                '<button class="btn btn-primary btn-sm w-100 mt-1" onclick="tambahKeKeranjang(' + p
                .id_produk + ')"><i class="bi bi-cart-plus"></i> Pesan Massal</button>' :
                '<button class="btn btn-secondary btn-sm w-100 mt-1" disabled>Stok Habis</button>';
            grid.append(
                '<div class="col-md-3 col-sm-6">' +
                '<div class="card h-100 shadow-sm">' +
                '<img src="' + img +
                '" class="card-img-top" style="height:150px;object-fit:contain;background:#f8f9fa" onclick="lihatDetail(' +
                p.id_produk + ')" role="button">' +
                '<div class="card-body d-flex flex-column p-2">' +
                '<span class="badge bg-primary mb-1" style="font-size:.7rem">Konveksi</span>' +
                '<h6 class="card-title mb-1" style="font-size:.9rem">' + p.nama_produk + '</h6>' +
                '<p class="fw-bold text-success mb-1">Rp ' + parseInt(p.harga).toLocaleString(
                    'id-ID') + ' <small class="text-muted fw-normal">/ pcs</small></p>' +
                stokBadge + btnPesan +
                '<button class="btn btn-outline-secondary btn-sm w-100 mt-1" onclick="lihatDetail(' +
                p.id_produk + ')"><i class="bi bi-eye"></i> Detail</button>' +
                '</div></div></div>'
            );
        });
    }, 'json');
}

function tambahKeKeranjang(id_produk) {
    $.get('/konveksi/api/produk.php?action=detail_publik&id=' + id_produk, function(p) {
        let idx = keranjang.findIndex(k => k.id_produk == id_produk);
        if (idx >= 0) {
            keranjang[idx].jumlah += 1;
        } else {
            keranjang.push({
                id_produk: p.id_produk,
                nama: p.nama_produk,
                harga: parseFloat(p.harga),
                jumlah: 1
            });
        }
        updateKeranjangBar();
        showToast(p.nama_produk + ' ditambahkan ke keranjang!');
    }, 'json');
}

function hapusDariKeranjang(id_produk) {
    keranjang = keranjang.filter(k => k.id_produk != id_produk);
    updateKeranjangBar();
    renderKeranjangTabel();
    hitungTotalCheckout();
}

function ubahQty(id_produk, delta) {
    let idx = keranjang.findIndex(k => k.id_produk == id_produk);
    if (idx < 0) return;
    keranjang[idx].jumlah = Math.max(1, keranjang[idx].jumlah + delta);
    updateKeranjangBar();
    renderKeranjangTabel();
    hitungTotalCheckout();
}

function updateKeranjangBar() {
    let total = keranjang.reduce((s, k) => s + k.harga * k.jumlah, 0);
    let jml = keranjang.reduce((s, k) => s + k.jumlah, 0);
    $('#jmlKeranjang').text(jml);
    $('#totalKeranjang').text('Rp ' + total.toLocaleString('id-ID'));
    if (keranjang.length > 0) $('#keranjangBar').removeClass('d-none');
    else $('#keranjangBar').addClass('d-none');
}

function renderKeranjangTabel() {
    let ukuran = $('#co_ukuran').val();
    let pct = ukuranPct[ukuran ? ukuran.toUpperCase() : ''] || 0;
    let tbody = $('#keranjangTabel');
    tbody.empty();
    keranjang.forEach(function(k) {
        let hargaUkuran = Math.round(k.harga * (1 + pct));
        let sub = hargaUkuran * k.jumlah;
        tbody.append(
            '<tr>' +
            '<td>' + k.nama + '</td>' +
            '<td><div class="input-group input-group-sm">' +
            '<button class="btn btn-outline-secondary" onclick="ubahQty(' + k.id_produk +
            ', -1)">-</button>' +
            '<input type="text" class="form-control text-center" value="' + k.jumlah + '" readonly>' +
            '<button class="btn btn-outline-secondary" onclick="ubahQty(' + k.id_produk +
            ', 1)">+</button>' +
            '</div></td>' +
            '<td>Rp ' + hargaUkuran.toLocaleString('id-ID') + '</td>' +
            '<td>Rp ' + sub.toLocaleString('id-ID') + '</td>' +
            '<td><button class="btn btn-sm btn-danger" onclick="hapusDariKeranjang(' + k.id_produk +
            ')"><i class="bi bi-trash"></i></button></td>' +
            '</tr>'
        );
    });
}

function hitungTotalCheckout() {
    let ukuran = $('#co_ukuran').val();
    let pct = ukuranPct[ukuran ? ukuran.toUpperCase() : ''] || 0;
    let totalQty = keranjang.reduce((s, k) => s + k.jumlah, 0);
    let subtotal = keranjang.reduce((s, k) => s + Math.round(k.harga * (1 + pct)) * k.jumlah, 0);
    let diskonQty = totalQty > 50 ? Math.round(subtotal * 0.02) : 0;
    let totalAkhir = subtotal - diskonQty;

    $('#totalCheckout').text('Rp ' + subtotal.toLocaleString('id-ID'));

    if (diskonQty > 0) {
        $('#rowDiskonQty').removeClass('d-none');
        $('#nominalDiskonQty').text('-Rp ' + diskonQty.toLocaleString('id-ID'));
        $('#rowTotalAkhir').removeClass('d-none');
        $('#totalAkhirCheckout').text('Rp ' + totalAkhir.toLocaleString('id-ID'));
    } else {
        $('#rowDiskonQty').addClass('d-none');
        $('#rowTotalAkhir').addClass('d-none');
    }

    if (ukuran) {
        let hargaSatuan = keranjang.length > 0 ? Math.round(keranjang[0].harga * (1 + pct)) : 0;
        $('#infoHargaSatuan').text('Rp ' + hargaSatuan.toLocaleString('id-ID'));
        $('#infoTotalQty').text(totalQty + ' pcs');
        if (diskonQty > 0) {
            $('#infoDiskonQtyRow').removeClass('d-none');
            $('#infoDiskonQtyNominal').text('-Rp ' + diskonQty.toLocaleString('id-ID'));
        } else {
            $('#infoDiskonQtyRow').addClass('d-none');
        }
        $('#infoTotalAkhir').text('Rp ' + totalAkhir.toLocaleString('id-ID'));
        $('#infoHargaDinamis').show();
    } else {
        $('#infoHargaDinamis').hide();
    }
}

function bukaCheckout() {
    if (keranjang.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }
    renderKeranjangTabel();
    hitungTotalCheckout();
    $('#alertCheckout').html('');
    $('#co_file_desain').val('');
    $('#previewDesain').addClass('d-none');
    $('#previewDesainPdf').addClass('d-none');
    new bootstrap.Modal(document.getElementById('modalCheckout')).show();
}

$('#btnKirimOrder').click(function() {
    if (keranjang.length === 0) return;

    let ukuran = $('#co_ukuran').val();
    if (!ukuran) {
        $('#alertCheckout').html('<div class="alert alert-warning">Pilih ukuran terlebih dahulu.</div>');
        return;
    }

    let fileInput = document.getElementById('co_file_desain');
    let file = fileInput.files[0];
    if (file && file.size > 5 * 1024 * 1024) {
        $('#alertCheckout').html('<div class="alert alert-danger">Ukuran file maksimal 5 MB.</div>');
        return;
    }

    let items = keranjang.map(k => ({
        id_produk: k.id_produk,
        jumlah: k.jumlah
    }));
    $('#loadingOrder').removeClass('d-none');
    $('#btnKirimOrder').prop('disabled', true);

    let formData = new FormData();
    formData.append('action', 'create');
    formData.append('jenis_transaksi', 'konveksi');
    formData.append('jenis_pembayaran', $('#co_pembayaran').val());
    formData.append('ukuran', ukuran);
    formData.append('tanggal_selesai', $('#co_tgl_selesai').val());
    formData.append('deskripsi', $('#co_deskripsi').val());
    formData.append('items', JSON.stringify(items));
    if (file) formData.append('file_desain', file);

    $.ajax({
        url: '/konveksi/api/transaksi.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            $('#loadingOrder').addClass('d-none');
            $('#btnKirimOrder').prop('disabled', false);
            if (res.success) {
                keranjang = [];
                updateKeranjangBar();
                $('#modalCheckout').modal('hide');
                showToast('Pesanan konveksi berhasil dibuat!');
                setTimeout(() => window.location.href = '/konveksi/pelanggan/transaksi.php', 2000);
            } else {
                $('#alertCheckout').html('<div class="alert alert-danger">' + (res.error ||
                    'Gagal membuat pesanan.') + '</div>');
            }
        },
        error: function() {
            $('#loadingOrder').addClass('d-none');
            $('#btnKirimOrder').prop('disabled', false);
            $('#alertCheckout').html(
                '<div class="alert alert-danger">Terjadi kesalahan koneksi.</div>');
        }
    });
});

function lihatDetail(id) {
    produkAktif = null;
    $('#isiDetail').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
    $('#btnTambahDariDetail').addClass('d-none');
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
    $.get('/konveksi/api/produk.php?action=detail_publik&id=' + id, function(p) {
        produkAktif = p;
        let img = p.gambar ? '/konveksi/assets/uploads/' + p.gambar :
            'https://via.placeholder.com/400x180?text=No+Image';
        $('#isiDetail').html(
            '<img src="' + img +
            '" class="img-fluid rounded mb-3" style="width:100%;max-height:200px;object-fit:cover">' +
            '<table class="table table-sm table-bordered">' +
            '<tr><th>Nama</th><td>' + p.nama_produk + '</td></tr>' +
            '<tr><th>Kategori</th><td>' + (p.kategori || '-') + '</td></tr>' +
            '<tr><th>Harga</th><td class="fw-bold text-success">Rp ' + parseInt(p.harga).toLocaleString(
                'id-ID') + ' / pcs</td></tr>' +
            '<tr><th>Stok</th><td>' + p.stok + ' pcs</td></tr>' +
            '<tr><th>Deskripsi</th><td>' + (p.deskripsi || '-') + '</td></tr>' +
            '</table>'
        );
        if (parseInt(p.stok) > 0) $('#btnTambahDariDetail').removeClass('d-none');
    }, 'json');
}

$('#btnTambahDariDetail').click(function() {
    if (!produkAktif) return;
    tambahKeKeranjang(produkAktif.id_produk);
    bootstrap.Modal.getInstance(document.getElementById('modalDetail')).hide();
});

function showToast(pesan) {
    $('#toastPesan').text(pesan);
    new bootstrap.Toast(document.getElementById('toastNotif'), {
        delay: 2500
    }).show();
}

$('#co_file_desain').on('change', function() {
    let file = this.files[0];
    $('#previewDesain').addClass('d-none');
    $('#previewDesainPdf').addClass('d-none');
    if (!file) return;
    if (file.type === 'application/pdf') {
        $('#namaPdfDesain').text(' ' + file.name);
        $('#previewDesainPdf').removeClass('d-none');
    } else if (file.type.startsWith('image/')) {
        let reader = new FileReader();
        reader.onload = function(e) {
            $('#previewDesainImg').attr('src', e.target.result);
            $('#previewDesain').removeClass('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>