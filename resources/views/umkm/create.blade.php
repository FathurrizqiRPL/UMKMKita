@extends('layouts.app')

@section('content')

<div class="form-page">
    <div class="form-container">

        <a href="{{ route('dashboard') }}" class="back-link">← Kembali ke dashboard</a>

        <div class="form-heading">
            <span class="eyebrow">BUAT WEBSITE UMKM</span>
            <h1>Kenalkan usahamu<br><em>kepada lebih banyak orang.</em></h1>
            <p>Isi informasi dasar usaha kamu. Setelah selesai, UMKMKita akan membuat website untuk usahamu.</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <strong>Ada data yang perlu diperiksa.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-section">
                <div class="section-header">
                    <h3>Pilih Template</h3>
                    <span class="step-number">01</span>
                </div>

                <div class="template-navbar">
                    <label class="template-item">
                        <input type="radio" name="template" value="template1" {{ old('template', 'template1') === 'template1' ? 'checked' : '' }}>
                        <div class="template-content">
                            <span class="template-title">Klasik</span>
                            <a href="{{ route('preview.template', 'template1') }}" target="_blank" class="btn-preview">👁️ Lihat Desain</a>
                        </div>
                    </label>

                    <label class="template-item">
                        <input type="radio" name="template" value="template2" {{ old('template') === 'template2' ? 'checked' : '' }}>
                        <div class="template-content">
                            <span class="template-title">Modern</span>
                            <a href="{{ route('preview.template', 'template2') }}" target="_blank" class="btn-preview">👁️ Lihat Desain</a>
                        </div>
                    </label>
                </div>
            </div>

            <section class="form-card">
                <span class="step-number">02</span>

                <div class="card-header-title">
                    <h2>Informasi Usaha</h2>
                    <p>Masukkan informasi dasar mengenai usaha kamu.</p>
                </div>

                <div class="form-group">
                    <label for="name">Nama UMKM</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kedai Senja" required>
                </div>

                <div class="form-group">
                    <label for="category">Kategori UMKM</label>
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
                    <label for="business_type">Cara Usaha Beroperasi</label>
                    <select id="business_type" name="business_type" required>
                        <option value="tetap" @selected(old('business_type', 'tetap') === 'tetap')>Di Tempat</option>
                        <option value="keliling" @selected(old('business_type') === 'keliling')>Keliling</option>
                    </select>
                    <small class="field-help">Pilih "Di Tempat" untuk satu lokasi tetap, atau "Keliling" untuk beberapa titik standby.</small>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Usaha</label>
                    <textarea id="description" name="description" rows="5" placeholder="Ceritakan sedikit mengenai usaha kamu...">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor WhatsApp / Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                </div>
            </section>

            <section class="form-card" id="fixedBusinessSection">
                <span class="step-number">03</span>

                <div class="card-header-title">
                    <h2>Lokasi & Jam Operasional</h2>
                    <p>Tentukan lokasi utama dan jam operasional usaha kamu.</p>
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Pilih lokasi melalui peta di bawah" readonly>
                </div>

                <div class="form-group">
                    <label for="landmark">Patokan <span>(opsional)</span></label>
                    <input type="text" id="landmark" name="landmark" value="{{ old('landmark') }}" placeholder="Contoh: Sebelah Indomaret, depan masjid">
                </div>

                @include('umkm.partials.map-picker', [
                    'addressFieldId' => 'address',
                    'latitude' => old('latitude'),
                    'longitude' => old('longitude'),
                ])

                <div class="operating-hours-grid">
                    <div class="form-group">
                        <label for="opening_time">Jam Buka</label>
                        <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time') }}">
                    </div>

                    <div class="form-group">
                        <label for="closing_time">Jam Tutup</label>
                        <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time') }}">
                    </div>
                </div>
            </section>

            <section class="form-card" id="mobileBusinessSection">
                <span class="step-number">03</span>

                <div class="mobile-business-heading">
                    <span class="eyebrow">UMKM KELILING</span>
                    <h3>Titik Standby</h3>
                    <p>Tambahkan lokasi tempat biasa berhenti dan berjualan. Setiap lokasi dapat memiliki jam yang berbeda.</p>
                </div>

                @php
                    $oldLocations = old('locations', [[
                        'address' => '',
                        'landmark' => '',
                        'latitude' => '',
                        'longitude' => '',
                        'start_time' => '',
                        'end_time' => '',
                    ]]);
                @endphp

                <div id="mobileLocationsContainer">
                    @foreach ($oldLocations as $index => $location)
                        <div class="mobile-location-card" data-location-index="{{ $index }}">
                            <div class="mobile-location-header">
                                <div>
                                    <span class="mobile-location-number">TITIK STANDBY {{ $index + 1 }}</span>
                                    <h3 class="mobile-location-title">Lokasi {{ $index + 1 }}</h3>
                                </div>
                                <button type="button" class="remove-mobile-location">Hapus</button>
                            </div>

                            <div class="form-group">
                                <label>Cari Lokasi</label>
                                <div class="mobile-map-search">
                                    <input type="text" class="mobile-search-input" placeholder="Cari jalan, tempat, atau wilayah..." autocomplete="off">
                                    <button type="button" class="mobile-search-button">Cari</button>
                                </div>
                            </div>

                            <div class="mobile-map"></div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" class="mobile-address" name="locations[{{ $index }}][address]" value="{{ $location['address'] ?? '' }}" placeholder="Alamat akan muncul setelah memilih titik di peta" readonly>
                                <input type="hidden" class="mobile-latitude" name="locations[{{ $index }}][latitude]" value="{{ $location['latitude'] ?? '' }}">
                                <input type="hidden" class="mobile-longitude" name="locations[{{ $index }}][longitude]" value="{{ $location['longitude'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Patokan / Keterangan Rute</label>
                                <input type="text" class="mobile-landmark" name="locations[{{ $index }}][landmark]" value="{{ $location['landmark'] ?? '' }}" placeholder="Contoh: Depan Alfamart, lewat Jalan Melati">
                            </div>

                            <div class="mobile-time-grid">
                                <div class="form-group">
                                    <label>Mulai Berjualan</label>
                                    <input type="time" class="mobile-start-time" name="locations[{{ $index }}][start_time]" value="{{ $location['start_time'] ?? '' }}">
                                </div>

                                <div class="form-group">
                                    <label>Selesai</label>
                                    <input type="time" class="mobile-end-time" name="locations[{{ $index }}][end_time]" value="{{ $location['end_time'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="addMobileLocationButton" class="add-location-button">+ Tambah Lokasi</button>
            </section>

            <section class="form-card">
                <span class="step-number">04</span>

                <div class="card-header-title">
                    <h2>Foto Usaha</h2>
                    <p>Tambahkan logo dan foto sampul usaha kamu.</p>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label>Logo UMKM</label>
                        <div class="file-upload-box">
                            <div class="upload-icon">↑</div>
                            <strong>Pilih Logo</strong>
                            <span class="file-name" id="logo-name">JPG atau PNG, maksimal 2 MB</span>
                            <input type="file" name="logo" accept="image/*" onchange="document.getElementById('logo-name').innerText = this.files[0]?.name || 'JPG atau PNG, maksimal 2 MB'">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Sampul</label>
                        <div class="file-upload-box">
                            <div class="upload-icon">↑</div>
                            <strong>Pilih Sampul</strong>
                            <span class="file-name" id="cover-name">JPG atau PNG, maksimal 4 MB</span>
                            <input type="file" name="cover" accept="image/*" onchange="document.getElementById('cover-name').innerText = this.files[0]?.name || 'JPG atau PNG, maksimal 4 MB'">
                        </div>
                    </div>
                </div>
            </section>

            <div class="form-submit">
                <button class="primary-btn" type="submit">Buat Website Saya <span>→</span></button>
            </div>
        </form>
    </div>
</div>

<template id="mobileLocationTemplate">
    <div class="mobile-location-card">
        <div class="mobile-location-header">
            <div>
                <span class="mobile-location-number"></span>
                <h3 class="mobile-location-title"></h3>
            </div>
            <button type="button" class="remove-mobile-location">Hapus</button>
        </div>

        <div class="form-group">
            <label>Cari Lokasi</label>
            <div class="mobile-map-search">
                <input type="text" class="mobile-search-input" placeholder="Cari jalan, tempat, atau wilayah..." autocomplete="off">
                <button type="button" class="mobile-search-button">Cari</button>
            </div>
        </div>

        <div class="mobile-map"></div>

        <div class="form-group">
            <label>Alamat</label>
            <input type="text" class="mobile-address" placeholder="Alamat akan muncul setelah memilih titik di peta" readonly>
            <input type="hidden" class="mobile-latitude">
            <input type="hidden" class="mobile-longitude">
        </div>

        <div class="form-group">
            <label>Patokan / Keterangan Rute</label>
            <input type="text" class="mobile-landmark" placeholder="Contoh: Depan Alfamart, lewat Jalan Melati">
        </div>

        <div class="mobile-time-grid">
            <div class="form-group">
                <label>Mulai Berjualan</label>
                <input type="time" class="mobile-start-time">
            </div>

            <div class="form-group">
                <label>Selesai</label>
                <input type="time" class="mobile-end-time">
            </div>
        </div>
    </div>
</template>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/umkm-mobile-locations.js') }}?v={{ filemtime(public_path('js/umkm-mobile-locations.js')) }}"></script>

@endsection