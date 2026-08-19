@extends('layouts.manajer')
@section('title', 'Laporan Shift Kasir')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row" style="margin-bottom: 16px;">
    <div>
        <h2 class="page-head-title">⏰ Laporan Shift Kasir</h2>
        <p class="page-head-sub">Pantau pembukaan dan penutupan shift kasir beserta rekapitulasi kas masuk & selisih uang</p>
    </div>
    <a href="{{ url('/manajer/kasir-aktif') }}" class="btn-manajer-purple">
        <span>🟢</span> Lihat Kasir Aktif Sekarang
    </a>
</div>

{{-- FILTER TOOLBAR --}}
<form method="GET" action="{{ url('/manajer/shift') }}" class="manajer-filter-toolbar">
    <div style="display: flex; align-items: center; gap: 8px;">
        <label style="font-size: 13.5px; font-weight: 600; color: #475569;">📅 Tanggal:</label>
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="vb-inp" style="padding: 6px 10px; width: auto; font-size: 14px;">
    </div>

    <div style="display: flex; align-items: center; gap: 8px;">
        <label style="font-size: 13.5px; font-weight: 600; color: #475569;">👤 Kasir:</label>
        <select name="user_id" class="vb-inp" style="padding: 6px 10px; width: auto; font-size: 14px;">
            <option value="">-- Semua Kasir --</option>
            @foreach ($kasirList as $k)
                <option value="{{ $k->id }}" {{ request('user_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->name }} ({{ $k->username }})
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn-manajer-purple" style="padding: 6px 16px; font-size: 14px;">
        🔍 Filter
    </button>

    @if (request()->hasAny(['tanggal', 'user_id']))
        <a href="{{ url('/manajer/shift') }}" class="btn-action-sm">Reset Filter</a>
    @endif
</form>

{{-- TABEL LAPORAN SHIFT --}}
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th style="width: 50px;">#ID</th>
                <th>KASIR</th>
                <th>WAKTU SHIFT</th>
                <th style="text-align: right;">KAS AWAL</th>
                <th style="text-align: right;">KAS SISTEM</th>
                <th style="text-align: right;">KAS FISIK</th>
                <th style="text-align: center;">SELISIH KAS</th>
                <th style="text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shifts as $s)
                @php
                    $selisih = (float) $s->selisih;
                    $isSelesai = !is_null($s->selesai);
                @endphp
                <tr>
                    <td style="color: #94A3B8; font-family: monospace; font-size: 13px;">#{{ $s->id }}</td>
                    <td>
                        <div style="font-weight: 700; color: #0F172A;">👤 {{ $s->user->name ?? 'Kasir Dihapus' }}</div>
                        <div style="font-size: 12px; color: #64748B;">{{ $s->user->username ?? '-' }}</div>
                    </td>
                    <td>
                        <div style="font-size: 13.5px; font-weight: 600; color: #0F172A;">
                            Buka: {{ $s->mulai ? $s->mulai->translatedFormat('d M Y, H:i') : '-' }}
                        </div>
                        <div style="font-size: 12.5px; color: #64748B;">
                            Tutup: {{ $s->selesai ? $s->selesai->translatedFormat('d M Y, H:i') : 'Sedang Berjalan...' }}
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: 600;">
                        Rp {{ number_format($s->kas_awal, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; font-weight: 600; color: #2563EB;">
                        {{ $isSelesai ? 'Rp ' . number_format($s->kas_akhir_sistem, 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #0F172A;">
                        {{ $isSelesai ? 'Rp ' . number_format($s->kas_fisik, 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align: center;">
                        @if (!$isSelesai)
                            <span style="color: #94A3B8; font-size: 13px;">-</span>
                        @elseif ($selisih == 0)
                            <span style="background:#DCFCE7; color:#15803D; font-weight:700; font-size:12.5px; padding:3px 10px; border-radius:20px;">
                                ✓ Pas (Rp 0)
                            </span>
                        @elseif ($selisih > 0)
                            <span style="background:#DBEAFE; color:#1D4ED8; font-weight:700; font-size:12.5px; padding:3px 10px; border-radius:20px;">
                                + Lebih (Rp {{ number_format($selisih, 0, ',', '.') }})
                            </span>
                        @else
                            <span style="background:#FEE2E2; color:#B91C1C; font-weight:700; font-size:12.5px; padding:3px 10px; border-radius:20px;">
                                - Kurang (Rp {{ number_format(abs($selisih), 0, ',', '.') }})
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if ($isSelesai)
                            <span class="badge badge-gray">🏁 Selesai</span>
                        @else
                            <span class="badge badge-green">🟢 Sedang Aktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #94A3B8;">
                        Tidak ada riwayat shift yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div style="margin-top: 16px;">
    {{ $shifts->links() }}
</div>

@endsection
