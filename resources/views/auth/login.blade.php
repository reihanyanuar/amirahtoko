<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – AmiraToko POS</title>
    <!-- Font Inter dari Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">

            <!-- Header Branding Logo & Judul Toko -->
            <div class="login-brand">
                <div class="brand-logo-icon">🛒</div>
                <h1>AmirahToko</h1>
                <p class="login-sub">Sistem Informasi Kasir Toko</p>
            </div>

            <!-- Pesan Error (jika username/password salah) -->
            @if ($errors->any())
                <div class="login-error">
                    <span class="error-icon">⚠️</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Input Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <span class="input-icon">👤</span>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               placeholder="Masukkan username Anda"
                               value="{{ old('username') }}" 
                               required 
                               autofocus
                               autocomplete="off">
                    </div>
                </div>

                <!-- Input Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <span class="input-icon">🔒</span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               placeholder="Masukkan password Anda"
                               required>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn-login">
                    Masuk ke Sistem ➔
                </button>
            </form>

            <!-- Footer Kasir Info -->
            <div class="login-footer">
                <span>Kasir • Admin • Manajer</span>
            </div>

        </div>
    </div>

</body>
</html>