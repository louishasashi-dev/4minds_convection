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

### Halaman Publik

<img width="1919" height="877" alt="image" src="https://github.com/user-attachments/assets/55a6ae23-471a-4bdd-a8c4-a5412091c089" />



### Halaman Login

<img width="1919" height="891" alt="image" src="https://github.com/user-attachments/assets/bd770983-8537-4275-ac8f-034bce56873c" />

### Dashboard Admin

<img width="1911" height="883" alt="image" src="https://github.com/user-attachments/assets/092fe256-a85d-4f16-85ce-d28a454a2055" />

### Manajemen Produk

<img width="1919" height="885" alt="image" src="https://github.com/user-attachments/assets/d977d9fa-1095-42e6-9b57-b912bcfd1e78" />

### Manajemen Pelanggan

<img width="1919" height="889" alt="image" src="https://github.com/user-attachments/assets/9373e297-6131-48cd-bc08-cf7dcac0de2f" />

### Manajemen Transaksi

<img width="1919" height="884" alt="image" src="https://github.com/user-attachments/assets/5a3ae628-a741-4c7d-95b9-279fe0607b61" />

### Manajemen Pelunasan

<img width="1917" height="884" alt="image" src="https://github.com/user-attachments/assets/a0bf030e-27a0-48ec-9a59-93928070c786" />

### Manajemen Pengiriman

<img width="1919" height="884" alt="image" src="https://github.com/user-attachments/assets/3f30cc33-00dd-46f8-8334-1f0e3005152f" />

### Dashboard Pelanggan

<img width="1919" height="879" alt="image" src="https://github.com/user-attachments/assets/2292dcba-1f6d-4aca-982b-d99d634e40ac" />

### Katalog Produk

<img width="1919" height="889" alt="image" src="https://github.com/user-attachments/assets/2aca6ae9-c919-4185-a0b5-78d0dea17f10" />

### Pemesanan Jasa Konveksi

<img width="1919" height="886" alt="image" src="https://github.com/user-attachments/assets/74e7b348-731a-4830-96ce-2d81be9ed954" />

### Dokumen Transaksi

<img width="603" height="671" alt="image" src="https://github.com/user-attachments/assets/3ac2d158-2995-4e0e-a6db-c6375aa6631c" />
<img width="604" height="674" alt="image" src="https://github.com/user-attachments/assets/0205be36-fc82-4a46-bc0b-d2e8ecd423ed" />
<img width="599" height="669" alt="image" src="https://github.com/user-attachments/assets/26b77f7b-6492-461c-b1dc-1b748778c766" />

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

