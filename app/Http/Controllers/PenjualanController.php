<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class PenjualanController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        
        return view('kasir.penjualan', compact('barang'));
    }
}
