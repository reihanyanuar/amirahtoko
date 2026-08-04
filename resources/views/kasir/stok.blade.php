@extends('layouts.kasir')
@section('title', 'Stok Barang')

@section('content')

@php
    $totalProduk  = $barang->count();
    $stokMenipis  = $barang->filter(fn($b) => $b->JmlStock > 0 && $b->JmlStock <= ($b->MinStock ?? 10))->count();
    $stokHabis    = $barang->filter(fn($b) => $b->JmlStock <= 0)->count();
    $stokAman     = $totalProduk - $stokMenipis - $stokHabis;
@endphp

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon blue">📦</div>
        <div>
            <div class="stat-number">{{ $totalProduk }}</div>
            <div class="stat-label">Total Produk</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow">⚠️</div>
        <div>
            <div class="stat-number" style="color:#D97706">{{ $stokMenipis }}</div>
            <div class="stat-label">Stok Menipis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">❌</div>
        <div>
            <div class="stat-number" style="color:#DC2626">{{ $stokHabis }}</div>
            <div class="stat-label">Habis</div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="table-toolbar">
    <div class="search-bar" style="margin-bottom:0">
        <span class="search-icon">🔍</span>
        <input type="text" id="cariStok" placeholder="Cari produk atau barcode...">
    </div>
    <div class="filter-tabs">
        <button class="filter-tab active"      onclick="filterStok(this,'semua')">Semua</button>
        <button class="filter-tab active-warn" onclick="filterStok(this,'menipis')">⚠ Menipis</button>
        <button class="filter-tab active-ok"   onclick="filterStok(this,'aman')">✓ Aman</button>
    </div>
</div>

{{-- Table --}}
<div class="table-wrapper">
    <table class="table" id="stokTable">
        <thead>
            <tr>
                <th>BARCODE</th>
                <th>NAMA PRODUK</th>
                <th>KATEGORI</th>
                <th>STOK</th>
                <th>MIN STOK</th>
                <th>HARGA JUAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barang as $item)
                @php
                    $minStok = $item->MinStock ?? 10;
                    $isHabis  = $item->JmlStock <= 0;
                    $isMenipis= $item->JmlStock > 0 && $item->JmlStock <= $minStok;
                    $rowClass = $isHabis ? 'row-critical' : ($isMenipis ? 'row-low' : '');
                    $status   = $isHabis ? 'habis' : ($isMenipis ? 'menipis' : 'aman');

                    $katColors = [
                        'Minuman'        => 'badge-blue',
                        'Makanan'        => 'badge-orange',
                        'Snack'          => 'badge-purple',
                        'Rokok'          => 'badge-gray',
                        'Personal Care'  => 'badge-green',
                        'Kebutuhan Rumah'=> 'badge-yellow',
                    ];
                    $katClass = $katColors[$item->Jenis] ?? 'badge-gray';
                @endphp
                <tr class="{{ $rowClass }}" data-status="{{ $status }}" data-nama="{{ strtolower($item->NamaBrg) }}" data-kode="{{ strtolower($item->KodeBrg) }}">
                    <td style="font-family:'Courier New',monospace; font-size:12px; color:#64748B">{{ $item->KodeBrg }}</td>
                    <td>
                        <span style="font-weight:600; color:#0F172A">{{ $item->NamaBrg }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $katClass }}">{{ $item->Jenis ?? '-' }}</span>
                    </td>
                    <td>
                        <div class="stok-bar-wrap">
                            <span class="stok-val" style="color:{{ $isHabis ? '#DC2626' : ($isMenipis ? '#D97706' : '#0F172A') }}">
                                {{ $item->JmlStock }}
                            </span>
                            <span style="color:#94A3B8; font-size:12px">{{ $item->SatKcl }}</span>
                            @if($isHabis)
                                <span class="stok-warn-icon">🔴</span>
                            @elseif($isMenipis)
                                <span class="stok-warn-icon">⚠️</span>
                            @endif
                        </div>
                    </td>
                    <td style="color:#94A3B8; font-size:13px">{{ $minStok }} {{ $item->SatKcl }}</td>
                    <td style="font-weight:600; color:#16A34A">Rp {{ number_format($item->Harga1, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="table-empty">📦 Belum ada data barang.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
/* ---- Filter Tab ---- */
function filterStok(btn, status) {
    document.querySelectorAll('.filter-tab').forEach(b => {
        b.classList.remove('active');
        // reset ke default style (non-active)
    });
    btn.classList.add('active');

    document.querySelectorAll('#stokTable tbody tr[data-status]').forEach(row => {
        if (status === 'semua') {
            row.style.display = '';
        } else {
            row.style.display = row.dataset.status === status ? '' : 'none';
        }
    });
}

/* ---- Search ---- */
document.getElementById('cariStok').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#stokTable tbody tr[data-status]').forEach(row => {
        const nama = row.dataset.nama || '';
        const kode = row.dataset.kode || '';
        row.style.display = (nama.includes(q) || kode.includes(q)) ? '' : 'none';
    });
    // Reset filter tabs
    if (q) {
        document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    }
});
</script>
@endpush