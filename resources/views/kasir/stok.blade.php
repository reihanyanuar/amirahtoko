@extends('layouts.kasir')

@section('title', 'Stok Barang')

@section('content')

    <table class="table">
        <tr>
            <th>Kode</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Satuan</th>
            <th>Harga</th>
        </tr>
        @foreach ($barang as $item)
            <tr>
                <td>{{ $item->KodeBrg }}</td>
                <td>{{ $item->NamaBrg }}</td>
                <td>{{ $item->Jenis }}</td>
                <td>{{ $item->JmlStock }}</td>
                <td>{{ $item->SatKcl }}</td>
                <td>Rp {{ number_format($item->Harga1, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

@endsection