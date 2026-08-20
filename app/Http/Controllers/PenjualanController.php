<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Shift;
use App\Models\Pelanggan;

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

        $dariBarang = Barang::whereNotNull('Jenis')->where('Jenis', '!=', '')->pluck('Jenis');
        $dariMaster = \App\Models\MasterKategori::pluck('NamaKategori');
        $kategoriList = $dariMaster->merge($dariBarang)->unique()->sort()->values();

        return view('kasir.penjualan', compact('barang', 'kategoriList'));
    }

    public function cariPelanggan(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (!$q) {
            return response()->json([]);
        }

        $pelanggan = Pelanggan::where('KodePlg', 'like', "%{$q}%")
            ->orWhere('NamaPlg', 'like', "%{$q}%")
            ->limit(10)
            ->get(['KodePlg', 'NamaPlg', 'TingkatHrg']);

        return response()->json($pelanggan);
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

        $items = $request->input('items', []);
        $caraBayar = $request->input('cara_bayar', 'Tunai');
        $uangDiterima = (float) $request->input('bayar', 0);
        $kodePlg = $request->input('kode_plg', 'P0001');
        $namaPlg = $request->input('nama_plg', 'Customer umum');

        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Keranjang belanja kosong!'], 400);
        }

        // VALIDASI HPP HARGA vs MODAL (Toko Tidak Boleh Rugi)
        foreach ($items as $item) {
            $barang = Barang::find($item['kode']);
            if (!$barang) continue;

            $hargaUnit = (float) ($item['harga'] ?? 0);
            $diskon = (float) ($item['diskon'] ?? 0);
            $hargaNettoUnit = $hargaUnit - $diskon;

            // Hitung HPP sesuai satuan yang dijual (Dus/Lusin/Pcs)
            $satuan = $item['satuan'] ?? 'Pcs';
            $hppSatuan = (float) ($barang->Hpp ?? 0);
            if ($satuan === $barang->SatBsr && $barang->IsiBsr > 1) {
                $hppSatuan = (float) ($barang->HppBsr ?: ($barang->Hpp * $barang->IsiBsr));
            } elseif ($satuan === $barang->SatSdg && $barang->IsiSdg > 1) {
                $hppSatuan = (float) ($barang->HppSdg ?: ($barang->Hpp * $barang->IsiSdg));
            }

            if ($hargaNettoUnit < $hppSatuan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi ditolak! Harga "' . $item['nama'] . '" setelah diskon (Rp ' . number_format($hargaNettoUnit, 0, ',', '.') . ') di bawah HPP modal (Rp ' . number_format($hppSatuan, 0, ',', '.') . '). Toko akan rugi!'
                ], 422);
            }
        }

        $noNota = 'TX' . now()->format('YmdHis');
        $tanggal = now()->format('Y-m-d');
        $jam = now()->format('H:i:s');

        $totalTransaksi = collect($items)->sum(function ($i) {
            $h = (float) ($i['harga'] ?? 0);
            $d = (float) ($i['diskon'] ?? 0);
            return max(0, $h - $d) * (int) ($i['qty'] ?? 1);
        });

        $kembalian = $caraBayar === 'Tunai' ? max(0, $uangDiterima - $totalTransaksi) : 0;

        $items = array_values($items);
        $jumlahItem = count($items);

        foreach ($items as $index => $item) {
            $barang = Barang::find($item['kode']);
            $hargaUnit = (float) ($item['harga'] ?? 0);
            $diskon = (float) ($item['diskon'] ?? 0);
            $hargaNettoUnit = max(0, $hargaUnit - $diskon);
            $qty = (int) ($item['qty'] ?? 1);
            $totalHargaBaris = $hargaNettoUnit * $qty;
            $isBarisTerakhir = $index === $jumlahItem - 1;

            $isiPcs = $item['isiPcs'] ?? 1;
            $qtyDalamPcs = $qty * $isiPcs;

            $satuan = $item['satuan'] ?? ($barang->SatKcl ?? 'Pcs');
            $hppSatuan = (float) ($barang->Hpp ?? 0);
            if ($barang) {
                if ($satuan === $barang->SatBsr && $barang->IsiBsr > 1) {
                    $hppSatuan = (float) ($barang->HppBsr ?: ($barang->Hpp * $barang->IsiBsr));
                } elseif ($satuan === $barang->SatSdg && $barang->IsiSdg > 1) {
                    $hppSatuan = (float) ($barang->HppSdg ?: ($barang->Hpp * $barang->IsiSdg));
                }
            }

            Penjualan::create([
                'NoNota'      => $noNota,
                'Tanggal'     => $tanggal,
                'Jam'         => $jam,
                'CaraBayar'   => $caraBayar,
                'Operator'    => auth()->user()->username,
                'KodePlg'     => $kodePlg,
                'NamaPlg'     => $namaPlg,
                'IdKode'      => $noNota,
                'KodeBrg'     => $item['kode'],
                'NamaBrg'     => $item['nama'],
                'Hpp'         => $hppSatuan,
                'Harga'       => $hargaUnit,
                'Diskon'      => $diskon,
                'HargaDis'    => $hargaNettoUnit,
                'Qty'         => $qty,
                'Sat'         => $satuan,
                'TotalHarga'  => $totalHargaBaris,
                'SatJual'     => $satuan,
                'JmlBrg'      => $qtyDalamPcs,
                'Jumlah'      => $totalHargaBaris,
                'SisaKredit'  => 0,
                'Total'       => $totalHargaBaris,
                'Poin'        => 0,
                'Bayar'       => $totalHargaBaris,
                'Sisa'        => $isBarisTerakhir ? $kembalian : 0,
                'Ket'         => $isBarisTerakhir && $kembalian > 0 ? 'Kembalian' : null,
            ]);

            if ($barang) {
                $barang->JmlStock -= $qtyDalamPcs;
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
