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

        <!-- Search -->
        <div class="mb-3">
            <input type="text" id="searchPelanggan" class="form-control" style="max-width:300px"
                placeholder="🔍 Cari nama / email...">
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-bordered mb-0" id="tabelPelanggan">
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="add_name" class="form-control" placeholder="Masukkan nama lengkap">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="add_email" class="form-control" placeholder="Masukkan email">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" id="add_password" class="form-control" placeholder="Masukkan password">
                </div>
                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <input type="text" id="add_nohp" class="form-control" placeholder="Masukkan no HP">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea id="add_alamat" class="form-control" rows="2" placeholder="Masukkan alamat"></textarea>
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
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="edit_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="edit_email" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <input type="text" id="edit_nohp" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea id="edit_alamat" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru <small class="text-muted">(kosongkan jika tidak
                            diubah)</small></label>
                    <input type="password" id="edit_password" class="form-control"
                        placeholder="Isi jika ingin ganti password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="btnSimpanEdit">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi Pelanggan -->
<div class="modal fade" id="modalDetailTrx" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Transaksi Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="isiDetailTrx">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ukuran Pelanggan -->
<div class="modal fade" id="modalUkuran" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ukuran Tubuh — <span id="labelNamaPelanggan"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ukuran_id_pelanggan">

                <!-- Data yang sudah tersimpan -->
                <div id="infoUkuranTersimpan" class="alert alert-secondary py-2 mb-3" style="display:none">
                    <small class="text-muted">Data tersimpan saat ini:</small>
                    <div id="nilaiUkuranTersimpan" class="fw-bold mt-1"></div>
                </div>
                <div id="infoUkuranKosong" class="alert alert-warning py-2 mb-3" style="display:none">
                    <i class="bi bi-exclamation-triangle"></i> Pelanggan ini belum memiliki data ukuran.
                </div>

                <div class="mb-3">
                    <label class="form-label">Ukuran / Kode Ukuran <small class="text-muted">(contoh: M, L, XL, atau
                            42)</small></label>
                    <input type="text" id="input_ukuran" class="form-control" placeholder="contoh: L atau 42">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Ukuran <small class="text-muted">(opsional)</small></label>
                    <textarea id="input_catatan_ukuran" class="form-control" rows="3"
                        placeholder="contoh: Lingkar dada 90cm, pinggang 72cm, panjang baju 68cm"></textarea>
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

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    loadPelanggan();

    // Load data pelanggan
    function loadPelanggan(keyword = '') {
        $.get('/konveksi/api/pelanggan.php?action=list&q=' + keyword, function(data) {
            let tbody = $('#bodyPelanggan');
            tbody.empty();
            if (data.length === 0) {
                tbody.html(
                    '<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data pelanggan</td></tr>'
                );
                return;
            }
            data.forEach((p, i) => {
                tbody.append(`
                    <tr>
                        <td>${i + 1}</td>
                        <td>${p.name}</td>
                        <td>${p.email}</td>
                        <td>${p.no_hp || '-'}</td>
                        <td>${p.alamat || '-'}</td>
                        <td>${p.created_at.substring(0, 10)}</td>
                        <td>
                            <span class="badge bg-primary" style="cursor:pointer"
                                onclick="lihatTransaksi(${p.id_pelanggan}, '${p.name}')">
                                ${p.total_transaksi} transaksi
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="bukaEdit(${p.id_pelanggan}, '${p.name}', '${p.email}', '${p.no_hp || ''}', \`${p.alamat || ''}\`)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="hapusPelanggan(${p.id_pelanggan}, '${p.name}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }, 'json');
    }

    // Search realtime
    $('#searchPelanggan').on('keyup', function() {
        loadPelanggan($(this).val());
    });

    // Tambah pelanggan
    $('#btnSimpanTambah').click(function() {
        let name = $('#add_name').val().trim();
        let email = $('#add_email').val().trim();
        let password = $('#add_password').val();
        let no_hp = $('#add_nohp').val().trim();
        let alamat = $('#add_alamat').val().trim();

        if (!name || !email || !password) {
            alert('Nama, email, dan password wajib diisi!');
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
                $('#modalTambah').modal('hide');
                $('#add_name, #add_email, #add_password, #add_nohp, #add_alamat').val('');
                loadPelanggan();
                alert('Pelanggan berhasil ditambahkan!');
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });

    // Simpan edit
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
                $('#modalEdit').modal('hide');
                loadPelanggan();
                alert('Data pelanggan berhasil diperbarui!');
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });
});

function bukaEdit(id, name, email, no_hp, alamat) {
    $('#edit_id').val(id);
    $('#edit_name').val(name);
    $('#edit_email').val(email);
    $('#edit_nohp').val(no_hp);
    $('#edit_alamat').val(alamat);
    $('#edit_password').val('');
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

function hapusPelanggan(id, name) {
    if (!confirm('Yakin hapus pelanggan "' + name + '"?\nSemua data transaksinya juga akan terhapus!')) return;
    $.post('/konveksi/api/pelanggan.php', {
        action: 'delete',
        id
    }, function(res) {
        if (res.success) {
            loadPelanggan();
            alert('Pelanggan berhasil dihapus.');
        } else {
            alert('Error: ' + res.error);
        }
    }, 'json');
}

function bukaUkuran(id_pelanggan, nama) {
    $('#ukuran_id_pelanggan').val(id_pelanggan);
    $('#labelNamaPelanggan').text(nama);
    $('#input_ukuran').val('');
    $('#input_catatan_ukuran').val('');
    $('#alertUkuranAdmin').html('');
    $('#infoUkuranTersimpan').hide();
    $('#infoUkuranKosong').hide();

    // Load ukuran yang sudah ada untuk pelanggan ini
    $.get('/konveksi/api/ukuran_pelanggan.php?action=list', function(data) {
        // Filter data milik pelanggan ini
        let ukuran = data.find(u => u.id_pelanggan == id_pelanggan);
        if (ukuran) {
            $('#nilaiUkuranTersimpan').html(
                '<span class="badge bg-secondary me-2">' + ukuran.ukuran + '</span>' +
                (ukuran.catatan ? '<span class="text-muted">' + ukuran.catatan + '</span>' : '')
            );
            $('#infoUkuranTersimpan').show();
            $('#input_ukuran').val(ukuran.ukuran);
            $('#input_catatan_ukuran').val(ukuran.catatan || '');
        } else {
            $('#infoUkuranKosong').show();
        }
    }, 'json');

    new bootstrap.Modal(document.getElementById('modalUkuran')).show();
}

$('#btnSimpanUkuran').click(function() {
    let id_pelanggan = $('#ukuran_id_pelanggan').val();
    let ukuran = $('#input_ukuran').val().trim();
    let catatan = $('#input_catatan_ukuran').val().trim();

    if (!ukuran) {
        $('#alertUkuranAdmin').html('<div class="alert alert-warning py-2">Ukuran tidak boleh kosong.</div>');
        return;
    }

    $.post('/konveksi/api/ukuran_pelanggan.php', {
        action: 'save',
        id_pelanggan: id_pelanggan,
        ukuran: ukuran,
        catatan: catatan
    }, function(res) {
        if (res.success) {
            $('#alertUkuranAdmin').html(
                '<div class="alert alert-success py-2">Ukuran berhasil disimpan!</div>');
            // Refresh tabel supaya kolom ukuran ikut update
            loadPelanggan($('#searchPelanggan').val());
            setTimeout(() => $('#modalUkuran').modal('hide'), 1000);
        } else {
            $('#alertUkuranAdmin').html('<div class="alert alert-danger py-2">' + (res.error ||
                'Gagal menyimpan.') + '</div>');
        }
    }, 'json');
});

function lihatTransaksi(id, name) {
    $('#isiDetailTrx').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
    new bootstrap.Modal(document.getElementById('modalDetailTrx')).show();
    $.get('/konveksi/api/pelanggan.php?action=transaksi&id=' + id, function(data) {
        if (data.length === 0) {
            $('#isiDetailTrx').html(
                '<p class="text-muted text-center">Belum ada transaksi untuk pelanggan ini.</p>');
            return;
        }
        let html = `<h6 class="mb-3">Transaksi milik: <strong>${name}</strong></h6>
        <table class="table table-sm table-bordered">
            <thead class="table-dark">
                <tr><th>#</th><th>Jenis</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Tanggal</th></tr>
            </thead><tbody>`;
        data.forEach(t => {
            let badge = t.status === 'lunas' ? 'success' : t.status === 'pending' ? 'warning' : 'info';
            html += `<tr>
                <td>${t.id_transaksi}</td>
                <td>${t.jenis_transaksi.replace('_', ' ')}</td>
                <td>Rp ${parseInt(t.total_harga).toLocaleString('id-ID')}</td>
                <td>${t.jenis_pembayaran}</td>
                <td><span class="badge bg-${badge}">${t.status}</span></td>
                <td>${t.tanggal_transaksi.substring(0, 10)}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        $('#isiDetailTrx').html(html);
    }, 'json');
}
</script>