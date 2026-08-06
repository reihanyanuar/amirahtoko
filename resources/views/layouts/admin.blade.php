<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') - Toko Kelontong</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="app-layout">
        <div class="sidebar">
            <div class="sidebar-brand">
                <span class="brand-icon">🛒</span>
                <span>Nama Toko</span>
            </div>

            <div class="sidebar-user-box">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ url('/admin/barang') }}" class="nav-item {{ request()->is('admin/barang') ? 'active' : '' }}">Input Barang</a>
                <a href="{{ url('/admin/kategori') }}" class="nav-item {{ request()->is('admin/kategori') ? 'active' : '' }}">Kategori Produk</a>
                <a href="{{ url('/admin/supplier') }}" class="nav-item {{ request()->is('admin/supplier') ? 'active' : '' }}">Data Supplier</a>
                <a href="{{ url('/admin/riwayat') }}" class="nav-item {{ request()->is('admin/riwayat') ? 'active' : '' }}">Riwayat Transaksi</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="sidebar-logout">
                @csrf
                <button type="submit">Keluar</button>
            </form>
        </div>

        <div class="main-wrapper">
            <div class="topbar">
                <h1>@yield('title')</h1>
            </div>
            <div class="main-content">
                @if (session('sukses'))
                    <div class="admin-alert success">{{ session('sukses') }}</div>
                @endif
                @if ($errors->any())
                    <div class="admin-alert error">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>