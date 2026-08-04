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
            @php
                $kategoriList = $barang->pluck('Jenis')->unique()->filter()->sort()->values();
            @endphp
            @foreach ($kategoriList as $kat)
                <button class="category-chip" onclick="filterKategori(this, '{{ $kat }}')">{{ $kat }}</button>
            @endforeach
        </div>

        {{-- Product Grid --}}
        <div class="product-grid" id="productGrid">
            @foreach ($barang as $item)
                @php
                    $isLow = $item->JmlStock <= ($item->MinStock ?? 10) && $item->JmlStock > 0;
                    $isEmpty = $item->JmlStock <= 0;
                @endphp
                <div class="product-card"
                     data-kode="{{ $item->KodeBrg }}"
                     data-nama="{{ $item->NamaBrg }}"
                     data-harga="{{ $item->Harga1 }}"
                     data-kategori="{{ $item->Jenis }}"
                     onclick="tambahKeKeranjang('{{ $item->KodeBrg }}', '{{ addslashes($item->NamaBrg) }}', {{ $item->Harga1 }})">
                    <div class="product-card-icon">🛍️</div>
                    <div class="product-name">{{ $item->NamaBrg }}</div>
                    <div class="product-barcode">{{ $item->KodeBrg }}</div>
                    <div class="product-bottom-row">
                        <span class="product-price">Rp {{ number_format($item->Harga1, 0, ',', '.') }}</span>
                        @if($isEmpty)
                            <span class="stock-badge critical">Habis</span>
                        @elseif($isLow)
                            <span class="stock-badge low">{{ $item->JmlStock }} {{ $item->SatKcl }}</span>
                        @else
                            <span class="stock-badge">{{ $item->JmlStock }} {{ $item->SatKcl }}</span>
                        @endif
                    </div>
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

        <div class="modal-input-label">Uang Diterima</div>
        <input type="number" class="modal-input" id="uangDiterima"
               placeholder="0" oninput="hitungKembalian()">

        <div class="modal-kembalian" id="kembalianBox" style="display:none">
            <span class="modal-kembalian-label">Kembalian</span>
            <span class="modal-kembalian-val" id="kembalianVal">Rp 0</span>
        </div>

        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModalBayar()">Batal</button>
            <button class="btn-confirm" id="btnConfirmBayar" onclick="prosesPayment()">✔ Bayar Sekarang</button>
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
        const match = kat === '' || card.dataset.kategori === kat;
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

/* ---- Modal helpers ---- */
function openModalBayar() {
    if (cart.length === 0) { showToast('Keranjang masih kosong!', 'error'); return; }
    const total = cart.reduce((s, i) => s + i.harga * i.qty, 0);
    document.getElementById('mTotal').textContent = 'Rp ' + formatRupiah(total);
    document.getElementById('mItemCount').textContent = cart.reduce((s,i)=>s+i.qty,0) + ' item';
    document.getElementById('uangDiterima').value = '';
    document.getElementById('kembalianBox').style.display = 'none';
    document.getElementById('modalBayar').classList.add('show');
    setTimeout(() => document.getElementById('uangDiterima').focus(), 100);
}

function closeModalBayar() {
    document.getElementById('modalBayar').classList.remove('show');
}

function hitungKembalian() {
    const total = cart.reduce((s, i) => s + i.harga * i.qty, 0);
    const bayar = parseInt(document.getElementById('uangDiterima').value) || 0;
    const kembalian = bayar - total;
    const box = document.getElementById('kembalianBox');
    if (bayar > 0) {
        box.style.display = 'flex';
        document.getElementById('kembalianVal').textContent = 'Rp ' + formatRupiah(Math.max(0, kembalian));
        box.style.borderColor = kembalian >= 0 ? '#86EFAC' : '#FCA5A5';
        box.style.background  = kembalian >= 0 ? '#F0FDF4' : '#FEF2F2';
        document.getElementById('kembalianVal').style.color = kembalian >= 0 ? '#15803D' : '#B91C1C';
    } else {
        box.style.display = 'none';
    }
}

function prosesPayment() {
    const total = cart.reduce((s, i) => s + i.harga * i.qty, 0);
    const bayar = parseInt(document.getElementById('uangDiterima').value) || 0;
    if (bayar < total) { showToast('Uang kurang!', 'error'); return; }

    document.getElementById('btnConfirmBayar').disabled = true;
    document.getElementById('btnConfirmBayar').textContent = '⏳ Memproses...';

    fetch('/kasir/penjualan/simpan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ items: cart, bayar: bayar }),
    })
    .then(res => {
        if (!res.ok) return res.text().then(t => { throw new Error(t); });
        return res.json();
    })
    .then(data => {
        if (data.success) {
            closeModalBayar();
            showToast('✅ Transaksi berhasil! No: ' + data.no_nota, 'success');
            cart = [];
            renderKeranjang();
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan, coba lagi.', 'error');
        document.getElementById('btnConfirmBayar').disabled = false;
        document.getElementById('btnConfirmBayar').textContent = '✔ Bayar Sekarang';
    });
}

// Close modal on overlay click
document.getElementById('modalBayar').addEventListener('click', function(e) {
    if (e.target === this) closeModalBayar();
});
</script>
@endpush