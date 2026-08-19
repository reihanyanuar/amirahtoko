@extends('layouts.manajer')
@section('title', 'Laporan Transaksi Penjualan')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row" style="margin-bottom: 16px;">
    <div>
        <h2 class="page-head-title">📜 Laporan Transaksi Penjualan</h2>
        <p class="page-head-sub">Rekapitulasi seluruh riwayat transaksi kasir dengan filter tanggal fleksibel & metode pembayaran</p>
    </div>
</div>

{{-- FILTER TOOLBAR --}}
<form method="GET" action="{{ url('/manajer/laporan-transaksi') }}" class="manajer-filter-toolbar">
    <div style="display: flex; align-items: center; gap: 8px;">
        <label style="font-size: 13.5px; font-weight: 600; color: #475569;">📅 Dari:</label>
        <input type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="vb-inp" style="padding: 6px 10px; width: auto; font-size: 14px;">
    </div>

    <div style="display: flex; align-items: center; gap: 8px;">
        <label style="font-size: 13.5px; font-weight: 600; color: #475569;">Sampai:</label>
        <input type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="vb-inp" style="padding: 6px 10px; width: auto; font-size: 14px;">
    </div>

    <div style="display: flex; align-items: center; gap: 8px;">
        <label style="font-size: 13.5px; font-weight: 600; color: #475569;">👤 Kasir:</label>
        <select name="operator" class="vb-inp" style="padding: 6px 10px; width: auto; font-size: 14px;">
            <option value="">-- Semua Kasir --</option>
            @foreach ($operatorList as $op)
                <option value="{{ $op }}" {{ request('operator') == $op ? 'selected' : '' }}>{{ $op }}</option>
            @endforeach
        </select>
    </div>

    <div style="display: flex; align-items: center; gap: 8px;">
        <label style="font-size: 13.5px; font-weight: 600; color: #475569;">💳 Pembayaran:</label>
        <select name="cara_bayar" class="vb-inp" style="padding: 6px 10px; width: auto; font-size: 14px;">
            <option value="">-- Semua Metode --</option>
            <option value="Tunai" {{ request('cara_bayar') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="Transfer" {{ request('cara_bayar') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="QRIS" {{ request('cara_bayar') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
            <option value="Kredit" {{ request('cara_bayar') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
        </select>
    </div>

    <button type="submit" class="btn-manajer-purple" style="padding: 6px 16px; font-size: 14px;">
        🔍 Filter Laporan
    </button>
</form>

{{-- SUMMARY METRICS FOR SELECTED FILTER --}}
<div class="manajer-stats-grid">
    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-green">💰</div>
        <div class="stat-content">
            <div class="stat-val">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            <div class="stat-lbl">Total Omset Terfilter</div>
        </div>
    </div>

    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-blue">🧾</div>
        <div class="stat-content">
            <div class="stat-val">{{ number_format($totalTrx, 0, ',', '.') }}</div>
            <div class="stat-lbl">Total Transaksi</div>
        </div>
    </div>

    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-amber">📦</div>
        <div class="stat-content">
            <div class="stat-val">{{ number_format($totalItem, 0, ',', '.') }} Pcs</div>
            <div class="stat-lbl">Total Produk Terjual</div>
        </div>
    </div>
</div>

{{-- TABEL LAPORAN TRANSAKSI --}}
<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>NO. NOTA</th>
                <th>TANGGAL & WAKTU</th>
                <th>KASIR / OPERATOR</th>
                <th>METODE BAYAR</th>
                <th style="text-align: center;">TOTAL ITEM</th>
                <th style="text-align: right;">TOTAL TRANSAKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $t)
                <tr>
                    <td style="font-family: monospace; font-weight: 700; color: #0F172A; font-size: 14.5px;">
                        {{ $t->NoNota }}
                    </td>
                    <td style="font-size: 13.5px; color: #475569;">
                        <div>{{ date('d/m/Y', strtotime($t->Tanggal)) }}</div>
                        <div style="font-size: 12px; color: #94A3B8;">{{ $t->jam }} WIB</div>
                    </td>
                    <td style="font-weight: 600; color: #1E293B;">
                        👤 {{ $t->Operator ?: 'Umum' }}
                    </td>
                    <td>
                        <span class="badge {{ $t->CaraBayar === 'Tunai' ? 'badge-green' : ($t->CaraBayar === 'QRIS' ? 'badge-purple' : 'badge-blue') }}">
                            {{ $t->CaraBayar ?: 'Tunai' }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: 700; color: #0F172A;">{{ $t->jumlah_item }}</span> 
                        <span style="font-size: 12.5px; color: #64748B;">({{ $t->total_baris }} jenis)</span>
                    </td>
                    <td style="text-align: right; font-weight: 800; color: #15803D; font-size: 16px;">
                        Rp {{ number_format($t->total, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #94A3B8;">
                        Tidak ada data transaksi pada rentang tanggal yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div style="margin-top: 16px;">
    {{ $transaksi->links() }}
</div>

@endsection
