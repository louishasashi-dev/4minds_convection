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

        <!-- Search -->
        <div class="row mb-3 g-2">
            <div class="col-md-5">
                <input type="text" id="searchProduk" class="form-control" placeholder="🔍 Cari layanan konveksi...">
            </div>
        </div>

        <!-- Keranjang mini bar -->
        <div id="keranjangBar"
            class="alert alert-primary d-none mb-3 d-flex justify-content-between align-items-center">
            <span>🛒 <strong id="jmlKeranjang">0</strong> item dipilih &mdash; Total: <strong id="totalKeranjang">Rp
                    0</strong></span>
            <button class="btn btn-primary btn-sm" onclick="bukaCheckout()">Lanjut Checkout &rarr;</button>
        </div>

        <!-- Grid produk -->
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
                <button type="button" class="btn btn-success" id="btnTambahDariDetail">
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
                            <th style="width:120px">Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="keranjangTabel"></tbody>
                    <tfoot>
                        <tr class="table-warning fw-bold">
                            <td colspan="3" class="text-end">Total</td>
                            <td id="totalCheckout">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Pembayaran</label>
                        <select id="co_pembayaran" class="form-select">
                            <option value="lunas">Bayar Lunas</option>
                            <option value="dp">DP 50% dulu</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi / Catatan Pesanan <small
                                class="text-muted">(opsional)</small></label>
                        <textarea id="co_deskripsi" class="form-control" rows="3"
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
                        <div class="form-text">
                            Format: JPG, PNG, GIF, WEBP, BMP, SVG, PDF &bull; Maks. <strong>5 MB</strong>
                        </div>
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
            let img = p.gambar ?
                '/konveksi/assets/uploads/' + p.gambar :
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
                stokBadge +
                btnPesan +
                '<button class="btn btn-outline-secondary btn-sm w-100 mt-1" onclick="lihatDetail(' +
                p.id_produk + ')"><i class="bi bi-eye"></i> Detail</button>' +
                '</div></div></div>'
            );
        });
    }, 'json');
}

function tambahKeKeranjang(id_produk) {
    $.get('/konveksi/api/produk.php?action=detail_publik&id=' + id_produk, function(p) {
        let idx = keranjang.findIndex(function(k) {
            return k.id_produk == id_produk;
        });
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
    keranjang = keranjang.filter(function(k) {
        return k.id_produk != id_produk;
    });
    updateKeranjangBar();
    renderKeranjangTabel();
}

function ubahQty(id_produk, delta) {
    let idx = keranjang.findIndex(function(k) {
        return k.id_produk == id_produk;
    });
    if (idx < 0) return;
    keranjang[idx].jumlah = Math.max(1, keranjang[idx].jumlah + delta);
    updateKeranjangBar();
    renderKeranjangTabel();
}

function updateKeranjangBar() {
    let total = keranjang.reduce(function(s, k) {
        return s + k.harga * k.jumlah;
    }, 0);
    let jml = keranjang.reduce(function(s, k) {
        return s + k.jumlah;
    }, 0);
    $('#jmlKeranjang').text(jml);
    $('#totalKeranjang').text('Rp ' + total.toLocaleString('id-ID'));
    if (keranjang.length > 0) $('#keranjangBar').removeClass('d-none');
    else $('#keranjangBar').addClass('d-none');
}

function renderKeranjangTabel() {
    let tbody = $('#keranjangTabel');
    tbody.empty();
    let total = 0;
    keranjang.forEach(function(k) {
        let sub = k.harga * k.jumlah;
        total += sub;
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
            '<td>Rp ' + parseInt(k.harga).toLocaleString('id-ID') + '</td>' +
            '<td>Rp ' + parseInt(sub).toLocaleString('id-ID') + '</td>' +
            '<td><button class="btn btn-sm btn-danger" onclick="hapusDariKeranjang(' + k.id_produk +
            ')"><i class="bi bi-trash"></i></button></td>' +
            '</tr>'
        );
    });
    $('#totalCheckout').text('Rp ' + total.toLocaleString('id-ID'));
}

function bukaCheckout() {
    if (keranjang.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }
    renderKeranjangTabel();
    $('#alertCheckout').html('');
    // Reset field upload
    $('#co_file_desain').val('');
    $('#previewDesain').addClass('d-none');
    $('#previewDesainPdf').addClass('d-none');
    new bootstrap.Modal(document.getElementById('modalCheckout')).show();
}

$('#btnKirimOrder').click(function() {
    if (keranjang.length === 0) return;

    // Validasi ukuran file
    let fileInput = document.getElementById('co_file_desain');
    let file = fileInput.files[0];
    if (file && file.size > 5 * 1024 * 1024) {
        $('#alertCheckout').html('<div class="alert alert-danger">Ukuran file maksimal 5 MB.</div>');
        return;
    }

    let items = keranjang.map(function(k) {
        return {
            id_produk: k.id_produk,
            jumlah: k.jumlah
        };
    });

    $('#loadingOrder').removeClass('d-none');
    $('#btnKirimOrder').prop('disabled', true);

    // Gunakan FormData agar bisa kirim file
    let formData = new FormData();
    formData.append('action', 'create');
    formData.append('jenis_transaksi', 'konveksi');
    formData.append('jenis_pembayaran', $('#co_pembayaran').val());
    formData.append('tanggal_selesai', $('#co_tgl_selesai').val());
    formData.append('deskripsi', $('#co_deskripsi').val());
    formData.append('items', JSON.stringify(items));
    if (file) {
        formData.append('file_desain', file);
    }

    $.ajax({
        url: '/konveksi/api/transaksi.php',
        type: 'POST',
        data: formData,
        processData: false, // WAJIB untuk FormData
        contentType: false, // WAJIB untuk FormData
        dataType: 'json',
        success: function(res) {
            $('#loadingOrder').addClass('d-none');
            $('#btnKirimOrder').prop('disabled', false);
            if (res.success) {
                keranjang = [];
                updateKeranjangBar();
                $('#modalCheckout').modal('hide');
                showToast('Pesanan konveksi berhasil dibuat!');
                setTimeout(function() {
                    window.location.href = '/konveksi/pelanggan/transaksi.php';
                }, 2000);
            } else {
                $('#alertCheckout').html('<div class="alert alert-danger">' +
                    (res.error || 'Gagal membuat pesanan.') + '</div>');
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

// Preview file desain saat dipilih
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

$(document).ready(function() {
    loadProduk();
    $('#searchProduk').on('keyup', loadProduk);
});
</script>