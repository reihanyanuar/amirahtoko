@extends('layouts.manajer')
@section('title', 'Kasir Aktif Sekarang')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row" style="margin-bottom: 16px;">
    <div>
        <h2 class="page-head-title">🟢 Kasir Aktif Sekarang</h2>
        <p class="page-head-sub">Pantau kasir yang sedang membuka shift secara real-time beserta omset shift berjalan</p>
    </div>
    <a href="{{ url('/manajer/shift') }}" class="btn-action-sm">
        <span>📜</span> Lihat Riwayat Semua Shift
    </a>
</div>

{{-- AKTIF CASHIERS CARDS GRID --}}
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
    @forelse ($activeShifts as $s)
        @php
            $mulai = $s->mulai;
            $durasiMenit = $mulai ? $mulai->diffInMinutes(now()) : 0;
            $jam = floor($durasiMenit / 60);
            $menit = $durasiMenit % 60;
            $durasiStr = $jam > 0 ? "{$jam} jam {$menit} mnt" : "{$menit} menit";
        @endphp
        <div class="manajer-card" style="border-top: 4px solid #16A34A; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="user-avatar" style="width: 44px; height: 44px; font-size: 16px; background: linear-gradient(135deg, #16A34A, #4ADE80);">
                        {{ strtoupper(substr($s->user->name ?? 'KS', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 800; color: #0F172A;">{{ $s->user->name ?? 'Kasir' }}</div>
                        <div style="font-size: 13px; color: #64748B;">@ {{ $s->user->username ?? '-' }}</div>
                    </div>
                </div>
                <span class="badge badge-green">● Shift Aktif</span>
            </div>

            <div style="background: #F8FAFC; border-radius: 8px; padding: 12px; margin-bottom: 14px; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: #475569;">
                    <span>Waktu Mulai Shift:</span>
                    <strong>{{ $mulai ? $mulai->format('H:i') : '-' }} WIB</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: #475569;">
                    <span>Durasi Shift:</span>
                    <strong style="color: #2563EB;">{{ $durasiStr }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; color: #475569;">
                    <span>Modal / Kas Awal:</span>
                    <strong>Rp {{ number_format($s->kas_awal, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; border-top: 1px solid #F1F5F9; padding-top: 12px;">
                <div style="text-align: center; padding: 8px; background: #EFF6FF; border-radius: 8px;">
                    <div style="font-size: 12px; color: #1D4ED8; font-weight: 600;">Transaksi Shift Ini</div>
                    <div style="font-size: 18px; font-weight: 800; color: #1E40AF;">{{ $s->trx_count }}</div>
                </div>
                <div style="text-align: center; padding: 8px; background: #ECFDF5; border-radius: 8px;">
                    <div style="font-size: 12px; color: #15803D; font-weight: 600;">Omset Shift Ini</div>
                    <div style="font-size: 18px; font-weight: 800; color: #065F46;">Rp {{ number_format($s->omset, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; background: #FFFFFF; border-radius: 12px; border: 1.5px solid #E2E8F0; padding: 60px 20px; text-align: center; color: #94A3B8;">
            <div style="font-size: 40px; margin-bottom: 10px;">😴</div>
            <div style="font-size: 17px; font-weight: 700; color: #334155;">Tidak Ada Kasir yang Sedang Membuka Shift</div>
            <div style="font-size: 14px; margin-top: 4px; color: #64748B;">Semua kasir sedang offline atau telah menutup shift penjualan.</div>
        </div>
    @endforelse
</div>

@endsection
