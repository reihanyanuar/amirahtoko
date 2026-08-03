<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenjualanController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('kasir/penjualan', [PenjualanController::class, 'index']);
Route::post('kasir/penjualan/simpan', [PenjualanController::class, 'simpan']);

Route::get('/kasir/stok', [PenjualanController::class, 'stok']);