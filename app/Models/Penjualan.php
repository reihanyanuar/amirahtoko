<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    // Tabel lama ini TIDAK punya primary key asli.
    // Insert & select tetap bisa, tapi update()/delete() lewat Model instance perlu query manual (where...).
    public $incrementing = false;
    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'Urut',
        'Komp',
        'NoNota',
        'Tanggal',
        'Jam',
        'CaraBayar',
        'Jthtempo',
        'Operator',
        'KodePlg',
        'NamaPlg',
        'IdKode',
        'KodeBrg',
        'NamaBrg',
        'Hpp',
        'Harga',
        'Diskon',
        'HargaDis',
        'Qty',
        'Sat',
        'TotalHarga',
        'SatJual',
        'JmlBrg',
        'Jumlah',
        'SisaKredit',
        'Total',
        'Poin',
        'Bayar',
        'Sisa',
        'Ket',
    ];
}
