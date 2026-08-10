@extends('layouts.admin')
@section('title', 'Kategori Produk')

@section('content')

{{-- PAGE HEADER WITH ACTION BUTTON --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">Kategori Produk</h2>
        <p class="page-head-sub">{{ $kategoriList->count() }} kategori terdaftar</p>
    </div>
    <a href="{{ url('/admin/barang') }}" class="btn-add-blue">
        <span>+</span> Tambah Kategori
    </a>
</div>

{{-- CATEGORY CARDS GRID (FIGMA MATCHING UI) --}}
<div class="category-card-grid">
    @forelse ($kategoriList as $k)
        @php
            $katName = $k->Jenis;
            $katLower = strtolower($katName);
            $icon = '🏷️';
            $bgColor = '#EFF6FF';
            $iconColor = '#2563EB';

            if (str_contains($katLower, 'minum')) {
                $icon = '🥤';
                $bgColor = '#E0F2FE';
                $iconColor = '#0369A1';
            } elseif (str_contains($katLower, 'makan')) {
                $icon = '🍱';
                $bgColor = '#FFEDD5';
                $iconColor = '#C2410C';
            } elseif (str_contains($katLower, 'snack')) {
                $icon = '🍿';
                $bgColor = '#FCE7F3';
                $iconColor = '#BE185D';
            } elseif (str_contains($katLower, 'sembako') || str_contains($katLower, 'beras') || str_contains($katLower, 'gula')) {
                $icon = '🌾';
                $bgColor = '#FEF3C7';
                $iconColor = '#B45309';
            } elseif (str_contains($katLower, 'care') || str_contains($katLower, 'sabun')) {
                $icon = '🧴';
                $bgColor = '#E6F4EA';
                $iconColor = '#137333';
            } elseif (str_contains($katLower, 'rokok')) {
                $icon = '🚬';
                $bgColor = '#F1F5F9';
                $iconColor = '#475569';
            }
        @endphp

        <div class="category-item-card">
            <div class="cat-card-left">
                <div class="cat-card-icon" style="background: {{ $bgColor }}; color: {{ $iconColor }};">
                    {{ $icon }}
                </div>
                <div>
                    <div class="cat-card-name">{{ $katName }}</div>
                    <div class="cat-card-count">{{ $k->total_produk }} produk</div>
                </div>
            </div>
            <div>
                <a href="{{ url('/admin/barang?kategori=' . urlencode($katName)) }}" class="btn-icon-action edit" title="Lihat Produk">🔍</a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; background: #FFFFFF; border-radius: 14px; border: 1.5px solid #E2E8F0; padding: 40px; text-align: center; color: #94A3B8;">
            <div style="font-size: 32px; margin-bottom: 8px;">🏷️</div>
            <p style="font-size: 15px; font-weight: 600;">Belum ada kategori terdaftar.</p>
            <p style="font-size: 13.5px; margin-top: 4px;">Kategori otomatis ditambahkan saat Anda menginput produk baru.</p>
        </div>
    @endforelse
</div>

@endsection