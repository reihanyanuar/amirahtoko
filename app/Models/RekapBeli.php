<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapBeli extends Model
{
    protected $table = 'rekapbeli';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'NoNota',
        'Tanggal',
        'NoFaktur',
        'KodeSup',
        'NamaSup',
        'Alamat',
        'NilaiNota',
        'Bayar',
        'Sisa',
        'Status',
        'JthTempo',
    ];
}
