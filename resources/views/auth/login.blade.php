<x-guest-layout>
    <div class="auth-heading">
        <span class="mobile-brand">UMKM<span>Kita</span></span>
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
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                @endif
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

    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    <a href="{{ route('home') }}" class="back-home">← Kembali ke beranda</a>
</x-guest-layout>
