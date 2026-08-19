<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – AmiraToko Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layouts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="app-layout">
        {{-- SIDEBAR ADMIN --}}
        <div class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">🛒</div>
                <span>AmiraToko</span>
            </div>

            <div class="sidebar-user-box">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu Admin</div>
                <a href="{{ url('/admin/barang/tambah') }}" class="nav-item {{ request()->is('admin/barang/tambah') || request()->is('admin/barang/edit*') ? 'active' : '' }}">
                    <span class="nav-icon">📝</span> Input Barang
                </a>
                <a href="{{ url('/admin/barang') }}" class="nav-item {{ request()->routeIs('*') && request()->is('admin/barang') ? 'active' : '' }}">
                    <span class="nav-icon">📦</span> Stok Barang
                </a>
                <a href="{{ url('/admin/tambah-stok') }}" class="nav-item {{ request()->is('admin/tambah-stok*') ? 'active' : '' }}">
                    <span class="nav-icon">➕</span> Tambah Stok
                </a>
                <a href="{{ url('/admin/kategori') }}" class="nav-item {{ request()->is('admin/kategori*') ? 'active' : '' }}">
                    <span class="nav-icon">🏷️</span> Kategori Produk
                </a>
                <a href="{{ url('/admin/supplier') }}" class="nav-item {{ request()->is('admin/supplier*') ? 'active' : '' }}">
                    <span class="nav-icon">🏬</span> Data Supplier
                </a>
                <a href="{{ url('/admin/riwayat') }}" class="nav-item {{ request()->is('admin/riwayat*') ? 'active' : '' }}">
                    <span class="nav-icon">📜</span> Riwayat Transaksi
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
                    <span class="badge badge-blue">Panel Admin</span>
                </div>
            </div>
            <div class="main-content">
                @if (session('sukses'))
                    <div class="admin-alert success">✅ {{ session('sukses') }}</div>
                @endif
                @if ($errors->any())
                    <div class="admin-alert error">❌ {{ $errors->first() }}</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>