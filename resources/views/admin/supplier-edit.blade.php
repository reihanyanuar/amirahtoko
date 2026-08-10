@extends('layouts.admin')
@section('title', 'Edit Supplier')

@section('content')
<div class="admin-card" style="max-width:500px;">
    <h3>Edit Supplier: {{ $supplier->NamaSup }}</h3>
    <form method="POST" action="{{ url('/admin/supplier/update/' . $supplier->KodeSup) }}">
        @csrf
        <div class="form-row">
            <label>Kode Supplier (tidak bisa diubah)</label>
            <input type="text" value="{{ $supplier->KodeSup }}" disabled>
        </div>
        <div class="form-row">
            <label>Nama Supplier</label>
            <input type="text" name="NamaSup" required value="{{ $supplier->NamaSup }}">
        </div>
        <div class="form-row">
            <label>Alamat</label>
            <input type="text" name="Alamat" value="{{ $supplier->Alamat }}">
        </div>
        <button type="submit" class="btn-simpan">Update Supplier</button>
        <a href="{{ url('/admin/supplier') }}" style="display:block;text-align:center;margin-top:10px;color:#667085;font-size:13px;">Batal</a>
    </form>
</div>
@endsection
