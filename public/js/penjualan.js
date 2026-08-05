let cart = [];
let metodeBayarAktif = 'Tunai';

// ==================== KERANJANG ====================

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

// Format angka ke Rupiah (19500 -> 19.500)
function formatRupiah(angka) {
    return angka.toLocaleString('id-ID');
}

// ==================== MODAL BAYAR ====================

function openModalBayar() {
    if (cart.length === 0) { showToast('Keranjang masih kosong!', 'error'); return; }

    const total = cart.reduce((s, i) => s + i.harga * i.qty, 0);
    document.getElementById('mTotal').textContent = 'Rp ' + formatRupiah(total);
    document.getElementById('mItemCount').textContent = cart.reduce((s, i) => s + i.qty, 0) + ' item';
    document.getElementById('uangDiterima').value = '';
    document.getElementById('kembalianBox').style.display = 'none';

    // reset selalu ke Tunai tiap kali modal dibuka
    document.querySelectorAll('.pm-tab').forEach(b => b.classList.remove('active'));
    document.querySelector('.pm-tab[data-method="Tunai"]').classList.add('active');
    metodeBayarAktif = 'Tunai';
    document.getElementById('areaTunai').style.display = 'block';
    document.getElementById('areaNonTunai').style.display = 'none';
    document.getElementById('konfirmasiManual').checked = false;

    toggleTombolBayar();

    document.getElementById('modalBayar').classList.add('show');
    setTimeout(() => document.getElementById('uangDiterima').focus(), 100);
}

function closeModalBayar() {
    document.getElementById('modalBayar').classList.remove('show');
}

function pilihMetodeBayar(btn) {
    document.querySelectorAll('.pm-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    metodeBayarAktif = btn.dataset.method;

    const isTunai = metodeBayarAktif === 'Tunai';
    document.getElementById('areaTunai').style.display = isTunai ? 'block' : 'none';
    document.getElementById('areaNonTunai').style.display = isTunai ? 'none' : 'block';

    if (!isTunai) {
        document.getElementById('konfirmasiManual').checked = false;
    }

    toggleTombolBayar();
}

function isiQuickCash(nominal) {
    document.getElementById('uangDiterima').value = nominal;
    hitungKembalian();
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
        box.style.background = kembalian >= 0 ? '#F0FDF4' : '#FEF2F2';
        document.getElementById('kembalianVal').style.color = kembalian >= 0 ? '#15803D' : '#B91C1C';
    } else {
        box.style.display = 'none';
    }

    toggleTombolBayar();
}

function toggleTombolBayar() {
    const btn = document.getElementById('btnConfirmBayar');
    const total = cart.reduce((s, i) => s + i.harga * i.qty, 0);

    if (metodeBayarAktif === 'Tunai') {
        const bayar = parseInt(document.getElementById('uangDiterima').value) || 0;
        btn.disabled = bayar < total;
    } else {
        btn.disabled = !document.getElementById('konfirmasiManual').checked;
    }
}

function prosesPayment() {
    const total = cart.reduce((s, i) => s + i.harga * i.qty, 0);
    let bayar = total;

    if (metodeBayarAktif === 'Tunai') {
        bayar = parseInt(document.getElementById('uangDiterima').value) || 0;
        if (bayar < total) { showToast('Uang kurang!', 'error'); return; }
    }

    document.getElementById('btnConfirmBayar').disabled = true;
    document.getElementById('btnConfirmBayar').textContent = '⏳ Memproses...';

    fetch('/kasir/penjualan/simpan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ items: cart, bayar: bayar, cara_bayar: metodeBayarAktif }),
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

// Tutup modal kalau klik area luar (overlay)
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('modalBayar');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeModalBayar();
        });
    }
});