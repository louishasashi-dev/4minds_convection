<?php
session_start();
// Jika sudah login, langsung ke dashboard masing-masing
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $_SESSION['role'] . '/dashboard.php');
    exit;
}
// Jika belum login, tampilkan halaman publik
require_once 'config/db.php';

// Ambil produk pakaian jadi
$produk_jadi = $conn->query("SELECT * FROM produk WHERE jenis='pakaian_jadi' ORDER BY created_at DESC LIMIT 50");

// Ambil produk konveksi/jasa
$produk_konveksi = $conn->query("SELECT * FROM produk WHERE jenis='konveksi' ORDER BY created_at DESC LIMIT 6");

// Ambil ukuran model untuk form kustom
$ukuran_list = $conn->query("SELECT * FROM ukuran_model ORDER BY jenis");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Konveksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&display=swap" rel="stylesheet">
    <style>
    .navbar-brand {
        font-weight: bold;
        font-size: 1.4rem;
    }

    /* HERO SECTION */
    .hero {
        position: relative;
        min-height: 550px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;

        background-image: url('/konveksi/img/banner.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .hero h1 {
        font-family: 'Cinzel', serif;
        font-size: 4.5rem;
        font-weight: 700;
        color: #FFD700;
        letter-spacing: 4px;
        text-transform: uppercase;
    }

    /* Overlay tipis agar teks tetap terbaca */
    .hero::after {
        content: '';
        position: absolute;
        inset: 0;

        background: linear-gradient(rgba(0, 0, 0, 0.20),
                rgba(0, 0, 0, 0.15));

        z-index: 1;
    }

    /* Konten di atas gambar */
    .hero .container {
        position: relative;
        z-index: 2;
    }

    /* Judul */
    .hero h1 {
        color: #FFD700;
        font-size: 4rem;
        font-weight: 900;
        text-shadow:
            2px 2px 8px rgba(0, 0, 0, 0.7);
    }

    /* Deskripsi */
    .hero .lead {
        color: #ffffff;
        text-shadow:
            2px 2px 6px rgba(0, 0, 0, 0.7);
    }

    /* Tombol utama */
    .btn-cta {
        background: linear-gradient(135deg, #FFD700, #B8860B);
        border: none;
        color: #1a1a1a;
        font-weight: 700;
        padding: 14px 35px;
        border-radius: 50px;
        transition: .3s;
        box-shadow: 0 5px 20px rgba(212, 175, 55, .4);
    }

    .btn-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, .6);
        color: #000;
    }

    /* Tombol kedua */
    .btn-outline-light {
        border: 2px solid #FFD700;
        color: #FFD700;
        background: rgba(0, 0, 0, .35);
        padding: 14px 35px;
        border-radius: 50px;
        font-weight: 700;
        transition: .3s;
    }

    .btn-outline-light:hover {
        background: #FFD700;
        color: #000;
        border-color: #FFD700;
        transform: translateY(-3px);
    }

    /* Konten di atas overlay */
    .hero .container {
        position: relative;
        z-index: 2;
    }

    /* Teks utama dengan warna gold */
    .hero h1 {
        color: #FFD700;
        text-shadow: 3px 3px 6px #000;
        font-weight: 800;
        letter-spacing: 1px;
    }

    /* Teks deskripsi */
    .hero .lead {
        color: #f5e6d3;
        text-shadow: 1px 1px 2px #000;
        font-weight: 500;
    }

    /* Tombol CTA (Lihat Produk) - gaya gold & coklat */
    .btn-cta {
        background: #D4AF37;
        border: none;
        color: #2c1a0f;
        font-weight: bold;
        padding: 12px 30px;
        border-radius: 30px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-cta:hover {
        background: #B8860B;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Tombol outline (Pesan Kustom) */
    .btn-outline-light {
        border: 2px solid #D4AF37;
        color: #FFD700;
        background: rgba(0, 0, 0, 0.4);
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .btn-outline-light:hover {
        background: #D4AF37;
        color: #2c1a0f;
        border-color: #D4AF37;
        transform: translateY(-2px);
    }

    .section-title {
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .produk-card {
        transition: transform .2s;
        cursor: pointer;
    }

    .produk-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .15);
    }

    .produk-card img {
        height: 250px;
        object-fit: cover;
    }

    .badge-jenis {
        font-size: .7rem;
    }

    section {
        padding: 60px 0;
    }

    section:nth-child(even) {
        background: #f8f9fa;
    }

    footer {
        background: #2c3e50;
        color: #ecf0f1;
        padding: 30px 0;
    }

    /* Tombol pesan di card */
    .btn-pesan {
        background: #D4AF37;
        color: #2c1a0f;
        font-weight: 600;
        border: none;
    }

    .btn-pesan:hover {
        background: #B8860B;
        color: white;
    }

    .btn-pesan-outline {
        border: 1px solid #D4AF37;
        color: #B8860B;
        font-weight: 600;
        background: transparent;
    }

    .btn-pesan-outline:hover {
        background: #D4AF37;
        color: #2c1a0f;
    }

    .btn-daftar {
        color: #FFD700;
        font-weight: 700;
        letter-spacing: 0.5px;

        padding: 8px 14px;
        border: none;
        background: transparent;

        position: relative;
        transition: all .3s ease;
    }

    .btn-daftar::after {
        content: '';
        position: absolute;
        left: 14px;
        bottom: 4px;

        width: 0;
        height: 2px;

        background: #FFD700;
        transition: width .3s ease;
    }

    .btn-daftar:hover {
        color: #fff;
    }

    .btn-daftar:hover::after {
        width: calc(100% - 28px);
    }

    .navbar-brand {
        padding: 0;
    }

    .navbar-logo {
        height: 50px;
        width: auto;
        display: block;
        transition: transform .3s ease;
    }

    .navbar-brand:hover .navbar-logo {
        transform: scale(1.03);
    }

    /* Mobile */
    @media (max-width: 768px) {
        .navbar-logo {
            height: 40px;
        }
    }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background:#3B3208">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/konveksi/">
                <img src="/konveksi/assets/logo/logofavicon.png" alt="4MINDS Convection" class="navbar-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#pakaian-jadi">Pakaian Jadi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#jasa-konveksi">Jasa Konveksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#jahit-satuan">Jahit Satuan</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="/konveksi/auth/login.php" class="btn btn-daftar">LOGIN</a>
                    <a href="/konveksi/auth/register.php" class="btn btn-daftar">
                        DAFTAR
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO - dengan gambar background statis (tanpa animasi) -->
    <div class="hero">
        <div class="container text-center">
            <h1 class="display-3 fw-bold">4minds convection</h1>
            <p class="lead mt-3">Quality finished clothing, individual sewing services, <br> and mass production garment
                services according to needs
                Anda.</p>
            <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
                <a href="#pakaian-jadi" class="btn btn-cta">Lihat Produk</a>
                <a href="#jahit-satuan" class="btn btn-outline-light">Jahit Satuan</a>
            </div>
        </div>
    </div>

    <!-- SECTION: PAKAIAN JADI -->
    <section id="pakaian-jadi">
        <div class="container">
            <h2 class="section-title">👕 Pakaian Jadi</h2>
            <div class="row g-3" id="gridPakaianJadi">
                <?php if ($produk_jadi->num_rows === 0): ?>
                <p class="text-muted">Belum ada produk pakaian jadi.</p>
                <?php else: ?>
                <?php while ($p = $produk_jadi->fetch_assoc()): ?>
                <div class="col-6 col-md-3">
                    <div class="card produk-card h-100" onclick="lihatDetailProduk(<?= $p['id_produk'] ?>)">
                        <img src="<?= $p['gambar'] ? '/konveksi/assets/uploads/'.$p['gambar'] : 'https://via.placeholder.com/300x200?text=No+Image' ?>"
                            class="card-img-top" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                        <div class="card-body">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                            <p class="text-muted small mb-1"><?= $p['kategori'] ?? '-' ?></p>
                            <p class="fw-bold text-success mb-1">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                            <span class="badge bg-secondary badge-jenis">Stok: <?= $p['stok'] ?></span>
                        </div>
                        <div class="card-footer bg-white border-0 pb-3">
                            <button class="btn btn-sm w-100 btn-pesan"
                                onclick="event.stopPropagation(); pesanProduk(<?= $p['id_produk'] ?>, '<?= htmlspecialchars($p['nama_produk']) ?>', <?= $p['harga'] ?>, 'pakaian_jadi')">
                                🛒 Pesan
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- SECTION: JASA KONVEKSI -->
    <section id="jasa-konveksi">
        <div class="container">
            <h2 class="section-title">🏭 Jasa Konveksi</h2>
            <p class="text-muted mb-4">Layanan produksi massal untuk seragam, kaos, jaket, dan lainnya dengan harga
                grosir.</p>
            <div class="row g-3">
                <?php if ($produk_konveksi->num_rows === 0): ?>
                <p class="text-muted">Belum ada produk konveksi.</p>
                <?php else: ?>
                <?php while ($p = $produk_konveksi->fetch_assoc()): ?>
                <div class="col-6 col-md-4">
                    <div class="card produk-card h-100" onclick="lihatDetailProduk(<?= $p['id_produk'] ?>)">
                        <img src="<?= $p['gambar'] ? '/konveksi/assets/uploads/'.$p['gambar'] : 'https://via.placeholder.com/300x200?text=No+Image' ?>"
                            class="card-img-top" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                        <div class="card-body">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                            <p class="text-muted small mb-1"><?= $p['deskripsi'] ?? '-' ?></p>
                            <p class="fw-bold text-success mb-0">Rp <?= number_format($p['harga'], 0, ',', '.') ?>
                                <small class="text-muted fw-normal">/ pcs</small>
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 pb-3">
                            <button class="btn btn-sm w-100 btn-pesan-outline"
                                onclick="event.stopPropagation(); pesanProduk(<?= $p['id_produk'] ?>, '<?= htmlspecialchars($p['nama_produk']) ?>', <?= $p['harga'] ?>, 'konveksi')">
                                📦 Pesan Massal
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="jahit-satuan">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <h2 class="section-title">✂️ Jahit Satuan</h2>
                    <p class="text-muted">Ingin baju sesuai ukuran dan model Anda sendiri? Kami siap membuatkannya
                        khusus untuk Anda.</p>
                    <ul class="text-muted">
                        <li>Pilih model & ukuran bebas</li>
                        <li>Bahan pilihan sendiri</li>
                        <li>DP 50%, sisa lunas saat selesai</li>
                        <li>Estimasi selesai 7–14 hari kerja</li>
                    </ul>
                </div>
                <div class="col-md-7">
                    <div class="card shadow">
                        <div class="card-body p-4 text-center">
                            <div class="fs-1 mb-3">✂️</div>
                            <h5 class="mb-3">Pesan Jahit Satuan</h5>
                            <p class="text-muted mb-4">
                                Untuk memesan jahit satuan, silakan
                                <a href="/konveksi/auth/login.php"
                                    style="color:#D4AF37;font-weight:600;text-decoration:none">Login</a>
                                terlebih dahulu. Jika belum punya akun, silakan
                                <a href="/konveksi/auth/register.php"
                                    style="color:#2c7a2c;font-weight:600;text-decoration:none">Daftar</a>
                                sekarang.
                            </p>
                            <a href="/konveksi/auth/login.php" class="btn btn-cta me-2">Login</a>
                            <a href="/konveksi/auth/register.php" class="btn btn-outline-light">Daftar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container text-center">
            <p class="mb-1">🧵 <strong>Sistem Konveksi</strong></p>
            <p class="small text-secondary">Melayani dengan sepenuh hati · Kualitas terjamin</p>
        </div>
    </footer>

    <!-- Modal Detail Produk -->
    <div class="modal fade" id="modalDetailProduk" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="isiDetailProduk">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-pesan" id="btnPesanDariDetail">
                        🛒 Pesan Produk Ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Redirect Login -->
    <div class="modal fade" id="modalLogin" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="fs-1 mb-2">🔐</div>
                    <h6 id="pesanModalLogin">Untuk melanjutkan, silakan login atau daftar terlebih dahulu.</h6>
                    <div class="d-grid gap-2 mt-3">
                        <a id="btnLoginModal" href="/konveksi/auth/login.php" class="btn btn-primary">Login</a>
                        <a href="/konveksi/auth/register.php" class="btn btn-outline-secondary">Daftar Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    // Data ukuran dan multiplier harga
    const ukuranList = []; // diisi dari API
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

    // Load ukuran dari DB saat halaman siap
    $(document).ready(function() {
        $.get('/konveksi/api/ukuran.php?action=list_ukuran', function(data) {
            data.forEach(u => ukuranList.push(u.ukuran));
        }, 'json');
    });

    function pesanProduk(id, nama, harga, jenis) {
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'pelanggan'): ?>
        // Sudah login — buka modal pesan
        $('#pesan_id_produk').val(id);
        $('#pesan_harga_dasar').val(harga);
        $('#pesan_jenis_produk').val(jenis);
        $('#modalPesanJudul').text('Pesan: ' + nama);
        $('#alertPesan').html('');

        // Isi dropdown ukuran
        let sel = $('#pesan_ukuran');
        sel.empty();
        ukuranList.forEach((u, idx) => {
            let pct = ukuranPct[u.toUpperCase()] || 0;
            let hargaU = Math.round(harga * (1 + pct));
            let label = pct > 0 ? `${u} (+${pct*100}% = Rp ${hargaU.toLocaleString('id-ID')})` :
                `${u} (Rp ${hargaU.toLocaleString('id-ID')})`;
            sel.append(`<option value="${u}" data-pct="${pct}">${label}</option>`);
        });

        $('#pesan_qty').val(1);
        hitungHargaPesan();
        new bootstrap.Modal(document.getElementById('modalPesan')).show();
        <?php else: ?>
        // Belum login
        sessionStorage.setItem('pesan_produk', JSON.stringify({
            id,
            nama,
            harga,
            jenis
        }));
        $('#pesanModalLogin').text('Login dulu untuk memesan "' + nama + '"');
        $('#btnLoginModal').attr('href', '/konveksi/auth/login.php?redirect=transaksi&produk=' + id);
        new bootstrap.Modal(document.getElementById('modalLogin')).show();
        <?php endif; ?>
    }

    function hitungHargaPesan() {
        let hargaDasar = parseFloat($('#pesan_harga_dasar').val()) || 0;
        let pct = parseFloat($('#pesan_ukuran option:selected').data('pct')) || 0;
        let qty = parseInt($('#pesan_qty').val()) || 1;
        let hargaSatuan = Math.round(hargaDasar * (1 + pct));
        let subtotal = hargaSatuan * qty;

        // Diskon qty > 50
        let diskonQty = 0;
        if (qty > 50) {
            diskonQty = subtotal * 0.02;
            $('#row_diskon_qty').removeClass('d-none');
            $('#info_diskon_nominal').text('-Rp ' + Math.round(diskonQty).toLocaleString('id-ID'));
            $('#info_diskon_qty').text('✅ Kamu dapat diskon 2% karena pesan lebih dari 50 pcs!');
        } else {
            $('#row_diskon_qty').addClass('d-none');
            $('#info_diskon_qty').text('');
        }

        let total = subtotal - diskonQty;
        $('#info_harga_satuan').text('Rp ' + hargaSatuan.toLocaleString('id-ID'));
        $('#info_qty_display').text(qty + ' pcs');
        $('#info_total_pesan').text('Rp ' + Math.round(total).toLocaleString('id-ID'));
    }

    $('#btnKirimPesan').click(function() {
        let id_produk = $('#pesan_id_produk').val();
        let ukuran = $('#pesan_ukuran').val();
        let qty = parseInt($('#pesan_qty').val()) || 1;
        let pembayaran = $('#pesan_pembayaran').val();
        let jenis = $('#pesan_jenis_produk').val();

        if (!ukuran || qty < 1) {
            $('#alertPesan').html('<div class="alert alert-warning">Lengkapi pilihan ukuran dan jumlah.</div>');
            return;
        }

        $('#loadingPesan').removeClass('d-none');
        $('#btnKirimPesan').prop('disabled', true);

        $.post('/konveksi/api/transaksi.php', {
            action: 'create',
            jenis_transaksi: jenis,
            jenis_pembayaran: pembayaran,
            ukuran: ukuran,
            items: JSON.stringify([{
                id_produk: id_produk,
                jumlah: qty
            }])
        }, function(res) {
            $('#loadingPesan').removeClass('d-none');
            $('#btnKirimPesan').prop('disabled', false);
            if (res.success) {
                $('#modalPesan').modal('hide');
                let info =
                    `Pesanan berhasil! ID Transaksi: #${res.id_transaksi}\nTotal: Rp ${parseInt(res.total).toLocaleString('id-ID')}`;
                if (res.diskon_total > 0) info +=
                    `\nDiskon: Rp ${parseInt(res.diskon_total).toLocaleString('id-ID')}`;
                alert(info);
            } else {
                $('#alertPesan').html('<div class="alert alert-danger">' + res.error + '</div>');
            }
        }, 'json');
    });

    function lihatDetailProduk(id) {
        $('#isiDetailProduk').html(
            '<div class="text-center py-4"><div class="spinner-border text-success"></div></div>');
        new bootstrap.Modal(document.getElementById('modalDetailProduk')).show();
        $.get('/konveksi/api/produk.php?action=detail_publik&id=' + id, function(p) {
            if (!p || p.error) {
                $('#isiDetailProduk').html('<p class="text-danger">Produk tidak ditemukan.</p>');
                return;
            }
            let img = p.gambar ? '/konveksi/assets/uploads/' + p.gambar :
                'https://via.placeholder.com/400x250?text=No+Image';
            $('#isiDetailProduk').html(`
            <img src="${img}" class="img-fluid rounded mb-3" style="width:100%;max-height:220px;object-fit:cover">
            <h5>${p.nama_produk}</h5>
            <p class="text-muted">${p.kategori || ''} · ${p.jenis.replace('_',' ')}</p>
            <h5 class="text-success">Rp ${parseInt(p.harga).toLocaleString('id-ID')}</h5>
            <p>${p.deskripsi || 'Tidak ada deskripsi.'}</p>
            <p class="mb-0"><strong>Stok:</strong> ${p.stok} pcs</p>
            ${p.model ? '<p><strong>Model:</strong> ' + p.model + '</p>' : ''}
        `);
            $('#btnPesanDariDetail').off('click').on('click', function() {
                bootstrap.Modal.getInstance(document.getElementById('modalDetailProduk')).hide();
                pesanProduk(p.id_produk, p.nama_produk, p.harga, p.jenis);
            });
        }, 'json');
    }
    </script>
    <!-- Modal Pesan Produk dengan Ukuran -->
    <div class="modal fade" id="modalPesan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPesanJudul">Pesan Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pesan_id_produk">
                    <input type="hidden" id="pesan_harga_dasar">
                    <input type="hidden" id="pesan_jenis_produk">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Ukuran</label>
                        <select id="pesan_ukuran" class="form-select" onchange="hitungHargaPesan()">
                            <!-- diisi JS -->
                        </select>
                        <small class="text-muted">Ukuran lebih besar = harga menyesuaikan</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (pcs)</label>
                        <input type="number" id="pesan_qty" class="form-control" value="1" min="1"
                            oninput="hitungHargaPesan()">
                        <small class="text-muted" id="info_diskon_qty"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <select id="pesan_pembayaran" class="form-select">
                            <option value="lunas">Lunas</option>
                            <option value="dp">DP 50%</option>
                            <option value="cod">COD</option>
                        </select>
                    </div>

                    <div class="alert alert-light border mb-0">
                        <div class="d-flex justify-content-between">
                            <span>Harga satuan:</span>
                            <strong id="info_harga_satuan">Rp 0</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Jumlah:</span>
                            <strong id="info_qty_display">1 pcs</strong>
                        </div>
                        <div class="d-flex justify-content-between text-success d-none" id="row_diskon_qty">
                            <span>Diskon qty >50 pcs (2%):</span>
                            <strong id="info_diskon_nominal">-Rp 0</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-5">
                            <span class="fw-bold">Total:</span>
                            <strong class="text-success" id="info_total_pesan">Rp 0</strong>
                        </div>
                    </div>

                    <div id="alertPesan" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-pesan fw-bold" id="btnKirimPesan">
                        <span id="loadingPesan" class="spinner-border spinner-border-sm d-none"></span>
                        🛒 Pesan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>