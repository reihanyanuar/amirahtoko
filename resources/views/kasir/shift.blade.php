@extends('layouts.kasir')
@section('title', 'Shift')

@section('content')
@if (session('shift_selesai'))
    @php $s = session('shift_selesai'); @endphp
    <div style="background:#ECFDF3; border:1px solid #16A34A; border-radius:10px; padding:16px; margin-bottom:16px; max-width:480px;">
        <strong>Shift berhasil ditutup!</strong>
        <div style="margin-top:8px; font-size:13px; color:#334155;">
            Kas Akhir Sistem: Rp {{ number_format($s->kas_akhir_sistem, 0, ',', '.') }}<br>
            Kas Fisik: Rp {{ number_format($s->kas_fisik, 0, ',', '.') }}<br>
            Selisih:
            <strong style="color: {{ $s->selisih < 0 ? '#DC2626' : '#16A34A' }}">
                Rp {{ number_format($s->selisih, 0, ',', '.') }}
            </strong>
        </div>
    </div>
@endif
<div class="card" style="max-width:480px; background:#fff; border-radius:12px; border:1px solid #E1E6EE; padding:22px;">

    @if ($shiftAktif)
        <h3>Shift Sedang Berjalan</h3>
        <p style="color:#667085; font-size:13px; margin-bottom:16px;">
            Mulai {{ $shiftAktif->mulai->format('H:i') }}, kas awal Rp {{ number_format($shiftAktif->kas_awal, 0, ',', '.') }}
        </p>
        <form method="POST" action="{{ url('/kasir/shift/tutup') }}">
            @csrf
            <label>Jumlah Uang Fisik Dihitung</label>
            <input type="number" name="kas_fisik" required style="width:100%; padding:9px; margin:8px 0 16px; border:1px solid #D7DEE9; border-radius:7px;">
            <button type="submit" style="width:100%; padding:12px; background:#16233D; color:#fff; border:none; border-radius:8px; font-weight:600;">Tutup Shift</button>
        </form>
    @else
        <h3>Buka Shift Baru</h3>
        <form method="POST" action="{{ url('/kasir/shift/buka') }}">
            @csrf
            <label>Kas Awal</label>
            <input type="number" name="kas_awal" required style="width:100%; padding:9px; margin:8px 0 16px; border:1px solid #D7DEE9; border-radius:7px;">
            <button type="submit" style="width:100%; padding:12px; background:#4ADE80; color:#0C1A0F; border:none; border-radius:8px; font-weight:600;">Buka Shift</button>
        </form>
    @endif

</div>
@endsection