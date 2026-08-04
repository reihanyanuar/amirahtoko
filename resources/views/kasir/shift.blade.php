@extends('layouts.kasir')
@section('title', 'Shift')

@section('content')

@if (session('shift_selesai'))
    @php $s = session('shift_selesai'); @endphp
    <div class="shift-alert success">
        <strong>Shift berhasil ditutup!</strong>
        <div class="shift-alert-detail">
            Kas Akhir Sistem: Rp {{ number_format($s->kas_akhir_sistem, 0, ',', '.') }}<br>
            Kas Fisik: Rp {{ number_format($s->kas_fisik, 0, ',', '.') }}<br>
            Selisih:
            <strong class="{{ $s->selisih < 0 ? 'text-danger' : 'text-success' }}">
                Rp {{ number_format($s->selisih, 0, ',', '.') }}
            </strong>
        </div>
    </div>
@endif

@if (session('perlu_shift'))
    <div class="shift-alert warning">
        ⚠ Kamu harus <strong>Buka Shift</strong> dulu sebelum bisa melakukan transaksi Penjualan.
    </div>
@endif

<div class="shift-card">
    @if ($shiftAktif)
        <h3>Shift Sedang Berjalan</h3>
        <p class="desc">
            Mulai {{ $shiftAktif->mulai->format('H:i') }}, kas awal Rp {{ number_format($shiftAktif->kas_awal, 0, ',', '.') }}
        </p>
        <p class="pendapatan">
            Pendapatan sejauh ini: Rp {{ number_format($shiftAktif->pendapatan, 0, ',', '.') }}
        </p>
        <form method="POST" action="{{ url('/kasir/shift/tutup') }}">
            @csrf
            <label>Jumlah Uang Fisik Dihitung</label>
            <input type="number" name="kas_fisik" required>
            <button type="submit" class="btn-tutup-shift">Tutup Shift</button>
        </form>
    @else
        <h3>Buka Shift Baru</h3>
        <form method="POST" action="{{ url('/kasir/shift/buka') }}">
            @csrf
            <label>Kas Awal</label>
            <input type="number" name="kas_awal" required>
            <button type="submit" class="btn-buka-shift">Buka Shift</button>
        </form>
    @endif
</div>

@if ($riwayatShift->count() > 0)
    <h3 class="riwayat-title">Riwayat Shift Hari Ini</h3>

    @foreach ($riwayatShift as $r)
        <div class="riwayat-card">
            <div class="riwayat-head">
                <strong>{{ $r->mulai->format('H:i') }} — {{ $r->selesai->format('H:i') }}</strong>
                <span class="riwayat-durasi">Durasi {{ $r->mulai->diffForHumans($r->selesai, true) }}</span>
            </div>
            <div class="riwayat-grid">
                <div>
                    <div class="label">Kas Awal</div>
                    <strong>Rp {{ number_format($r->kas_awal, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <div class="label">Pendapatan</div>
                    <strong class="text-success">Rp {{ number_format($r->kas_akhir_sistem - $r->kas_awal, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <div class="label">Kas Akhir</div>
                    <strong>Rp {{ number_format($r->kas_fisik, 0, ',', '.') }}</strong>
                </div>
                <div>
                    <div class="label">Selisih</div>
                    <strong class="{{ $r->selisih < 0 ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($r->selisih, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>
    @endforeach
@endif

@endsection