@extends('layouts.admin')
@section('title', 'Data Barang')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">Data Barang</h2>
        <p class="page-head-sub">{{ $barang->count() }} produk terdaftar</p>
    </div>
    <a href="{{ url('/admin/barang/tambah') }}" class="btn-add-green">
        <span>+</span> Tambah Produk
    </a>
</div>

{{-- SEARCH BAR --}}
<div class="admin-search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" id="cariBarangAdmin" placeholder="Cari nama atau barcode..." oninput="filterBarangTable()">
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
                @endphp
                <tr class="barang-row" data-search="{{ strtolower($b->KodeBrg . ' ' . $b->NamaBrg . ' ' . $b->Jenis) }}">
                    <td style="font-family: monospace; font-size: 14px; color: #64748B;">{{ $b->KodeBrg }}</td>
                    <td style="font-weight: 700; color: #0F172A;">{{ $b->NamaBrg }}</td>
                    <td>
                        <span class="badge-kat {{ $badgeClass }}">{{ $b->Jenis ?: 'Umum' }}</span>
                    </td>
                    <td style="font-weight: 700;">Rp {{ number_format($b->Harga1, 0, ',', '.') }}</td>
                    <td style="color: #64748B;">Rp {{ number_format($b->Hpp, 0, ',', '.') }}</td>
                    <td>
                        <strong style="color: {{ $b->JmlStock <= 5 ? '#DC2626' : '#16A34A' }};">
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
function filterBarangTable() {
    const q = document.getElementById('cariBarangAdmin').value.toLowerCase();
    document.querySelectorAll('.barang-row').forEach(row => {
        const text = row.dataset.search;
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>

@endsection