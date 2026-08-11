@extends('layouts.admin')
@section('title', 'Riwayat Transaksi')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">Riwayat Transaksi</h2>
        <p class="page-head-sub">{{ $riwayat->count() }} transaksi ditemukan hari ini</p>
    </div>
</div>

{{-- SEARCH & FILTER BAR ROW --}}
<div class="filter-bar-row">
    <div class="admin-search-wrap" style="margin-bottom: 0;">
        <span class="search-icon">🔍</span>
        <input type="text" id="cariRiwayatAdmin" placeholder="Cari ID atau kasir..." oninput="filterRiwayatTable()">
    </div>

    <div class="filter-pills-group">
        <button type="button" class="filter-pill-btn active" onclick="filterStatus(this, '')">Semua</button>
        <button type="button" class="filter-pill-btn" onclick="filterStatus(this, 'Selesai')">Selesai</button>
    </div>
</div>

{{-- TRANSACTIONS TABLE --}}
<div class="table-container">
    <table class="table" id="tabelRiwayat">
        <thead>
            <tr>
                <th>ID TRANSAKSI</th>
                <th>WAKTU</th>
                <th>KASIR</th>
                <th>ITEM</th>
                <th>METODE</th>
                <th>TOTAL</th>
                <th>STATUS</th>
                <th style="text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($riwayat as $r)
                @php
                    $caraBayarLower = strtolower($r->cara_bayar);
                    $metodeBadgeClass = 'badge-gray';
                    if (str_contains($caraBayarLower, 'tunai')) $metodeBadgeClass = 'badge-green';
                    elseif (str_contains($caraBayarLower, 'qris')) $metodeBadgeClass = 'badge-blue';
                    elseif (str_contains($caraBayarLower, 'transfer')) $metodeBadgeClass = 'badge-purple';
                @endphp
                <tr class="riwayat-row" data-search="{{ strtolower($r->NoNota . ' ' . $r->Operator) }}" data-status="Selesai">
                    <td style="font-family: monospace; font-weight: 700; color: #0F172A;">{{ $r->NoNota }}</td>
                    <td style="color: #64748B;">{{ $r->jam }}</td>
                    <td style="font-weight: 600; color: #334155;">{{ $r->Operator }}</td>
                    <td>{{ $r->jumlah_barang }} item</td>
                    <td>
                        <span class="badge {{ $metodeBadgeClass }}">{{ $r->cara_bayar }}</span>
                    </td>
                    <td style="font-weight: 800; color: #0F172A;">Rp {{ number_format($r->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-green">Selesai</span>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="btn-icon-action edit" title="Lihat Detail Transaksi" onclick="alert('Detail Nota {{ $r->NoNota }} oleh {{ $r->Operator }}')">📄</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #94A3B8;">
                        <div style="font-size: 32px; margin-bottom: 8px;">📜</div>
                        <p style="font-size: 15px; font-weight: 600;">Belum ada riwayat transaksi hari ini.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
let activeStatusFilter = '';

function filterRiwayatTable() {
    const q = document.getElementById('cariRiwayatAdmin').value.toLowerCase();
    document.querySelectorAll('.riwayat-row').forEach(row => {
        const text = row.dataset.search;
        const status = row.dataset.status;
        const matchText = text.includes(q);
        const matchStatus = activeStatusFilter === '' || status === activeStatusFilter;
        row.style.display = (matchText && matchStatus) ? '' : 'none';
    });
}

function filterStatus(btn, status) {
    document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeStatusFilter = status;
    filterRiwayatTable();
}
</script>

@endsection