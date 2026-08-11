<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Shift;

echo "=== CHECK USER & SHIFT ===\n";
$users = User::all();
foreach ($users as $u) {
    $shift = Shift::where('user_id', $u->id)->whereNull('selesai')->first();
    echo "User ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | Shift Aktif: " . ($shift ? "ADA (ID {$shift->id}, Kas Awal {$shift->kas_awal})" : "TIDAK ADA") . "\n";
}
