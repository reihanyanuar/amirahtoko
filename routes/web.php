<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AdminController;

Route::get('/', fn () => redirect('/login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['role:kasir,admin,manajer'])->prefix('kasir')->group(function () {
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::post('/penjualan/simpan', [PenjualanController::class, 'simpan']);
    Route::get('/stok', [PenjualanController::class, 'stok']);
    Route::get('/riwayat', [PenjualanController::class, 'riwayat']);

    Route::get('/shift', [ShiftController::class, 'index']);
    Route::post('/shift/buka', [ShiftController::class, 'buka']);
    Route::post('/shift/tutup', [ShiftController::class, 'tutup']);
});

Route::middleware(['role:admin,manajer'])->prefix('admin')->group(function () {
    Route::get('/barang', [AdminController::class, 'barang']);
    Route::post('/barang/simpan', [AdminController::class, 'simpanBarang']);

    Route::get('/kategori', [AdminController::class, 'kategori']);

    Route::get('/supplier', [AdminController::class, 'supplier']);
    Route::post('/supplier/simpan', [AdminController::class, 'simpanSupplier']);

    Route::get('/riwayat', [AdminController::class, 'riwayat']);

    Route::get('/barang/edit/{kode}', [AdminController::class, 'editBarang']);
    Route::post('/barang/update/{kode}', [AdminController::class, 'updateBarang']);
    Route::post('/barang/hapus/{kode}', [AdminController::class, 'hapusBarang']);

    Route::get('/supplier/edit/{kode}', [AdminController::class, 'editSupplier']);
    Route::post('/supplier/update/{kode}', [AdminController::class, 'updateSupplier']);
    Route::post('/supplier/hapus/{kode}', [AdminController::class, 'hapusSupplier']);  
});