<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisAcc extends Model
{
    protected $table = 'jenisacc';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'KelompokAcc',
    ];
}
