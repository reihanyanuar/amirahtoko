<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek error terbaru di log
echo "=== CEK CaraBayar MAX LENGTH ===\n";
echo "varchar(6) = max 6 karakter\n";
echo "'Tunai'    = " . strlen('Tunai') . " karakter ✓\n";
echo "'Transfer' = " . strlen('Transfer') . " karakter ← MASALAH! Lebih dari 6!\n";
echo "'QRIS'     = " . strlen('QRIS') . " karakter ✓\n\n";

// Fix kolom CaraBayar menjadi varchar(15)
echo "=== FIX: ALTER TABLE penjualan ===\n";
DB::statement("ALTER TABLE penjualan MODIFY CaraBayar varchar(15) NULL");
echo "CaraBayar sudah diubah ke varchar(15) ✓\n";

// Verifikasi
$col = DB::selectOne("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'penjualan' AND COLUMN_NAME = 'CaraBayar'");
echo "Tipe sekarang: " . $col->COLUMN_TYPE . "\n";
