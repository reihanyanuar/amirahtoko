<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Penjualan;

class AdminController extends Controller
{
    // ============ INPUT BARANG ============

    public function barang()
    {
        $barang = Barang::orderByDesc('KodeBrg')->get();
        $supplier = Supplier::all();
        $kategoriList = Barang::select('Jenis')->distinct()->whereNotNull('Jenis')->pluck('Jenis');

        return view('admin.barang', compact('barang', 'supplier', 'kategoriList'));
    }

    public function simpanBarang(Request $request)
    {
        $request->validate([
            'KodeBrg' => 'required|unique:barang,KodeBrg',
            'NamaBrg' => 'required',
            'Hpp'     => 'required|numeric',
            'Harga1'  => 'required|numeric',
        ]);

        $satuanKecil = $request->SatKcl ?: 'Pcs';

        Barang::create([
            'KodeBrg'  => $request->KodeBrg,
            'KodeSdg'  => $request->KodeSdg ?: $request->KodeBrg,
            'KodeBsr'  => $request->KodeBsr ?: $request->KodeBrg,
            'NamaBrg'  => $request->NamaBrg,
            'NamaSup'  => $request->NamaSup,
            'Jenis'    => $request->Jenis,
            'IsiBsr'   => $request->IsiBsr ?: 1,
            'IsiSdg'   => $request->IsiSdg ?: 1,
            'SatBsr'   => $request->SatBsr ?: $satuanKecil,
            'SatSdg'   => $request->SatSdg ?: $satuanKecil,
            'SatKcl'   => $satuanKecil,
            'HppBsr'   => $request->HppBsr ?: $request->Hpp,
            'HppSdg'   => $request->HppSdg ?: $request->Hpp,
            'Hpp'      => $request->Hpp,
            'HrgBsr'   => $request->HrgBsr ?: $request->Harga1,
            'HrgSdg'   => $request->HrgSdg ?: $request->Harga1,
            'Harga1'   => $request->Harga1,
            'Harga2'   => $request->Harga2 ?: $request->Harga1,
            'Limit2'   => $request->Limit2 ?: 0,
            'Harga3'   => $request->Harga3 ?: $request->Harga1,
            'Limit3'   => $request->Limit3 ?: 0,
            'JmlStock' => $request->JmlStock ?? 0,
            'Catatan'  => $request->Catatan ?: '-',
        ]);

        return redirect('/admin/barang')->with('sukses', 'Barang "' . $request->NamaBrg . '" berhasil ditambahkan.');
    }

    public function editBarang($kode)
    {
        $barang = Barang::findOrFail($kode);
        $supplier = Supplier::all();
        $kategoriList = Barang::select('Jenis')->distinct()->whereNotNull('Jenis')->pluck('Jenis');

        return view('admin.barang-edit', compact('barang', 'supplier', 'kategoriList'));
    }

    public function updateBarang(Request $request, $kode)
    {
        $barang = Barang::findOrFail($kode);

        $barang->update([
            'NamaBrg'  => $request->NamaBrg,
            'NamaSup'  => $request->NamaSup,
            'Jenis'    => $request->Jenis,
            'IsiBsr'   => $request->IsiBsr ?: 1,
            'IsiSdg'   => $request->IsiSdg ?: 1,
            'SatBsr'   => $request->SatBsr ?: $barang->SatKcl,
            'SatSdg'   => $request->SatSdg ?: $barang->SatKcl,
            'SatKcl'   => $request->SatKcl ?: 'Pcs',
            'KodeBsr'  => $request->KodeBsr ?: $kode,
            'KodeSdg'  => $request->KodeSdg ?: $kode,
            'HppBsr'   => $request->HppBsr ?: $request->Hpp,
            'HppSdg'   => $request->HppSdg ?: $request->Hpp,
            'Hpp'      => $request->Hpp,
            'HrgBsr'   => $request->HrgBsr ?: $request->Harga1,
            'HrgSdg'   => $request->HrgSdg ?: $request->Harga1,
            'Harga1'   => $request->Harga1,
            'Harga2'   => $request->Harga2 ?: $request->Harga1,
            'Limit2'   => $request->Limit2 ?: 0,
            'Harga3'   => $request->Harga3 ?: $request->Harga1,
            'Limit3'   => $request->Limit3 ?: 0,
            'JmlStock' => $request->JmlStock,
            'Catatan'  => $request->Catatan ?: '-',
        ]);

        return redirect('/admin/barang')->with('sukses', 'Barang "' . $barang->NamaBrg . '" berhasil diperbarui.');
    }

    public function hapusBarang($kode)
    {
        $barang = Barang::findOrFail($kode);
        $nama = $barang->NamaBrg;
        $barang->delete();

        return redirect('/admin/barang')->with('sukses', 'Barang "' . $nama . '" berhasil dihapus.');
    }

    public function editSupplier($kode)
    {
        $supplier = Supplier::findOrFail($kode);
        return view('admin.supplier-edit', compact('supplier'));
    }

    public function updateSupplier(Request $request, $kode)
    {
        $supplier = Supplier::findOrFail($kode);

        $supplier->update([
            'NamaSup' => $request->NamaSup,
            'Alamat'  => $request->Alamat,
        ]);

        return redirect('/admin/supplier')->with('sukses', 'Supplier "' . $supplier->NamaSup . '" berhasil diperbarui.');
    }

    public function hapusSupplier($kode)
    {
        $supplier = Supplier::findOrFail($kode);
        $nama = $supplier->NamaSup;
        $supplier->delete();

        return redirect('/admin/supplier')->with('sukses', 'Supplier "' . $nama . '" berhasil dihapus.');
    }

    // ============ KATEGORI PRODUK ============
    // Catatan: kategori diambil dari nilai unik kolom Jenis di tabel barang,
    // BUKAN dari tabel jenisbarang terpisah (struktur kolomnya belum dikonfirmasi).

    public function kategori()
    {
        $kategoriList = Barang::select('Jenis', DB::raw('COUNT(*) as total_produk'))
            ->whereNotNull('Jenis')
            ->where('Jenis', '!=', '')
            ->groupBy('Jenis')
            ->orderBy('Jenis')
            ->get();

        return view('admin.kategori', compact('kategoriList'));
    }

    // ============ DATA SUPPLIER ============

    public function supplier()
    {
        $supplier = Supplier::orderBy('NamaSup')->get();
        return view('admin.supplier', compact('supplier'));
    }

    public function simpanSupplier(Request $request)
    {
        $request->validate([
            'KodeSup' => 'required|unique:supplier,KodeSup',
            'NamaSup' => 'required',
        ]);

        Supplier::create([
            'KodeSup' => $request->KodeSup,
            'NamaSup' => $request->NamaSup,
            'Alamat'  => $request->Alamat,
        ]);

        return redirect('/admin/supplier')->with('sukses', 'Supplier "' . $request->NamaSup . '" berhasil ditambahkan.');
    }

    // ============ RIWAYAT TRANSAKSI (SEMUA KASIR) ============

    public function riwayat()
    {
        $riwayat = Penjualan::whereDate('Tanggal', today())
            ->select(
                'NoNota',
                'Operator',
                DB::raw('MIN(Jam) as jam'),
                DB::raw('MAX(CaraBayar) as cara_bayar'),
                DB::raw('SUM(TotalHarga) as total'),
                DB::raw('COUNT(*) as jumlah_barang')
            )
            ->groupBy('NoNota', 'Operator')
            ->orderByDesc('NoNota')
            ->get();

        return view('admin.riwayat', compact('riwayat'));
    }
}