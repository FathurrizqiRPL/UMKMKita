@extends('layouts.app')

@section('content')

<div class="edit-page">
    <div class="edit-container">

        <a href="{{ route('dashboard') }}" class="edit-back-link">← Kembali ke dashboard</a>

        <div class="edit-heading">
            <span class="edit-eyebrow">PENGATURAN WEBSITE</span>
            <h1>Perbarui informasi<br><em>{{ $umkm->name }}.</em></h1>
            <p>Ubah informasi usaha, lokasi, jam operasional, dan tampilan website kamu.</p>
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

        <form action="{{ route('umkm.update') }}" method="POST" enctype="multipart/form-data" class="edit-form">
            @csrf
            @method('PUT')

            <section class="edit-card">
                <div class="edit-card-title">
                    <span>INFORMASI USAHA</span>
                    <h2>Profil UMKM</h2>
                </div>

                <div class="form-group">
                    <label for="name">Nama UMKM</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $umkm->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="category">Kategori UMKM</label>
                    <select id="category" name="category" required>
                        <option value="Kuliner" @selected(old('category', $umkm->category) === 'Kuliner')>Kuliner</option>
                        <option value="Fashion" @selected(old('category', $umkm->category) === 'Fashion')>Fashion</option>
                        <option value="Jasa" @selected(old('category', $umkm->category) === 'Jasa')>Jasa</option>
                        <option value="Kerajinan" @selected(old('category', $umkm->category) === 'Kerajinan')>Kerajinan</option>
                        <option value="Kecantikan" @selected(old('category', $umkm->category) === 'Kecantikan')>Kecantikan</option>
                        <option value="Otomotif" @selected(old('category', $umkm->category) === 'Otomotif')>Otomotif</option>
                        <option value="Lainnya" @selected(old('category', $umkm->category) === 'Lainnya')>Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="business_type">Cara Usaha Beroperasi</label>
                    <select id="business_type" name="business_type" required>
                        <option value="tetap" @selected(old('business_type', $umkm->business_type ?? 'tetap') === 'tetap')>Di Tempat</option>
                        <option value="keliling" @selected(old('business_type', $umkm->business_type ?? 'tetap') === 'keliling')>Keliling</option>
                    </select>
                    <small class="field-help">Pilih "Di Tempat" untuk satu lokasi tetap, atau "Keliling" untuk beberapa titik standby.</small>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Usaha</label>
                    <textarea id="description" name="description" rows="5" placeholder="Ceritakan tentang usaha kamu...">{{ old('description', $umkm->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor WhatsApp / Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $umkm->phone) }}" placeholder="Contoh: 081234567890">
                </div>
            </section>

            <section class="edit-card" id="fixedBusinessSection">
                <div class="edit-card-title">
                    <span>LOKASI USAHA</span>
                    <h2>Lokasi & Jam Operasional</h2>
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $umkm->address) }}" placeholder="Pilih lokasi melalui peta di bawah" readonly>
                </div>

                <div class="form-group">
                    <label for="landmark">Patokan <span>(opsional)</span></label>
                    <input type="text" id="landmark" name="landmark" value="{{ old('landmark', $umkm->landmark) }}" placeholder="Contoh: Sebelah Indomaret, depan masjid">
                </div>

                @include('umkm.partials.map-picker', [
                    'addressFieldId' => 'address',
                    'latitude' => old('latitude', $umkm->latitude),
                    'longitude' => old('longitude', $umkm->longitude),
                ])

                <div class="operating-hours-grid">
                    <div class="form-group">
                        <label for="opening_time">Jam Buka</label>
                        <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time', $umkm->opening_time ? substr($umkm->opening_time, 0, 5) : '') }}">
                    </div>

                    <div class="form-group">
                        <label for="closing_time">Jam Tutup</label>
                        <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time', $umkm->closing_time ? substr($umkm->closing_time, 0, 5) : '') }}">
                    </div>
                </div>
            </section>

            @php
                $savedLocations = $umkm->locations->map(fn ($location) => [
                    'address' => $location->address,
                    'landmark' => $location->landmark,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'start_time' => $location->start_time ? substr($location->start_time, 0, 5) : '',
                    'end_time' => $location->end_time ? substr($location->end_time, 0, 5) : '',
                ])->toArray();

                if (count($savedLocations) === 0) {
                    $savedLocations = [[
                        'address' => '',
                        'landmark' => '',
                        'latitude' => '',
                        'longitude' => '',
                        'start_time' => '',
                        'end_time' => '',
                    ]];
                }

                $editLocations = old('locations', $savedLocations);
            @endphp

            <section class="edit-card" id="mobileBusinessSection">
                <div class="edit-card-title">
                    <span>UMKM KELILING</span>
                    <h2>Titik Standby</h2>
                    <p>Atur lokasi dan jam tempat kamu biasa berhenti untuk berjualan.</p>
                </div>

                <div id="mobileLocationsContainer">
                    @foreach ($editLocations as $index => $location)
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
                                <input type="text" class="mobile-address" name="locations[{{ $index }}][address]" value="{{ $location['address'] ?? '' }}" placeholder="Pilih titik melalui peta" readonly>
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

            <section class="edit-card">
                <div class="edit-card-title">
                    <span>IDENTITAS VISUAL</span>
                    <h2>Logo & Foto Sampul</h2>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label>Logo UMKM</label>

                        @if ($umkm->logo)
                            <div class="current-image">
                                <img src="{{ asset('storage/' . $umkm->logo) }}" alt="Logo {{ $umkm->name }}">
                            </div>
                        @endif

                        <input type="file" name="logo" accept="image/*">
                        <small class="field-help">Kosongkan jika tidak ingin mengganti logo.</small>
                    </div>

                    <div class="form-group">
                        <label>Foto Sampul</label>

                        @if ($umkm->cover)
                            <div class="current-image current-image-cover">
                                <img src="{{ asset('storage/' . $umkm->cover) }}" alt="Sampul {{ $umkm->name }}">
                            </div>
                        @endif

                        <input type="file" name="cover" accept="image/*">
                        <small class="field-help">Kosongkan jika tidak ingin mengganti foto sampul.</small>
                    </div>
                </div>
            </section>

            <div class="edit-submit">
                <button class="edit-submit-button" type="submit">Simpan Perubahan <span>→</span></button>
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
            <input type="text" class="mobile-address" placeholder="Pilih titik melalui peta" readonly>
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