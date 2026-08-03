<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'Urut',
        'NoNota',
        'Tanggal',
        'Jam',
        'Operator',
        'CaraBayar',
        'Jthtempo',
        'NoFaktur',
        'KodeSup',
        'NamaSup',
        'KodeBrg',
        'NamaBrg',
        'Qty',
        'Sat',
        'Harga',
        'Diskon',
        'HargaDis',
        'TotalHarga',
        'Awal',
        'Akhir',
        'Bayar',
        'Sisa',
        'Ket',
    ];
}
