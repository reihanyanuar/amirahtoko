@extends('layouts.kasir')
@section('title', 'Kasir / POS')

@section('content')

<div class="pos-layout">

    {{-- ======================== PRODUK ======================== --}}
    <div class="products-col">

        {{-- Search --}}
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" id="cariBarang" placeholder="Cari nama barang atau scan barcode...">
        </div>

        {{-- Category Filter --}}
        <div class="category-filter" id="categoryFilter">
            <button class="category-chip active" onclick="filterKategori(this, '')">Semua</button>
            @foreach ($kategoriList as $kat)
                <button class="category-chip" onclick="filterKategori(this, '{{ $kat }}')">{{ $kat }}</button>
            @endforeach
        </div>

        {{-- Product Grid --}}
        <div class="product-grid" id="productGrid">
            @foreach ($barang as $item)
                @php $adaMultiSatuan = ($item->IsiBsr > 1 || $item->IsiSdg > 1); @endphp
               <div class="product-card"
                    data-jenis="{{ $item->Jenis }}"
                    @if(!$adaMultiSatuan) onclick="tambahKeKeranjang('{{ $item->KodeBrg }}', '{{ $item->NamaBrg }}', {{ $item->Harga1 }}, '{{ $item->SatKcl }}', 1)" @endif
                    data-kode="{{ $item->KodeBrg }}"
                    data-kode-sdg="{{ $item->KodeSdg }}"
                    data-kode-bsr="{{ $item->KodeBsr }}"
                    data-nama="{{ $item->NamaBrg }}"
                    data-harga-pcs="{{ $item->Harga1 }}"
                    data-sat-kcl="{{ $item->SatKcl }}"
                    data-harga-sdg="{{ $item->HrgSdg }}"
                    data-sat-sdg="{{ $item->SatSdg }}"
                    data-isi-sdg="{{ $item->IsiSdg }}"
                    data-harga-bsr="{{ $item->HrgBsr }}"
                    data-sat-bsr="{{ $item->SatBsr }}"
                    data-isi-bsr="{{ $item->IsiBsr }}">

                    <div class="product-name">{{ $item->NamaBrg }}</div>
                    <div class="product-price">Rp {{ number_format($item->Harga1, 0, ',', '.') }} / {{ $item->SatKcl }}</div>
                    <div class="product-stock">Stok: {{ $item->JmlStock }} {{ $item->SatKcl }}</div>

                    @if ($adaMultiSatuan)
                        <div class="unit-picker">
                            <button type="button" onclick="event.stopPropagation(); tambahDenganSatuan(this, 'kcl')">{{ $item->SatKcl }}</button>
                            @if ($item->IsiSdg > 1)
                                <button type="button" onclick="event.stopPropagation(); tambahDenganSatuan(this, 'sdg')">{{ $item->SatSdg }}</button>
                            @endif
                            @if ($item->IsiBsr > 1)
                                <button type="button" onclick="event.stopPropagation(); tambahDenganSatuan(this, 'bsr')">{{ $item->SatBsr }}</button>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ======================== KERANJANG ======================== --}}
    <div class="cart-col">
        <div class="cart-head">
            <span class="cart-title">🛒 Keranjang</span>
            <span class="cart-count" id="cartCount">0 item</span>
        </div>

        <div class="cart-items" id="cartItems">
            <div class="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <span>Belum ada barang dipilih</span>
            </div>
        </div>

        <div class="cart-summary">
            <div class="sum-row total">
                <span>Total</span>
                <span id="cartTotal">Rp 0</span>
            </div>
            <button class="pay-btn" id="payBtn" onclick="openModalBayar()">
                Bayar
            </button>
        </div>
    </div>

</div>

{{-- ======================== MODAL BAYAR ======================== --}}
<div class="modal-overlay" id="modalBayar">
    <div class="modal-box">
        <div class="modal-title">💳 Konfirmasi Pembayaran</div>

        <div class="modal-info-row">
            <span>Jumlah Item</span>
            <span id="mItemCount">0 item</span>
        </div>
        <div class="modal-info-row total">
            <span>Total Bayar</span>
            <span id="mTotal">Rp 0</span>
        </div>

        {{-- Metode Bayar --}}
        <div class="modal-input-label">Metode Bayar</div>
        <div style="display:flex; gap:8px; margin-bottom:14px;">
            <button type="button" class="pm-tab active" data-method="Tunai" onclick="pilihMetodeBayar(this)">💵 Tunai</button>
            <button type="button" class="pm-tab" data-method="Transfer" onclick="pilihMetodeBayar(this)">🏦 Transfer</button>
            <button type="button" class="pm-tab" data-method="QRIS" onclick="pilihMetodeBayar(this)">📱 QRIS</button>
        </div>

        {{-- Area Tunai --}}
        <div id="areaTunai">
            <div class="modal-input-label">Uang Diterima</div>
            <input type="number" class="modal-input" id="uangDiterima"
                   placeholder="0" oninput="hitungKembalian()">

            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:6px; margin:10px 0 14px;">
                <button type="button" class="btn-cancel" style="padding:6px; font-size:13px;" onclick="isiQuickCash(10000)">10rb</button>
                <button type="button" class="btn-cancel" style="padding:6px; font-size:13px;" onclick="isiQuickCash(20000)">20rb</button>
                <button type="button" class="btn-cancel" style="padding:6px; font-size:13px;" onclick="isiQuickCash(50000)">50rb</button>
                <button type="button" class="btn-cancel" style="padding:6px; font-size:13px;" onclick="isiQuickCash(100000)">100rb</button>
            </div>

            <div class="modal-kembalian" id="kembalianBox" style="display:none">
                <span class="modal-kembalian-label">Kembalian</span>
                <span class="modal-kembalian-val" id="kembalianVal">Rp 0</span>
            </div>
        </div>

        {{-- Area Non-Tunai --}}
        <div id="areaNonTunai" style="display:none">
            <div style="background:#FEF9C3; border:1px solid #FDE68A; border-radius:9px; padding:14px; font-size:14px; color:#92400E; margin-bottom:12px;">
                ⚠️ Pastikan pembayaran sudah diterima sebelum konfirmasi.
            </div>
            <label style="display:flex; align-items:center; gap:10px; font-size:14px; cursor:pointer; font-weight:600; color:#334155;">
                <input type="checkbox" id="konfirmasiManual" onchange="toggleTombolBayar()" style="width:18px;height:18px;">
                Pembayaran sudah diterima
            </label>
        </div>

        <div class="modal-actions" style="margin-top:18px;">
            <button type="button" class="btn-cancel" onclick="closeModalBayar()">Batal</button>
            <button type="button" class="btn-confirm" id="btnConfirmBayar" onclick="prosesPayment()" disabled>✔ Bayar Sekarang</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/penjualan.js') }}"></script>
<script>
/* ---- Filter Kategori ---- */
function filterKategori(btn, kat) {
    document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.product-card').forEach(card => {
        const match = kat === '' || card.dataset.jenis === kat;
        card.style.display = match ? '' : 'none';
    });
}

/* ---- Search ---- */
document.getElementById('cariBarang').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const nama = card.dataset.nama.toLowerCase();
        const kode = card.dataset.kode.toLowerCase();
        card.style.display = (nama.includes(q) || kode.includes(q)) ? '' : 'none';
    });
});
</script>
@endpush