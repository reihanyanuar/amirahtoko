@extends('layouts.manajer')
@section('title', 'Kelola Akun Pengguna')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-head-row" style="margin-bottom: 16px;">
    <div>
        <h2 class="page-head-title">👥 Kelola Akun Pengguna</h2>
        <p class="page-head-sub">Atur hak akses, tambah akun kasir/admin baru, atau ganti password</p>
    </div>
    <button type="button" class="btn-manajer-purple" onclick="bukaModalTambah()">
        <span>+</span> Tambah Akun Baru
    </button>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="manajer-filter-toolbar">
    <div class="admin-search-wrap" style="flex: 1; margin-bottom: 0;">
        <span class="search-icon">🔍</span>
        <input type="text" id="cariAkun" placeholder="Cari nama, username, atau role..." oninput="filterAkunTable()">
    </div>
    <div style="font-size: 14px; color: #64748B; font-weight: 600;">
        Total: <strong style="color: #0F172A;">{{ $users->count() }}</strong> Akun Terdaftar
    </div>
</div>

{{-- TABEL USERS --}}
<div class="table-container">
    <table class="table" id="tabelUsers">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>PENGGUNA</th>
                <th>USERNAME</th>
                <th>ROLE / HAK AKSES</th>
                <th>STATUS</th>
                <th>TERDAFTAR SEJAK</th>
                <th style="text-align: center;">AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $idx => $u)
                @php
                    $roleClass = match($u->role) {
                        'manajer' => 'badge-role-manajer',
                        'admin'   => 'badge-role-admin',
                        default   => 'badge-role-kasir',
                    };
                @endphp
                <tr class="user-row" data-search="{{ strtolower($u->name . ' ' . $u->username . ' ' . $u->role) }}">
                    <td style="color: #94A3B8; font-size: 13.5px;">{{ $idx + 1 }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar" style="width: 34px; height: 34px; font-size: 13px; background: {{ $u->role === 'manajer' ? 'linear-gradient(135deg, #7C3AED, #A78BFA)' : ($u->role === 'admin' ? 'linear-gradient(135deg, #2563EB, #60A5FA)' : 'linear-gradient(135deg, #16A34A, #4ADE80)') }};">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #0F172A;">{{ $u->name }}</div>
                                @if ($u->id === auth()->id())
                                    <span style="font-size: 11px; font-weight: 700; color: #7C3AED;">(Akun Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-family: monospace; font-size: 14.5px; color: #475569; font-weight: 600;">
                        {{ $u->username }}
                    </td>
                    <td>
                        <span class="badge-role {{ $roleClass }}">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td>
                        @if ($u->is_active ?? true)
                            <span class="badge-status-active">● Aktif</span>
                        @else
                            <span class="badge-status-inactive">● Dinonaktifkan</span>
                        @endif
                    </td>
                    <td style="color: #64748B; font-size: 13.5px;">
                        {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td style="text-align: center;">
                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                            {{-- Edit --}}
                            <button type="button" class="btn-action-sm" 
                                onclick="bukaModalEdit({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->username) }}', '{{ $u->role }}')"
                                title="Edit Nama / Role">
                                ✏️ Edit
                            </button>

                            {{-- Reset Password --}}
                            <button type="button" class="btn-action-sm" 
                                onclick="bukaModalReset({{ $u->id }}, '{{ addslashes($u->name) }}')"
                                title="Reset Password">
                                🔑 Password
                            </button>

                            {{-- Toggle Status --}}
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ url('/manajer/akun/toggle-status/' . $u->id) }}" style="display: inline;"
                                    onsubmit="return confirm('Yakin ingin {{ ($u->is_active ?? true) ? 'menonaktifkan' : 'mengaktifkan' }} akun \"{{ addslashes($u->name) }}\"?')">
                                    @csrf
                                    @if ($u->is_active ?? true)
                                        <button type="submit" class="btn-action-sm danger" title="Nonaktifkan Akun">
                                            🚫 Nonaktifkan
                                        </button>
                                    @else
                                        <button type="submit" class="btn-action-sm success" title="Aktifkan Akun">
                                            ✔ Aktifkan
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94A3B8;">
                        Belum ada akun pengguna.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ==================== 1. MODAL TAMBAH AKUN ==================== --}}
<div id="modalTambahAkun" class="manajer-modal-backdrop" style="display: none;">
    <div class="manajer-modal-card">
        <div class="modal-header-title">👤 Tambah Akun Pengguna Baru</div>
        <div class="modal-header-sub">Buat akun untuk kasir, admin, atau manajer toko baru</div>

        <form method="POST" action="{{ url('/manajer/akun/simpan') }}">
            @csrf
            <div class="modal-form-group">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="name" required placeholder="cth. Budi Santoso">
            </div>

            <div class="modal-form-group">
                <label>Username Login <span style="color:red">*</span></label>
                <input type="text" name="username" required placeholder="cth. budi01 (tanpa spasi)">
            </div>

            <div class="modal-form-group">
                <label>Password Awal <span style="color:red">*</span></label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
            </div>

            <div class="modal-form-group">
                <label>Hak Akses / Role <span style="color:red">*</span></label>
                <select name="role" required>
                    <option value="kasir">Kasir (Hanya akses halaman Kasir/POS & Shift)</option>
                    <option value="admin">Admin (Akses Input Barang, Stok, Kategori, Supplier)</option>
                    <option value="manajer">Manajer (Akses Penuh: Kelola Akun, Laporan, Statistik)</option>
                </select>
            </div>

            <div class="modal-action-row">
                <button type="button" class="btn-action-sm" onclick="tutupModalTambah()">Batal</button>
                <button type="submit" class="btn-manajer-purple">✔ Simpan Akun Baru</button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== 2. MODAL EDIT AKUN ==================== --}}
<div id="modalEditAkun" class="manajer-modal-backdrop" style="display: none;">
    <div class="manajer-modal-card">
        <div class="modal-header-title">✏️ Edit Akun Pengguna</div>
        <div class="modal-header-sub">Perbarui informasi nama, username, atau hak akses</div>

        <form method="POST" id="formEditAkun" action="">
            @csrf
            <div class="modal-form-group">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="name" id="edit_name" required>
            </div>

            <div class="modal-form-group">
                <label>Username Login <span style="color:red">*</span></label>
                <input type="text" name="username" id="edit_username" required>
            </div>

            <div class="modal-form-group">
                <label>Hak Akses / Role <span style="color:red">*</span></label>
                <select name="role" id="edit_role" required>
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                    <option value="manajer">Manajer</option>
                </select>
            </div>

            <div class="modal-action-row">
                <button type="button" class="btn-action-sm" onclick="tutupModalEdit()">Batal</button>
                <button type="submit" class="btn-manajer-purple">✔ Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== 3. MODAL RESET PASSWORD ==================== --}}
<div id="modalResetPass" class="manajer-modal-backdrop" style="display: none;">
    <div class="manajer-modal-card">
        <div class="modal-header-title">🔑 Reset Password Akun</div>
        <div class="modal-header-sub" id="resetSubText">Masukkan password baru untuk pengguna</div>

        <form method="POST" id="formResetPass" action="">
            @csrf
            <div class="modal-form-group">
                <label>Password Baru <span style="color:red">*</span></label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
            </div>

            <div class="modal-form-group">
                <label>Ulangi Password Baru <span style="color:red">*</span></label>
                <input type="password" name="password_confirmation" required minlength="6" placeholder="Ketik ulang password baru">
            </div>

            <div class="modal-action-row">
                <button type="button" class="btn-action-sm" onclick="tutupModalReset()">Batal</button>
                <button type="submit" class="btn-manajer-purple">✔ Update Password</button>
            </div>
        </form>
    </div>
</div>

<script>
// Filter Search Tabel Akun
function filterAkunTable() {
    const q = document.getElementById('cariAkun').value.toLowerCase();
    document.querySelectorAll('.user-row').forEach(row => {
        const text = row.dataset.search || '';
        row.style.display = text.includes(q) ? '' : 'none';
    });
}

// Modal Tambah
function bukaModalTambah() {
    document.getElementById('modalTambahAkun').style.display = 'flex';
}
function tutupModalTambah() {
    document.getElementById('modalTambahAkun').style.display = 'none';
}

// Modal Edit
function bukaModalEdit(id, name, username, role) {
    document.getElementById('formEditAkun').action = "{{ url('/manajer/akun/update') }}/" + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
    document.getElementById('modalEditAkun').style.display = 'flex';
}
function tutupModalEdit() {
    document.getElementById('modalEditAkun').style.display = 'none';
}

// Modal Reset Password
function bukaModalReset(id, name) {
    document.getElementById('formResetPass').action = "{{ url('/manajer/akun/reset-password') }}/" + id;
    document.getElementById('resetSubText').textContent = "Atur password baru untuk akun: " + name;
    document.getElementById('modalResetPass').style.display = 'flex';
}
function tutupModalReset() {
    document.getElementById('modalResetPass').style.display = 'none';
}

// Tutup modal jika klik luar kartu
document.querySelectorAll('.manajer-modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});
</script>

@endsection