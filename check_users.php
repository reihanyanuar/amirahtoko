<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
echo "=== DAFTAR USER DAN USERNAME ===\n";
$users = User::all(['id','name','username','role']);
foreach ($users as $u) {
    echo "ID: {$u->id} | name: {$u->name} | username: {$u->username} | role: {$u->role}\n";
}
