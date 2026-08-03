<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    protected $table = 'mutasi';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'NoMutasi',
        'Tanggal',
        'Jam',
        'Tujuan',
        'Keterangan',
        'KodeBrg',
        'NamaBrg',
        'Harga',
        'Jumlah',
        'Sat',
        'TotalHarga',
        'StockAwal',
        'StockAkhir',
    ];
}
