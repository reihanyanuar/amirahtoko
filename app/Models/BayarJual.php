<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BayarJual extends Model
{
    protected $table = 'bayarjual';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'TglBayar',
        'NoNota',
        'KodePlg',
        'NamaPlg',
        'Alamat',
        'NamaSales',
        'Bayar',
        'JmlSisa',
        'Status',
    ];
}
