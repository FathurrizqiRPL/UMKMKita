@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/create.css') }}">
@endpush

@section('content')
<div class="form-page">
    <div class="form-container">
        <a href="{{ route('dashboard') }}" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Kembali ke dashboard
        </a>

        <div class="form-heading">
            <h1>Kenalkan usahamu<br><em>kepada lebih banyak orang.</em></h1>
            <p>Isi informasi dasar di bawah ini. Setelah disimpan, UMKMKita akan membuat alamat website untuk usahamu.</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <strong>Periksa kembali data kamu:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data" class="main-form">
            @csrf
<!-- 01. Pilih Template -->
<div class="form-section">
    <div class="section-header">
        <h3>Pilih Template</h3>
        <span class="step-badge">01</span>
    </div>
    
    <div class="template-navbar">
        <!-- Template Klasik -->
        <label class="template-item">
            <input type="radio" name="template" value="template1" checked>
            <div class="template-content">
                <span class="template-title">Klasik</span>
                <a href="{{ route('preview.template', 'template1') }}" target="_blank" class="btn-preview">
                    👁️ Lihat Desain
                </a>
            </div>
        </label>

        <!-- Template Modern -->
        <label class="template-item">
            <input type="radio" name="template" value="template2">
            <div class="template-content">
                <span class="template-title">Modern</span>
                <a href="{{ route('preview.template', 'template2') }}" target="_blank" class="btn-preview">
                    👁️ Lihat Desain
                </a>
            </div>
        </label>
    </div>
</div>
            <!-- Section 01 -->
            <section class="form-card">
                <span class="step-number">01</span>
                <div class="card-header-title">
                    <h2>Informasi Usaha</h2>
                </div>

                <div class="form-group">
                    <label for="name">Nama UMKM</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kedai Senja" required>
                </div>

                <div class="form-group">
                    <label for="category">Jenis UMKM</label>
                    <select id="category" name="category" required>
                        <option value="">Pilih kategori</option>
                        <option value="Kuliner" @selected(old('category') === 'Kuliner')>Kuliner</option>
                        <option value="Fashion" @selected(old('category') === 'Fashion')>Fashion</option>
                        <option value="Jasa" @selected(old('category') === 'Jasa')>Jasa</option>
                        <option value="Kerajinan" @selected(old('category') === 'Kerajinan')>Kerajinan</option>
                        <option value="Kecantikan" @selected(old('category') === 'Kecantikan')>Kecantikan</option>
                        <option value="Otomotif" @selected(old('category') === 'Otomotif')>Otomotif</option>
                        <option value="Lainnya" @selected(old('category') === 'Lainnya')>Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="5" placeholder="Ceritakan sedikit tentang usaha kamu...">{{ old('description') }}</textarea>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label for="phone">No. WhatsApp / Telepon</label>
                        <input type="number" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label class="form-group">
                            Alamat dari Maps
                            <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Pilih lokasi melalui peta di bawah" readonly>
                        </label>
                    </div>
                    <label class="form-group">
                        Patokan <span style="font-weight:400;color:#8a8d9c;">(opsional)</span>
                        <input type="text" name="landmark" value="{{ old('landmark') }}" placeholder="Contoh: Sebelah Indomaret, depan masjid">
                    </label>
                </div>
                @include('umkm.partials.map-picker', [
                        'addressFieldId' => 'address',
                        'latitude' => old('latitude'),
                        'longitude' => old('longitude'),
                    ])
            </section>

            <section class="form-card">
                <span class="step-number">02</span>
                <div class="card-header-title">
                    <h2>Foto Usaha</h2>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label>Logo UMKM</label>
                        <div class="file-upload-box">
                            <div class="upload-icon"></div>
                            <strong>Pilih Logo</strong>
                            <span class="file-name" id="logo-name">Format JPG, PNG (Max 2MB)</span>
                            <input type="file" name="logo" accept="image/*" onchange="document.getElementById('logo-name').innerText = this.files[0]?.name || 'Format JPG, PNG (Max 2MB)'">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Sampul</label>
                        <div class="file-upload-box">
                            <div class="upload-icon"></div>
                            <strong>Pilih Sampul</strong>
                            <span class="file-name" id="cover-name">Format JPG, PNG (Max 2MB)</span>
                            <input type="file" name="cover" accept="image/*" onchange="document.getElementById('cover-name').innerText = this.files[0]?.name || 'Format JPG, PNG (Max 2MB)'">
                        </div>
                    </div>
                </div>
            </section>

            <div class="form-submit">
                <button class="primary-btn" type="submit">
                    Buat Website Saya <span>→</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection