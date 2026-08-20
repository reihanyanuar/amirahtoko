<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Penjualan;
use App\Models\MasterKategori;
use App\Models\TambahStok;
use App\Models\Pelanggan;

class AdminController extends Controller
{
    // ============ INPUT BARANG ============

    public function barang()
    {
        $barang = Barang::orderByDesc('KodeBrg')->get();
        $supplier = Supplier::all();
        $kategoriList = $this->getKategoriList();

        return view('admin.barang', compact('barang', 'supplier', 'kategoriList'));
    }

    public function tambahBarang()
    {
        $supplier = Supplier::all();
        $kategoriList = $this->getKategoriList();

        return view('admin.barang-tambah', compact('supplier', 'kategoriList'));
    }

    public function checkKodeBarang($kode)
    {
        $barang = Barang::where('KodeBrg', $kode)
            ->orWhere('KodeBsr', $kode)
            ->orWhere('KodeSdg', $kode)
            ->first();

        if ($barang) {
            return response()->json([
                'exists'       => true,
                'kode'         => $barang->KodeBrg,
                'nama'         => $barang->NamaBrg,
                'redirect_url' => url('/admin/barang/edit/' . $barang->KodeBrg)
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function simpanBarang(Request $request)
    {
        // Cek jika Kode Barang/Barcode sudah terdaftar -> Otomatis alihkan ke Edit Mode
        $existing = Barang::where('KodeBrg', $request->KodeBrg)
            ->orWhere('KodeBsr', $request->KodeBrg)
            ->orWhere('KodeSdg', $request->KodeBrg)
            ->first();

        if ($existing) {
            return redirect('/admin/barang/edit/' . $existing->KodeBrg)
                ->with('info', 'Barang "' . $existing->NamaBrg . '" dengan barcode [' . $request->KodeBrg . '] sudah terdaftar! Dialihkan ke Form Edit.');
        }

        $messages = [
            'KodeBrg.required' => 'Kode Barang wajib diisi!',
            'NamaBrg.required' => 'Nama Barang wajib diisi!',
            'NamaSup.required' => 'Supplier wajib dipilih!',
            'Jenis.required'   => 'Jenis Kategori wajib diisi!',
            'Hpp.required'     => 'HPP Pcs / Modal wajib diisi!',
            'Hpp.numeric'      => 'HPP Pcs / Modal harus berupa angka!',
            'Harga1.required'  => 'Harga Pcs Utama / Jual wajib diisi!',
            'Harga1.numeric'   => 'Harga Pcs Utama / Jual harus berupa angka!',
        ];

        $request->validate([
            'KodeBrg' => 'required|unique:barang,KodeBrg',
            'NamaBrg' => 'required',
            'NamaSup' => 'required',
            'Jenis'   => 'required',
            'Hpp'     => 'required|numeric',
            'Harga1'  => 'required|numeric',
        ], $messages);

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
        $kategoriList = $this->getKategoriList();

        return view('admin.barang-edit', compact('barang', 'supplier', 'kategoriList'));
    }

    public function updateBarang(Request $request, $kode)
    {
        $barang = Barang::findOrFail($kode);

        $messages = [
            'NamaBrg.required' => 'Nama Barang wajib diisi!',
            'NamaSup.required' => 'Supplier wajib dipilih!',
            'Jenis.required'   => 'Jenis Kategori wajib diisi!',
            'Hpp.required'     => 'HPP Pcs / Modal wajib diisi!',
            'Hpp.numeric'      => 'HPP Pcs / Modal harus berupa angka!',
            'Harga1.required'  => 'Harga Pcs Utama / Jual wajib diisi!',
            'Harga1.numeric'   => 'Harga Pcs Utama / Jual harus berupa angka!',
        ];

        $request->validate([
            'NamaBrg' => 'required',
            'NamaSup' => 'required',
            'Jenis'   => 'required',
            'Hpp'     => 'required|numeric',
            'Harga1'  => 'required|numeric',
        ], $messages);

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

    private function getKategoriList()
    {
        $dariBarang = Barang::whereNotNull('Jenis')->where('Jenis', '!=', '')->pluck('Jenis');
        $dariMaster = MasterKategori::pluck('NamaKategori');
        return $dariMaster->merge($dariBarang)->unique()->sort()->values();
    }

    public function kategori()
    {
        // Ambil dari barang (dengan jumlah produk)
        $dariBarang = Barang::select('Jenis', DB::raw('COUNT(*) as total_produk'))
            ->whereNotNull('Jenis')
            ->where('Jenis', '!=', '')
            ->groupBy('Jenis')
            ->pluck('total_produk', 'Jenis')
            ->toArray();

        // Ambil dari master_kategori
        $dariMaster = MasterKategori::orderBy('NamaKategori')->pluck('NamaKategori');

        // Gabungkan: master + barang, hilangkan duplikat
        $semuaNama = $dariMaster->merge(array_keys($dariBarang))->unique()->sort()->values();

        $kategoriList = $semuaNama->map(function ($nama) use ($dariBarang) {
            return (object)[
                'Jenis'        => $nama,
                'total_produk' => $dariBarang[$nama] ?? 0,
                'dari_master'  => !isset($dariBarang[$nama]),
            ];
        });

        return view('admin.kategori', compact('kategoriList'));
    }

    public function simpanKategori(Request $request)
    {
        $nama = trim($request->NamaKategori);

        if (!$nama) {
            return back()->with('error', 'Nama kategori tidak boleh kosong!');
        }

        // Cek sudah ada di barang atau master
        $adaDiBarang = Barang::where('Jenis', $nama)->exists();
        $adaDiMaster = MasterKategori::where('NamaKategori', $nama)->exists();

        if ($adaDiBarang || $adaDiMaster) {
            return back()->with('error', 'Kategori "' . $nama . '" sudah ada!');
        }

        MasterKategori::create(['NamaKategori' => $nama]);

        return redirect('/admin/kategori')->with('sukses', 'Kategori "' . $nama . '" berhasil ditambahkan.');
    }

    public function updateKategori(Request $request)
    {
        $lama = $request->jenis_lama;
        $baru = trim($request->jenis_baru);

        if (!$baru) {
            return back()->with('error', 'Nama kategori baru tidak boleh kosong!');
        }

        // Update di tabel barang
        Barang::where('Jenis', $lama)->update(['Jenis' => $baru]);

        // Update/rename di master_kategori jika ada
        MasterKategori::where('NamaKategori', $lama)->update(['NamaKategori' => $baru]);

        return redirect('/admin/kategori')->with('sukses', 'Kategori "' . $lama . '" berhasil diubah menjadi "' . $baru . '".');
    }

    public function hapusKategori(Request $request)
    {
        $jenis = $request->jenis;

        // Hapus dari barang (kosongkan Jenis)
        Barang::where('Jenis', $jenis)->update(['Jenis' => null]);

        // Hapus dari master_kategori jika ada
        MasterKategori::where('NamaKategori', $jenis)->delete();

        return redirect('/admin/kategori')->with('sukses', 'Kategori "' . $jenis . '" berhasil dihapus.');
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

    // ============ TAMBAH STOK (RESTOCK CEPAT) ============

    public function tambahStok()
    {
        $barangList = Barang::orderBy('NamaBrg')->get();
        $riwayat = TambahStok::orderByDesc('Tanggal')->orderByDesc('Jam')->take(30)->get();

        return view('admin.tambah-stok', compact('barangList', 'riwayat'));
    }

    public function simpanTambahStok(Request $request)
    {
        $request->validate([
            'KodeBrg' => 'required',
            'Qty'     => 'required|numeric|min:1',
            'Sat'     => 'required',
        ], [
            'KodeBrg.required' => 'Silakan pilih barang terlebih dahulu!',
            'Qty.required'     => 'Jumlah stok wajib diisi!',
            'Qty.min'          => 'Jumlah stok minimal 1!',
            'Sat.required'     => 'Satuan wajib dipilih!',
        ]);

        $barang = Barang::findOrFail($request->KodeBrg);
        $stokAwal = (int) $barang->JmlStock;

        $qtyInput = (int) $request->Qty;
        $satuan   = $request->Sat;
        $jmlPcs   = $qtyInput;

        // Cek apakah satuan Dus atau Lusin untuk dikalikan
        $namaSatBsr = $barang->SatBsr ?: 'Dus';
        $namaSatSdg = $barang->SatSdg ?: 'Lusin';

        if ($satuan === $namaSatBsr && $barang->IsiBsr > 1) {
            $jmlPcs = $qtyInput * (int) $barang->IsiBsr;
        } elseif ($satuan === $namaSatSdg && $barang->IsiSdg > 1) {
            $jmlPcs = $qtyInput * (int) $barang->IsiSdg;
        }

        $stokAkhir = $stokAwal + $jmlPcs;

        // Simpan ke tabel tambahstok
        $noNota = 'TS' . date('YmdHis');
        TambahStok::create([
            'Urut'     => 1,
            'NoNota'   => $noNota,
            'Tanggal'  => date('Y-m-d'),
            'Jam'      => date('H:i:s'),
            'Operator' => auth()->user()->name ?? 'Admin',
            'IdKode'   => $barang->KodeBrg,
            'Ket'      => 'Tambah Stok',
            'KodeBrg'  => $barang->KodeBrg,
            'KodeSdg'  => $barang->KodeSdg ?: $barang->KodeBrg,
            'KodeBsr'  => $barang->KodeBsr ?: $barang->KodeBsr,
            'NamaBrg'  => $barang->NamaBrg,
            'Qty'      => $qtyInput,
            'Sat'      => $satuan,
            'JmlBrg'   => $jmlPcs,
            'Catatan'  => $request->Catatan ?: '-',
            'Awal'     => $stokAwal,
            'Akhir'    => $stokAkhir,
        ]);

        // Increment stok barang
        $barang->increment('JmlStock', $jmlPcs);

        return redirect('/admin/tambah-stok')->with('sukses', "Stok \"{$barang->NamaBrg}\" berhasil ditambah {$jmlPcs} Pcs (+{$qtyInput} {$satuan}). Stok sekarang: {$stokAkhir} Pcs.");
    }

    // ============ KELOLA DATA PELANGGAN ============

    public function pelanggan()
    {
        $pelanggan = Pelanggan::orderBy('KodePlg')->get();
        return view('admin.pelanggan', compact('pelanggan'));
    }

    public function simpanPelanggan(Request $request)
    {
        $request->validate([
            'KodePlg' => 'required|unique:pelanggan,KodePlg',
            'NamaPlg' => 'required',
        ], [
            'KodePlg.required' => 'Kode Pelanggan wajib diisi!',
            'KodePlg.unique'   => 'Kode Pelanggan sudah terdaftar!',
            'NamaPlg.required' => 'Nama Pelanggan wajib diisi!',
        ]);

        Pelanggan::create([
            'KodePlg'     => strtoupper(trim($request->KodePlg)),
            'NamaPlg'     => $request->NamaPlg,
            'Alamat'      => $request->Alamat ?: '-',
            'TingkatHrg'  => $request->TingkatHrg ?: 1,
            'NamaSales'   => $request->NamaSales ?: '-',
            'Jadwal'      => $request->Jadwal ?: '-',
            'SaldoKredit' => $request->SaldoKredit ?: 0,
            'Poin'        => $request->Poin ?: 0,
        ]);

        return redirect('/admin/pelanggan')->with('sukses', 'Data pelanggan "' . $request->NamaPlg . '" berhasil ditambahkan!');
    }

    public function updatePelanggan(Request $request, $kode)
    {
        $pelanggan = Pelanggan::where('KodePlg', $kode)->firstOrFail();

        $request->validate([
            'NamaPlg' => 'required',
        ], [
            'NamaPlg.required' => 'Nama Pelanggan wajib diisi!',
        ]);

        $pelanggan->update([
            'NamaPlg'     => $request->NamaPlg,
            'Alamat'      => $request->Alamat ?: '-',
            'TingkatHrg'  => $request->TingkatHrg ?: 1,
            'NamaSales'   => $request->NamaSales ?: '-',
            'Jadwal'      => $request->Jadwal ?: '-',
            'SaldoKredit' => $request->SaldoKredit ?: 0,
            'Poin'        => $request->Poin ?: 0,
        ]);

        return redirect('/admin/pelanggan')->with('sukses', 'Data pelanggan "' . $request->NamaPlg . '" berhasil diperbarui!');
    }

    public function hapusPelanggan($kode)
    {
        if ($kode === 'P0001') {
            return redirect('/admin/pelanggan')->with('error', 'Pelanggan Utama "Customer umum" (P0001) tidak boleh dihapus!');
        }

        $pelanggan = Pelanggan::where('KodePlg', $kode)->firstOrFail();
        $nama = $pelanggan->NamaPlg;
        $pelanggan->delete();

        return redirect('/admin/pelanggan')->with('sukses', 'Pelanggan "' . $nama . '" berhasil dihapus!');
    }
}