<?php
require_once '../includes/header.php';
if ($_SESSION['role'] !== 'pelanggan') {
    header('Location: /konveksi/auth/login.php'); exit;
}
?>
<?php require_once '../includes/sidebar_pelanggan.php'; ?>
<div class="flex-grow-1">
    <div class="topbar">
        <span>Status Pengiriman</span>
        <span>👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    </div>
    <div class="main-content">

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Transaksi</th>
                                <th>Ekspedisi</th>
                                <th>No Resi</th>
                                <th>Tgl Kirim</th>
                                <th>Estimasi Tiba</th>
                                <th>Tgl Tiba</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="bodyPengiriman">
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="spinner-border text-primary"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Toast Notifikasi -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div id="toastNotif" class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body" id="toastPesan"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
<script>
let currentData = [];
let lastUpdateTime = new Date();

$(document).ready(function() {
    loadData();
    // Auto cek update setiap 10 detik
    setInterval(function() {
        checkUpdate();
    }, 10000);
});

function loadData() {
    $.get('/konveksi/api/pengiriman.php?action=list', function(data) {
        currentData = data;
        renderTable(data);
        lastUpdateTime = new Date();
    }, 'json').fail(function() {
        $('#bodyPengiriman').html(
            '<tr><td colspan="8" class="text-center text-danger py-4">Gagal memuat data. Coba refresh halaman.</td></tr>'
            );
    });
}

function checkUpdate() {
    $.get('/konveksi/api/pengiriman.php?action=list', function(newData) {
        // Cek apakah ada perubahan status
        let hasUpdate = false;
        let updatedItems = [];

        if (currentData.length !== newData.length) {
            hasUpdate = true;
        } else {
            for (let i = 0; i < newData.length; i++) {
                if (currentData[i] && currentData[i].status !== newData[i].status) {
                    hasUpdate = true;
                    updatedItems.push({
                        id: newData[i].id_transaksi,
                        oldStatus: currentData[i].status,
                        newStatus: newData[i].status
                    });
                }
            }
        }

        if (hasUpdate) {
            currentData = newData;
            renderTable(newData);

            // Tampilkan notifikasi untuk setiap perubahan
            if (updatedItems.length > 0) {
                let pesan = '';
                for (let item of updatedItems) {
                    if (item.newStatus === 'sampai') {
                        pesan = '📦 Paket Anda sudah sampai!';
                    } else if (item.newStatus === 'dikirim') {
                        pesan = '🚚 Paket Anda sedang dalam perjalanan!';
                    }
                }
                if (pesan) showToast(pesan);
                else showToast('📢 Status pengiriman Anda telah diperbarui!');
            } else {
                showToast('📢 Status pengiriman Anda telah diperbarui!');
            }
        }
    }, 'json');
}

function renderTable(data) {
    let tbody = $('#bodyPengiriman');
    tbody.empty();

    if (!data || data.length === 0) {
        tbody.html(
            '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pengiriman.</td></tr>'
        );
        return;
    }

    data.forEach((p, i) => {
        let badge = '';
        let statusText = '';
        let statusIcon = '';

        if (p.status === 'sampai') {
            badge = 'success';
            statusText = 'Sampai';
            statusIcon = '✅';
        } else if (p.status === 'dikirim') {
            badge = 'info';
            statusText = 'Dikirim';
            statusIcon = '📦';
        } else {
            badge = 'secondary';
            statusText = 'Belum Dikirim';
            statusIcon = '⏳';
        }

        // Tombol COD hanya jika status sudah sampai dan pembayaran COD serta belum dikonfirmasi
        let btnCOD = '';
        if (p.status === 'sampai' && p.jenis_pembayaran === 'cod') {
            let sudahBayar = (p.status_pembayaran === 'terkonfirmasi');
            if (!sudahBayar) {
                btnCOD = `<br><button class="btn btn-success btn-sm mt-1" onclick="bayarCOD(${p.id_transaksi})">
                            <i class="bi bi-cash-coin"></i> Bayar COD
                          </button>`;
            } else {
                btnCOD = `<br><span class="badge bg-success">✓ Sudah Dibayar</span>`;
            }
        }

        // Animasi highlight jika status baru berubah
        let rowClass = '';
        if (p.status === 'sampai') {
            rowClass = 'table-success';
        } else if (p.status === 'dikirim') {
            rowClass = 'table-info';
        }

        tbody.append(`
            <tr id="row_${p.id_transaksi}" class="${rowClass}">
                <td>${i + 1}</td>
                <td>
                    <strong>#${p.id_transaksi}</strong><br>
                    <small class="text-muted">${p.jenis_transaksi ? p.jenis_transaksi.replace(/_/g,' ') : '-'}</small>
                </td>
                <td>${p.kurir || '-'}</td>
                <td>${p.no_resi ? `<code>${p.no_resi}</code>` : '<span class="text-muted">-</span>'}</td>
                <td>${p.tanggal_kirim || '-'}</td>
                <td>${p.estimasi_sampai || '-'}</td>
                <td id="tiba_${p.id_transaksi}">${p.tanggal_tiba || '-'}</td>
                <td id="status_${p.id_transaksi}">
                    <span class="badge bg-${badge}">${statusIcon} ${statusText}</span>
                    ${btnCOD}
                </td>
            </tr>
        `);
    });
}

function showToast(pesan) {
    $('#toastPesan').text(pesan);
    let toast = new bootstrap.Toast(document.getElementById('toastNotif'), {
        delay: 4000,
        autohide: true
    });
    toast.show();
}

function bayarCOD(id_transaksi) {
    if (!confirm('Konfirmasi pembayaran COD untuk transaksi #' + id_transaksi +
            '?\n\nAnda akan membayar saat barang diterima.')) return;

    let fd = new FormData();
    fd.append('action', 'bayar');
    fd.append('id_transaksi', id_transaksi);
    fd.append('metode', 'cod');
    fd.append('jumlah', 0);

    $.ajax({
        url: '/konveksi/api/pelunasan.php',
        type: 'POST',
        data: fd,
        contentType: false,
        processData: false,
        success: function(res) {
            if (res.success) {
                showToast('✅ Pembayaran COD berhasil dikirim! Menunggu konfirmasi admin.');
                // Refresh data setelah 2 detik
                setTimeout(function() {
                    loadData();
                }, 2000);
            } else {
                alert('Error: ' + (res.error || 'Gagal melakukan pembayaran'));
            }
        },
        error: function(xhr) {
            alert('Gagal koneksi ke server. Error: ' + xhr.status);
        },
        dataType: 'json'
    });
}

// Tambahan: Refresh manual dengan tombol (opsional)
// function refreshManual() {
//     loadData();
//     showToast('🔄 Memuat ulang data...');
// }
// 
</script>

<!-- Tambahan tombol refresh manual di topbar (opsional) -->
<!-- <style>
.topbar {
    background: white;
    padding: 12px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
}
.main-content {
    padding: 20px;
}
.table td, .table th {
    vertical-align: middle;
}
code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 12px;
}
</style> -->