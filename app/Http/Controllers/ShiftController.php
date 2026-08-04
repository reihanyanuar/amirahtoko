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

        if ($shiftAktif) {
            $shiftAktif->pendapatan = $this->hitungPendapatan($shiftAktif);
        }

        $riwayatShift = Shift::where('user_id', auth()->id())
            ->whereNotNull('selesai')
            ->whereDate('mulai', today())
            ->orderByDesc('mulai')
            ->get();

        return view('kasir.shift', compact('shiftAktif', 'riwayatShift'));
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

        $pendapatan = $this->hitungPendapatan($shift);
        $kasAkhirSistem = $shift->kas_awal + $pendapatan;

        $shift->update([
            'kas_akhir_sistem' => $kasAkhirSistem,
            'kas_fisik'        => $request->kas_fisik,
            'selisih'          => $request->kas_fisik - $kasAkhirSistem,
            'selesai'          => now(),
        ]);

        return redirect('/kasir/shift')->with('shift_selesai', $shift);
    }

    private function hitungPendapatan(Shift $shift): float
    {
        $query = Penjualan::where('Operator', auth()->user()->username)
            ->whereDate('Tanggal', '>=', $shift->mulai->format('Y-m-d'));

        if ($shift->selesai) {
            $query->whereDate('Tanggal', '<=', $shift->selesai->format('Y-m-d'));
        }

        return (float) $query->sum('TotalHarga');
    }
}