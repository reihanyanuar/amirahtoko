let cart = [];
let metodeBayarAktif = 'Tunai';
let pelangganAktif = { kode: 'P0001', nama: 'Customer umum' };

// ==================== PELANGGAN ====================

function setPelanggan(kode, nama, tingkatHrg = 1) {
    pelangganAktif = { kode, nama, tingkatHrg: parseInt(tingkatHrg || 1) };
    const badge = document.getElementById('customerBadge');
    if (badge) badge.textContent = `${kode} - ${nama}`;

    const btnReset = document.getElementById('btnResetPelanggan');
    if (btnReset) btnReset.style.display = (kode === 'P0001') ? 'none' : 'inline-block';

    const dropdown = document.getElementById('customerDropdown');
    if (dropdown) dropdown.style.display = 'none';

    const input = document.getElementById('cariPelangganInput');
    if (input) input.value = '';

    hitungkanHargaTier();
    renderKeranjang();
}

function resetPelanggan() {
    setPelanggan('P0001', 'Customer umum', 1);
}

function initPelangganSearch() {
    const input = document.getElementById('cariPelangganInput');
    const dropdown = document.getElementById('customerDropdown');
    if (!input || !dropdown) return;

    let debounceTimer = null;
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (!q) {
            dropdown.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/kasir/pelanggan/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        dropdown.innerHTML = '<div class="customer-option" style="color:#94A3B8;">Tidak ditemukan</div>';
                    } else {
                        dropdown.innerHTML = data.map(p => `
                            <div class="customer-option" onclick="setPelanggan('${p.KodePlg}', '${p.NamaPlg.replace(/'/g, "\\'")}', ${p.TingkatHrg || 1})">
                                <span class="cust-code">${p.KodePlg}</span>
                                <span class="cust-name">${p.NamaPlg}</span>
                            </div>
                        `).join('');
                    }
                    dropdown.style.display = 'block';
                })
                .catch(() => { dropdown.style.display = 'none'; });
        }, 200);
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
}

// ==================== KERANJANG & HARGA ====================

function tambahKeKeranjang(kode, nama, harga, satuan, isiPcs, meta = {}) {
    const cartKey = kode + '-' + satuan;
    const itemAda = cart.find(item => item.cartKey === cartKey);

    if (itemAda) {
        itemAda.qty += 1;
    } else {
        cart.push({
            cartKey,
            kode,
            nama,
            hargaBase: harga,
            harga: harga,
            satuan,
            isiPcs,
            qty: 1,
            diskon: 0,
            harga2: meta.harga2 || harga,
            limit2: meta.limit2 || 0,
            harga3: meta.harga3 || harga,
            limit3: meta.limit3 || 0,
            hpp: meta.hpp || 0,
            hppBsr: meta.hppBsr || 0,
            hppSdg: meta.hppSdg || 0
        });
    }
    hitungkanHargaTier();
    renderKeranjang();
}

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

    const meta = {
        harga2: parseFloat(card.dataset.harga2 || harga),
        limit2: parseFloat(card.dataset.limit2 || 0),
        harga3: parseFloat(card.dataset.harga3 || harga),
        limit3: parseFloat(card.dataset.limit3 || 0),
        hpp: parseFloat(card.dataset.hpp || 0),
        hppBsr: parseFloat(card.dataset.hppBsr || 0),
        hppSdg: parseFloat(card.dataset.hppSdg || 0)
    };

    tambahKeKeranjang(kode, nama, harga, satuan, isiPcs, meta);
}

// Hitung harga grosir otomatis (Harga2 & Harga3 jika melewati Limit2 / Limit3 ATAU Tingkat Harga Pelanggan)
function hitungkanHargaTier() {
    const tingkatPlg = parseInt(pelangganAktif.tingkatHrg || 1);
    cart.forEach(item => {
        if (item.isiPcs === 1) {
            const totalPcs = item.qty * item.isiPcs;
            if (tingkatPlg === 3 && item.harga3 > 0) {
                item.harga = item.harga3;
            } else if (tingkatPlg === 2 && item.harga2 > 0) {
                item.harga = item.harga2;
            } else if (item.limit3 > 0 && totalPcs >= item.limit3) {
                item.harga = item.harga3;
            } else if (item.limit2 > 0 && totalPcs >= item.limit2) {
                item.harga = item.harga2;
            } else {
                item.harga = item.hargaBase;
            }
        }
    });
}

function prosesScan(barcodeInput) {
    const kode = barcodeInput.trim();
    if (!kode) return;

    const cards = document.querySelectorAll('.product-card');
    let ditemukan = false;

    for (const card of cards) {
        const meta = {
            harga2: parseFloat(card.dataset.harga2 || 0),
            limit2: parseFloat(card.dataset.limit2 || 0),
            harga3: parseFloat(card.dataset.harga3 || 0),
            limit3: parseFloat(card.dataset.limit3 || 0),
            hpp: parseFloat(card.dataset.hpp || 0),
            hppBsr: parseFloat(card.dataset.hppBsr || 0),
            hppSdg: parseFloat(card.dataset.hppSdg || 0)
        };

        if (card.dataset.kode === kode) {
            tambahKeKeranjang(card.dataset.kode, card.dataset.nama, parseFloat(card.dataset.hargaPcs), card.dataset.satKcl, 1, meta);
            ditemukan = true;
            break;
        }
        if (card.dataset.kodeSdg === kode && parseFloat(card.dataset.isiSdg) > 1) {
            tambahKeKeranjang(card.dataset.kode, card.dataset.nama, parseFloat(card.dataset.hargaSdg), card.dataset.satSdg, parseFloat(card.dataset.isiSdg), meta);
            ditemukan = true;
            break;
        }
        if (card.dataset.kodeBsr === kode && parseFloat(card.dataset.isiBsr) > 1) {
            tambahKeKeranjang(card.dataset.kode, card.dataset.nama, parseFloat(card.dataset.hargaBsr), card.dataset.satBsr, parseFloat(card.dataset.isiBsr), meta);
            ditemukan = true;
            break;
        }
    }

    if (!ditemukan) {
        showToast('Barcode "' + kode + '" tidak ditemukan', 'error');
    }
}

function ubahQty(cartKey, delta) {
    const item = cart.find(i => i.cartKey === cartKey);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.cartKey !== cartKey);
    }
    hitungkanHargaTier();
    renderKeranjang();
}

function ubahDiskon(cartKey, val) {
    const item = cart.find(i => i.cartKey === cartKey);
    if (!item) return;
    const d = parseFloat(val) || 0;
    item.diskon = Math.max(0, d);
    renderKeranjang();
}

function getSubtotal(item) {
    const hargaNetto = Math.max(0, item.harga - (item.diskon || 0));
    return hargaNetto * item.qty;
}

function getTotalTransaksi() {
    return cart.reduce((s, i) => s + getSubtotal(i), 0);
}

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
        const subtotal = getSubtotal(item);
        total += subtotal;
        const adaDiskon = item.diskon > 0;
        const hargaNetto = Math.max(0, item.harga - item.diskon);

        html += `
        <div class="cart-item">
            <div style="flex:1;min-width:0">
                <div class="ci-name">${item.nama} <span style="color:#98A2B3;font-weight:400">(${item.satuan})</span></div>
                <div class="ci-sub">
                    Rp ${formatRupiah(item.harga)}
                    ${adaDiskon ? `<span style="color:#DC2626; font-weight:700;">(-Rp ${formatRupiah(item.diskon)})</span>` : ''}
                    × ${item.qty}
                </div>
                <div style="display:flex; align-items:center; gap:4px; margin-top:4px;">
                    <span style="font-size:11.5px; color:#64748B;">Diskon Rp:</span>
                    <input type="number" class="ci-discount-input" value="${item.diskon || 0}" min="0"
                           onchange="ubahDiskon('${item.cartKey}', this.value)"
                           oninput="ubahDiskon('${item.cartKey}', this.value)">
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
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

function formatRupiah(angka) {
    return angka.toLocaleString('id-ID');
}

// ==================== VALIDASI HPP & MODAL BAYAR ====================

function validasiHppModal() {
    for (const item of cart) {
        const hargaNetto = item.harga - (item.diskon || 0);
        let hppMin = item.hpp;
        if (item.satuan !== 'Pcs') {
            if (item.hppBsr > 0 && item.isiPcs > 1) hppMin = item.hppBsr;
            else if (item.hppSdg > 0 && item.isiPcs > 1) hppMin = item.hppSdg;
            else hppMin = item.hpp * item.isiPcs;
        }

        if (hargaNetto < hppMin) {
            showToast(`⚠️ Harga "${item.nama}" setelah diskon (Rp ${formatRupiah(hargaNetto)}) di bawah HPP modal (Rp ${formatRupiah(hppMin)}). Toko akan rugi!`, 'error');
            return false;
        }
    }
    return true;
}

function openModalBayar() {
    if (cart.length === 0) { showToast('Keranjang masih kosong!', 'error'); return; }

    if (!validasiHppModal()) return;

    const total = getTotalTransaksi();
    document.getElementById('mTotal').textContent = 'Rp ' + formatRupiah(total);
    document.getElementById('mItemCount').textContent = cart.reduce((s, i) => s + i.qty, 0) + ' item';
    document.getElementById('uangDiterima').value = '';
    document.getElementById('kembalianBox').style.display = 'none';

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
    const total = getTotalTransaksi();
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
    const total = getTotalTransaksi();

    if (metodeBayarAktif === 'Tunai') {
        const bayar = parseInt(document.getElementById('uangDiterima').value) || 0;
        btn.disabled = bayar < total;
    } else {
        btn.disabled = !document.getElementById('konfirmasiManual').checked;
    }
}

function prosesPayment() {
    if (!validasiHppModal()) return;

    const total = getTotalTransaksi();
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
        body: JSON.stringify({
            items: cart,
            bayar: bayar,
            cara_bayar: metodeBayarAktif,
            kode_plg: pelangganAktif.kode,
            nama_plg: pelangganAktif.nama
        }),
    })
        .then(res => {
            if (!res.ok) return res.json().then(data => { throw new Error(data.message || 'Terjadi kesalahan'); });
            return res.json();
        })
        .then(data => {
            if (data.success) {
                closeModalBayar();
                showToast('✅ Transaksi berhasil! No: ' + data.no_nota, 'success');
                cart = [];
                resetPelanggan();
                renderKeranjang();
                setTimeout(() => location.reload(), 2000);
            }
        })
        .catch((err) => {
            showToast(err.message || 'Terjadi kesalahan, coba lagi.', 'error');
            document.getElementById('btnConfirmBayar').disabled = false;
            document.getElementById('btnConfirmBayar').textContent = '✔ Bayar Sekarang';
        });
}

document.addEventListener('DOMContentLoaded', function () {
    initPelangganSearch();

    const overlay = document.getElementById('modalBayar');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeModalBayar();
        });
    }

    const inputCari = document.getElementById('cariBarang');
    if (inputCari) {
        inputCari.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (typeof prosesScan === 'function') {
                    prosesScan(this.value);
                }
                this.value = '';
            }
        });
    }
});