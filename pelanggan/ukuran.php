<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Ukuran Tubuh Saya</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="alert alert-info">
            <i class="bi bi-rulers"></i>
            Data ukuran tubuh Anda digunakan sebagai referensi saat melakukan transaksi jahit satuan.
        </div>

        <div class="card mb-4" style="max-width: 500px;">
            <div class="card-header fw-bold"><i class="bi bi-pencil-square"></i> Simpan Ukuran Tubuh</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Ukuran / Kode Ukuran <small class="text-muted">(contoh: M, L, XL, atau
                            ukuran cm)</small></label>
                    <input type="text" id="inputUkuran" class="form-control" placeholder="contoh: L atau 42">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan <small class="text-muted">(opsional)</small></label>
                    <textarea id="inputCatatan" class="form-control" rows="3"
                        placeholder="contoh: Lingkar dada 90cm, pinggang 72cm, panjang baju 68cm"></textarea>
                </div>
                <div id="alertUkuran"></div>
                <button class="btn btn-primary" onclick="simpanUkuran()">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>

        <div class="card" style="max-width: 500px;">
            <div class="card-header fw-bold">Data Tersimpan</div>
            <div class="card-body" id="dataUkuran">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
$(document).ready(function() {
    loadUkuran();
});

function loadUkuran() {
    $.get('/konveksi/api/ukuran_pelanggan.php?action=list', function(data) {
        let area = $('#dataUkuran');
        if (data.length === 0) {
            area.html('<p class="text-muted mb-0">Belum ada data ukuran tersimpan.</p>');
            return;
        }
        let d = data[0]; // 1 data per pelanggan
        area.html(`
            <p class="mb-1"><strong>Ukuran:</strong> ${d.ukuran}</p>
            <p class="mb-1"><strong>Catatan:</strong> ${d.catatan || '-'}</p>
            <p class="mb-0 text-muted"><small>Terakhir diupdate: ${d.update_at.substring(0,10)}</small></p>
        `);
        // Isi form dengan data yang sudah ada
        $('#inputUkuran').val(d.ukuran);
        $('#inputCatatan').val(d.catatan);
    }, 'json');
}

function simpanUkuran() {
    let ukuran = $('#inputUkuran').val().trim();
    let catatan = $('#inputCatatan').val().trim();
    if (!ukuran) {
        $('#alertUkuran').html('<div class="alert alert-warning">Ukuran tidak boleh kosong.</div>');
        return;
    }
    $.post('/konveksi/api/ukuran_pelanggan.php', {
        action: 'save',
        ukuran,
        catatan
    }, function(res) {
        if (res.success) {
            $('#alertUkuran').html('<div class="alert alert-success">Ukuran berhasil disimpan!</div>');
            loadUkuran();
        } else {
            $('#alertUkuran').html('<div class="alert alert-danger">' + (res.error || 'Gagal') + '</div>');
        }
    }, 'json');
}
</script>