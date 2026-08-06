let cart = [];
let metodeBayarAktif = 'Tunai';

// ==================== KERANJANG ====================

// Fungsi tambah barang ke keranjang (sekarang perlu info satuan)
function tambahKeKeranjang(kode, nama, harga, satuan, isiPcs) {
    const cartKey = kode + '-' + satuan; // supaya beda satuan = baris beda di keranjang
    const itemAda = cart.find(item => item.cartKey === cartKey);

    if (itemAda) {
        itemAda.qty += 1;
    } else {
        cart.push({ cartKey, kode, nama, harga, satuan, isiPcs, qty: 1 });
    }
    renderKeranjang();
}

// Dipanggil dari tombol satuan di kartu produk
function tambahDenganSatuan(btn, tipeSatuan) {
    const card = btn.closest('.product-card');
    const kode = card.dataset.kode;
    const nama = card.dataset.nama;

    let harga, satuan, isiPcs;

    if (tipeSatuan === 'kcl') {
        harga = parseFloat(card.dataset.hargaPcs);
        satuan = card.dataset.satKcl;
        isiPcs = 1;
    } else if (tipeSatuan === 'sdg') {
        harga = parseFloat(card.dataset.hargaSdg);
        satuan = card.dataset.satSdg;
        isiPcs = parseFloat(card.dataset.isiSdg);
    } else if (tipeSatuan === 'bsr') {
        harga = parseFloat(card.dataset.hargaBsr);
        satuan = card.dataset.satBsr;
        isiPcs = parseFloat(card.dataset.isiBsr);
    }

    tambahKeKeranjang(kode, nama, harga, satuan, isiPcs);
}

// Fungsi ubah jumlah (tombol +/-)
function ubahQty(cartKey, delta) {
    const item = cart.find(i => i.cartKey === cartKey);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.cartKey !== cartKey);
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
                <div class="ci-name">${item.nama} <span style="color:#98A2B3;font-weight:400">(${item.satuan})</span></div>
                <div class="ci-sub">Rp ${formatRupiah(item.harga)} × ${item.qty}</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <div class="qty-ctl">
                    <button onclick="ubahQty('${item.cartKey}', -1)">−</button>
                    <span>${item.qty}</span>
                    <button onclick="ubahQty('${item.cartKey}', 1)">+</button>
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