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

                    <small class="field-help">
                        Pilih "Di Tempat" untuk satu lokasi tetap, atau "Keliling" untuk beberapa titik standby.
                    </small>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi Usaha</label>
                    <textarea id="description" name="description" rows="5" placeholder="Ceritakan tentang usaha kamu...">{{ old('description', $umkm->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Nomor WhatsApp / Telepon</label>
                    <input type="text" id="phone" name="phone"
                        value="{{ old('phone', $umkm->phone) }}"
                        placeholder="Contoh: 081234567890">
                </div>
            </section>

            <section class="edit-card" id="fixedBusinessSection">
                <div class="edit-card-title">
                    <span>LOKASI USAHA</span>
                    <h2>Lokasi & Jam Operasional</h2>
                </div>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <input type="text" id="address" name="address"
                        value="{{ old('address', $umkm->address) }}"
                        placeholder="Pilih lokasi melalui peta di bawah" readonly>
                </div>

                <div class="form-group">
                    <label for="landmark">Patokan <span>(opsional)</span></label>
                    <input type="text" id="landmark" name="landmark"
                        value="{{ old('landmark', $umkm->landmark) }}"
                        placeholder="Contoh: Sebelah Indomaret, depan masjid">
                </div>

                @include('umkm.partials.map-picker', [
                    'addressFieldId' => 'address',
                    'latitude' => old('latitude', $umkm->latitude),
                    'longitude' => old('longitude', $umkm->longitude),
                ])

                <div class="operating-hours-grid">
                    <div class="form-group">
                        <label for="opening_time">Jam Buka</label>

                        <input type="text" id="opening_time" name="opening_time" class="time-24-input"
                            value="{{ old('opening_time', $umkm->opening_time ? substr($umkm->opening_time, 0, 5) : '') }}"
                            placeholder="07:00" inputmode="numeric"
                            maxlength="5" autocomplete="off">

                        <small class="field-help">Format 24 jam, contoh: 07:00</small>
                    </div>

                    <div class="form-group">
                        <label for="closing_time">Jam Tutup</label>

                        <input type="text" id="closing_time" name="closing_time" class="time-24-input"
                            value="{{ old('closing_time', $umkm->closing_time ? substr($umkm->closing_time, 0, 5) : '') }}"
                            placeholder="22:00" inputmode="numeric"
                            maxlength="5" autocomplete="off">

                        <small class="field-help">Format 24 jam, contoh: 22:00</small>
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
                                <label>Pilih Lokasi</label>

                                <div class="mobile-map-search">
                                    <input type="text" class="mobile-search-input"
                                        placeholder="Cari jalan, tempat, atau wilayah..." autocomplete="off">

                                    <button type="button" class="mobile-search-button">Cari</button>

                                    <button type="button" class="mobile-search-button mobile-locate-button">
                                        Cari Lokasi Saya
                                    </button>
                                </div>
                            </div>

                            <div class="mobile-map"></div>

                            <div class="form-group">
                                <label>Alamat</label>

                                <input type="text" class="mobile-address"
                                    name="locations[{{ $index }}][address]"
                                    value="{{ $location['address'] ?? '' }}"
                                    placeholder="Pilih titik melalui peta" readonly>

                                <input type="hidden" class="mobile-latitude"
                                    name="locations[{{ $index }}][latitude]"
                                    value="{{ $location['latitude'] ?? '' }}">

                                <input type="hidden" class="mobile-longitude"
                                    name="locations[{{ $index }}][longitude]"
                                    value="{{ $location['longitude'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Patokan / Keterangan Rute</label>

                                <input type="text" class="mobile-landmark"
                                    name="locations[{{ $index }}][landmark]"
                                    value="{{ $location['landmark'] ?? '' }}"
                                    placeholder="Contoh: Depan Alfamart, lewat Jalan Melati">
                            </div>

                            <div class="mobile-time-grid">
                                <div class="form-group">
                                    <label>Mulai Berjualan</label>

                                    <input type="text" class="mobile-start-time time-24-input"
                                        name="locations[{{ $index }}][start_time]"
                                        value="{{ $location['start_time'] ?? '' }}"
                                        placeholder="07:00" inputmode="numeric"
                                        maxlength="5" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    <label>Selesai</label>

                                    <input type="text" class="mobile-end-time time-24-input"
                                        name="locations[{{ $index }}][end_time]"
                                        value="{{ $location['end_time'] ?? '' }}"
                                        placeholder="17:00" inputmode="numeric"
                                        maxlength="5" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="addMobileLocationButton" class="add-location-button">
                    + Tambah Lokasi
                </button>
            </section>

            <section class="edit-card poster-manager-card">
                <div class="edit-card-title">
                    <span>POSTER MENU / KATALOG</span>
                    <h2>Poster yang Sudah Kamu Punya</h2>
                    <p>Upload menu makanan, daftar minuman, price list, paket layanan, atau katalog. Produk manual di dashboard tetap bisa dipakai bersamaan.</p>
                </div>

                @if ($umkm->posters->isNotEmpty())
                    <div class="existing-poster-grid">
                        @foreach ($umkm->posters as $poster)
                            <article class="existing-poster-card" data-existing-poster>
                                <div class="existing-poster-preview">
                                    <img src="{{ asset('storage/' . $poster->image) }}"
                                        alt="{{ $poster->title ?: 'Poster ' . $umkm->name }}">
                                </div>

                                <div class="existing-poster-fields">
                                    <div class="form-group">
                                        <label>Judul Poster <span>(opsional)</span></label>
                                        <input type="text"
                                            name="existing_posters[{{ $poster->id }}][title]"
                                            value="{{ old('existing_posters.' . $poster->id . '.title', $poster->title) }}"
                                            placeholder="Contoh: Menu Minuman">
                                    </div>

                                    <div class="form-group">
                                        <label>Ganti Gambar <span>(opsional)</span></label>

                                        <label class="poster-upload compact-poster-upload">
                                            <span class="poster-upload-icon">↑</span>

                                            <span>
                                                <strong>Pilih gambar baru</strong>
                                                <small data-poster-file-name>Kosongkan jika tidak ingin mengganti</small>
                                            </span>

                                            <input type="file"
                                                name="existing_posters[{{ $poster->id }}][image]"
                                                accept="image/jpeg,image/png,image/webp"
                                                data-poster-file>
                                        </label>
                                    </div>

                                    <input type="checkbox"
                                        name="delete_posters[]"
                                        value="{{ $poster->id }}"
                                        class="poster-delete-input"
                                        data-delete-poster-input>

                                    <button type="button"
                                        class="poster-delete-button"
                                        data-delete-existing-poster>
                                        Hapus Poster
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="poster-empty-state">
                        Belum ada poster atau katalog yang diupload.
                    </div>
                @endif

                @php $newPosters = old('posters', []); @endphp

                <div class="poster-list edit-poster-list"
                    data-poster-container
                    data-next-index="{{ count($newPosters) }}">

                    @foreach ($newPosters as $index => $poster)
                        <div class="poster-field-card" data-poster-card>
                            <div class="poster-field-head">
                                <div>
                                    <span class="poster-kicker">POSTER BARU {{ $index + 1 }}</span>
                                    <strong class="poster-field-title">Poster / Katalog</strong>
                                </div>

                                <button type="button" class="poster-remove" data-remove-poster>
                                    Hapus
                                </button>
                            </div>

                            <div class="poster-field-grid">
                                <div class="form-group">
                                    <label>Judul Poster <span>(opsional)</span></label>
                                    <input type="text"
                                        name="posters[{{ $index }}][title]"
                                        value="{{ $poster['title'] ?? '' }}"
                                        placeholder="Contoh: Paket Pijat B">
                                </div>

                                <div class="form-group">
                                    <label>Gambar Poster</label>

                                    <label class="poster-upload">
                                        <span class="poster-upload-icon">↑</span>

                                        <span>
                                            <strong>Pilih gambar</strong>
                                            <small data-poster-file-name>JPG, PNG, atau WEBP · maks. 5 MB</small>
                                        </span>

                                        <input type="file"
                                            name="posters[{{ $index }}][image]"
                                            accept="image/jpeg,image/png,image/webp"
                                            data-poster-file>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="add-poster-button" data-add-poster>
                    + Tambah Poster
                </button>
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
                                <img src="{{ asset('storage/' . $umkm->logo) }}"
                                    alt="Logo {{ $umkm->name }}">
                            </div>
                        @endif

                        <input type="file" name="logo" accept="image/*">
                        <small class="field-help">Kosongkan jika tidak ingin mengganti logo.</small>
                    </div>

                    <div class="form-group">
                        <label>Foto Sampul</label>

                        @if ($umkm->cover)
                            <div class="current-image current-image-cover">
                                <img src="{{ asset('storage/' . $umkm->cover) }}"
                                    alt="Sampul {{ $umkm->name }}">
                            </div>
                        @endif

                        <input type="file" name="cover" accept="image/*">
                        <small class="field-help">Kosongkan jika tidak ingin mengganti foto sampul.</small>
                    </div>
                </div>
            </section>

            <div class="edit-submit">
                <button class="edit-submit-button" type="submit">
                    Simpan Perubahan <span>→</span>
                </button>
            </div>
        </form>
    </div>
</div>

<template id="posterFieldTemplate">
    <div class="poster-field-card" data-poster-card>
        <div class="poster-field-head">
            <div>
                <span class="poster-kicker"></span>
                <strong class="poster-field-title">Poster / Katalog</strong>
            </div>

            <button type="button" class="poster-remove" data-remove-poster>Hapus</button>
        </div>

        <div class="poster-field-grid">
            <div class="form-group">
                <label>Judul Poster <span>(opsional)</span></label>
                <input type="text" data-poster-title
                    placeholder="Contoh: Menu Minuman, Paket Pijat A">
            </div>

            <div class="form-group">
                <label>Gambar Poster</label>

                <label class="poster-upload">
                    <span class="poster-upload-icon">↑</span>

                    <span>
                        <strong>Pilih gambar</strong>
                        <small data-poster-file-name>JPG, PNG, atau WEBP · maks. 5 MB</small>
                    </span>

                    <input type="file"
                        accept="image/jpeg,image/png,image/webp"
                        data-poster-file>
                </label>
            </div>
        </div>
    </div>
</template>

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
            <label>Pilih Lokasi</label>

            <div class="mobile-map-search">
                <input type="text" class="mobile-search-input"
                    placeholder="Cari jalan, tempat, atau wilayah..." autocomplete="off">

                <button type="button" class="mobile-search-button">Cari</button>

                <button type="button" class="mobile-search-button mobile-locate-button">
                    Cari Lokasi Saya
                </button>
            </div>
        </div>

        <div class="mobile-map"></div>

        <div class="form-group">
            <label>Alamat</label>

            <input type="text" class="mobile-address"
                placeholder="Pilih titik melalui peta" readonly>

            <input type="hidden" class="mobile-latitude">
            <input type="hidden" class="mobile-longitude">
        </div>

        <div class="form-group">
            <label>Patokan / Keterangan Rute</label>

            <input type="text" class="mobile-landmark"
                placeholder="Contoh: Depan Alfamart, lewat Jalan Melati">
        </div>

        <div class="mobile-time-grid">
            <div class="form-group">
                <label>Mulai Berjualan</label>

                <input type="text" class="mobile-start-time time-24-input"
                    placeholder="07:00" inputmode="numeric"
                    maxlength="5" autocomplete="off">
            </div>

            <div class="form-group">
                <label>Selesai</label>

                <input type="text" class="mobile-end-time time-24-input"
                    placeholder="17:00" inputmode="numeric"
                    maxlength="5" autocomplete="off">
            </div>
        </div>
    </div>
</template>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/umkm-mobile-locations.js') }}?v={{ filemtime(public_path('js/umkm-mobile-locations.js')) }}"></script>
<script src="{{ asset('js/umkm-posters.js') }}?v={{ filemtime(public_path('js/umkm-posters.js')) }}"></script>

@endsection
