@extends('layouts.admin')
@section('title', 'Edit Barang')

@section('content')
<div class="admin-card" style="max-width:700px;">
    <h3>Edit Barang: {{ $barang->NamaBrg }}</h3>
    <form method="POST" action="{{ url('/admin/barang/update/' . $barang->KodeBrg) }}">
        @csrf

        <div class="form-row">
            <label>Kode Barang (tidak bisa diubah)</label>
            <input type="text" value="{{ $barang->KodeBrg }}" disabled>
        </div>

        <div class="form-row">
            <label>Nama Barang</label>
            <input type="text" name="NamaBrg" required value="{{ $barang->NamaBrg }}">
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Supplier</label>
                <select name="NamaSup">
                    <option value="">-- Pilih --</option>
                    @foreach ($supplier as $s)
                        <option value="{{ $s->NamaSup }}" {{ $barang->NamaSup == $s->NamaSup ? 'selected' : '' }}>{{ $s->NamaSup }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label>Jenis Kelompok</label>
                <input type="text" name="Jenis" list="daftarKategori" value="{{ $barang->Jenis }}">
                <datalist id="daftarKategori">
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k }}">
                    @endforeach
                </datalist>
            </div>
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Jumlah Isi Dus (dalam Pcs)</label>
                <input type="number" name="IsiBsr" value="{{ $barang->IsiBsr }}">
            </div>
            <div class="form-row">
                <label>Jumlah Isi Lusin (dalam Pcs)</label>
                <input type="number" name="IsiSdg" value="{{ $barang->IsiSdg }}">
            </div>
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Nama Satuan Dos</label>
                <input type="text" name="SatBsr" value="{{ $barang->SatBsr }}">
            </div>
            <div class="form-row">
                <label>Barcode Dus</label>
                <input type="text" name="KodeBsr" value="{{ $barang->KodeBsr }}">
            </div>
        </div>
        <div class="form-row">
            <label>HPP Dus (Rp)</label>
            <input type="number" name="HppBsr" value="{{ $barang->HppBsr }}">
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Nama Satuan Lusin</label>
                <input type="text" name="SatSdg" value="{{ $barang->SatSdg }}">
            </div>
            <div class="form-row">
                <label>Barcode Lusin</label>
                <input type="text" name="KodeSdg" value="{{ $barang->KodeSdg }}">
            </div>
        </div>
        <div class="form-row">
            <label>HPP Lusin (Rp)</label>
            <input type="number" name="HppSdg" value="{{ $barang->HppSdg }}">
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Nama Satuan Bijian</label>
                <input type="text" name="SatKcl" required value="{{ $barang->SatKcl }}">
            </div>
            <div class="form-row">
                <label>HPP Pcs (Rp)</label>
                <input type="number" name="Hpp" required value="{{ $barang->Hpp }}">
            </div>
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Harga Dus (Rp)</label>
                <input type="number" name="HrgBsr" value="{{ $barang->HrgBsr }}">
            </div>
            <div class="form-row">
                <label>Harga Lusin (Rp)</label>
                <input type="number" name="HrgSdg" value="{{ $barang->HrgSdg }}">
            </div>
        </div>

        <div class="form-row">
            <label>Harga Pcs 1 (Rp)</label>
            <input type="number" name="Harga1" required value="{{ $barang->Harga1 }}">
        </div>

        <div class="form-row-two">
            <div class="form-row">
                <label>Harga Pcs 2 (Rp)</label>
                <input type="number" name="Harga2" value="{{ $barang->Harga2 }}">
            </div>
            <div class="form-row">
                <label>Batas Jumlah (utk Harga Pcs 2)</label>
                <input type="number" name="Limit2" value="{{ $barang->Limit2 }}">
            </div>
        </div>
        <div class="form-row-two">
            <div class="form-row">
                <label>Harga Pcs 3 (Rp)</label>
                <input type="number" name="Harga3" value="{{ $barang->Harga3 }}">
            </div>
            <div class="form-row">
                <label>Batas Jumlah (utk Harga Pcs 3)</label>
                <input type="number" name="Limit3" value="{{ $barang->Limit3 }}">
            </div>
        </div>

        <div class="form-row">
            <label>Stock</label>
            <input type="number" name="JmlStock" required value="{{ $barang->JmlStock }}">
        </div>

        <div class="form-row">
            <label>Catatan / Ket.</label>
            <input type="text" name="Catatan" value="{{ $barang->Catatan }}">
        </div>

        <button type="submit" class="btn-simpan">Update Barang</button>
        <a href="{{ url('/admin/barang') }}" style="display:block;text-align:center;margin-top:10px;color:#667085;font-size:13px;">Batal</a>
    </form>
</div>
@endsection
