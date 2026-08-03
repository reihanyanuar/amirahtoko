<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditSaldo extends Model
{
    protected $table = 'editsaldo';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'Urut',
        'Tanggal',
        'Jam',
        'UserName',
        'KodePlg',
        'NamaPlg',
        'SaldoKreditAwal',
        'JmlBayar',
        'SaldoSkr',
        'Ket',
    ];
}
