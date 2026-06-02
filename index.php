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
$produk_jadi = $conn->query("SELECT * FROM produk WHERE jenis='pakaian_jadi' ORDER BY created_at DESC LIMIT 8");

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
        height: 200px;
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
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background:#2c3e50">
        <div class="container">
            <a class="navbar-brand" href="/konveksi/">🧵 Konveksi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#pakaian-jadi">Pakaian Jadi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#jasa-konveksi">Jasa Konveksi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kustom">Pakaian Kustom</a></li>
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
                <a href="#kustom" class="btn btn-outline-light">Pesan Kustom</a>
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

    <!-- SECTION: FORM KUSTOM (JAHIT SATUAN) -->
    <section id="kustom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5 mb-4 mb-md-0">
                    <h2 class="section-title">✂️ Pakaian Kustom</h2>
                    <p class="text-muted">Ingin baju sesuai ukuran dan model Anda sendiri? Isi form berikut dan kami
                        akan membuatkannya untuk Anda.</p>
                    <ul class="text-muted">
                        <li>Pilih model & ukuran bebas</li>
                        <li>Bahan pilihan sendiri</li>
                        <li>DP 50%, sisa lunas saat selesai</li>
                        <li>Estimasi selesai 7–14 hari kerja</li>
                    </ul>
                </div>
                <div class="col-md-7">
                    <div class="card shadow">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Form Pemesanan Kustom</h5>
                            <div id="alertKustom"></div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Pakaian</label>
                                <select id="kustom_jenis" class="form-select">
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
                                <label class="form-label">Ukuran / Referensi Model</label>
                                <select id="kustom_ukuran" class="form-select">
                                    <option value="">-- Pilih Ukuran --</option>
                                    <?php
                                $ukuran_list->data_seek(0);
                                while ($u = $ukuran_list->fetch_assoc()):
                                ?>
                                    <option value="<?= $u['id_ukuran_model'] ?>">
                                        <?= htmlspecialchars($u['jenis'] . ' - ' . $u['ukuran']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                    <option value="custom">Ukuran Sendiri (isi di catatan)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Pcs</label>
                                <input type="number" id="kustom_jumlah" class="form-control" min="1" value="1"
                                    placeholder="Masukkan jumlah">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan / Catatan</label>
                                <textarea id="kustom_catatan" class="form-control" rows="3"
                                    placeholder="Contoh: Ukuran dada 100cm, panjang 70cm, warna navy, bahan cotton combed 30s..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Estimasi Selesai</label>
                                <input type="date" id="kustom_estimasi" class="form-control"
                                    min="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                            </div>
                            <button class="btn w-100 btn-cta" onclick="submitKustom()">
                                ✂️ Kirim Permintaan Kustom
                            </button>
                            <p class="text-muted small text-center mt-2">
                                Anda akan diminta login/daftar untuk melanjutkan pemesanan
                            </p>
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
    function pesanProduk(id, nama, harga, jenis) {
        sessionStorage.setItem('pesan_produk', JSON.stringify({
            id,
            nama,
            harga,
            jenis
        }));
        $('#pesanModalLogin').text('Login dulu untuk memesan "' + nama + '"');
        $('#btnLoginModal').attr('href', '/konveksi/auth/login.php?redirect=transaksi&produk=' + id);
        new bootstrap.Modal(document.getElementById('modalLogin')).show();
    }

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

    function submitKustom() {
        let jenis = $('#kustom_jenis').val();
        let catatan = $('#kustom_catatan').val();
        let jumlah = $('#kustom_jumlah').val();
        if (!jenis || !catatan || !jumlah) {
            $('#alertKustom').html('<div class="alert alert-warning">Harap isi semua field yang diperlukan.</div>');
            return;
        }
        sessionStorage.setItem('pesan_kustom', JSON.stringify({
            jenis,
            ukuran: $('#kustom_ukuran').val(),
            jumlah,
            catatan,
            estimasi: $('#kustom_estimasi').val()
        }));
        $('#btnLoginModal').attr('href', '/konveksi/auth/login.php?redirect=kustom');
        $('#pesanModalLogin').text('Login atau daftar dulu untuk melanjutkan pemesanan kustom.');
        new bootstrap.Modal(document.getElementById('modalLogin')).show();
    }
    </script>
</body>

</html>