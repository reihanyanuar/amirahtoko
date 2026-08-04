<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id', 'kas_awal', 'kas_akhir_sistem', 'kas_fisik', 'selisih', 'mulai', 'selesai'
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];
}
