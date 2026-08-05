<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Shift;

class PenjualanController extends Controller
{
    public function index()
    {
        // cek apakah kasir yang login punya shift aktif (selesai==NULL)
        $shiftAktif = Shift::where('user_id', auth()->id())->whereNull('selesai')->first();

        // jika BELUM maka akan redirect langsung ke halaman shift
        if (!$shiftAktif) {
            return redirect('/kasir/shift')->with('perlu_shift', true);
        }

        $barang = Barang::all();
        return view('kasir.penjualan', compact('barang'));
    }

    public function simpan(Request $request)
    {
        // proteksi tambahan: Cek kembali shift saat klik bayar
        $shiftAktif = Shift::where('user_id', auth()->id())->whereNull('selesai')->first();

        if (!$shiftAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Shift belum dibuka atau sudah ditutup!'
            ], 400);
        }

        $items = $request->input('items');
        $caraBayar = $request->input('cara_bayar', 'Tunai');
        $uangDiterima = (float) $request->input('bayar', 0);

        $noNota = 'TX' . now()->format('YmdHis');
        $tanggal = now()->format('Y-m-d');
        $jam = now()->format('H:i:s');

        $totalTransaksi = collect($items)->sum(fn ($i) => $i['harga'] * $i['qty']);
        $kembalian = $caraBayar === 'Tunai' ? max(0, $uangDiterima - $totalTransaksi) : 0;

        $items = array_values($items);
        $jumlahItem = count($items);

        foreach ($items as $index => $item) {
            $barang = Barang::find($item['kode']);
            $totalHargaBaris = $item['harga'] * $item['qty'];
            $isBarisTerakhir = $index === $jumlahItem - 1;

            Penjualan::create([
                'NoNota'      => $noNota,
                'Tanggal'     => $tanggal,
                'Jam'         => $jam,
                'CaraBayar'   => $caraBayar,
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
                'Sisa'        => $isBarisTerakhir ? $kembalian : 0,
                'Ket'         => $isBarisTerakhir && $kembalian > 0 ? 'Kembalian' : null,
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

    public function riwayat()
    {
        $riwayat = Penjualan::where('Operator', auth()->user()->username)
        ->whereDate('Tanggal', today())
        ->select(
            'NoNota',
            DB::raw('MIN(Jam) as jam'),
            DB::raw('MAX(CaraBayar) as cara_bayar'),
            DB::raw('SUM(TotalHarga) as total'),
            DB::raw('COUNT(*) as jumlah_barang')
        )
        ->groupBy('NoNota')
        ->orderByDesc('NoNota')
        ->get();

        return view('kasir.riwayat', compact('riwayat'));
    }

}
