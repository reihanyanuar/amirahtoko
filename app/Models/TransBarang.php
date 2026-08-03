<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransBarang extends Model
{
    protected $table = 'transbarang';

    protected $primaryKey = 'NoUrut';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'NoUrut',
        'Tgl',
        'Jam',
        'Username',
        'StokAwal',
        'Qty',
        'Ket',
        'KodeBrg',
        'NamaBrg',
        'Warna',
        'Uk',
        'Jenis',
        'NamaSup',
        'JmlStock',
        'Sat',
        'Hpp',
        'Harga1',
    ];
}
