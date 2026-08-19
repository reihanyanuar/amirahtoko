@extends('layouts.manajer')
@section('title', 'Dashboard & Statistik Toko')

@section('content')

{{-- 1. STATS METRIC CARDS --}}
<div class="manajer-stats-grid">
    {{-- Card 1: Omset Hari Ini --}}
    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-green">💰</div>
        <div class="stat-content">
            <div class="stat-val">Rp {{ number_format($totalOmsetHariIni, 0, ',', '.') }}</div>
            <div class="stat-lbl">Omset Hari Ini</div>
        </div>
    </div>

    {{-- Card 2: Transaksi Hari Ini --}}
    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-blue">🧾</div>
        <div class="stat-content">
            <div class="stat-val">{{ $totalTrxHariIni }}</div>
            <div class="stat-lbl">Transaksi Selesai</div>
        </div>
    </div>

    {{-- Card 3: Pcs Terjual --}}
    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-amber">📦</div>
        <div class="stat-content">
            <div class="stat-val">{{ $totalPcsHariIni }} Pcs</div>
            <div class="stat-lbl">Produk Terjual</div>
        </div>
    </div>

    {{-- Card 4: Kasir Aktif --}}
    <div class="manajer-stat-card">
        <div class="stat-icon-wrapper stat-icon-purple">🟢</div>
        <div class="stat-content">
            <div class="stat-val">{{ $kasirAktifCount }} Kasir</div>
            <div class="stat-lbl">Sedang Buka Shift</div>
        </div>
    </div>
</div>

{{-- 2. DASHBOARD MAIN GRID: GRAFIK (KIRI) & PRODUK TERLARIS (KANAN) --}}
<div class="dashboard-grid-2col">

    {{-- GRAFIK PENJUALAN 7 HARI --}}
    <div class="manajer-card">
        <div class="manajer-card-header">
            <div class="manajer-card-title">
                <span>📈</span> Tren Penjualan 7 Hari Terakhir
            </div>
            <span style="font-size: 13px; color: #64748B; font-weight: 600;">Omset Harian</span>
        </div>
        <div style="position: relative; height: 280px; width: 100%;">
            <canvas id="chartPenjualan"></canvas>
        </div>
    </div>
    
    {{-- PRODUK TERLARIS --}}
    <div class="manajer-card">
        <div class="manajer-card-header">
            <div class="manajer-card-title">
                <span>🏆</span> Top 5 Produk Terlaris
            </div>
            <span style="font-size: 12.5px; color: #64748B;">Bulan Ini</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            @forelse ($produkTerlaris as $idx => $p)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #F8FAFC; border-radius: 8px; border: 1px solid #F1F5F9;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="width: 24px; height: 24px; border-radius: 50%; background: {{ $idx == 0 ? '#FEF08A' : ($idx == 1 ? '#E2E8F0' : '#FFEDD5') }}; color: {{ $idx == 0 ? '#854D0E' : '#334155' }}; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                            {{ $idx + 1 }}
                        </span>
                        <div>
                            <div style="font-weight: 700; font-size: 14.5px; color: #0F172A;">{{ $p->NamaBrg }}</div>
                            <div style="font-size: 12.5px; color: #64748B;">Omset: Rp {{ number_format($p->total_omset, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <span style="font-weight: 800; font-size: 14.5px; color: #15803D; background: #DCFCE7; padding: 3px 10px; border-radius: 20px;">
                        {{ $p->total_qty }} Pcs
                    </span>
                </div>
            @empty
                <div style="text-align: center; padding: 30px; color: #94A3B8; font-size: 14px;">
                    Belum ada data penjualan bulan ini.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- 3. REKAP KINERJA KASIR HARI INI --}}
<div class="manajer-card">
    <div class="manajer-card-header">
        <div class="manajer-card-title">
            <span>👥</span> Kinerja Kasir Hari Ini
        </div>
        <a href="{{ url('/manajer/shift') }}" class="btn-action-sm">Lihat Detail Shift ➔</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>NAMA KASIR / OPERATOR</th>
                    <th style="text-align: center;">JUMLAH TRANSAKSI</th>
                    <th style="text-align: right;">TOTAL OMSET HARI INI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kasirHariIni as $k)
                    <tr>
                        <td style="font-weight: 700; color: #0F172A;">
                            👤 {{ $k->Operator }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-blue">{{ $k->total_trx }} Transaksi</span>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #15803D; font-size: 16px;">
                            Rp {{ number_format($k->total_omset, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 24px; color: #94A3B8;">
                            Belum ada transaksi kasir yang tercatat hari ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CHART JS SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartPenjualan').getContext('2d');
    
    const labels = {!! json_encode($chartLabels) !!};
    const dataOmset = {!! json_encode($chartOmset) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Omset (Rp)',
                data: dataOmset,
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#7C3AED',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Omset: Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + ' Jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000) + ' Rb';
                            }
                            return 'Rp ' + value;
                        },
                        font: { family: 'Inter', size: 12 }
                    },
                    grid: { color: '#F1F5F9' }
                },
                x: {
                    ticks: { font: { family: 'Inter', size: 12 } },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

@endsection
