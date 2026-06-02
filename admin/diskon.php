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
        <span>Kelola Diskon</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Diskon</h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Tambah Diskon
            </button>
        </div>

        <!-- Filter -->
        <div class="row mb-3 g-2">
            <div class="col-md-3">
                <select id="filterJenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="loyalitas">Loyalitas</option>
                    <option value="dinamis">Dinamis</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>

        <!-- Info Box -->
        <div class="row g-3 mb-4" id="infoBox">
            <div class="col-md-4">
                <div class="card text-white bg-success text-center p-3">
                    <div class="fs-3 fw-bold" id="totalAktif">-</div>
                    <div class="small">Diskon Aktif</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-secondary text-center p-3">
                    <div class="fs-3 fw-bold" id="totalNonaktif">-</div>
                    <div class="small">Diskon Nonaktif</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-primary text-center p-3">
                    <div class="fs-3 fw-bold" id="totalSemua">-</div>
                    <div class="small">Total Diskon</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nama Diskon</th>
                            <th>Jenis</th>
                            <th>Persentase</th>
                            <th>Syarat</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyDiskon"></tbody>
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
                <h5 class="modal-title">Tambah Diskon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Diskon</label>
                    <input type="text" id="add_nama" class="form-control" placeholder="Contoh: Diskon Pelanggan Setia">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Diskon</label>
                    <select id="add_jenis" class="form-select">
                        <option value="loyalitas">Loyalitas (berdasarkan riwayat pelanggan)</option>
                        <option value="dinamis">Dinamis (berdasarkan jumlah order)</option>
                    </select>
                    <div class="form-text" id="infoJenis">
                        Diskon loyalitas diberikan kepada pelanggan yang sudah sering bertransaksi.
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Persentase Diskon (%)</label>
                    <div class="input-group">
                        <input type="number" id="add_persentase" class="form-control" placeholder="Contoh: 10" min="1"
                            max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Syarat <small class="text-muted">(opsional)</small></label>
                    <input type="text" id="add_syarat" class="form-control"
                        placeholder="Contoh: Minimal 5 transaksi, atau Minimal order Rp 500.000">
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
                <h5 class="modal-title">Edit Diskon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label class="form-label">Nama Diskon</label>
                    <input type="text" id="edit_nama" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jenis Diskon</label>
                    <select id="edit_jenis" class="form-select">
                        <option value="loyalitas">Loyalitas</option>
                        <option value="dinamis">Dinamis</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Persentase Diskon (%)</label>
                    <div class="input-group">
                        <input type="number" id="edit_persentase" class="form-control" min="1" max="100">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Syarat</label>
                    <input type="text" id="edit_syarat" class="form-control">
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
    loadDiskon();

    function loadDiskon() {
        let jenis = $('#filterJenis').val();
        let status = $('#filterStatus').val();
        $.get('/konveksi/api/diskon.php?action=list', function(data) {
            let tbody = $('#bodyDiskon');
            let aktif = 0;
            let nonaktif = 0;
            tbody.empty();

            let filtered = data.filter(d => {
                if (jenis && d.jenis_diskon !== jenis) return false;
                if (status && d.status !== status) return false;
                return true;
            });

            data.forEach(d => {
                if (d.status === 'aktif') aktif++;
                else nonaktif++;
            });

            $('#totalAktif').text(aktif);
            $('#totalNonaktif').text(nonaktif);
            $('#totalSemua').text(data.length);

            if (filtered.length === 0) {
                tbody.html(
                    '<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data diskon</td></tr>'
                    );
                return;
            }

            filtered.forEach((d, i) => {
                let jBadge = d.jenis_diskon === 'loyalitas' ? 'info' : 'warning';
                let sBadge = d.status === 'aktif' ? 'success' : 'secondary';
                let toggleBtn = d.status === 'aktif' ?
                    `<button class="btn btn-sm btn-secondary" onclick="toggleStatus(${d.id_diskon}, 'nonaktif')" title="Nonaktifkan">
                            <i class="bi bi-pause-circle"></i>
                       </button>` :
                    `<button class="btn btn-sm btn-success" onclick="toggleStatus(${d.id_diskon}, 'aktif')" title="Aktifkan">
                            <i class="bi bi-play-circle"></i>
                       </button>`;

                tbody.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td><strong>${d.nama_diskon}</strong></td>
                        <td><span class="badge bg-${jBadge}">${d.jenis_diskon}</span></td>
                        <td>
                            <span class="fs-5 fw-bold text-success">${d.persentase}%</span>
                        </td>
                        <td>${d.syarat || '<span class="text-muted">-</span>'}</td>
                        <td><span class="badge bg-${sBadge}">${d.status}</span></td>
                        <td>${d.created_at.substring(0, 10)}</td>
                        <td class="d-flex gap-1">
                            ${toggleBtn}
                            <button class="btn btn-sm btn-warning"
                                onclick="bukaEdit(${d.id_diskon}, '${d.nama_diskon}', '${d.jenis_diskon}', ${d.persentase}, \`${d.syarat || ''}\`)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger"
                                onclick="hapusDiskon(${d.id_diskon}, '${d.nama_diskon}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }, 'json');
    }

    $('#filterJenis, #filterStatus').on('change', loadDiskon);

    // Info jenis dinamis
    $('#add_jenis').on('change', function() {
        let info = {
            loyalitas: 'Diskon loyalitas diberikan kepada pelanggan yang sudah sering bertransaksi.',
            dinamis: 'Diskon dinamis diberikan berdasarkan jumlah atau nilai order tertentu.'
        };
        $('#infoJenis').text(info[$(this).val()]);
    });

    // Simpan tambah
    $('#btnSimpanTambah').click(function() {
        let nama = $('#add_nama').val().trim();
        let persentase = $('#add_persentase').val();
        if (!nama || !persentase) {
            alert('Nama dan persentase wajib diisi!');
            return;
        }
        if (persentase < 1 || persentase > 100) {
            alert('Persentase harus antara 1 - 100!');
            return;
        }
        $.post('/konveksi/api/diskon.php', {
            action: 'create',
            nama_diskon: nama,
            jenis_diskon: $('#add_jenis').val(),
            persentase,
            syarat: $('#add_syarat').val()
        }, function(res) {
            if (res.success) {
                $('#modalTambah').modal('hide');
                $('#add_nama, #add_persentase, #add_syarat').val('');
                loadDiskon();
                alert('Diskon berhasil ditambahkan!');
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });

    // Simpan edit
    $('#btnSimpanEdit').click(function() {
        let id = $('#edit_id').val();
        let nama = $('#edit_nama').val().trim();
        let persentase = $('#edit_persentase').val();
        if (!nama || !persentase) {
            alert('Nama dan persentase wajib diisi!');
            return;
        }
        if (persentase < 1 || persentase > 100) {
            alert('Persentase harus antara 1 - 100!');
            return;
        }
        $.post('/konveksi/api/diskon.php', {
            action: 'update',
            id_diskon: id,
            nama_diskon: nama,
            jenis_diskon: $('#edit_jenis').val(),
            persentase,
            syarat: $('#edit_syarat').val()
        }, function(res) {
            if (res.success) {
                $('#modalEdit').modal('hide');
                loadDiskon();
                alert('Diskon berhasil diperbarui!');
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });
});

function bukaEdit(id, nama, jenis, persentase, syarat) {
    $('#edit_id').val(id);
    $('#edit_nama').val(nama);
    $('#edit_jenis').val(jenis);
    $('#edit_persentase').val(persentase);
    $('#edit_syarat').val(syarat);
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function toggleStatus(id, status) {
    let pesan = status === 'aktif' ? 'Aktifkan diskon ini?' : 'Nonaktifkan diskon ini?';
    if (!confirm(pesan)) return;
    $.post('/konveksi/api/diskon.php', {
        action: 'toggle',
        id_diskon: id,
        status
    }, function(res) {
        if (res.success) {
            loadDiskon();
        } else {
            alert('Error: ' + res.error);
        }
    }, 'json');
}

function hapusDiskon(id, nama) {
    if (!confirm('Yakin hapus diskon "' + nama + '"?')) return;
    $.post('/konveksi/api/diskon.php', {
        action: 'delete',
        id_diskon: id
    }, function(res) {
        if (res.success) {
            loadDiskon();
            alert('Diskon berhasil dihapus.');
        } else {
            alert('Error: ' + res.error);
        }
    }, 'json');
}

function loadDiskon() {
    // sudah didefinisikan di dalam $(document).ready
    // fungsi ini dibuat global agar bisa dipanggil dari luar
    $('#filterJenis').trigger('change');
}
</script>