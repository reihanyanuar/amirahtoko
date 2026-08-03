<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table = 'retur';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'NoRetur',
        'Tanggal',
        'KodeSup',
        'NamaSup',
        'Keterangan',
        'KodeBrg',
        'NamaBrg',
        'Harga',
        'Jumlah',
        'Sat',
        'TotalHarga',
    ];
}
