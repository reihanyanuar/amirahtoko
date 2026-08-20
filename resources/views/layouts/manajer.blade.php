<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – AmiraToko Manajer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layouts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manajer.css') }}">
    {{-- Chart.js for Dashboard Charts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-layout">
        {{-- SIDEBAR MANAJER --}}
        <div class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo" style="background: linear-gradient(135deg, #7C3AED, #A78BFA);">👑</div>
                <span>AmiraToko</span>
            </div>

            <div class="sidebar-user-box" style="border-left: 3px solid #8B5CF6;">
                <div class="user-avatar" style="background: linear-gradient(135deg, #7C3AED, #A78BFA);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'MN', 0, 2)) }}
                </div>
                <div>
                    <div class="user-name">{{ auth()->user()->name ?? 'Manajer' }}</div>
                    <div class="user-role" style="color: #A78BFA;">Manajer Toko</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Manajer</div>
                
                <a href="{{ url('/manajer/dashboard') }}" class="nav-item {{ request()->is('manajer/dashboard*') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Dashboard & Statistik
                </a>

                <a href="{{ url('/manajer/akun') }}" class="nav-item {{ request()->is('manajer/akun*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span> Kelola Akun
                </a>

                <a href="{{ url('/manajer/pelanggan') }}" class="nav-item {{ request()->is('manajer/pelanggan*') || request()->is('admin/pelanggan*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span> Data Pelanggan
                </a>

                <a href="{{ url('/manajer/shift') }}" class="nav-item {{ request()->is('manajer/shift*') ? 'active' : '' }}">
                    <span class="nav-icon">⏰</span> Laporan Shift
                </a>

                <a href="{{ url('/manajer/laporan-transaksi') }}" class="nav-item {{ request()->is('manajer/laporan-transaksi*') ? 'active' : '' }}">
                    <span class="nav-icon">📜</span> Laporan Transaksi
                </a>

                <a href="{{ url('/manajer/kasir-aktif') }}" class="nav-item {{ request()->is('manajer/kasir-aktif*') ? 'active' : '' }}">
                    <span class="nav-icon">🟢</span> Kasir Aktif
                </a>

                <div class="nav-label" style="margin-top:14px;">Akses Admin</div>
                <a href="{{ url('/admin/barang') }}" class="nav-item">
                    <span class="nav-icon">📦</span> Stok Barang Toko
                </a>
                <a href="{{ url('/admin/tambah-stok') }}" class="nav-item">
                    <span class="nav-icon">➕</span> Tambah Stok
                </a>
            </nav>

            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">⬅ Keluar</button>
                </form>
            </div>
        </div>

        {{-- MAIN CONTENT AREA --}}
        <div class="main-wrapper">
            <div class="topbar">
                <h1>@yield('title')</h1>
                <div class="topbar-right">
                    <span class="topbar-badge-manajer">👑 Panel Manajer</span>
                    <span class="topbar-date">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
            <div class="main-content">
                @if (session('sukses'))
                    <div class="admin-alert success" style="background:#F0FDF4; border:1px solid #BBF7D0; color:#15803D; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-weight:600;">
                        ✅ {{ session('sukses') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="admin-alert error" style="background:#FEF2F2; border:1px solid #FECACA; color:#DC2626; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-weight:600;">
                        ❌ {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="admin-alert error" style="background:#FEF2F2; border:1px solid #FECACA; color:#DC2626; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-weight:600;">
                        ❌ {{ $errors->first() }}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
