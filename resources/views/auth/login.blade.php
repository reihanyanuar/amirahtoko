<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Toko Kelontong</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-avatar"></div>
            <h1>Toko Kelontong</h1>
            <p class="login-sub">Login sebagai role anda</p>

            @if ($errors->any())
                <div class="login-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Masuk</button>
            </form>
        </div>
    </div>

</body>
</html>