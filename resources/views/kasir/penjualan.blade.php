@extends('layouts.kasir')

@section('title', 'Penjualan')

@section('content')

    <div class="pos-layout">

        <div class="products-col">
            <div class="search-bar">
                <input type="text" id="cariBarang" placeholder="Cari nama barang atau scan barcode...">
            </div>

            <div class="product-grid">
                @foreach ($barang as $item)
                    <div class="product-card"
                         data-kode="{{ $item->KodeBrg }}"
                         data-nama="{{ $item->NamaBrg }}"
                         data-harga="{{ $item->Harga1 }}">
                        <div class="product-name">{{ $item->NamaBrg }}</div>
                        <div class="product-price">Rp {{ number_format($item->Harga1, 0, ',', '.') }}</div>
                        <div class="product-stock">Stok: {{ $item->JmlStock }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="cart-col">
            <div class="cart-head">Keranjang</div>
            <div class="cart-items" id="cartItems">
                <p class="cart-empty">Belum ada barang dipilih</p>
            </div>
            <div class="cart-summary">
                <div class="sum-row total">
                    <span>Total</span>
                    <span id="cartTotal">Rp 0</span>
                </div>
                <button class="pay-btn">Bayar</button>
            </div>
        </div>

    </div>

@endsection