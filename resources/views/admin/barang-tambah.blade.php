@extends('layouts.admin')
@section('title', 'Input Barang')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row" style="margin-bottom:12px;">
    <div>
        <h2 class="page-head-title">📝 Input Barang</h2>
        <p class="page-head-sub">Isi data produk sesuai urutan · Tekan Enter untuk pindah ke field berikutnya</p>
    </div>
    <a href="{{ url('/admin/barang') }}" class="btn-add-blue">📦 Lihat Stok Barang</a>
</div>

{{-- COMPACT FORM CONTAINER --}}
<div class="vb-form-card">
    <div class="vb-card-header">
        <span>Form Input Data Barang</span>
    </div>

    <form id="formTambahBarang" method="POST" action="{{ url('/admin/barang/simpan') }}">
        @csrf

        <div class="vb-form-grid-2col">
            {{-- KOLOM KIRI: FIELD 1-14 (INFORMASI UMUM, DUS, LUSIN) --}}
            <div class="vb-col-section">
                {{-- 1. Kode Barang Pcs (Barcode) --}}
                <div class="vb-row">
                    <label class="vb-lbl">1. Kode Barang Pcs (Barcode) <span style="color:red">*</span></label>
                    <input type="text" name="KodeBrg" id="f_KodeBrg" required placeholder="Arahkan scanner / ketik kode barang..." class="vb-inp" autofocus>
                </div>

                {{-- 2. Nama Barang --}}
                <div class="vb-row">
                    <label class="vb-lbl">2. Nama Barang <span style="color:red">*</span></label>
                    <input type="text" name="NamaBrg" id="f_NamaBrg" required placeholder="cth. Gula Pasir 1kg" class="vb-inp">
                </div>

                {{-- 3. Supplier & 4. Jenis Kategori --}}
                {{-- 3. Supplier & 4. Jenis Kategori --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">3. Supplier <span style="color:red">*</span></label>
                        <div class="vb-search-select" id="wrap_NamaSup">
                            <input type="hidden" name="NamaSup" id="f_NamaSup" required value="">
                            <button type="button" class="vb-select-btn" id="btn_NamaSup">
                                <span class="vb-select-text placeholder">-- Pilih Supplier --</span>
                                <span class="vb-select-arrow">▼</span>
                            </button>
                            <div class="vb-select-dropdown" style="display:none;">
                                <div class="vb-select-search-box">
                                    <input type="text" class="vb-select-search-input" placeholder="🔍 Cari supplier..." autocomplete="off">
                                </div>
                                <ul class="vb-select-options">
                                    @foreach ($supplier as $s)
                                        <li class="vb-option-item" data-value="{{ $s->NamaSup }}">{{ $s->NamaSup }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">4. Jenis Kategori <span style="color:red">*</span></label>
                        <div class="vb-search-select" id="wrap_Jenis">
                            <input type="hidden" name="Jenis" id="f_Jenis" required value="{{ request('kategori') }}">
                            <button type="button" class="vb-select-btn" id="btn_Jenis">
                                <span class="vb-select-text {{ request('kategori') ? '' : 'placeholder' }}">
                                    {{ request('kategori') ?: '-- Pilih Kategori --' }}
                                </span>
                                <span class="vb-select-arrow">▼</span>
                            </button>
                            <div class="vb-select-dropdown" style="display:none;">
                                <div class="vb-select-search-box">
                                    <input type="text" class="vb-select-search-input" placeholder="🔍 Cari / Tambah kategori..." autocomplete="off">
                                </div>
                                <ul class="vb-select-options">
                                    @foreach ($kategoriList as $k)
                                        <li class="vb-option-item" data-value="{{ $k }}">{{ $k }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 5. Jumlah Isi Dus (dalam Pcs) & 6. Nama Satuan Dus --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">5. Jumlah Isi Dus (dalam Pcs)</label>
                        <input type="number" name="IsiBsr" id="f_IsiBsr" placeholder="cth. 24" class="vb-inp" oninput="hitungKonversi()">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">6. Nama Satuan Dus</label>
                        <input type="text" name="SatBsr" id="f_SatBsr" placeholder="Dus" class="vb-inp">
                    </div>
                </div>

                {{-- 7. Barcode Dus & 8. HPP Dus --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">7. Barcode Dus</label>
                        <input type="text" name="KodeBsr" id="f_KodeBsr" placeholder="Kosongkan jika sama" class="vb-inp">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">8. HPP Dus</label>
                        <input type="number" name="HppBsr" id="f_HppBsr" placeholder="0" class="vb-inp">
                    </div>
                </div>

                {{-- 9. Harga Dus (Rp) --}}
                <div class="vb-row">
                    <label class="vb-lbl">9. Harga Dus (Rp)</label>
                    <input type="number" name="HrgBsr" id="f_HrgBsr" placeholder="0" class="vb-inp">
                </div>

                {{-- 10. Jumlah Isi Lusin (dalam Pcs) & 11. Nama Satuan Lusin --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">10. Jumlah Isi Lusin (dalam Pcs)</label>
                        <input type="number" name="IsiSdg" id="f_IsiSdg" placeholder="cth. 12" class="vb-inp" oninput="hitungKonversi()">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">11. Nama Satuan Lusin</label>
                        <input type="text" name="SatSdg" id="f_SatSdg" placeholder="Lusin" class="vb-inp">
                    </div>
                </div>

                {{-- 12. Barcode Lusin & 13. HPP Lusin --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">12. Barcode Lusin</label>
                        <input type="text" name="KodeSdg" id="f_KodeSdg" placeholder="Kosongkan jika sama" class="vb-inp">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">13. HPP Lusin</label>
                        <input type="number" name="HppSdg" id="f_HppSdg" placeholder="0" class="vb-inp">
                    </div>
                </div>

                {{-- 14. Harga Lusin --}}
                <div class="vb-row">
                    <label class="vb-lbl">14. Harga Lusin</label>
                    <input type="number" name="HrgSdg" id="f_HrgSdg" placeholder="0" class="vb-inp">
                </div>
            </div>

            {{-- KOLOM KANAN: FIELD 15-24 (SATUAN BIJIAN, HARGA GROSIR, STOK & KONVERSI) --}}
            <div class="vb-col-section">
                {{-- 15. Nama Satuan Bijian & 16. HPP Pcs --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">15. Nama Satuan Bijian <span style="color:red">*</span></label>
                        <input type="text" name="SatKcl" id="f_SatKcl" required placeholder="Pcs" class="vb-inp">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">16. HPP Pcs <span style="color:red">*</span></label>
                        <input type="number" name="Hpp" id="f_Hpp" required placeholder="0" class="vb-inp">
                    </div>
                </div>

                {{-- 17. Harga Pcs --}}
                <div class="vb-row">
                    <label class="vb-lbl">17. Harga Pcs <span style="color:red">*</span></label>
                    <input type="number" name="Harga1" id="f_Harga1" required placeholder="0" class="vb-inp">
                </div>

                {{-- 18. Harga Pcs 2 (Grosir) & 19. Batas Jumlah Harga 2 --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">18. Harga Pcs 2 (Grosir)</label>
                        <input type="number" name="Harga2" id="f_Harga2" placeholder="0" class="vb-inp">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">19. Batas Jumlah Harga 2</label>
                        <input type="number" name="Limit2" id="f_Limit2" placeholder="0" class="vb-inp">
                    </div>
                </div>

                {{-- 20. Harga Pcs 3 (Grosir) & 21. Batas Jumlah Harga 3 --}}
                <div class="vb-row-two">
                    <div class="vb-subcol">
                        <label class="vb-lbl">20. Harga Pcs 3 (Grosir)</label>
                        <input type="number" name="Harga3" id="f_Harga3" placeholder="0" class="vb-inp">
                    </div>
                    <div class="vb-subcol">
                        <label class="vb-lbl">21. Batas Jumlah Harga 3</label>
                        <input type="number" name="Limit3" id="f_Limit3" placeholder="0" class="vb-inp">
                    </div>
                </div>

                {{-- 22. Stock Awal --}}
                <div class="vb-row">
                    <label class="vb-lbl">22. Stock Awal <span style="color:red">*</span></label>
                    <input type="number" name="JmlStock" id="f_JmlStock" required value="0" class="vb-inp" oninput="hitungKonversi()">
                </div>

                {{-- 23. Konversi --}}
                <div class="vb-row">
                    <label class="vb-lbl">23. Konversi</label>
                    <div class="vb-input-suffix">
                        <input type="text" id="f_KonversiHasil" readonly placeholder="0" class="vb-inp readonly">
                        <span class="vb-suffix" id="f_KonversiSatuan">Dus</span>
                    </div>
                </div>

                {{-- 24. Catatan / Keterangan --}}
                <div class="vb-row">
                    <label class="vb-lbl">24. Catatan / Keterangan</label>
                    <input type="text" name="Catatan" id="f_Catatan" placeholder="-" class="vb-inp">
                </div>

                {{-- INFORMASI BANTU RUMUS KONVERSI --}}
                <div class="vb-info-box" id="infoKonversiNote">
                    💡 <strong>Rumus Konversi:</strong> Stock Awal ÷ Jumlah Isi Dus
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS FOOTER --}}
        <div class="vb-form-footer">
            <button type="reset" class="pos-btn" onclick="setTimeout(hitungKonversi, 50)">📄 Reset</button>
            <button type="submit" class="pos-btn primary">✔ Simpan Produk Baru</button>
            <a href="{{ url('/admin/barang') }}" class="pos-btn">↩ Batal / Kembali</a>
        </div>
    </form>
</div>

<script>
// ============================================================
// SEARCHABLE SELECT DROPDOWN COMPONENT
// ============================================================
function initSearchableSelect(wrapperId, placeholderText, nextElementId) {
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;

    const btn = wrapper.querySelector('.vb-select-btn');
    const textSpan = wrapper.querySelector('.vb-select-text');
    const dropdown = wrapper.querySelector('.vb-select-dropdown');
    const searchInput = wrapper.querySelector('.vb-select-search-input');
    const optionsList = wrapper.querySelector('.vb-select-options');
    const items = optionsList.querySelectorAll('.vb-option-item');
    const hiddenInput = wrapper.querySelector('input[type="hidden"]');

    function openDropdown() {
        document.querySelectorAll('.vb-search-select').forEach(w => {
            if (w !== wrapper) {
                w.classList.remove('open');
                const d = w.querySelector('.vb-select-dropdown');
                if (d) d.style.display = 'none';
            }
        });

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

    function selectValue(val) {
        hiddenInput.value = val;
        if (val) {
            textSpan.textContent = val;
            textSpan.classList.remove('placeholder');
        } else {
            textSpan.textContent = placeholderText;
            textSpan.classList.add('placeholder');
        }
        closeDropdown();

        if (nextElementId) {
            const nextEl = document.getElementById(nextElementId);
            if (nextEl) nextEl.focus();
        }
    }

    function filterOptions(query) {
        const q = query.toLowerCase();
        let visibleCount = 0;

        items.forEach(item => {
            const val = item.dataset.value.toLowerCase();
            if (val.includes(q)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        let emptyMsg = optionsList.querySelector('.vb-option-empty');
        if (visibleCount === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('li');
                emptyMsg.className = 'vb-option-empty';
                optionsList.appendChild(emptyMsg);
            }
            emptyMsg.textContent = query ? `Gunakan "${query}"` : 'Tidak ada pilihan';
            emptyMsg.style.display = 'block';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
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
                selectValue(visibleItems[0].dataset.value);
            } else if (this.value.trim() !== '') {
                selectValue(this.value.trim());
            }
        } else if (e.key === 'Escape') {
            closeDropdown();
            btn.focus();
        }
    });

    items.forEach(item => {
        item.addEventListener('click', function () {
            selectValue(this.dataset.value);
        });
    });

    if (hiddenInput.value) {
        textSpan.textContent = hiddenInput.value;
        textSpan.classList.remove('placeholder');
    }
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.vb-search-select')) {
        document.querySelectorAll('.vb-search-select').forEach(w => {
            w.classList.remove('open');
            const d = w.querySelector('.vb-select-dropdown');
            if (d) d.style.display = 'none';
        });
    }
});

// ============================================================
// ENTER KEY NAVIGATION ACCORDING TO FIELD ORDER
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    initSearchableSelect('wrap_NamaSup', '-- Pilih Supplier --', 'btn_Jenis');
    initSearchableSelect('wrap_Jenis', '-- Pilih Kategori --', 'f_IsiBsr');

    const form = document.getElementById('formTambahBarang');
    const inputs = Array.from(form.querySelectorAll('input:not([readonly]):not([type=hidden]), button.vb-select-btn, select'));
    
    // Auto focus Kode Barang agar siap di-scan langsung
    const kodeInp = document.getElementById('f_KodeBrg');
    if (kodeInp) kodeInp.focus();

    inputs.forEach((inp, idx) => {
        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !inp.classList.contains('vb-select-btn') && inp.tagName !== 'TEXTAREA') {
                e.preventDefault();
                if (idx + 1 < inputs.length) {
                    inputs[idx + 1].focus();
                } else {
                    form.submit();
                }
            }
        });
    });

    hitungKonversi();
});

// ============================================================
// KONVERSI OTOMATIS: Stock Awal ÷ Jumlah Isi Dus
// ============================================================
function hitungKonversi() {
    const stokAwal = parseFloat(document.getElementById('f_JmlStock').value) || 0;
    const isiDus   = parseFloat(document.getElementById('f_IsiBsr').value) || 0;
    const isiLusin = parseFloat(document.getElementById('f_IsiSdg').value) || 0;

    const inpHasil = document.getElementById('f_KonversiHasil');
    const spanSat  = document.getElementById('f_KonversiSatuan');
    const note     = document.getElementById('infoKonversiNote');

    if (stokAwal > 0 && isiDus > 0) {
        const dus = (stokAwal / isiDus).toFixed(2).replace(/\.?0+$/, '');
        inpHasil.value = dus;
        spanSat.textContent = 'Dus';
        note.innerHTML = `📦 <strong>${stokAwal} Pcs</strong> ÷ <strong>${isiDus} Pcs/Dus</strong> = <strong>${dus} Dus</strong>`;
    } else if (stokAwal > 0 && isiLusin > 0) {
        const lusin = (stokAwal / isiLusin).toFixed(2).replace(/\.?0+$/, '');
        inpHasil.value = lusin;
        spanSat.textContent = 'Lusin';
        note.innerHTML = `📋 <strong>${stokAwal} Pcs</strong> ÷ <strong>${isiLusin} Pcs/Lusin</strong> = <strong>${lusin} Lusin</strong>`;
    } else {
        inpHasil.value = stokAwal;
        spanSat.textContent = 'Pcs';
        note.innerHTML = `💡 <strong>Rumus Konversi:</strong> Stock Awal ÷ Jumlah Isi Dus`;
    }
}
</script>

@endsection
