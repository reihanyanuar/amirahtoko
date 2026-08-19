@extends('layouts.admin')
@section('title', 'Tambah Stok Barang')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row" style="margin-bottom:16px;">
    <div>
        <h2 class="page-head-title">➕ Tambah Stok Barang (Restock Cepat)</h2>
        <p class="page-head-sub">Pilih barang, tentukan satuan (Dus / Lusin / Pcs), stok akan otomatis dikonversi dan ditambahkan</p>
    </div>
    <a href="{{ url('/admin/barang') }}" class="btn-add-blue">📦 Lihat Stok Semua Barang</a>
</div>

{{-- GRID LAYOUT: FORM KIRI, RIWAYAT KANAN --}}
<div style="display: grid; grid-template-columns: 420px 1fr; gap: 20px; align-items: start;">

    {{-- ==================== FORM TAMBAH STOK (KOLOM KIRI) ==================== --}}
    <div class="vb-form-card" style="padding: 20px;">
        <div style="font-size: 16px; font-weight: 800; color: #0F172A; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <span>📥</span> Form Restock Barang
        </div>

        <form method="POST" action="{{ url('/admin/tambah-stok/simpan') }}" id="formTambahStok">
            @csrf

            {{-- 1. PILIH BARANG (CUSTOM SEARCHABLE SELECT) --}}
            <div class="vb-row" style="margin-bottom: 12px;">
                <label class="vb-lbl">1. Pilih Produk <span style="color:red">*</span></label>
                <div class="vb-search-select" id="wrap_KodeBrg">
                    <input type="hidden" name="KodeBrg" id="f_KodeBrg" required value="{{ old('KodeBrg') }}">
                    <button type="button" class="vb-select-btn" id="btn_KodeBrg">
                        <span class="vb-select-text placeholder">-- Pilih / Cari Produk --</span>
                        <span class="vb-select-arrow">▼</span>
                    </button>
                    <div class="vb-select-dropdown" style="display:none;">
                        <div class="vb-select-search-box">
                            <input type="text" class="vb-select-search-input" placeholder="🔍 Ketik nama barang / barcode..." autocomplete="off">
                        </div>
                        <ul class="vb-select-options" style="max-height: 220px;">
                            @foreach ($barangList as $b)
                                <li class="vb-option-item" 
                                    data-value="{{ $b->KodeBrg }}"
                                    data-nama="{{ $b->NamaBrg }}"
                                    data-stok="{{ $b->JmlStock }}"
                                    data-sat-kcl="{{ $b->SatKcl ?: 'Pcs' }}"
                                    data-sat-sdg="{{ $b->SatSdg ?: 'Lusin' }}"
                                    data-isi-sdg="{{ $b->IsiSdg ?: 1 }}"
                                    data-sat-bsr="{{ $b->SatBsr ?: 'Dus' }}"
                                    data-isi-bsr="{{ $b->IsiBsr ?: 1 }}">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <span><strong>{{ $b->NamaBrg }}</strong> <small style="color:#64748B;">({{ $b->KodeBrg }})</small></span>
                                        <span style="font-size:12px; font-weight:700; color: {{ $b->JmlStock <= 10 ? '#DC2626' : '#16A34A' }};">
                                            {{ $b->JmlStock }} {{ $b->SatKcl ?: 'Pcs' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 2. INFO STOK SAAT INI --}}
            <div id="boxInfoStok" style="display:none; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:8px; padding:10px 14px; margin-bottom:14px;">
                <div style="font-size:13px; color:#64748B; margin-bottom:2px;">Stok Saat Ini di Toko:</div>
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <span id="labelNamaBarang" style="font-weight:700; color:#0F172A; font-size:15px;">-</span>
                    <span id="badgeStokSekarang" style="font-weight:800; font-size:15px; padding:2px 10px; border-radius:6px; background:#EFF6FF; color:#2563EB;">0 Pcs</span>
                </div>
            </div>

            {{-- 3. PILIHAN SATUAN & JUMLAH (QTY) --}}
            <div class="vb-row-two" style="margin-bottom: 12px;">
                <div class="vb-subcol">
                    <label class="vb-lbl">2. Satuan Restock <span style="color:red">*</span></label>
                    <select name="Sat" id="f_Sat" required class="vb-inp" onchange="hitungKalkulasiRestock()">
                        <option value="Pcs">Pcs (Satuan)</option>
                    </select>
                </div>
                <div class="vb-subcol">
                    <label class="vb-lbl">3. Jumlah Restock <span style="color:red">*</span></label>
                    <input type="number" name="Qty" id="f_Qty" required min="1" value="1" placeholder="cth. 5" class="vb-inp" oninput="hitungKalkulasiRestock()">
                </div>
            </div>

            {{-- 4. HASIL KONVERSI & PROYEKSI STOK BARU --}}
            <div class="vb-info-box" id="boxKalkulasi" style="margin-bottom: 14px; display:none;">
                <div id="teksFormula" style="font-size: 14px; color: #1E293B; margin-bottom: 4px;">
                    ⚡ <strong>Konversi:</strong> 1 Pcs = 1 Pcs
                </div>
                <div id="teksProyeksi" style="font-size: 14px; font-weight: 700; color: #16A34A;">
                    📈 Stok Awal 0 + 1 = <strong>1 Pcs</strong>
                </div>
            </div>

            {{-- 5. CATATAN / KETERANGAN --}}
            <div class="vb-row" style="margin-bottom: 18px;">
                <label class="vb-lbl">4. Catatan / Sumber Barang (Opsional)</label>
                <input type="text" name="Catatan" id="f_Catatan" placeholder="cth. Restock dari supplier / gudang" class="vb-inp">
            </div>

            {{-- ACTION BUTTON --}}
            <div style="display:flex; gap:10px;">
                <button type="submit" class="pos-btn primary" style="flex:1; justify-content:center; padding:10px; font-size:15px;">
                    ✔ Simpan & Tambah Stok
                </button>
            </div>
        </form>
    </div>

    {{-- ==================== TABEL RIWAYAT TERAKHIR (KOLOM KANAN) ==================== --}}
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px;">
            <div style="font-size: 16px; font-weight: 800; color: #0F172A; display:flex; align-items:center; gap:6px;">
                <span>📜</span> Riwayat Restock Terakhir
            </div>
            <div class="admin-search-wrap" style="margin-bottom:0; width:220px;">
                <span class="search-icon">🔍</span>
                <input type="text" id="cariRiwayat" placeholder="Cari di riwayat..." oninput="filterRiwayatRestock()">
            </div>
        </div>

        <div class="table-container" style="max-height: 520px; overflow-y: auto;">
            <table class="table" id="tabelRiwayat">
                <thead>
                    <tr>
                        <th>TANGGAL & JAM</th>
                        <th>NAMA PRODUK</th>
                        <th style="text-align:center;">TAMBAHAN</th>
                        <th style="text-align:center;">TOTAL PCS</th>
                        <th style="text-align:center;">PERUBAHAN STOK</th>
                        <th>OPERATOR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr class="riwayat-row" data-search="{{ strtolower($r->NoNota . ' ' . $r->NamaBrg . ' ' . $r->Operator . ' ' . $r->Catatan) }}">
                            <td style="font-size: 13px; color: #64748B; white-space:nowrap;">
                                <div style="font-weight:700; color:#0F172A;">{{ date('d/m/Y', strtotime($r->Tanggal)) }}</div>
                                <div style="font-size:12px;">{{ $r->Jam }} · <span style="font-family:monospace;">{{ $r->NoNota }}</span></div>
                            </td>
                            <td>
                                <strong style="color: #0F172A;">{{ $r->NamaBrg }}</strong>
                                @if($r->Catatan && $r->Catatan !== '-')
                                    <div style="font-size: 12px; color: #64748B;">💬 {{ $r->Catatan }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; white-space:nowrap;">
                                <span style="font-weight: 700; color: #2563EB;">+{{ $r->Qty }}</span>
                                <span style="color: #64748B; font-size: 13px;">{{ $r->Sat }}</span>
                            </td>
                            <td style="text-align: center; white-space:nowrap;">
                                <span style="display: inline-block; background: #DCFCE7; color: #15803D; font-weight: 800; font-size: 13.5px; padding: 3px 10px; border-radius: 6px;">
                                    +{{ $r->JmlBrg }} Pcs
                                </span>
                            </td>
                            <td style="text-align: center; font-size: 13.5px; white-space:nowrap;">
                                <span style="color:#64748B;">{{ $r->Awal }}</span>
                                <span style="color:#2563EB; font-weight:800;"> ➔ </span>
                                <strong style="color:#16A34A;">{{ $r->Akhir }}</strong>
                            </td>
                            <td style="font-size: 13.5px; color: #475569; white-space:nowrap;">
                                👤 {{ $r->Operator }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94A3B8;">
                                <div style="font-size: 28px; margin-bottom: 6px;">📦</div>
                                <div>Belum ada riwayat tambah stok.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// Data barang aktif yang sedang dipilih
let barangTerpilih = null;

// ============================================================
// SEARCHABLE SELECT DROPDOWN LOGIC
// ============================================================
function initSearchableSelectBarang() {
    const wrapper = document.getElementById('wrap_KodeBrg');
    if (!wrapper) return;

    const btn = wrapper.querySelector('.vb-select-btn');
    const textSpan = wrapper.querySelector('.vb-select-text');
    const dropdown = wrapper.querySelector('.vb-select-dropdown');
    const searchInput = wrapper.querySelector('.vb-select-search-input');
    const optionsList = wrapper.querySelector('.vb-select-options');
    const items = optionsList.querySelectorAll('.vb-option-item');
    const hiddenInput = document.getElementById('f_KodeBrg');

    function openDropdown() {
        wrapper.classList.add('open');
        dropdown.style.display = 'block';
        searchInput.value = '';
        filterOptions('');
        setTimeout(() => searchInput.focus(), 50);
    }

    function closeDropdown() {
        wrapper.classList.remove('open');
        dropdown.style.display = 'none';
    }

    function selectOption(item) {
        const kode = item.dataset.value;
        const nama = item.dataset.nama;
        const stok = parseInt(item.dataset.stok) || 0;
        const satKcl = item.dataset.satKcl;
        const satSdg = item.dataset.satSdg;
        const isiSdg = parseInt(item.dataset.isiSdg) || 1;
        const satBsr = item.dataset.satBsr;
        const isiBsr = parseInt(item.dataset.isiBsr) || 1;

        hiddenInput.value = kode;
        textSpan.textContent = nama;
        textSpan.classList.remove('placeholder');

        barangTerpilih = {
            kode, nama, stok, satKcl, satSdg, isiSdg, satBsr, isiBsr
        };

        // Update Info Box
        document.getElementById('boxInfoStok').style.display = 'block';
        document.getElementById('labelNamaBarang').textContent = nama;
        const badge = document.getElementById('badgeStokSekarang');
        badge.textContent = stok + ' ' + satKcl;
        badge.style.color = stok <= 10 ? '#DC2626' : '#2563EB';

        // Update Satuan dropdown options
        const selectSat = document.getElementById('f_Sat');
        selectSat.innerHTML = '';

        // Pcs
        const optKcl = document.createElement('option');
        optKcl.value = satKcl;
        optKcl.textContent = satKcl + ' (1 ' + satKcl + ')';
        selectSat.appendChild(optKcl);

        // Lusin (Sedang) jika ada
        if (isiSdg > 1) {
            const optSdg = document.createElement('option');
            optSdg.value = satSdg;
            optSdg.textContent = satSdg + ' (' + isiSdg + ' ' + satKcl + ')';
            selectSat.appendChild(optSdg);
        }

        // Dus (Besar) jika ada
        if (isiBsr > 1) {
            const optBsr = document.createElement('option');
            optBsr.value = satBsr;
            optBsr.textContent = satBsr + ' (' + isiBsr + ' ' + satKcl + ')';
            selectSat.appendChild(optBsr);
        }

        hitungKalkulasiRestock();
        closeDropdown();

        // Focus ke jumlah input
        document.getElementById('f_Qty').focus();
        document.getElementById('f_Qty').select();
    }

    function filterOptions(query) {
        const q = query.toLowerCase();
        items.forEach(item => {
            const nama = (item.dataset.nama || '').toLowerCase();
            const kode = (item.dataset.value || '').toLowerCase();
            if (nama.includes(q) || kode.includes(q)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        if (wrapper.classList.contains('open')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    btn.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
            e.preventDefault();
            openDropdown();
        }
    });

    searchInput.addEventListener('input', function () {
        filterOptions(this.value);
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const visibleItems = Array.from(items).filter(i => i.style.display !== 'none');
            if (visibleItems.length > 0) {
                selectOption(visibleItems[0]);
            }
        } else if (e.key === 'Escape') {
            closeDropdown();
            btn.focus();
        }
    });

    items.forEach(item => {
        item.addEventListener('click', function () {
            selectOption(this);
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#wrap_KodeBrg')) {
            closeDropdown();
        }
    });
}

// ============================================================
// KALKULASI REAL-TIME KONVERSI RESTOCK
// ============================================================
function hitungKalkulasiRestock() {
    if (!barangTerpilih) {
        document.getElementById('boxKalkulasi').style.display = 'none';
        return;
    }

    const qty = parseInt(document.getElementById('f_Qty').value) || 0;
    const sat = document.getElementById('f_Sat').value;
    const box = document.getElementById('boxKalkulasi');

    if (qty <= 0) {
        box.style.display = 'none';
        return;
    }

    let multiplier = 1;
    if (sat === barangTerpilih.satBsr && barangTerpilih.isiBsr > 1) {
        multiplier = barangTerpilih.isiBsr;
    } else if (sat === barangTerpilih.satSdg && barangTerpilih.isiSdg > 1) {
        multiplier = barangTerpilih.isiSdg;
    }

    const totalPcs = qty * multiplier;
    const stokAkhir = barangTerpilih.stok + totalPcs;

    let formulaText = `⚡ <strong>Konversi:</strong> ${qty} ${sat} = <strong>+${totalPcs} ${barangTerpilih.satKcl}</strong>`;
    if (multiplier > 1) {
        formulaText = `⚡ <strong>Konversi:</strong> ${qty} ${sat} × ${multiplier} = <strong>+${totalPcs} ${barangTerpilih.satKcl}</strong>`;
    }

    document.getElementById('teksFormula').innerHTML = formulaText;
    document.getElementById('teksProyeksi').innerHTML = `📈 Stok Awal (${barangTerpilih.stok}) + ${totalPcs} = <strong style="font-size:16px;">${stokAkhir} ${barangTerpilih.satKcl}</strong>`;

    box.style.display = 'block';
}

// ============================================================
// SEARCH PADA TABEL RIWAYAT
// ============================================================
function filterRiwayatRestock() {
    const q = document.getElementById('cariRiwayat').value.toLowerCase();
    document.querySelectorAll('.riwayat-row').forEach(row => {
        const text = row.dataset.search || '';
        row.style.display = text.includes(q) ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initSearchableSelectBarang();
});
</script>

@endsection
