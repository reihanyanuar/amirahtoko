let cart = [];

// Fungsi tambah barang ke keranjang (dipanggil dari onclick di card)
function tambahKeKeranjang(kode, nama, harga) {
    const itemAda = cart.find(item => item.kode === kode);
    if (itemAda) {
        itemAda.qty += 1;
    } else {
        cart.push({ kode, nama, harga, qty: 1 });
    }
    renderKeranjang();
}

// Fungsi ubah jumlah (tombol +/-)
function ubahQty(kode, delta) {
    const item = cart.find(i => i.kode === kode);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.kode !== kode);
    }
    renderKeranjang();
}

// Render ulang tampilan keranjang & total
function renderKeranjang() {
    const cartItemsEl = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');
    const cartCountEl = document.getElementById('cartCount');

    const totalQty = cart.reduce((s, i) => s + i.qty, 0);
    if (cartCountEl) cartCountEl.textContent = totalQty + ' item';

    if (cart.length === 0) {
        cartItemsEl.innerHTML = `
            <div class="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <span>Belum ada barang dipilih</span>
            </div>`;
        if (cartTotalEl) cartTotalEl.textContent = 'Rp 0';
        return;
    }

    let html = '';
    let total = 0;

    cart.forEach(item => {
        const subtotal = item.harga * item.qty;
        total += subtotal;

        html += `
            <div class="cart-item">
                <div style="flex:1;min-width:0">
                    <div class="ci-name">${item.nama}</div>
                    <div class="ci-sub">Rp ${formatRupiah(item.harga)} × ${item.qty}</div>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="qty-ctl">
                        <button onclick="ubahQty('${item.kode}', -1)">−</button>
                        <span>${item.qty}</span>
                        <button onclick="ubahQty('${item.kode}', 1)">+</button>
                    </div>
                    <div class="ci-price">Rp ${formatRupiah(subtotal)}</div>
                </div>
            </div>`;
    });

    cartItemsEl.innerHTML = html;
    if (cartTotalEl) cartTotalEl.textContent = 'Rp ' + formatRupiah(total);
}

// Format angka ke Rupiah (19500 → 19.500)
function formatRupiah(angka) {
    return angka.toLocaleString('id-ID');
}