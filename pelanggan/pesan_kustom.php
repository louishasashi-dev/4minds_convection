<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') { header('Location: /konveksi/auth/login.php'); exit; }
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Pesan Jahit Satuan</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-4">✂️ Form Permintaan Jahit Satuan</h5>
                        <div id="alertForm"></div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Pakaian <span class="text-danger">*</span></label>
                            <select id="jenis_pakaian" class="form-select">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="kaos">Kaos</option>
                                <option value="kemeja">Kemeja</option>
                                <option value="jaket">Jaket</option>
                                <option value="celana">Celana</option>
                                <option value="gamis">Gamis</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ukuran / Referensi</label>
                            <input type="text" id="ukuran" class="form-control"
                                placeholder="Contoh: L, XL, atau ukuran dada 100cm">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Pcs <span class="text-danger">*</span></label>
                            <input type="number" id="jumlah" class="form-control" min="1" value="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan / Catatan <span class="text-danger">*</span></label>
                            <textarea id="catatan" class="form-control" rows="4"
                                placeholder="Contoh: warna navy, bahan cotton combed 30s, ada logo di dada kiri..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimasi Selesai <small
                                    class="text-muted">(opsional)</small></label>
                            <input type="date" id="estimasi" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Desain / Referensi
                                <small class="text-muted">(opsional — gambar atau PDF)</small>
                            </label>
                            <input type="file" id="file_desain" class="form-control"
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
                        <button class="btn btn-success w-100" onclick="kirimPesan()">
                            <span id="loadingKirim" class="spinner-border spinner-border-sm d-none"></span>
                            ✂️ Kirim Permintaan
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <h6 class="p-3 mb-0 border-bottom fw-bold">Riwayat Permintaan Saya</h6>
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Harga</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="bodyRiwayat">
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <div class="spinner-border text-primary spinner-border-sm"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
// Preview file desain saat dipilih
$('#file_desain').on('change', function() {
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
    loadRiwayat();
});

function kirimPesan() {
    let jenis = $('#jenis_pakaian').val();
    let catatan = $('#catatan').val().trim();
    let jumlah = $('#jumlah').val();

    if (!jenis || !catatan || !jumlah) {
        $('#alertForm').html('<div class="alert alert-warning">Harap isi semua field yang wajib diisi.</div>');
        return;
    }

    // Validasi ukuran file
    let fileInput = document.getElementById('file_desain');
    let file = fileInput.files[0];
    if (file && file.size > 5 * 1024 * 1024) {
        $('#alertForm').html('<div class="alert alert-danger">Ukuran file maksimal 5 MB.</div>');
        return;
    }

    $('#loadingKirim').removeClass('d-none');

    let formData = new FormData();
    formData.append('action', 'submit');
    formData.append('jenis_pakaian', jenis);
    formData.append('ukuran', $('#ukuran').val());
    formData.append('jumlah', jumlah);
    formData.append('catatan', catatan);
    formData.append('estimasi', $('#estimasi').val());
    if (file) formData.append('file_desain', file);

    $.ajax({
        url: '/konveksi/api/pesan_jahit.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            $('#loadingKirim').addClass('d-none');
            if (res.success) {
                $('#alertForm').html(
                    '<div class="alert alert-success">✅ Permintaan berhasil dikirim! Admin akan segera meninjau dan menentukan harga.</div>'
                );
                // Reset semua field termasuk file & preview
                $('#jenis_pakaian, #ukuran, #catatan, #estimasi').val('');
                $('#jumlah').val(1);
                $('#file_desain').val('');
                $('#previewDesain').addClass('d-none');
                $('#previewDesainPdf').addClass('d-none');
                loadRiwayat();
            } else {
                $('#alertForm').html('<div class="alert alert-danger">Error: ' + res.error + '</div>');
            }
        },
        error: function() {
            $('#loadingKirim').addClass('d-none');
            $('#alertForm').html('<div class="alert alert-danger">Terjadi kesalahan koneksi.</div>');
        }
    });
}

function loadRiwayat() {
    $.get('/konveksi/api/pesan_jahit.php?action=list', function(data) {
        let tbody = $('#bodyRiwayat');
        tbody.empty();
        if (data.length === 0) {
            tbody.html(
                '<tr><td colspan="6" class="text-center text-muted py-3">Belum ada permintaan.</td></tr>');
            return;
        }
        data.forEach((p, i) => {
            let badge = p.status === 'disetujui' ? 'success' : p.status === 'ditolak' ? 'danger' :
                'warning';
            let harga = p.harga_disetujui > 0 ? 'Rp ' + parseInt(p.harga_disetujui).toLocaleString(
                'id-ID') : '<span class="text-muted">Menunggu</span>';
            tbody.append(`<tr>
                <td>${i+1}</td>
                <td>${p.jenis_pakaian}</td>
                <td>${p.jumlah} pcs</td>
                <td><span class="badge bg-${badge}">${p.status}</span></td>
                <td>${harga}</td>
                <td>${p.created_at.substring(0,10)}</td>
            </tr>`);
        });
    }, 'json');
}
</script>