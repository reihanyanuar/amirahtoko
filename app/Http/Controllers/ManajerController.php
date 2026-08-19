<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Penjualan;
use App\Models\Shift;
use App\Models\Barang;
use Carbon\Carbon;

class ManajerController extends Controller
{
    // ============================================================
    // 1. DASHBOARD & STATISTIK PENJUALAN
    // ============================================================
    public function dashboard()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Metrik Penjualan Hari Ini
        $totalOmsetHariIni = Penjualan::whereDate('Tanggal', $today)->sum('TotalHarga');
        $totalTrxHariIni   = Penjualan::whereDate('Tanggal', $today)->distinct('NoNota')->count();
        $totalPcsHariIni   = Penjualan::whereDate('Tanggal', $today)->sum('Qty');
        $kasirAktifCount   = Shift::whereNull('selesai')
            ->whereHas('user', fn($q) => $q->where('role', 'kasir'))
            ->count();

        // Grafik Penjualan 7 Hari Terakhir
        $tujuhHariLalu = Carbon::today()->subDays(6)->format('Y-m-d');
        $dataGrafikRaw = Penjualan::whereDate('Tanggal', '>=', $tujuhHariLalu)
            ->select(
                'Tanggal',
                DB::raw('SUM(TotalHarga) as omset'),
                DB::raw('COUNT(DISTINCT NoNota) as total_trx')
            )
            ->groupBy('Tanggal')
            ->orderBy('Tanggal')
            ->get()
            ->keyBy(function($item) {
                return Carbon::parse($item->Tanggal)->format('Y-m-d');
            });

        $chartLabels = [];
        $chartOmset  = [];
        $chartTrx    = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = Carbon::today()->subDays($i);
            $tglStr = $tgl->format('Y-m-d');
            $chartLabels[] = $tgl->translatedFormat('D, d M');

            if (isset($dataGrafikRaw[$tglStr])) {
                $chartOmset[] = (int) $dataGrafikRaw[$tglStr]->omset;
                $chartTrx[]   = (int) $dataGrafikRaw[$tglStr]->total_trx;
            } else {
                $chartOmset[] = 0;
                $chartTrx[]   = 0;
            }
        }

        // Top 5 Produk Terlaris (Bulan Ini)
        $awalBulan = Carbon::now()->startOfMonth()->format('Y-m-d');
        $produkTerlaris = Penjualan::whereDate('Tanggal', '>=', $awalBulan)
            ->select(
                'NamaBrg',
                DB::raw('SUM(Qty) as total_qty'),
                DB::raw('SUM(TotalHarga) as total_omset')
            )
            ->groupBy('NamaBrg')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Ringkasan Kinerja Kasir Hari Ini
        $kasirHariIni = Penjualan::whereDate('Tanggal', $today)
            ->select(
                'Operator',
                DB::raw('COUNT(DISTINCT NoNota) as total_trx'),
                DB::raw('SUM(TotalHarga) as total_omset')
            )
            ->groupBy('Operator')
            ->orderByDesc('total_omset')
            ->get();

        return view('manajer.dashboard', compact(
            'totalOmsetHariIni',
            'totalTrxHariIni',
            'totalPcsHariIni',
            'kasirAktifCount',
            'chartLabels',
            'chartOmset',
            'chartTrx',
            'produkTerlaris',
            'kasirHariIni'
        ));
    }

    // ============================================================
    // 2. KELOLA AKUN (USERS)
    // ============================================================
    public function akun()
    {
        $users = User::orderBy('id')->get();
        return view('manajer.akun', compact('users'));
    }

    public function simpanAkun(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:kasir,admin,manajer',
        ], [
            'username.unique'   => 'Username sudah digunakan, pilih username lain!',
            'password.min'      => 'Password minimal harus 6 karakter!',
            'role.in'           => 'Pilih role yang valid (Kasir, Admin, atau Manajer)!',
        ]);

        User::create([
            'name'      => $request->name,
            'username'  => strtolower(trim($request->username)),
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => true,
        ]);

        return redirect('/manajer/akun')->with('sukses', "Akun \"{$request->name}\" ({$request->role}) berhasil dibuat!");
    }

    public function updateAkun(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'role'     => 'required|in:kasir,admin,manajer',
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => strtolower(trim($request->username)),
            'role'     => $request->role,
        ]);

        return redirect('/manajer/akun')->with('sukses', "Data akun \"{$user->name}\" berhasil diperbarui!");
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.min'       => 'Password baru minimal 6 karakter!',
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/manajer/akun')->with('sukses', "Password untuk akun \"{$user->name}\" berhasil direset!");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Cegah manajer menonaktifkan akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect('/manajer/akun')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang digunakan!');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
        return redirect('/manajer/akun')->with('sukses', "Akun \"{$user->name}\" berhasil {$statusText}.");
    }

    // ============================================================
    // 3. LAPORAN SHIFT
    // ============================================================
    public function laporanShift(Request $request)
    {
        $query = Shift::with('user')->orderByDesc('id');

        if ($request->filled('tanggal')) {
            $query->whereDate('mulai', $request->tanggal);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $shifts = $query->paginate(15)->withQueryString();
        $kasirList = User::where('role', 'kasir')->get();

        return view('manajer.shift', compact('shifts', 'kasirList'));
    }

    // ============================================================
    // 4. LAPORAN TRANSAKSI MENYELURUH
    // ============================================================
    public function laporanTransaksi(Request $request)
    {
        $tglMulai = $request->tgl_mulai ?: Carbon::today()->format('Y-m-d');
        $tglSelesai = $request->tgl_selesai ?: Carbon::today()->format('Y-m-d');

        $query = Penjualan::whereBetween('Tanggal', [$tglMulai, $tglSelesai]);

        if ($request->filled('operator')) {
            $query->where('Operator', $request->operator);
        }

        if ($request->filled('cara_bayar')) {
            $query->where('CaraBayar', $request->cara_bayar);
        }

        // Summary metrics
        $totalOmset = (clone $query)->sum('TotalHarga');
        $totalTrx   = (clone $query)->distinct('NoNota')->count('NoNota');
        $totalItem  = (clone $query)->sum('Qty');

        // Daftar transaksi per nota
        $transaksi = $query->select(
                'NoNota',
                'Tanggal',
                'Operator',
                'CaraBayar',
                DB::raw('MIN(Jam) as jam'),
                DB::raw('SUM(TotalHarga) as total'),
                DB::raw('SUM(Qty) as jumlah_item'),
                DB::raw('COUNT(*) as total_baris')
            )
            ->groupBy('NoNota', 'Tanggal', 'Operator', 'CaraBayar')
            ->orderByDesc('Tanggal')
            ->orderByDesc('jam')
            ->paginate(20)
            ->withQueryString();

        $operatorList = Penjualan::select('Operator')->distinct()->whereNotNull('Operator')->pluck('Operator');

        return view('manajer.laporan-transaksi', compact(
            'transaksi',
            'tglMulai',
            'tglSelesai',
            'totalOmset',
            'totalTrx',
            'totalItem',
            'operatorList'
        ));
    }

    // ============================================================
    // 5. KASIR AKTIF SEKARANG
    // ============================================================
    public function kasirAktif()
    {
        // Hanya tampilkan shift dari user dengan role 'kasir' saja
        $activeShifts = Shift::with('user')
            ->whereNull('selesai')
            ->whereHas('user', function($q) {
                $q->where('role', 'kasir');
            })
            ->orderBy('mulai', 'desc')
            ->get();

        // Hitung total transaksi per kasir di shift saat ini
        // PENTING: Operator di tabel penjualan diisi dengan USERNAME (bukan nama lengkap)
        $activeShifts->transform(function($shift) {
            $shiftMulai   = $shift->mulai;
            $usernameKasir = $shift->user->username ?? '';

            $transaksi = Penjualan::where('Operator', $usernameKasir)
                ->whereDate('Tanggal', Carbon::parse($shiftMulai)->format('Y-m-d'))
                ->where('Jam', '>=', Carbon::parse($shiftMulai)->format('H:i:s'))
                ->select(
                    DB::raw('COUNT(DISTINCT NoNota) as total_trx'),
                    DB::raw('SUM(TotalHarga) as total_omset')
                )
                ->first();

            $shift->trx_count = $transaksi->total_trx ?? 0;
            $shift->omset     = $transaksi->total_omset ?? 0;
            return $shift;
        });

        return view('manajer.kasir-aktif', compact('activeShifts'));
    }
}
