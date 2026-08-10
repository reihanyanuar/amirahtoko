@extends('layouts.admin')
@section('title', 'Input Barang')

@section('content')

{{-- PAGE HEADER WITH ACTION BUTTON --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">Input Barang</h2>
        <p class="page-head-sub">{{ $barang->count() }} produk terdaftar</p>
    </div>
    <button type="button" class="btn-add-green" onclick="openModalBarang()">
        <span>+</span> Tambah Produk
    </button>
</div>

{{-- SEARCH BAR --}}
<div class="admin-search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" id="cariBarangAdmin" placeholder="Cari nama atau barcode..." oninput="filterBarangTable()">
</div>

{{-- DATA TABLE --}}
<div class="table-container">
    <table class="table" id="tabelBarang">
        <thead>
            <tr>
                <th>BARCODE</th>
                <th>NAMA PRODUK</th>
                <th>KATEGORI</th>
                <th>HARGA JUAL</th>
                <th>HARGA MODAL</th>
                <th>STOK</th>
                <th style="text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $b)
                @php
                    $katLower = strtolower($b->Jenis);
                    $badgeClass = 'badge-kat-default';
                    if (str_contains($katLower, 'minum')) $badgeClass = 'badge-kat-minuman';
                    elseif (str_contains($katLower, 'makan')) $badgeClass = 'badge-kat-makanan';
                    elseif (str_contains($katLower, 'snack')) $badgeClass = 'badge-kat-snack';
                    elseif (str_contains($katLower, 'sembako') || str_contains($katLower, 'beras') || str_contains($katLower, 'gula')) $badgeClass = 'badge-kat-sembako';
                    elseif (str_contains($katLower, 'care') || str_contains($katLower, 'sabun')) $badgeClass = 'badge-kat-personal-care';
                    elseif (str_contains($katLower, 'rokok')) $badgeClass = 'badge-kat-rokok';
                @endphp
                <tr class="barang-row" data-search="{{ strtolower($b->KodeBrg . ' ' . $b->NamaBrg . ' ' . $b->Jenis) }}">
                    <td style="font-family: monospace; font-size: 13.5px; color: #64748B;">{{ $b->KodeBrg }}</td>
                    <td style="font-weight: 700; color: #0F172A;">{{ $b->NamaBrg }}</td>
                    <td>
                        <span class="badge-kat {{ $badgeClass }}">{{ $b->Jenis ?: 'Umum' }}</span>
                    </td>
                    <td style="font-weight: 700;">Rp {{ number_format($b->Harga1, 0, ',', '.') }}</td>
                    <td style="color: #64748B;">Rp {{ number_format($b->Hpp, 0, ',', '.') }}</td>
                    <td>
                        <strong style="color: {{ $b->JmlStock <= 5 ? '#DC2626' : '#16A34A' }};">
                            {{ $b->JmlStock }}
                        </strong> 
                        <span style="color: #64748B; font-size: 13px;">{{ $b->SatKcl }}</span>
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ url('/admin/barang/edit/' . $b->KodeBrg) }}" class="btn-icon-action edit" title="Edit Barang">✏️</a>
                        <form method="POST" action="{{ url('/admin/barang/hapus/' . $b->KodeBrg) }}" style="display:inline" onsubmit="return confirm('Yakin hapus {{ addslashes($b->NamaBrg) }}?')">
                            @csrf
                            <button type="submit" class="btn-icon-action delete" title="Hapus Barang">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- MODAL FORM TAMBAH BARANG --}}
<div class="admin-modal-overlay" id="modalBarang">
    <div class="admin-modal-box">
        <div class="admin-modal-head">
            <h3 class="admin-modal-title">📦 Tambah Produk Baru</h3>
            <button type="button" class="admin-modal-close" onclick="closeModalBarang()">✕</button>
        </div>

        <form method="POST" action="{{ url('/admin/barang/simpan') }}">
            @csrf

            <div class="form-row-two">
                <div class="form-row">
                    <label>Kode Barang (Barcode Primary)</label>
                    <input type="text" name="KodeBrg" required placeholder="cth. GL2">
                </div>
                <div class="form-row">
                    <label>Nama Barang</label>
                    <input type="text" name="NamaBrg" required placeholder="cth. Gula Pasar 1kg">
                </div>
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Supplier</label>
                    <select name="NamaSup" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($supplier as $s)
                            <option value="{{ $s->NamaSup }}">{{ $s->NamaSup }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Jenis Kategori</label>
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
                    <label>Jumlah Isi Dus (dalam Pcs)</label>
                    <input type="number" name="IsiBsr" placeholder="cth. 24">
                </div>
                <div class="form-row">
                    <label>Jumlah Isi Lusin (dalam Pcs)</label>
                    <input type="number" name="IsiSdg" placeholder="cth. 12">
                </div>
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Nama Satuan Dos</label>
                    <input type="text" name="SatBsr" placeholder="Dus">
                </div>
                <div class="form-row">
                    <label>Barcode Dus</label>
                    <input type="text" name="KodeBsr" placeholder="Kosongkan jika sama">
                </div>
            </div>
            <div class="form-row">
                <label>HPP Dus (Rp)</label>
                <input type="number" name="HppBsr" placeholder="0">
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Nama Satuan Lusin</label>
                    <input type="text" name="SatSdg" placeholder="Lusin">
                </div>
                <div class="form-row">
                    <label>Barcode Lusin</label>
                    <input type="text" name="KodeSdg" placeholder="Kosongkan jika sama">
                </div>
            </div>
            <div class="form-row">
                <label>HPP Lusin (Rp)</label>
                <input type="number" name="HppSdg" placeholder="0">
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Nama Satuan Bijian</label>
                    <input type="text" name="SatKcl" required placeholder="Pcs">
                </div>
                <div class="form-row">
                    <label>HPP Pcs / Modal (Rp)</label>
                    <input type="number" name="Hpp" required placeholder="0">
                </div>
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Harga Dus (Rp)</label>
                    <input type="number" name="HrgBsr" placeholder="0">
                </div>
                <div class="form-row">
                    <label>Harga Lusin (Rp)</label>
                    <input type="number" name="HrgSdg" placeholder="0">
                </div>
            </div>

            <div class="form-row">
                <label>Harga Pcs Utama / Jual (Rp)</label>
                <input type="number" name="Harga1" required placeholder="0">
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Harga Pcs 2 (Grosir)</label>
                    <input type="number" name="Harga2" placeholder="0">
                </div>
                <div class="form-row">
                    <label>Batas Jumlah Harga 2</label>
                    <input type="number" name="Limit2" placeholder="0">
                </div>
            </div>

            <div class="form-row-two">
                <div class="form-row">
                    <label>Harga Pcs 3 (Grosir)</label>
                    <input type="number" name="Harga3" placeholder="0">
                </div>
                <div class="form-row">
                    <label>Batas Jumlah Harga 3</label>
                    <input type="number" name="Limit3" placeholder="0">
                </div>
            </div>

            <div class="form-row">
                <label>Stock Awal</label>
                <input type="number" name="JmlStock" required value="0">
            </div>

            <div class="form-row">
                <label>Catatan / Keterangan</label>
                <input type="text" name="Catatan" placeholder="-">
            </div>

            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="button" class="btn-action-delete" style="flex:1; padding:12px; font-size:15px;" onclick="closeModalBarang()">Batal</button>
                <button type="submit" class="btn-add-green" style="flex:2; justify-content:center; padding:12px;">✔ Simpan Produk Baru</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalBarang() {
    document.getElementById('modalBarang').classList.add('show');
}
function closeModalBarang() {
    document.getElementById('modalBarang').classList.remove('show');
}
function filterBarangTable() {
    const q = document.getElementById('cariBarangAdmin').value.toLowerCase();
    document.querySelectorAll('.barang-row').forEach(row => {
        const text = row.dataset.search;
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
document.getElementById('modalBarang').addEventListener('click', function(e) {
    if (e.target === this) closeModalBarang();
});
</script>

@endsection