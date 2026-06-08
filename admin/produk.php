<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php');
    exit;
}
require_once '../config/db.php';

// Ambil ukuran model untuk dropdown
$ukuran_list = $conn->query("SELECT * FROM ukuran_model ORDER BY jenis");
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Data Produk</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Produk</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </button>
        </div>

        <!-- Filter -->
        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <input type="text" id="searchProduk" class="form-control form-control-sm"
                    placeholder="🔍 Cari nama produk...">
            </div>
            <div class="col-md-3">
                <select id="filterJenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="pakaian_jadi">Pakaian Jadi</option>
                    <option value="konveksi">Konveksi</option>
                </select>
            </div>
        </div>

        <!-- Grid Produk -->
        <div class="row g-3" id="gridProduk">
            <div class="col-12 text-center py-4">
                <div class="spinner-border text-primary"></div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" id="add_nama" class="form-control" placeholder="Masukkan nama produk">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <input type="text" id="add_kategori" class="form-control"
                            placeholder="Contoh: Kaos, Jaket, Seragam">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis</label>
                        <select id="add_jenis" class="form-select">
                            <option value="jahit_satuan">Jahit Satuan</option>
                            <option value="pakaian_jadi">Pakaian Jadi</option>
                            <option value="konveksi">Konveksi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" id="add_harga" class="form-control" placeholder="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stok</label>
                        <input type="number" id="add_stok" class="form-control" placeholder="0" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" id="add_model" class="form-control" placeholder="Contoh: Slim fit, Regular">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ukuran Referensi</label>
                        <select id="add_ukuran_model" class="form-select">
                            <option value="">-- Tidak ada --</option>
                            <?php while ($u = $ukuran_list->fetch_assoc()): ?>
                            <option value="<?= $u['id_ukuran_model'] ?>">
                                <?= htmlspecialchars($u['jenis'] . ' - ' . $u['ukuran']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea id="add_deskripsi" class="form-control" rows="2"
                            placeholder="Deskripsi singkat produk"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" id="add_gambar" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img id="preview_add" src="" class="img-thumbnail d-none" style="max-height:150px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanTambah">
                    <span id="loadingTambah" class="spinner-border spinner-border-sm d-none"></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" id="edit_nama" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategori</label>
                        <input type="text" id="edit_kategori" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis</label>
                        <select id="edit_jenis" class="form-select">
                            <option value="jahit_satuan">Jahit Satuan</option>
                            <option value="pakaian_jadi">Pakaian Jadi</option>
                            <option value="konveksi">Konveksi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" id="edit_harga" class="form-control" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stok</label>
                        <input type="number" id="edit_stok" class="form-control" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" id="edit_model" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea id="edit_deskripsi" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ganti Gambar <small class="text-muted">(kosongkan jika tidak
                                diubah)</small></label>
                        <input type="file" id="edit_gambar" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img id="preview_edit" src="" class="img-thumbnail" style="max-height:120px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnSimpanEdit">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="isiDetail"></div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    loadProduk();

    function loadProduk() {
        let q = $('#searchProduk').val();
        let jenis = $('#filterJenis').val();
        $.get('/konveksi/api/produk.php?action=list&q=' + q + '&jenis=' + jenis, function(data) {
            let grid = $('#gridProduk');
            grid.empty();
            if (data.length === 0) {
                grid.html(
                    '<p class="text-muted text-center col-12 py-4">Tidak ada produk ditemukan.</p>');
                return;
            }
            data.forEach(p => {
                let img = p.gambar ? '/konveksi/assets/uploads/' + p.gambar :
                    'https://via.placeholder.com/300x180?text=No+Image';
                let jBadge = p.jenis === 'pakaian_jadi' ? 'success' : p.jenis === 'konveksi' ?
                    'primary' : 'warning';
                grid.append(`
                    <div class="col-md-3 col-6">
                        <div class="card h-100 shadow-sm">
                            <img src="${img}" class="card-img-top" style="height:220px;object-fit:cover">
                            <div class="card-body p-2">
                                <span class="badge bg-${jBadge} mb-1" style="font-size:.7rem">${p.jenis.replace(/_/g,' ')}</span>
                                <h6 class="card-title mb-1" style="font-size:.9rem">${p.nama_produk}</h6>
                                <p class="text-success fw-bold mb-1">Rp ${parseInt(p.harga).toLocaleString('id-ID')}</p>
                                <p class="text-muted mb-0" style="font-size:.8rem">Stok: ${p.stok}</p>
                            </div>
                            <div class="card-footer bg-white p-2 d-flex gap-1">
                                <button class="btn btn-sm btn-info flex-fill" onclick="lihatDetail(${p.id_produk})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning flex-fill" onclick="bukaEdit(${p.id_produk})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger flex-fill" onclick="hapusProduk(${p.id_produk}, this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });
        }, 'json');
    }

    $('#searchProduk').on('keyup', loadProduk);
    $('#filterJenis').on('change', loadProduk);

    // Preview gambar tambah
    $('#add_gambar').on('change', function() {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = e => {
                $('#preview_add').attr('src', e.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Preview gambar edit
    $('#edit_gambar').on('change', function() {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = e => {
                $('#preview_edit').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Simpan tambah
    $('#btnSimpanTambah').click(function() {
        let nama = $('#add_nama').val().trim();
        let harga = $('#add_harga').val();
        if (!nama || !harga) {
            alert('Nama dan harga wajib diisi!');
            return;
        }

        $('#loadingTambah').removeClass('d-none');
        let formData = new FormData();
        formData.append('action', 'create');
        formData.append('nama_produk', nama);
        formData.append('kategori', $('#add_kategori').val());
        formData.append('jenis', $('#add_jenis').val());
        formData.append('harga', harga);
        formData.append('stok', $('#add_stok').val() || 0);
        formData.append('model', $('#add_model').val());
        formData.append('deskripsi', $('#add_deskripsi').val());
        formData.append('id_ukuran_model', $('#add_ukuran_model').val());
        if ($('#add_gambar')[0].files[0]) {
            formData.append('gambar', $('#add_gambar')[0].files[0]);
        }

        $.ajax({
            url: '/konveksi/api/produk.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#loadingTambah').addClass('d-none');
                if (res.success) {
                    $('#modalTambah').modal('hide');
                    loadProduk();
                    alert('Produk berhasil ditambahkan!');
                } else {
                    alert('Error: ' + res.error);
                }
            },
            dataType: 'json'
        });
    });

    // Simpan edit
    $('#btnSimpanEdit').click(function() {
        let id = $('#edit_id').val();
        let nama = $('#edit_nama').val().trim();
        let harga = $('#edit_harga').val();
        if (!nama || !harga) {
            alert('Nama dan harga wajib diisi!');
            return;
        }

        let formData = new FormData();
        formData.append('action', 'update');
        formData.append('id_produk', id);
        formData.append('nama_produk', nama);
        formData.append('kategori', $('#edit_kategori').val());
        formData.append('jenis', $('#edit_jenis').val());
        formData.append('harga', harga);
        formData.append('stok', $('#edit_stok').val() || 0);
        formData.append('model', $('#edit_model').val());
        formData.append('deskripsi', $('#edit_deskripsi').val());
        if ($('#edit_gambar')[0].files[0]) {
            formData.append('gambar', $('#edit_gambar')[0].files[0]);
        }

        $.ajax({
            url: '/konveksi/api/produk.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    $('#modalEdit').modal('hide');
                    loadProduk();
                    alert('Produk berhasil diperbarui!');
                } else {
                    alert('Error: ' + res.error);
                }
            },
            dataType: 'json'
        });
    });
});

function lihatDetail(id) {
    $.get('/konveksi/api/produk.php?action=detail_publik&id=' + id, function(p) {
        let img = p.gambar ? '/konveksi/assets/uploads/' + p.gambar :
            'https://via.placeholder.com/400x200?text=No+Image';
        $('#isiDetail').html(`
            <img src="${img}" class="img-fluid rounded mb-3" style="width:100%;max-height:250px;object-fit:cover">
            <table class="table table-sm table-bordered">
                <tr><th>Nama</th><td>${p.nama_produk}</td></tr>
                <tr><th>Kategori</th><td>${p.kategori || '-'}</td></tr>
                <tr><th>Jenis</th><td>${p.jenis.replace(/_/g,' ')}</td></tr>
                <tr><th>Harga</th><td>Rp ${parseInt(p.harga).toLocaleString('id-ID')}</td></tr>
                <tr><th>Stok</th><td>${p.stok} pcs</td></tr>
                <tr><th>Model</th><td>${p.model || '-'}</td></tr>
                <tr><th>Deskripsi</th><td>${p.deskripsi || '-'}</td></tr>
            </table>
        `);
        new bootstrap.Modal(document.getElementById('modalDetail')).show();
    }, 'json');
}

function bukaEdit(id) {
    $.get('/konveksi/api/produk.php?action=detail_publik&id=' + id, function(p) {
        $('#edit_id').val(p.id_produk);
        $('#edit_nama').val(p.nama_produk);
        $('#edit_kategori').val(p.kategori);
        $('#edit_jenis').val(p.jenis);
        $('#edit_harga').val(p.harga);
        $('#edit_stok').val(p.stok);
        $('#edit_model').val(p.model);
        $('#edit_deskripsi').val(p.deskripsi);
        let img = p.gambar ? '/konveksi/assets/uploads/' + p.gambar :
            'https://via.placeholder.com/300x150?text=No+Image';
        $('#preview_edit').attr('src', img);
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }, 'json');
}

function hapusProduk(id, btn) {
    let nama = $(btn).closest('.card').find('.card-title').text();
    if (!confirm('Yakin hapus produk "' + nama + '"?')) return;
    $.post('/konveksi/api/produk.php', {
        action: 'delete',
        id_produk: id
    }, function(res) {
        if (res.success) {
            alert('Produk berhasil dihapus.');
            location.reload();
        } else {
            alert('Error: ' + res.error);
        }
    }, 'json');
}
</script>