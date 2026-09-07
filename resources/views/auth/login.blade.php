<x-guest-layout>
    <div class="auth-heading">
        <h2>Selamat datang kembali.</h2>
        <p>Masuk untuk mengelola website UMKM kamu.</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
            @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <div class="field-label-row">
                <label for="password">Password</label>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <label class="remember-row">
            <input type="checkbox" name="remember">
            <span>Ingat saya</span>
        </label>

        <button type="submit" class="auth-submit">Masuk ke Dashboard <span>→</span></button>
    </form>

    <div class="auth-divider">
        <span>atau</span>
    </div>

    <a href="{{ route('auth.google') }}" class="google-auth-button">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M21.6 12.23c0-.71-.06-1.4-.18-2.07H12v3.91h5.38a4.6 4.6 0 0 1-2 3.02v2.51h3.24c1.9-1.75 2.98-4.33 2.98-7.37Z"/>
            <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.4l-3.24-2.51c-.9.6-2.05.96-3.38.96-2.6 0-4.8-1.76-5.59-4.12H3.07v2.59A10 10 0 0 0 12 22Z"/>
            <path fill="#FBBC05" d="M6.41 13.93A6.03 6.03 0 0 1 6.1 12c0-.67.11-1.32.31-1.93V7.48H3.07A10 10 0 0 0 2 12c0 1.62.39 3.15 1.07 4.52l3.34-2.59Z"/>
            <path fill="#EA4335" d="M12 5.95c1.47 0 2.79.5 3.83 1.49l2.87-2.87A9.65 9.65 0 0 0 12 2a10 10 0 0 0-8.93 5.48l3.34 2.59C7.2 7.71 9.4 5.95 12 5.95Z"/>
        </svg>
        Masuk dengan Google
    </a>
    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    <a href="{{ route('home') }}" class="back-home">← Kembali ke beranda</a>
</x-guest-layout>
