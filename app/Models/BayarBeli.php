<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BayarBeli extends Model
{
    protected $table = 'bayarbeli';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'TglBayar',
        'NoNota',
        'KodeSup',
        'NamaSup',
        'Alamat',
        'Bayar',
        'JmlSisa',
    ];
}
