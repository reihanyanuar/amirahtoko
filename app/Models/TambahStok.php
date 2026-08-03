<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TambahStok extends Model
{
    protected $table = 'tambahstok';

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
        'IdKode',
        'Ket',
        'KodeBrg',
        'KodeSdg',
        'KodeBsr',
        'NamaBrg',
        'Qty',
        'Sat',
        'JmlBrg',
        'Catatan',
        'Awal',
        'Akhir',
    ];
}
