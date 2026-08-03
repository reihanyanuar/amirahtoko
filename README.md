Amirahtoko — Web Kasir Toko (Laravel 12)

Sistem kasir & stok toko, dibangun ulang dari sistem lama Amirahsoft SmartGrosir (desktop, Visual Basic) ke versi web menggunakan Laravel 12. Database MySQL lama (amirahsmart) tetap dipakai — data lama ikut dipindahkan, bukan mulai dari nol.

Role Pengguna
Kasir — Penjualan, Stok Barang (lihat saja), Riwayat Transaksi, Buka/Tutup Shift
Admin — Penjualan, Input Barang, Kategori Produk, Riwayat Transaksi
Manajer — Penjualan, Dashboard & Statistik, Riwayat Transaksi, Laporan Shift, Kelola Akun
Kebutuhan sebelum install

Pastikan sudah terpasang di komputer:

PHP 8.3 atau lebih baru
Composer
MySQL / MariaDB (disarankan pakai Laragon untuk Windows, sudah termasuk semua ini)
Git
Langkah setup di komputer baru
1. Ambil kode project

Kalau ini pertama kali ambil project:

bash
git clone https://github.com/reihanyanuar/amirahtoko.git
cd amirahtoko

Kalau sudah pernah clone sebelumnya, cukup ambil update terbaru:

bash
git pull
2. Install semua dependency PHP
bash
composer install
3. Siapkan file .env

File .env sengaja tidak ikut di-upload ke GitHub (demi keamanan, karena isinya termasuk kredensial database). Jadi perlu dibuat manual:

bash
copy .env.example .env

(di Windows CMD/PowerShell; kalau di Mac/Linux pakai cp .env.example .env)

Lalu buka file .env, sesuaikan bagian koneksi database:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amirahsmart
DB_USERNAME=root
DB_PASSWORD=

(sesuaikan username/password dengan setting MySQL di komputer masing-masing)

4. Generate application key
bash
php artisan key:generate
5. Siapkan database

Database (amirahsmart) tidak ikut ter-upload ke GitHub — Git hanya menyimpan kode program, bukan isi database. Jadi database perlu di-import terpisah:

Buat database baru bernama amirahsmart di MySQL
Import file dbamirahsmart.sql ke database tersebut (lewat phpMyAdmin, HeidiSQL, atau command line)
Jalankan migration tambahan (untuk kolom yang ditambahkan lewat Laravel):
bash
   php artisan migrate
6. Jalankan server
bash
php artisan serve

Buka browser ke:

http://127.0.0.1:8000/login
Alur kerja Git sehari-hari

Setelah mengubah kode, kirim ke GitHub:

bash
git add .
git commit -m "deskripsi singkat perubahan"
git push

Mengambil perubahan terbaru dari GitHub:

bash
git pull
Catatan
File .env dan isi database tidak disinkronkan lewat Git — masing-masing komputer perlu setup sendiri sesuai langkah di atas.
Project masih dalam tahap pengembangan aktif.