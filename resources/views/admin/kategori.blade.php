@extends('layouts.admin')
@section('title', 'Kategori Produk')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">Kategori Produk</h2>
        <p class="page-head-sub">{{ $kategoriList->count() }} kategori terdaftar · Kategori otomatis dari data barang</p>
    </div>
    <button type="button" class="btn-add-green" onclick="bukaModalTambahKat()">
        <span>+</span> Tambah Kategori
    </button>
</div>

{{-- MODAL TAMBAH KATEGORI --}}
<div id="modalTambahKat" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#FFFFFF; border-radius:14px; padding:28px 28px 20px; width:420px; max-width:95vw; box-shadow:0 8px 40px rgba(0,0,0,0.18);">
        <h3 style="font-size:18px; font-weight:800; color:#0F172A; margin-bottom:6px;">🏷️ Tambah Kategori Baru</h3>
        <p style="font-size:13.5px; color:#64748B; margin-bottom:18px;">Ketik nama kategori lalu klik Simpan.</p>
        <form method="POST" action="{{ url('/admin/kategori/simpan') }}" id="formTambahKat">
            @csrf
            <div style="margin-bottom:18px;">
                <label style="font-size:14px; font-weight:600; color:#475569; display:block; margin-bottom:6px;">Nama Kategori Baru <span style="color:red">*</span></label>
                <input type="text" name="NamaKategori" id="inputKatBaru" class="vb-inp"
                       style="width:100%; font-size:16px; padding:9px 12px;"
                       placeholder="cth. ROKOK, MINUMAN, SEMBAKO...">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="tutupModalTambahKat()" style="padding:9px 20px; border:1.5px solid #CBD5E1; border-radius:8px; background:#F8FAFC; font-size:14px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:9px 24px; background:#16A34A; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer;">✔ Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>


<div id="modalEditKat" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#FFFFFF; border-radius:14px; padding:28px 28px 20px; width:420px; max-width:95vw; box-shadow:0 8px 40px rgba(0,0,0,0.18);">
        <h3 style="font-size:18px; font-weight:800; color:#0F172A; margin-bottom:18px;">✏️ Ubah Nama Kategori</h3>
        <form id="formEditKat" method="POST" action="{{ url('/admin/kategori/update') }}">
            @csrf
            <input type="hidden" name="jenis_lama" id="editJenisLama">
            <div style="margin-bottom:14px;">
                <label style="font-size:14px; font-weight:600; color:#475569; display:block; margin-bottom:6px;">Nama Kategori Lama</label>
                <input type="text" id="editLabelLama" readonly class="vb-inp readonly" style="width:100%;">
            </div>
            <div style="margin-bottom:18px;">
                <label style="font-size:14px; font-weight:600; color:#475569; display:block; margin-bottom:6px;">Nama Kategori Baru <span style="color:red">*</span></label>
                <input type="text" name="jenis_baru" id="editJenisBaru" required class="vb-inp" style="width:100%; font-size:16px; padding:9px 12px;" placeholder="Ketik nama kategori baru...">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="tutupModalEdit()" style="padding:9px 20px; border:1.5px solid #CBD5E1; border-radius:8px; background:#F8FAFC; font-size:14px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:9px 24px; background:#2563EB; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer;">✔ Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- KATEGORI TABLE --}}
<div class="table-container">
    <table class="table" id="tabelKategori">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>NAMA KATEGORI</th>
                <th style="text-align:center;">JUMLAH PRODUK</th>
                <th style="text-align:center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kategoriList as $i => $k)
                <tr>
                    <td style="color:#94A3B8; font-size:13px;">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span style="font-size:20px;">🏷️</span>
                            <span style="font-weight:700; font-size:16px; color:#0F172A;">{{ $k->Jenis }}</span>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <span style="display:inline-block; background:#EFF6FF; color:#2563EB; font-weight:700; font-size:14px; padding:4px 14px; border-radius:20px;">
                            {{ $k->total_produk }} produk
                        </span>
                    </td>
                    <td style="text-align:center;">
                        {{-- Edit --}}
                        <button 
                            type="button"
                            class="btn-icon-action edit"
                            title="Ubah Nama Kategori"
                            onclick="bukaModalEdit('{{ addslashes($k->Jenis) }}')"
                        >✏️ Edit</button>

                        {{-- Hapus --}}
                        <form method="POST" action="{{ url('/admin/kategori/hapus') }}" style="display:inline"
                              onsubmit="return confirm('Yakin hapus kategori \"{{ addslashes($k->Jenis) }}\"?\nSemua produk di kategori ini akan kehilangan kategorinya.')">
                            @csrf
                            <input type="hidden" name="jenis" value="{{ $k->Jenis }}">
                            <button type="submit" class="btn-icon-action delete" title="Hapus Kategori">🗑️ Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:40px; color:#94A3B8;">
                        <div style="font-size:32px; margin-bottom:8px;">🏷️</div>
                        <p style="font-size:15px; font-weight:600;">Belum ada kategori terdaftar.</p>
                        <p style="font-size:13.5px; margin-top:4px;">Kategori otomatis ditambahkan saat Anda menginput produk baru.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
// ---- MODAL TAMBAH KATEGORI ----
function bukaModalTambahKat() {
    document.getElementById('inputKatBaru').value = '';
    document.getElementById('inputKatBaru').style.borderColor = '';
    document.getElementById('modalTambahKat').style.display = 'flex';
    setTimeout(() => document.getElementById('inputKatBaru').focus(), 50);
}

function tutupModalTambahKat() {
    document.getElementById('modalTambahKat').style.display = 'none';
}

// Tutup modal tambah jika klik di luar
document.getElementById('modalTambahKat').addEventListener('click', function(e) {
    if (e.target === this) tutupModalTambahKat();
});

// ---- MODAL EDIT KATEGORI ----
function bukaModalEdit(namaKategori) {
    document.getElementById('editJenisLama').value  = namaKategori;
    document.getElementById('editLabelLama').value  = namaKategori;
    document.getElementById('editJenisBaru').value  = namaKategori;
    document.getElementById('modalEditKat').style.display = 'flex';
    setTimeout(() => document.getElementById('editJenisBaru').select(), 50);
}

function tutupModalEdit() {
    document.getElementById('modalEditKat').style.display = 'none';
}

// Klik di luar modal untuk tutup
document.getElementById('modalEditKat').addEventListener('click', function(e) {
    if (e.target === this) tutupModalEdit();
});
</script>

@endsection