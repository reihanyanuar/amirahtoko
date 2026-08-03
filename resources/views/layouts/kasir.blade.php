<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') - Toko Kelontong</title>
    <link rel="stylesheet" href="{{ asset('css/layouts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <a href="{{ url('/kasir/penjualan') }}" class="nav-item {{ request()->is('kasir/penjualan') ? 'active' : '' }}">Penjualan</a>
                <a href="{{ url('/kasir/stok') }}" class="nav-item {{ request()->is('kasir/stok') ? 'active' : '' }}">Stok Barang</a>
                <a href="{{ url('/kasir/shift') }}" class="nav-item {{ request()->is('kasir/shift') ? 'active' : '' }}">Shift</a>
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
                @yield('content')
            </div>
        </div>

    </div>

    <script src="{{ asset('js/penjualan.js') }}"></script>
</body>
</html>