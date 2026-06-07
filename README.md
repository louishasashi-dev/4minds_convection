# 4MINDS Convection

Sistem Informasi Manajemen Konveksi berbasis web yang dirancang untuk membantu proses operasional bisnis konveksi mulai dari pengelolaan produk, pemesanan pelanggan, transaksi, pelunasan, pengiriman, hingga pelaporan.

Aplikasi memiliki dua jenis pengguna utama:

* Admin
* Pelanggan

## 📋 Deskripsi Proyek

4MINDS Convection merupakan aplikasi web yang dikembangkan untuk mendigitalisasi proses bisnis konveksi yang sebelumnya dilakukan secara manual.

Melalui sistem ini, admin dapat mengelola produk, pelanggan, transaksi, pengiriman, dan laporan, sementara pelanggan dapat melakukan pemesanan, melihat status transaksi, mengelola data ukuran, dan memantau pengiriman pesanan.

Proyek ini dikembangkan menggunakan PHP Native dengan database MySQL.

---

## 🚀 Fitur Utama

### Panel Admin

* Dashboard Admin
* Manajemen Produk
* Manajemen Pelanggan
* Manajemen Diskon
* Manajemen Transaksi
* Manajemen Pelunasan
* Manajemen Pengiriman
* Dokumen Transaksi
* Data Referensi Ukuran
* Laporan Penjualan

### Panel Pelanggan

* Dashboard Pelanggan
* Katalog Produk
* Pemesanan Produk
* Pemesanan Jasa Konveksi
* Upload Desain Custom
* Pengelolaan Data Ukuran
* Pelunasan Pesanan
* Monitoring Pengiriman
* Dokumen Transaksi

---

## 🛠️ Teknologi yang Digunakan

### Backend

* PHP Native

### Frontend

* HTML
* CSS
* JavaScript

### Database

* MySQL

### Web Server

* Laragon
* XAMPP

### Version Control

* Git
* GitHub

---

## 📸 Screenshot Aplikasi

### Halaman Login

Tambahkan screenshot halaman login.

![Login](screenshots/login.png)

---

### Dashboard Admin

Tambahkan screenshot dashboard admin.

![Dashboard Admin](screenshots/admin-dashboard.png)

---

### Manajemen Produk

Tambahkan screenshot halaman produk admin.

![Manajemen Produk](screenshots/admin-produk.png)

---

### Manajemen Pelanggan

Tambahkan screenshot halaman pelanggan admin.

![Manajemen Pelanggan](screenshots/admin-pelanggan.png)

---

### Manajemen Transaksi

Tambahkan screenshot halaman transaksi.

![Transaksi](screenshots/admin-transaksi.png)

---

### Manajemen Pelunasan

Tambahkan screenshot halaman pelunasan.

![Pelunasan](screenshots/admin-pelunasan.png)

---

### Manajemen Pengiriman

Tambahkan screenshot halaman pengiriman.

![Pengiriman](screenshots/admin-pengiriman.png)

---

### Dashboard Pelanggan

Tambahkan screenshot dashboard pelanggan.

![Dashboard Pelanggan](screenshots/pelanggan-dashboard.png)

---

### Katalog Produk

Tambahkan screenshot katalog produk.

![Produk](screenshots/pelanggan-produk.png)

---

### Pemesanan Jasa Konveksi

Tambahkan screenshot halaman jasa konveksi.

![Jasa Konveksi](screenshots/pelanggan-konveksi.png)

---

### Dokumen Transaksi

Tambahkan screenshot dokumen transaksi.

![Dokumen Transaksi](screenshots/dokumen-transaksi.png)

---

## 📊 Analisis dan Perancangan Sistem

Dokumentasi yang tersedia dalam proyek ini meliputi:

* Use Case Diagram
* Activity Diagram
* Sequence Diagram
* Class Diagram
* Analisis Kebutuhan Sistem

Dokumen dapat ditemukan pada folder:

```text
Uml konveksi/
Dokumen/
```

---

## 📂 Struktur Project

```text
admin/
api/
assets/
auth/
config/
database/
img/
includes/
pelanggan/
index.php
```

---

## ⚙️ Instalasi dan Menjalankan Project

### 1. Clone Repository

```bash
git clone https://github.com/louishasashi-dev/4minds_convection.git
```

### 2. Pindahkan Project

Laragon:

```text
C:\laragon\www\
```

atau XAMPP:

```text
C:\xampp\htdocs\
```

### 3. Buat Database

Buat database baru pada MySQL.

Contoh:

```sql
CREATE DATABASE konveksi_db;
```

### 4. Import Database

Import file database yang tersedia pada folder:

```text
database/
```

### 5. Konfigurasi Database

Sesuaikan konfigurasi koneksi database pada file:

```text
config/db.php
```

### 6. Jalankan Aplikasi

Buka browser:

```text
http://localhost/konveksi
```

---

## 👨‍💻 Kontribusi Pribadi

Pada proyek ini saya terlibat dalam:

* Analisis kebutuhan sistem
* Perancangan UML
* Perancangan database MySQL
* Pengembangan frontend
* Pengembangan backend PHP
* Integrasi database
* Pengujian dan debugging aplikasi

---

## 📌 Catatan

Repository ini dibuat sebagai proyek akademik dan digunakan sebagai portofolio pengembangan aplikasi web berbasis PHP Native.

---
## 👥 Tim Pengembang

Proyek ini dikembangkan secara kolaboratif sebagai tugas kelompok.

### Kontribusi Tim

#### Louis Hasashi Halim
###### Github : https://github.com/louishasashi-dev
**Lead Developer**

* Pengembangan mayoritas fitur aplikasi
* Implementasi backend menggunakan PHP Native
* Pengembangan antarmuka pengguna (frontend)
* Perancangan dan integrasi database MySQL
* Implementasi modul transaksi, pelanggan, produk, pengiriman, dan pelunasan
* Pengujian dan debugging aplikasi
* Berkontribusi pada dokumentasi analisis sistem dan UML

#### Muhamad Reza Faurizki
###### Github : https://github.com/mozzki-f
**System Analyst & Supporting Developer**

* Menyusun mayoritas dokumentasi UML dan elisitasi kebutuhan sistem
* Membantu perbaikan bug pada fitur pengelolaan ukuran model
* Memperbaiki logika pengurangan stok produk
* Membantu penyempurnaan antarmuka pengguna dan tata letak halaman
* Berpartisipasi dalam proses pengujian sistem

#### Bunaya Ardik Saputra
###### Github : https://github.com/bunaya123
**Product Research Contributor**

* Memberikan ide dan referensi produk yang digunakan dalam aplikasi
* Membantu penyusunan kebutuhan bisnis terkait katalog produk

#### Fachrur Hannan Williyan
###### Github : https://github.com/fachrurhannan
**Presentation & Product Content Contributor**

* Berkontribusi dalam penyusunan materi presentasi proyek
* Membantu proses presentasi hasil pengembangan sistem
* Berpartisipasi sebagai model pada dokumentasi dan foto produk yang digunakan dalam aplikasi

### Catatan

Meskipun proyek ini dikerjakan secara berkelompok, mayoritas pengembangan aplikasi dan implementasi sistem dilakukan oleh Lead Developer, sementara anggota tim lainnya berkontribusi pada analisis sistem, perbaikan fitur, dokumentasi, konten produk, dan presentasi proyek.

