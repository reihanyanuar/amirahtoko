<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManajerController;

Route::get('/', fn () => redirect('/login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Halaman kasir (penjualan, stok, riwayat) — semua role bisa akses
Route::middleware(['role:kasir,admin,manajer'])->prefix('kasir')->group(function () {
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::post('/penjualan/simpan', [PenjualanController::class, 'simpan']);
    Route::get('/pelanggan/search', [PenjualanController::class, 'cariPelanggan']);
    Route::get('/stok', [PenjualanController::class, 'stok']);
    Route::get('/riwayat', [PenjualanController::class, 'riwayat']);
});

// Shift HANYA untuk role kasir — Admin & Manajer tidak bisa buka/tutup shift
Route::middleware(['role:kasir'])->prefix('kasir')->group(function () {
    Route::get('/shift', [ShiftController::class, 'index']);
    Route::post('/shift/buka', [ShiftController::class, 'buka']);
    Route::post('/shift/tutup', [ShiftController::class, 'tutup']);
});

Route::middleware(['role:admin,manajer'])->prefix('admin')->group(function () {
    Route::get('/barang', [AdminController::class, 'barang']);
    Route::get('/barang/tambah', [AdminController::class, 'tambahBarang']);
    Route::get('/barang/check/{kode}', [AdminController::class, 'checkKodeBarang']);
    Route::post('/barang/simpan', [AdminController::class, 'simpanBarang']);

    Route::get('/kategori', [AdminController::class, 'kategori']);
    Route::post('/kategori/simpan', [AdminController::class, 'simpanKategori']);
    Route::post('/kategori/update', [AdminController::class, 'updateKategori']);
    Route::post('/kategori/hapus', [AdminController::class, 'hapusKategori']);

    Route::get('/supplier', [AdminController::class, 'supplier']);
    Route::post('/supplier/simpan', [AdminController::class, 'simpanSupplier']);

    Route::get('/pelanggan', [AdminController::class, 'pelanggan']);
    Route::post('/pelanggan/simpan', [AdminController::class, 'simpanPelanggan']);
    Route::post('/pelanggan/update/{kode}', [AdminController::class, 'updatePelanggan']);
    Route::post('/pelanggan/hapus/{kode}', [AdminController::class, 'hapusPelanggan']);

    Route::get('/riwayat', [AdminController::class, 'riwayat']);

    Route::get('/tambah-stok', [AdminController::class, 'tambahStok']);
    Route::post('/tambah-stok/simpan', [AdminController::class, 'simpanTambahStok']);

    Route::get('/barang/edit/{kode}', [AdminController::class, 'editBarang']);
    Route::post('/barang/update/{kode}', [AdminController::class, 'updateBarang']);
    Route::post('/barang/hapus/{kode}', [AdminController::class, 'hapusBarang']);

    Route::get('/supplier/edit/{kode}', [AdminController::class, 'editSupplier']);
    Route::post('/supplier/update/{kode}', [AdminController::class, 'updateSupplier']);
    Route::post('/supplier/hapus/{kode}', [AdminController::class, 'hapusSupplier']);  
});

Route::middleware(['role:manajer'])->prefix('manajer')->group(function () {
    // 1. Dashboard & Statistik
    Route::get('/dashboard', [ManajerController::class, 'dashboard']);

    // 2. Kelola Akun
    Route::get('/akun', [ManajerController::class, 'akun']);
    Route::post('/akun/simpan', [ManajerController::class, 'simpanAkun']);
    Route::post('/akun/update/{id}', [ManajerController::class, 'updateAkun']);
    Route::post('/akun/reset-password/{id}', [ManajerController::class, 'resetPassword']);
    Route::post('/akun/toggle-status/{id}', [ManajerController::class, 'toggleStatus']);

    // 2b. Data Pelanggan (Akses langsung Manajer)
    Route::get('/pelanggan', [AdminController::class, 'pelanggan']);

    // 3. Laporan Shift
    Route::get('/shift', [ManajerController::class, 'laporanShift']);

    // 4. Laporan Transaksi Menyeluruh
    Route::get('/laporan-transaksi', [ManajerController::class, 'laporanTransaksi']);

    // 5. Kasir Aktif
    Route::get('/kasir-aktif', [ManajerController::class, 'kasirAktif']);
});