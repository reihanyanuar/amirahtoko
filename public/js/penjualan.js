let cart = [];

// 1. Pasang event listener ke semua kartu produk
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', () => {
        const kode = card.dataset.kode;
        const nama = card.dataset.nama;
        const harga = parseFloat(card.dataset.harga);

        tambahKeKeranjang(kode, nama, harga);
    });
});

// 2. Fungsi tambah barang ke keranjang
function tambahKeKeranjang(kode, nama, harga) {
    const itemAda = cart.find(item => item.kode === kode);

    if (itemAda) {
        itemAda.qty += 1;
    } else {
        cart.push({ kode, nama, harga, qty: 1 });
    }

    renderKeranjang();
}

// 3. Fungsi ubah jumlah (dipanggil dari tombol +/-)
function ubahQty(kode, delta) {
    const item = cart.find(i => i.kode === kode);
    if (!item) return;

    item.qty += delta;

    if (item.qty <= 0) {
        cart = cart.filter(i => i.kode !== kode);
    }

    renderKeranjang();
}

// 4. Render ulang tampilan keranjang & total
function renderKeranjang() {
    const cartItemsEl = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');

    if (cart.length === 0) {
        cartItemsEl.innerHTML = '<p class="cart-empty">Belum ada barang dipilih</p>';
        cartTotalEl.textContent = 'Rp 0';
        return;
    }

    let html = '';
    let total = 0;

    cart.forEach(item => {
        const subtotal = item.harga * item.qty;
        total += subtotal;

        html += `
            <div class="cart-item">
                <div>
                    <div class="ci-name">${item.nama}</div>
                    <div class="ci-sub">Rp ${formatRupiah(item.harga)} x ${item.qty}</div>
                </div>
                <div class="qty-ctl">
                    <button onclick="ubahQty('${item.kode}', -1)">-</button>
                    <span>${item.qty}</span>
                    <button onclick="ubahQty('${item.kode}', 1)">+</button>
                </div>
            </div>
        `;
    });

    cartItemsEl.innerHTML = html;
    cartTotalEl.textContent = 'Rp ' + formatRupiah(total);
}

// 5. Fungsi bantu format angka jadi Rupiah (19500 -> 19.500)
function formatRupiah(angka) {
    return angka.toLocaleString('id-ID');
}

document.querySelector('.pay-btn').addEventListener('click', () => {
    if (cart.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }

    fetch('/kasir/penjualan/simpan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ items: cart }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Transaksi berhasil! No Nota: ' + data.no_nota);
                cart = [];
                renderKeranjang();
                location.reload(); // refresh biar stok barang ke-update di tampilan
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan, coba lagi.');
            console.error(err);
        });
});