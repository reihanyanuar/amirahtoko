@extends('layouts.admin')
@section('title', 'Data Pelanggan Toko')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row">
    <div>
        <h2 class="page-head-title">👤 Data Pelanggan Toko</h2>
        <p class="page-head-sub">Kelola daftar pelanggan tetap, saldo kredit, poin belanja & tingkat harga khusus</p>
    </div>
    <button type="button" class="btn-add-green" onclick="openModalTambahPelanggan()">
        <span>+</span> Tambah Pelanggan
    </button>
</div>

{{-- SEARCH & FILTER TOOLBAR --}}
<div class="admin-search-row" style="margin-bottom: 16px;">
    <div class="admin-search-wrap" style="flex:1; max-width:400px;">
        <span class="search-icon">🔍</span>
        <input type="text" id="cariPelangganAdmin" placeholder="Cari kode atau nama pelanggan..." onkeyup="filterPelangganTable()">
    </div>
</div>

{{-- TABEL PELANGGAN --}}
<div class="table-container">
    <table class="table" id="tabelPelanggan">
        <thead>
            <tr>
                <th style="width: 110px;">KODE</th>
                <th>NAMA PELANGGAN</th>
                <th>ALAMAT</th>
                <th style="text-align: center; width: 120px;">TINGKAT HARGA</th>
                <th style="text-align: right;">SALDO KREDIT</th>
                <th style="text-align: center; width: 90px;">POIN</th>
                <th style="text-align: center; width: 130px;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelanggan as $p)
                <tr class="pelanggan-row" data-search="{{ strtolower($p->KodePlg . ' ' . $p->NamaPlg . ' ' . $p->Alamat) }}">
                    <td style="font-family: monospace; font-weight: 700; color: #1D4ED8; font-size: 14px;">
                        {{ $p->KodePlg }}
                    </td>
                    <td>
                        <div style="font-weight: 700; color: #0F172A; font-size: 15px;">{{ $p->NamaPlg }}</div>
                        @if ($p->KodePlg === 'P0001')
                            <span class="badge badge-gray" style="font-size: 11px; padding: 2px 6px;">Default System</span>
                        @endif
                    </td>
                    <td style="color: #475569; font-size: 14px;">
                        {{ $p->Alamat ?: '-' }}
                    </td>
                    <td style="text-align: center;">
                        <span class="badge badge-purple" style="font-weight: 700;">
                            Tingkat {{ $p->TingkatHrg ?: 1 }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 700; color: {{ $p->SaldoKredit > 0 ? '#DC2626' : '#15803D' }};">
                        Rp {{ number_format($p->SaldoKredit, 0, ',', '.') }}
                    </td>
                    <td style="text-align: center; font-weight: 700; color: #D97706;">
                        ⭐ {{ number_format($p->Poin, 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 6px; justify-content: center;">
                            <button type="button" class="btn-action-sm" onclick='openModalEditPelanggan({{ json_encode($p) }})' title="Edit Pelanggan">
                                ✏️ Edit
                            </button>
                            @if ($p->KodePlg !== 'P0001')
                                <form method="POST" action="{{ url('/admin/pelanggan/hapus/' . $p->KodePlg) }}" onsubmit="return confirm('Hapus pelanggan {{ $p->NamaPlg }}?')">
                                    @csrf
                                    <button type="submit" class="btn-action-sm danger" title="Hapus Pelanggan">
                                        🗑️
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94A3B8;">
                        Belum ada data pelanggan. Klik "+ Tambah Pelanggan" untuk menambahkan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL TAMBAH PELANGGAN --}}
<div class="admin-modal-backdrop" id="modalTambahPelanggan" style="display: none;">
    <div class="admin-modal-card" style="width: 480px;">
        <div class="admin-modal-title">👤 Tambah Pelanggan Baru</div>
        <p class="admin-modal-sub">Isi data pelanggan untuk mendaftarkan pelanggan tetap toko</p>

        <form method="POST" action="{{ url('/admin/pelanggan/simpan') }}">
            @csrf
            <div class="modal-form-group">
                <label>Kode Pelanggan <span style="color:red">*</span></label>
                <input type="text" name="KodePlg" required placeholder="cth. P0003" style="text-transform: uppercase;">
            </div>

            <div class="modal-form-group">
                <label>Nama Pelanggan <span style="color:red">*</span></label>
                <input type="text" name="NamaPlg" required placeholder="cth. Toko Berkah / Pak Budi">
            </div>

            <div class="modal-form-group">
                <label>Alamat / Kota</label>
                <input type="text" name="Alamat" placeholder="cth. Lumajang">
            </div>

            <div class="modal-form-group">
                <label>Tingkat Harga Khusus</label>
                <select name="TingkatHrg">
                    <option value="1">Tingkat 1 (Harga Normal / Pcs)</option>
                    <option value="2">Tingkat 2 (Harga Grosir 2)</option>
                    <option value="3">Tingkat 3 (Harga Grosir 3)</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="modal-form-group">
                    <label>Saldo Kredit (Rp)</label>
                    <input type="number" name="SaldoKredit" value="0" min="0">
                </div>
                <div class="modal-form-group">
                    <label>Poin Awal</label>
                    <input type="number" name="Poin" value="0" min="0">
                </div>
            </div>

            <div class="modal-action-row">
                <button type="button" class="btn-batal-form" onclick="closeModalTambahPelanggan()">Batal</button>
                <button type="submit" class="btn-simpan-form">✔ Simpan Pelanggan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT PELANGGAN --}}
<div class="admin-modal-backdrop" id="modalEditPelanggan" style="display: none;">
    <div class="admin-modal-card" style="width: 480px;">
        <div class="admin-modal-title">✏️ Edit Data Pelanggan</div>
        <p class="admin-modal-sub">Perbarui informasi pelanggan, saldo kredit, dan tingkat harga</p>

        <form id="formEditPelanggan" method="POST" action="">
            @csrf
            <div class="modal-form-group">
                <label>Kode Pelanggan</label>
                <input type="text" id="edit_KodePlg" readonly style="background: #F1F5F9; color: #64748B;">
            </div>

            <div class="modal-form-group">
                <label>Nama Pelanggan <span style="color:red">*</span></label>
                <input type="text" name="NamaPlg" id="edit_NamaPlg" required>
            </div>

            <div class="modal-form-group">
                <label>Alamat / Kota</label>
                <input type="text" name="Alamat" id="edit_Alamat">
            </div>

            <div class="modal-form-group">
                <label>Tingkat Harga Khusus</label>
                <select name="TingkatHrg" id="edit_TingkatHrg">
                    <option value="1">Tingkat 1 (Harga Normal / Pcs)</option>
                    <option value="2">Tingkat 2 (Harga Grosir 2)</option>
                    <option value="3">Tingkat 3 (Harga Grosir 3)</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div class="modal-form-group">
                    <label>Saldo Kredit (Rp)</label>
                    <input type="number" name="SaldoKredit" id="edit_SaldoKredit" min="0">
                </div>
                <div class="modal-form-group">
                    <label>Poin</label>
                    <input type="number" name="Poin" id="edit_Poin" min="0">
                </div>
            </div>

            <div class="modal-action-row">
                <button type="button" class="btn-batal-form" onclick="closeModalEditPelanggan()">Batal</button>
                <button type="submit" class="btn-simpan-form">✔ Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterPelangganTable() {
    const q = document.getElementById('cariPelangganAdmin').value.toLowerCase();
    document.querySelectorAll('.pelanggan-row').forEach(row => {
        const text = row.dataset.search;
        row.style.display = text.includes(q) ? '' : 'none';
    });
}

function openModalTambahPelanggan() {
    document.getElementById('modalTambahPelanggan').style.display = 'flex';
}

function closeModalTambahPelanggan() {
    document.getElementById('modalTambahPelanggan').style.display = 'none';
}

function openModalEditPelanggan(p) {
    document.getElementById('edit_KodePlg').value = p.KodePlg;
    document.getElementById('edit_NamaPlg').value = p.NamaPlg;
    document.getElementById('edit_Alamat').value = p.Alamat || '';
    document.getElementById('edit_TingkatHrg').value = p.TingkatHrg || 1;
    document.getElementById('edit_SaldoKredit').value = p.SaldoKredit || 0;
    document.getElementById('edit_Poin').value = p.Poin || 0;

    document.getElementById('formEditPelanggan').action = "{{ url('/admin/pelanggan/update') }}/" + p.KodePlg;
    document.getElementById('modalEditPelanggan').style.display = 'flex';
}

function closeModalEditPelanggan() {
    document.getElementById('modalEditPelanggan').style.display = 'none';
}

// Close modals when clicking backdrop
document.querySelectorAll('.admin-modal-backdrop').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>

@endsection
