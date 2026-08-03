<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $primaryKey = 'KodePlg';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'KodePlg',
        'NamaPlg',
        'Alamat',
        'TingkatHrg',
        'NamaSales',
        'Jadwal',
        'SaldoKredit',
        'Poin',
    ];
}
