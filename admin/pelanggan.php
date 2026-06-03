<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /konveksi/auth/login.php'); exit;
}
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Pelanggan</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    .sidebar {
        min-height: 100vh;
        background: #2c3e50;
    }

    .sidebar a {
        color: #ecf0f1;
        text-decoration: none;
        display: block;
        padding: 10px 20px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background: #1abc9c;
        color: #fff;
    }

    .sidebar .brand {
        padding: 20px;
        font-size: 1.2rem;
        font-weight: bold;
        color: #fff;
        border-bottom: 1px solid #34495e;
    }

    .main-content {
        padding: 20px;
    }

    .topbar {
        background: #fff;
        padding: 10px 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal {
        display: none !important;
    }

    .modal.show {
        display: block !important;
    }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php require_once '../includes/sidebar_admin.php'; ?>
        <div class="flex-grow-1">
            <div class="topbar">
                <span>Data Pelanggan</span>
                <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            </div>
            <div class="main-content">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Daftar Pelanggan</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle"></i> Tambah Pelanggan
                    </button>
                </div>

                <div class="mb-3">
                    <input type="text" id="searchPelanggan" class="form-control" placeholder="🔍 Cari nama / email..."
                        autocomplete="new-password">
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Alamat</th>
                                    <th>Terdaftar</th>
                                    <th>Transaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bodyPelanggan"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label>
                        <input type="text" id="add_name" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" id="add_email" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Password</label>
                        <input type="password" id="add_password" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">No HP</label>
                        <input type="text" id="add_nohp" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Alamat</label>
                        <textarea id="add_alamat" class="form-control" rows="2"></textarea>
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
                    <h5 class="modal-title">Edit Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <div class="mb-3"><label class="form-label">Nama Lengkap</label>
                        <input type="text" id="edit_name" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Email</label>
                        <input type="email" id="edit_email" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">No HP</label>
                        <input type="text" id="edit_nohp" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Alamat</label>
                        <textarea id="edit_alamat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3"><label class="form-label">Password Baru <small class="text-muted">(kosongkan jika
                                tidak diubah)</small></label>
                        <input type="password" id="edit_password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning" id="btnSimpanEdit">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Transaksi -->
    <div class="modal fade" id="modalDetailTrx" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riwayat Transaksi Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="isiDetailTrx"></div>
            </div>
        </div>
    </div>

    <!-- Modal Ukuran -->
    <div class="modal fade" id="modalUkuran" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ukuran — <span id="labelNamaPelanggan"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ukuran_id_pelanggan">
                    <div id="infoUkuranTersimpan" class="alert alert-secondary py-2 mb-3 d-none">
                        <small class="text-muted">Tersimpan saat ini:</small>
                        <div id="nilaiUkuranTersimpan" class="fw-bold mt-1"></div>
                    </div>
                    <div id="infoUkuranKosong" class="alert alert-warning py-2 mb-3 d-none">
                        Belum ada data ukuran.
                    </div>
                    <div class="mb-3"><label class="form-label">Ukuran (contoh: M, L, XL, 42)</label>
                        <input type="text" id="input_ukuran" class="form-control">
                    </div>
                    <div class="mb-3"><label class="form-label">Catatan <small
                                class="text-muted">(opsional)</small></label>
                        <textarea id="input_catatan_ukuran" class="form-control" rows="3"></textarea>
                    </div>
                    <div id="alertUkuranAdmin"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanUkuran">
                        <i class="bi bi-save"></i> Simpan Ukuran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {

        // Reset semua modal saat load
        document.querySelectorAll('.modal').forEach(function(el) {
            el.style.display = 'none';
            el.classList.remove('show');
            el.removeAttribute('aria-modal');
            el.setAttribute('aria-hidden', 'true');
        });
        document.querySelectorAll('.modal-backdrop').forEach(function(el) {
            el.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');

        $('#searchPelanggan').val('').attr('readonly', true);
        setTimeout(function() {
            $('#searchPelanggan').attr('readonly', false);
            loadPelanggan();
        }, 300);

        $('#searchPelanggan').on('input', function() {
            loadPelanggan($(this).val());
        });

        $(document).on('click', '.btn-edit', function() {
            let d = $(this).data();
            $('#edit_id').val(d.id);
            $('#edit_name').val(d.name);
            $('#edit_email').val(d.email);
            $('#edit_nohp').val(d.nohp);
            $('#edit_alamat').val(d.alamat);
            $('#edit_password').val('');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit')).show();
        });

        $(document).on('click', '.btn-ukuran', function() {
            let id = $(this).data('id');
            let nama = $(this).data('name');
            $('#ukuran_id_pelanggan').val(id);
            $('#labelNamaPelanggan').text(nama);
            $('#input_ukuran').val('');
            $('#input_catatan_ukuran').val('');
            $('#alertUkuranAdmin').html('');
            $('#infoUkuranTersimpan').addClass('d-none');
            $('#infoUkuranKosong').addClass('d-none');

            $.get('/konveksi/api/ukuran_pelanggan.php?action=list', function(data) {
                let ukuran = data.find(u => u.id_pelanggan == id);
                if (ukuran) {
                    $('#nilaiUkuranTersimpan').html('<span class="badge bg-secondary me-2">' +
                        ukuran.ukuran + '</span>' +
                        (ukuran.catatan ? '<span class="text-muted">' + ukuran.catatan +
                            '</span>' : ''));
                    $('#infoUkuranTersimpan').removeClass('d-none');
                    $('#input_ukuran').val(ukuran.ukuran);
                    $('#input_catatan_ukuran').val(ukuran.catatan || '');
                } else {
                    $('#infoUkuranKosong').removeClass('d-none');
                }
            }, 'json');

            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUkuran')).show();
        });

        $(document).on('click', '.btn-hapus', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            if (!confirm('Yakin hapus pelanggan "' + name + '"?')) return;
            $.post('/konveksi/api/pelanggan.php', {
                action: 'delete',
                id: id
            }, function(res) {
                if (res.success) {
                    loadPelanggan();
                    alert('Pelanggan berhasil dihapus.');
                } else alert('Error: ' + res.error);
            }, 'json');
        });

        $(document).on('click', '.btn-trx', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            $('#isiDetailTrx').html(
                '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>'
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalDetailTrx')).show();
            $.get('/konveksi/api/pelanggan.php?action=transaksi&id=' + id, function(data) {
                if (data.length === 0) {
                    $('#isiDetailTrx').html(
                        '<p class="text-muted text-center">Belum ada transaksi.</p>');
                    return;
                }
                let html = '<h6 class="mb-3">Transaksi: <strong>' + name + '</strong></h6>';
                html +=
                    '<table class="table table-sm table-bordered"><thead class="table-dark"><tr><th>#</th><th>Jenis</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Tanggal</th></tr></thead><tbody>';
                data.forEach(t => {
                    let badge = t.status === 'lunas' ? 'success' : t.status ===
                        'pending' ? 'warning' : 'info';
                    html += '<tr><td>' + t.id_transaksi + '</td><td>' + t
                        .jenis_transaksi.replace(/_/g, ' ') + '</td><td>Rp ' +
                        parseInt(t.total_harga).toLocaleString('id-ID') + '</td><td>' +
                        t.jenis_pembayaran +
                        '</td><td><span class="badge bg-' + badge + '">' + t.status +
                        '</span></td><td>' +
                        t.tanggal_transaksi.substring(0, 10) + '</td></tr>';
                });
                html += '</tbody></table>';
                $('#isiDetailTrx').html(html);
            }, 'json');
        });

        $('#btnSimpanTambah').click(function() {
            let name = $('#add_name').val().trim();
            let email = $('#add_email').val().trim();
            let password = $('#add_password').val();
            let no_hp = $('#add_nohp').val().trim();
            let alamat = $('#add_alamat').val().trim();
            if (!name || !email || !password) {
                alert('Nama, email, password wajib diisi!');
                return;
            }
            $.post('/konveksi/api/pelanggan.php', {
                action: 'create',
                name,
                email,
                password,
                no_hp,
                alamat
            }, function(res) {
                if (res.success) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTambah'))
                        .hide();
                    $('#add_name,#add_email,#add_password,#add_nohp,#add_alamat').val('');
                    loadPelanggan();
                    alert('Pelanggan berhasil ditambahkan!');
                } else alert('Error: ' + res.error);
            }, 'json');
        });

        $('#btnSimpanEdit').click(function() {
            let id = $('#edit_id').val();
            let name = $('#edit_name').val().trim();
            let email = $('#edit_email').val().trim();
            let no_hp = $('#edit_nohp').val().trim();
            let alamat = $('#edit_alamat').val().trim();
            let password = $('#edit_password').val();
            if (!name || !email) {
                alert('Nama dan email wajib diisi!');
                return;
            }
            $.post('/konveksi/api/pelanggan.php', {
                action: 'update',
                id,
                name,
                email,
                no_hp,
                alamat,
                password
            }, function(res) {
                if (res.success) {
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEdit'))
                        .hide();
                    loadPelanggan();
                    alert('Data berhasil diperbarui!');
                } else alert('Error: ' + res.error);
            }, 'json');
        });

        $('#btnSimpanUkuran').click(function() {
            let id_pelanggan = $('#ukuran_id_pelanggan').val();
            let ukuran = $('#input_ukuran').val().trim();
            let catatan = $('#input_catatan_ukuran').val().trim();
            if (!ukuran) {
                $('#alertUkuranAdmin').html(
                    '<div class="alert alert-warning py-2">Ukuran tidak boleh kosong.</div>');
                return;
            }
            $.post('/konveksi/api/ukuran_pelanggan.php', {
                action: 'save',
                id_pelanggan,
                ukuran,
                catatan
            }, function(res) {
                if (res.success) {
                    $('#alertUkuranAdmin').html(
                        '<div class="alert alert-success py-2">Ukuran berhasil disimpan!</div>'
                    );
                    loadPelanggan();
                    setTimeout(() => bootstrap.Modal.getOrCreateInstance(document
                        .getElementById('modalUkuran')).hide(), 1000);
                } else {
                    $('#alertUkuranAdmin').html('<div class="alert alert-danger py-2">' + (res
                        .error || 'Gagal menyimpan.') + '</div>');
                }
            }, 'json');
        });

    });

    function loadPelanggan(keyword = '') {
        $.get('/konveksi/api/pelanggan.php?action=list&q=' + encodeURIComponent(keyword), function(data) {
            let tbody = $('#bodyPelanggan');
            tbody.empty();
            if (!Array.isArray(data) || data.length === 0) {
                tbody.html(
                    '<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data pelanggan</td></tr>'
                );
                return;
            }
            data.forEach(function(p, i) {
                let tr = document.createElement('tr');
                tr.innerHTML =
                    '<td>' + (i + 1) + '</td>' +
                    '<td>' + p.name + '</td>' +
                    '<td>' + p.email + '</td>' +
                    '<td>' + (p.no_hp || '-') + '</td>' +
                    '<td>' + (p.alamat || '-') + '</td>' +
                    '<td>' + p.created_at.substring(0, 10) + '</td>' +
                    '<td><span class="badge bg-primary btn-trx" style="cursor:pointer" data-id="' + p
                    .id_pelanggan + '" data-name="' + p.name + '">' + p.total_transaksi +
                    ' transaksi</span></td>' +
                    '<td>' +
                    '<button class="btn btn-sm btn-warning btn-edit me-1" data-id="' + p.id_pelanggan +
                    '" data-name="' + p.name + '" data-email="' + p.email + '" data-nohp="' + (p
                        .no_hp || '') + '" data-alamat="' + (p.alamat || '') +
                    '"><i class="bi bi-pencil"></i></button>' +
                    '<button class="btn btn-sm btn-info btn-ukuran me-1" data-id="' + p.id_pelanggan +
                    '" data-name="' + p.name + '"><i class="bi bi-rulers"></i></button>' +
                    '<button class="btn btn-sm btn-danger btn-hapus" data-id="' + p.id_pelanggan +
                    '" data-name="' + p.name + '"><i class="bi bi-trash"></i></button>' +
                    '</td>';
                tbody.append(tr);
            });
        }, 'json');
    }
    </script>
</body>

</html>