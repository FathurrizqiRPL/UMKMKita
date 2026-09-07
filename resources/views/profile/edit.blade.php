@extends('layouts.app')

@section('content')

<div class="profile-page">
    <div class="profile-container">
        <a href="{{ route('dashboard') }}" class="profile-back">
            ← Kembali ke dashboard
        </a>

        <div class="profile-heading">
            <span>PENGATURAN AKUN</span>
            <h1>Profil Saya</h1>
            <p>Kelola foto, informasi akun, dan keamanan akun UMKMKita kamu.</p>
        </div>

        <div class="profile-layout">
            <aside class="profile-summary-card">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}"
                        alt="{{ $user->name }}" class="profile-large-photo">
                @else
                    <div class="profile-large-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
                <span class="profile-badge">Akun UMKMKita</span>
            </aside>

            <div class="profile-settings">
                <section class="profile-card">
                    <div class="profile-card-heading">
                        <span>01</span>

                        <div>
                            <h2>Informasi Profil</h2>
                            <p>Perbarui foto dan nama akun kamu.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form">
                        @csrf
                        @method('PATCH')

                        <div class="profile-photo-editor">
                            <div class="profile-photo-preview">
                                @if($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}"
                                        alt="{{ $user->name }}" id="profilePreviewImage">
                                @else
                                    <div class="profile-photo-placeholder" id="profilePhotoPlaceholder">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>

                                    <img src="" alt="Preview foto profil" id="profilePreviewImage" hidden>
                                @endif
                            </div>

                            <div class="profile-photo-actions">
                                <strong>Foto Profil</strong>
                                <small>JPG, PNG, atau WEBP. Maksimal 2 MB.</small>

                                <label for="profilePhotoInput" class="profile-photo-button">
                                    Pilih Foto
                                </label>

                                <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" hidden>
                            </div>
                        </div>

                        @error('profile_photo')
                            <small class="profile-error">{{ $message }}</small>
                        @enderror

                        <label class="profile-field">
                            <span>Nama</span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>

                            @error('name')
                                <small class="profile-error">{{ $message }}</small>
                            @enderror
                        </label>

                        <label class="profile-field">
                            <span>Email</span>
                            <input type="email" value="{{ $user->email }}" readonly>
                            <small>Email akun tidak dapat diubah.</small>
                        </label>

                        <div class="profile-form-action">
                            @if(session('status') === 'profile-updated')
                                <span class="profile-success">
                                    Profil berhasil diperbarui.
                                </span>
                            @endif

                            <button type="submit">Simpan Profil</button>
                        </div>
                    </form>
                </section>

                @if($user->has_password)
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span>02</span>

                            <div>
                                <h2>Ubah Password</h2>
                                <p>Perbarui password untuk menjaga keamanan akun.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}" class="profile-form">
                            @csrf
                            @method('PUT')

                            <label class="profile-field">
                                <span>Password Saat Ini</span>
                                <input type="password" name="current_password">

                                @error('current_password', 'updatePassword')
                                    <small class="profile-error">{{ $message }}</small>
                                @enderror
                            </label>

                            <div class="profile-two-col">
                                <label class="profile-field">
                                    <span>Password Baru</span>
                                    <input type="password" name="password">

                                    @error('password', 'updatePassword')
                                        <small class="profile-error">{{ $message }}</small>
                                    @enderror
                                </label>

                                <label class="profile-field">
                                    <span>Konfirmasi Password</span>
                                    <input type="password" name="password_confirmation">
                                </label>
                            </div>

                            <div class="profile-form-action">
                                @if(session('status') === 'password-updated')
                                    <span class="profile-success">
                                        Password berhasil diperbarui.
                                    </span>
                                @endif

                                <button type="submit">Ubah Password</button>
                            </div>
                        </form>
                    </section>
                @else
                    <section class="profile-card">
                        <div class="profile-card-heading">
                            <span>02</span>

                            <div>
                                <h2>Password</h2>
                                <p>Akun ini menggunakan Google untuk masuk.</p>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
const profilePhotoInput = document.getElementById('profilePhotoInput');
const profilePreviewImage = document.getElementById('profilePreviewImage');
const profilePhotoPlaceholder = document.getElementById('profilePhotoPlaceholder');

profilePhotoInput?.addEventListener('change', () => {
    const file = profilePhotoInput.files[0];
    if (!file) return;

    profilePreviewImage.src = URL.createObjectURL(file);
    profilePreviewImage.hidden = false;

    if (profilePhotoPlaceholder) profilePhotoPlaceholder.hidden = true;
});
</script>

@endsection
