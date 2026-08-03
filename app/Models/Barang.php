<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $primaryKey = 'KodeBrg';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'KodeBrg', 'NamaBrg', 'KodeSdg', 'KodeBsr',
        'IsiBsr', 'IsiSdg', 'NamaSup', 'Jenis',
        'SatBsr', 'SatSdg', 'SatKcl', 'JmlStock',
        'Hpp', 'HppSdg', 'HppBsr', 'HrgBsr', 'HrgSdg',
        'Harga1', 'Harga2', 'Limit2', 'Harga3', 'Limit3',
        'Catatan',
    ];
}