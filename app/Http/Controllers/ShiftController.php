<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Penjualan;

class ShiftController extends Controller
{
    public function index()
    {
        $shiftAktif = Shift::where('user_id', auth()->id())->whereNull('selesai')->first();
        return view('kasir.shift', compact('shiftAktif'));
    }

    public function buka(Request $request)
    {
        $request->validate(['kas_awal' => 'required|numeric']);

        Shift::create([
            'user_id'  => auth()->id(),
            'kas_awal' => $request->kas_awal,
            'mulai'    => now(),
        ]);

        return redirect('/kasir/shift');
    }

    public function tutup(Request $request)
    {
        $shift = Shift::where('user_id', auth()->id())->whereNull('selesai')->firstOrFail();

        $totalTunai = Penjualan::where('Operator', auth()->user()->username)
            ->whereDate('Tanggal', $shift->mulai->format('Y-m-d'))
            ->where('CaraBayar', 'Tunai')
            ->sum('TotalHarga');

        $kasAkhirSistem = $shift->kas_awal + $totalTunai;

        $shift->update([
            'kas_akhir_sistem' => $kasAkhirSistem,
            'kas_fisik'        => $request->kas_fisik,
            'selisih'          => $request->kas_fisik - $kasAkhirSistem,
            'selesai'          => now(),
        ]);

        return redirect('/kasir/shift')->with('shift_selesai', $shift);
    }
}