<x-guest-layout>
    <div class="auth-heading">
        <span class="mobile-brand">UMKM<span>Kita</span></span>
        <h2>Buat akun UMKMKita.</h2>
        <p>Daftar gratis dan mulai buat website untuk usahamu.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="field">
            <label for="name">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama kamu">
            @error('name') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com">
            @error('email') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
            @error('password') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password">
            @error('password_confirmation') <span class="field-error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="auth-submit">Buat Akun Gratis <span>→</span></button>
    </form>

    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    <a href="{{ route('home') }}" class="back-home">← Kembali ke beranda</a>
</x-guest-layout>
