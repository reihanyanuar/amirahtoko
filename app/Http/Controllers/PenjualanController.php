<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Penjualan;

class PenjualanController extends Controller
{
    public function index()
    {
        $barang = Barang::all();
        return view('kasir.penjualan', compact('barang'));
    }

    public function simpan(Request $request)
    {
        $items = $request->input('items');
        $noNota = 'TX' . now()->format('YmdHis');
        $tanggal = now()->format('Y-m-d');
        $jam = now()->format('H:i:s');

        foreach ($items as $item) {
            $barang = Barang::find($item['kode']);

            $totalHargaBaris = $item['harga'] * $item['qty'];

            Penjualan::create([
                'NoNota'      => $noNota,
                'Tanggal'     => $tanggal,
                'Jam'         => $jam,
                'CaraBayar'   => 'Tunai',
                'Operator'    => auth()->user()->username,
                'KodePlg'     => 'P0001',
                'NamaPlg'     => 'Customer umum',
                'IdKode'      => $noNota,
                'KodeBrg'     => $item['kode'],
                'NamaBrg'     => $item['nama'],
                'Hpp'         => $barang->Hpp ?? 0,
                'Harga'       => $item['harga'],
                'Diskon'      => 0,
                'HargaDis'    => $item['harga'],
                'Qty'         => $item['qty'],
                'Sat'         => $barang->SatKcl ?? 'Pcs',
                'TotalHarga'  => $totalHargaBaris,
                'SatJual'     => $barang->SatKcl ?? 'Pcs',
                'JmlBrg'      => $item['qty'],
                'Jumlah'      => $totalHargaBaris,
                'SisaKredit'  => 0,
                'Total'       => $totalHargaBaris,
                'Poin'        => 0,
                'Bayar'       => $totalHargaBaris,
                'Sisa'        => 0,
            ]);

            if ($barang) {
                $barang->JmlStock -= $item['qty'];
                $barang->save();
            }
        }

        return response()->json(['success' => true, 'no_nota' => $noNota]);
    }
    public function stok()
    {
        $barang = Barang::all();
        return view('kasir.stok', compact('barang'));
    }

}
