@extends('layouts.kasir')
@section('title', 'Riwayat Transaksi')

@section('content')

@if ($riwayat->count() > 0)
    <table class="table">
        <tr>
            <th>Jam</th>
            <th>No. Nota</th>
            <th>Jumlah Barang</th>
            <th>Metode Bayar</th>
            <th>Total</th>
        </tr>
        @foreach ($riwayat as $r)
            <tr>
                <td>{{ $r->jam }}</td>
                <td>{{ $r->NoNota }}</td>
                <td>{{ $r->jumlah_barang }} item</td>
                <td>{{ $r->cara_bayar }}</td>
                <td>Rp {{ number_format($r->total, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
@else
    <div class="riwayat-card">
        <p style="color:#98A2B3; text-align:center;">Belum ada transaksi hari ini.</p>
    </div>
@endif

@endsection