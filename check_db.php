<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STRUKTUR TABEL penjualan ===\n";
$cols = DB::select('DESCRIBE penjualan');
foreach ($cols as $c) {
    echo "{$c->Field} | {$c->Type} | Null={$c->Null} | Default=" . ($c->Default ?? 'NULL') . "\n";
}
