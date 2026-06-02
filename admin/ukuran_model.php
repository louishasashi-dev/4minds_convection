<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php');
    exit;
}
require_once '../config/db.php';
?>
<?php require_once '../includes/sidebar_admin.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Ukuran & Model</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Data Ukuran & Model</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Ukuran
            </button>
        </div>

        <!-- Filter -->
        <div class="row mb-3 g-2">
            <div class="col-md-4">
                <input type="text" id="searchUkuran" class="form-control form-control-sm"
                    placeholder="🔍 Cari jenis / ukuran...">
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Jenis</th>
                            <th>Ukuran</th>
                            <th>Deskripsi</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyUkuran"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Ukuran & Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Jenis Pakaian</label>
                    <input type="text" id="add_jenis" class="form-control"
                        placeholder="Contoh: Kaos, Kemeja, Jaket, Celana">
                    <div class="mt-2 d-flex flex-wrap gap-1" id="jenisQuick">
                        <span class="badge bg-secondary" style="cursor:pointer" onclick="pilihJenis('Kaos')">Kaos</span>
                        <span class="badge bg-secondary" style="cursor:pointer"
                            onclick="pilihJenis('Kemeja')">Kemeja</span>
                        <span class="badge bg-secondary" style="cursor:pointer"
                            onclick="pilihJenis('Jaket')">Jaket</span>
                        <span class="badge bg-secondary" style="cursor:pointer"
                            onclick="pilihJenis('Celana')">Celana</span>
                        <span class="badge bg-secondary" style="cursor:pointer"
                            onclick="pilihJenis('Gamis')">Gamis</span>
                        <span class="badge bg-secondary" style="cursor:pointer"
                            onclick="pilihJenis('Seragam')">Seragam</span>
                        <span class="badge bg-secondary" style="cursor:pointer" onclick="pilihJenis('Jas')">Jas</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ukuran</label>
                    <input type="text" id="add_ukuran" class="form-control"
                        placeholder="Contoh: S, M, L, XL, XXL, atau 38, 40, 42">
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        <span class="badge bg-info" style="cursor:pointer" onclick="pilihUkuran('S')">S</span>
                        <span class="badge bg-info" style="cursor:pointer" onclick="pilihUkuran('M')">M</span>
                        <span class="badge bg-info" style="cursor:pointer" onclick="pilihUkuran('L')">L</span>
                        <span class="badge bg-info" style="cursor:pointer" onclick="pilihUkuran('XL')">XL</span>
                        <span class="badge bg-info" style="cursor:pointer" onclick="pilihUkuran('XXL')">XXL</span>
                        <span class="badge bg-info" style="cursor:pointer" onclick="pilihUkuran('XXXL')">XXXL</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi <small class="text-muted">(opsional)</small></label>
                    <textarea id="add_deskripsi" class="form-control" rows="2"
                        placeholder="Contoh: Lingkar dada 88cm, panjang 68cm"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanTambah">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ukuran & Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Jenis Pakaian</label>
                    <input type="text" id="edit_jenis" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Ukuran</label>
                    <input type="text" id="edit_ukuran" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="edit_deskripsi" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnSimpanEdit">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    loadUkuran();

    function loadUkuran(keyword = '') {
        $.get('/konveksi/api/ukuran_model.php?action=list&q=' + keyword, function(data) {
            let tbody = $('#bodyUkuran');
            tbody.empty();
            if (data.length === 0) {
                tbody.html(
                    '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data ukuran</td></tr>'
                    );
                return;
            }
            data.forEach((u, i) => {
                tbody.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td><span class="badge bg-primary">${u.jenis}</span></td>
                        <td><strong>${u.ukuran}</strong></td>
                        <td>${u.deskripsi || '<span class="text-muted">-</span>'}</td>
                        <td>${u.created_at.substring(0, 10)}</td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                onclick="bukaEdit(${u.id_ukuran_model}, '${u.jenis}', '${u.ukuran}', \`${u.deskripsi || ''}\`)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger"
                                onclick="hapusUkuran(${u.id_ukuran_model}, '${u.jenis} - ${u.ukuran}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }, 'json');
    }

    $('#searchUkuran').on('keyup', function() {
        loadUkuran($(this).val());
    });

    // Simpan tambah
    $('#btnSimpanTambah').click(function() {
        let jenis = $('#add_jenis').val().trim();
        let ukuran = $('#add_ukuran').val().trim();
        if (!jenis || !ukuran) {
            alert('Jenis dan ukuran wajib diisi!');
            return;
        }
        $.post('/konveksi/api/ukuran_model.php', {
            action: 'create',
            jenis,
            ukuran,
            deskripsi: $('#add_deskripsi').val()
        }, function(res) {
            if (res.success) {
                $('#modalTambah').modal('hide');
                $('#add_jenis, #add_ukuran, #add_deskripsi').val('');
                loadUkuran();
                alert('Ukuran berhasil ditambahkan!');
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });

    // Simpan edit
    $('#btnSimpanEdit').click(function() {
        let id = $('#edit_id').val();
        let jenis = $('#edit_jenis').val().trim();
        let ukuran = $('#edit_ukuran').val().trim();
        if (!jenis || !ukuran) {
            alert('Jenis dan ukuran wajib diisi!');
            return;
        }
        $.post('/konveksi/api/ukuran_model.php', {
            action: 'update',
            id,
            jenis,
            ukuran,
            deskripsi: $('#edit_deskripsi').val()
        }, function(res) {
            if (res.success) {
                $('#modalEdit').modal('hide');
                loadUkuran();
                alert('Ukuran berhasil diperbarui!');
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });
});

function pilihJenis(val) {
    $('#add_jenis').val(val);
}

function pilihUkuran(val) {
    $('#add_ukuran').val(val);
}

function bukaEdit(id, jenis, ukuran, deskripsi) {
    $('#edit_id').val(id);
    $('#edit_jenis').val(jenis);
    $('#edit_ukuran').val(ukuran);
    $('#edit_deskripsi').val(deskripsi);
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function hapusUkuran(id, label) {
    if (!confirm('Yakin hapus ukuran "' + label + '"?')) return;
    $.post('/konveksi/api/ukuran_model.php', {
        action: 'delete',
        id
    }, function(res) {
        if (res.success) {
            loadUkuran();
            alert('Ukuran berhasil dihapus.');
        } else {
            alert('Error: ' + res.error);
        }
    }, 'json');
}
</script>