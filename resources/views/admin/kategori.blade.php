@extends('layouts.admin')
@section('title', 'Kategori Produk')

@section('content')
<div class="admin-card" style="max-width:600px;">
    <h3>Kategori yang Sedang Dipakai</h3>
    <p class="field-hint" style="margin-top:-8px;">
        Kategori diambil otomatis dari kolom Kategori di data Barang. Tambah kategori baru dengan mengisinya langsung saat Input Barang.
    </p>
    <div class="chip-list">
        @forelse ($kategoriList as $k)
            <span class="chip">{{ $k }}</span>
        @empty
            <p style="color:#98A2B3; font-size:13px;">Belum ada kategori.</p>
        @endforelse
    </div>
</div>
@endsection