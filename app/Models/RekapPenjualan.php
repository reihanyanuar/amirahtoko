<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapPenjualan extends Model
{
    protected $table = 'rekappenjualan';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'NoNota',
        'Tanggal',
        'KodePlg',
        'NamaPlg',
        'Alamat',
        'NamaSales',
        'NilaiNota',
        'Bayar1',
        'Bayar2',
        'Retur',
        'Sisa',
        'Status',
        'Jthtempo',
    ];
}
