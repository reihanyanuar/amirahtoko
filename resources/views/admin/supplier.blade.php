@extends('layouts.admin')
@section('title', 'Data Supplier')

@section('content')
<div class="admin-grid-2">

    <div class="admin-card">
        <h3>Tambah Supplier</h3>
        <form method="POST" action="{{ url('/admin/supplier/simpan') }}">
            @csrf
            <div class="form-row">
                <label>Kode Supplier</label>
                <input type="text" name="KodeSup" required placeholder="cth. S0004">
            </div>
            <div class="form-row">
                <label>Nama Supplier</label>
                <input type="text" name="NamaSup" required>
            </div>
            <div class="form-row">
                <label>Alamat</label>
                <input type="text" name="Alamat">
            </div>
            <button type="submit" class="btn-simpan">Simpan Supplier</button>
        </form>
    </div>

    <div class="admin-card">
        <h3>Daftar Supplier ({{ $supplier->count() }})</h3>
        <table class="table">
            <tr><th>Kode</th><th>Nama</th><th>Alamat</th><th>Aksi</th></tr>
            @foreach ($supplier as $s)
                <tr>
                    <td>{{ $s->KodeSup }}</td>
                    <td>{{ $s->NamaSup }}</td>
                    <td>{{ $s->Alamat }}</td>
                    <td>
                        <a href="{{ url('/admin/supplier/edit/' . $s->KodeSup) }}">Edit</a>
                        &nbsp;|&nbsp;
                        <form method="POST" action="{{ url('/admin/supplier/hapus/' . $s->KodeSup) }}" style="display:inline" onsubmit="return confirm('Yakin mau hapus {{ $s->NamaSup }}?')">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:#DC2626;cursor:pointer;padding:0;">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

</div>
@endsection