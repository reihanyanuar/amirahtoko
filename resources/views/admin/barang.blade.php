@extends('layouts.admin')
@section('title', 'Input Barang')

@section('content')
<form method="POST" action="{{ url('/admin/barang/simpan') }}">
    @csrf

    <div class="form-row">
        <label>KODE Barang</label>
        <input type="text" name="KodeBrg" required placeholder="cth. GL2">
    </div>

    <div class="form-row">
        <label>Nama Barang</label>
        <input type="text" name="NamaBrg" required>
    </div>

    <div class="form-row-two">
        <div class="form-row">
            <label>Supplier</label>
            <select name="NamaSup" required>
                <option value="">-- Pilih --</option>
                @foreach ($supplier as $s)
                    <option value="{{ $s->NamaSup }}">{{ $s->NamaSup }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <label>Jenis Kelompok</label>
            <input type="text" name="Jenis" list="daftarKategori" required placeholder="cth. SEMBAKO">
            <datalist id="daftarKategori">
                @foreach ($kategoriList as $k)
                    <option value="{{ $k }}">
                @endforeach
            </datalist>
        </div>
    </div>

    <div class="form-row-two">
    <div class="form-row">
        <label>Nama Satuan Dos</label>
        <input type="text" name="SatBsr" placeholder="Dus">
    </div>
    <div class="form-row">
        <label>Barcode Dus</label>
        <input type="text" name="KodeBsr" placeholder="kosongkan jika sama dengan Kode Barang">
    </div>
    </div>
    <div class="form-row">
        <label>HPP Dus (Rp)</label>
        <input type="number" name="HppBsr">
    </div>

    <div class="form-row-two">
        <div class="form-row">
            <label>Nama Satuan Lusin</label>
            <input type="text" name="SatSdg" placeholder="Lusin">
        </div>
        <div class="form-row">
            <label>Barcode Lusin</label>
            <input type="text" name="KodeSdg" placeholder="kosongkan jika sama dengan Kode Barang">
        </div>
    </div>
    <div class="form-row">
        <label>HPP Lusin (Rp)</label>
        <input type="number" name="HppSdg">
    </div>

    <div class="form-row-two">
        <div class="form-row">
            <label>Nama Satuan Bijian</label>
            <input type="text" name="SatKcl" required placeholder="Pcs">
        </div>
        <div class="form-row">
            <label>HPP Pcs (Rp)</label>
            <input type="number" name="Hpp" required>
        </div>
    </div>

    <div class="form-row-two">
        <div class="form-row">
            <label>Harga Dus (Rp)</label>
            <input type="number" name="HrgBsr" required>
        </div>
        <div class="form-row">
            <label>Harga Lusin (Rp)</label>
            <input type="number" name="HrgSdg" required>
        </div>
    </div>

    <div class="form-row">
        <label>Harga Pcs 1 (Rp)</label>
        <input type="number" name="Harga1" required>
    </div>

    <div class="form-row-two">
        <div class="form-row">
            <label>Harga Pcs 2 (Rp)</label>
            <input type="number" name="Harga2" required>
        </div>
        <div class="form-row">
            <label>Batas Jumlah (utk Harga Pcs 2)</label>
            <input type="number" name="Limit2" required>
        </div>
    </div>

    <div class="form-row-two">
        <div class="form-row">
            <label>Harga Pcs 3 (Rp)</label>
            <input type="number" name="Harga3" required>
        </div>
        <div class="form-row">
            <label>Batas Jumlah (utk Harga Pcs 3)</label>
            <input type="number" name="Limit3" required>
        </div>
    </div>

    <div class="form-row">
        <label>Stock Awal</label>
        <input type="number" name="JmlStock" required value="0">
    </div>

    <div class="form-row">
        <label>Catatan / Ket.</label>
        <input type="text" name="Catatan">
    </div>

    <button type="submit" class="btn-simpan">Simpan Barang</button>
</form>
@endsection