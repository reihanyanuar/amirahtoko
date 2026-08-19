@extends('layouts.admin')
@section('title', 'Stok Barang')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">📦 Stok Barang</h2>
        <p class="page-head-sub">{{ $barang->count() }} produk terdaftar</p>
    </div>
    <a href="{{ url('/admin/barang/tambah') }}" class="btn-add-green">
        <span>+</span> Tambah Produk
    </a>
</div>

{{-- SEARCH BAR & FILTER STOK --}}
<div style="display: flex; gap: 16px; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap;">
    <div class="admin-search-wrap" style="flex: 1; min-width: 260px; margin-bottom: 0;">
        <span class="search-icon">🔍</span>
        <input type="text" id="cariBarangAdmin" placeholder="Cari nama atau barcode..." oninput="filterBarangTable()">
    </div>

    {{-- FILTER STOK CHIPS --}}
    <div style="display: flex; gap: 8px;">
        <button type="button" class="filter-pill-btn active" onclick="filterStokAdmin(this, 'semua')">Semua Stok</button>
        <button type="button" class="filter-pill-btn" onclick="filterStokAdmin(this, 'menipis')">⚠️ Stok Menipis (≤ 10)</button>
        <button type="button" class="filter-pill-btn" onclick="filterStokAdmin(this, 'aman')">✓ Stok Aman (> 10)</button>
    </div>
</div>

{{-- DATA TABLE --}}
<div class="table-container">
    <table class="table" id="tabelBarang">
        <thead>
            <tr>
                <th>BARCODE</th>
                <th>NAMA PRODUK</th>
                <th>KATEGORI</th>
                <th>HARGA JUAL</th>
                <th>HARGA MODAL</th>
                <th>STOK</th>
                <th style="text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $b)
                @php
                    $katLower = strtolower($b->Jenis);
                    $badgeClass = 'badge-kat-default';
                    if (str_contains($katLower, 'minum')) $badgeClass = 'badge-kat-minuman';
                    elseif (str_contains($katLower, 'makan')) $badgeClass = 'badge-kat-makanan';
                    elseif (str_contains($katLower, 'snack')) $badgeClass = 'badge-kat-snack';
                    elseif (str_contains($katLower, 'sembako') || str_contains($katLower, 'beras') || str_contains($katLower, 'gula')) $badgeClass = 'badge-kat-sembako';
                    elseif (str_contains($katLower, 'care') || str_contains($katLower, 'sabun')) $badgeClass = 'badge-kat-personal-care';
                    elseif (str_contains($katLower, 'rokok')) $badgeClass = 'badge-kat-rokok';

                    $statusStok = $b->JmlStock <= 10 ? 'menipis' : 'aman';
                @endphp
                <tr class="barang-row"
                    data-search="{{ strtolower($b->KodeBrg . ' ' . $b->NamaBrg . ' ' . $b->Jenis) }}"
                    data-status="{{ $statusStok }}">
                    <td style="font-family: monospace; font-size: 14px; color: #64748B;">{{ $b->KodeBrg }}</td>
                    <td style="font-weight: 700; color: #0F172A;">{{ $b->NamaBrg }}</td>
                    <td>
                        <span class="badge-kat {{ $badgeClass }}">{{ $b->Jenis ?: 'Umum' }}</span>
                    </td>
                    <td style="font-weight: 700;">Rp {{ number_format($b->Harga1, 0, ',', '.') }}</td>
                    <td style="color: #64748B;">Rp {{ number_format($b->Hpp, 0, ',', '.') }}</td>
                    <td>
                        <strong style="color: {{ $b->JmlStock <= 10 ? '#DC2626' : '#16A34A' }};">
                            {{ $b->JmlStock }}
                        </strong> 
                        <span style="color: #64748B; font-size: 14px;">{{ $b->SatKcl }}</span>
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ url('/admin/barang/edit/' . $b->KodeBrg) }}" class="btn-icon-action edit" title="Edit Barang">✏️ Edit</a>
                        <form method="POST" action="{{ url('/admin/barang/hapus/' . $b->KodeBrg) }}" style="display:inline" onsubmit="return confirm('Yakin hapus {{ addslashes($b->NamaBrg) }}?')">
                            @csrf
                            <button type="submit" class="btn-icon-action delete" title="Hapus Barang">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
let currentStokFilter = 'semua';

function filterStokAdmin(btn, status) {
    currentStokFilter = status;
    
    // Switch active class on filter pill buttons
    document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    tampilkanDataFiltered();
}

function filterBarangTable() {
    tampilkanDataFiltered();
}

function tampilkanDataFiltered() {
    const q = document.getElementById('cariBarangAdmin').value.toLowerCase();
    
    document.querySelectorAll('.barang-row').forEach(row => {
        const text = row.dataset.search || '';
        const status = row.dataset.status || '';

        const cocokSearch = text.includes(q);
        const cocokStok   = (currentStokFilter === 'semua') || (status === currentStokFilter);

        row.style.display = (cocokSearch && cocokStok) ? '' : 'none';
    });
}
</script>

@endsection