<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') – TokoKasir</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layouts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <div class="app-layout">

        {{-- SIDEBAR --}}
        <div class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">🛒</div>
                <span>TokoKasir</span>
            </div>

            <div class="sidebar-user-box">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-label">Menu</div>
                <a href="{{ url('/kasir/penjualan') }}"
                   class="nav-item {{ request()->is('kasir/penjualan') ? 'active' : '' }}">
                    <span class="nav-icon">🏪</span> Penjualan
                </a>
                <a href="{{ url('/kasir/stok') }}"
                   class="nav-item {{ request()->is('kasir/stok') ? 'active' : '' }}">
                    <span class="nav-icon">📦</span> Stok Barang
                </a>
                <a href="{{ url('/kasir/shift') }}"
                   class="nav-item {{ request()->is('kasir/shift') ? 'active' : '' }}">
                    <span class="nav-icon">🕐</span> Shift
                </a>
                <a href="{{ url('/kasir/riwayat') }}" class="nav-item {{ request()->is('kasir/riwayat') ? 'active' : '' }}">
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

        {{-- MAIN --}}
        <div class="main-wrapper">
            <div class="topbar">
                <h1>@yield('title')</h1>
                <div class="topbar-right">
                    <span class="topbar-date" id="topbarDate"></span>
                </div>
            </div>
            <div class="main-content">
                @yield('content')
            </div>
        </div>

    </div>

    {{-- Toast notification --}}
    <div class="toast" id="toastMsg">
        <span id="toastIcon">✅</span>
        <span id="toastText"></span>
    </div>

    <script>
        // Topbar clock
        function updateClock() {
            const el = document.getElementById('topbarDate');
            if (!el) return;
            const now = new Date();
            el.textContent = now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' })
                + '  •  ' + now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        }
        updateClock(); setInterval(updateClock, 30000);

        // Toast helper
        function showToast(msg, type='success') {
            const t = document.getElementById('toastMsg');
            const icon = document.getElementById('toastIcon');
            const text = document.getElementById('toastText');
            t.className = 'toast show ' + type;
            icon.textContent = type === 'success' ? '✅' : '❌';
            text.textContent = msg;
            setTimeout(() => t.className = 'toast', 3500);
        }
    </script>
    @stack('scripts')
</body>
</html>