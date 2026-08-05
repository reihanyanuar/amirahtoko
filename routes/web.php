<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ShiftController;

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