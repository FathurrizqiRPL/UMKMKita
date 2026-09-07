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

        <form id="createUmkmForm" action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <section class="form-card">
                <span class="step-number">01</span>

                <div class="card-header-title">
                    <h2>Informasi Usaha</h2>
                    <p>Masukkan informasi dasar mengenai usaha kamu.</p>
                </div>

                <div class="form-group">
                    <label for="name">Nama UMKM</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Kedai Senja" required>
                </div>

                <div class="form-group">
                    <label for="slug">URL Website UMKM</label>

                    <div class="slug-input">
                        <span class="slug-prefix">{{ rtrim(config('app.url'), '/') }}/umkm/</span>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="kedai-senja" required>
                    </div>

                    <small class="field-help">
                        Gunakan huruf kecil dan tanda hubung (-) tanpa spasi. Contoh: <code>kedai-senja</code>
                    </small>
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

                    <small class="field-help">
                        Pilih "Di Tempat" untuk satu lokasi tetap, atau "Keliling" untuk beberapa titik standby.
                    </small>
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
                <span class="step-number">02</span>

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
                        <input type="text" id="opening_time" name="opening_time" class="time-24-input"
                            value="{{ old('opening_time') }}" placeholder="07:00"
                            inputmode="numeric" maxlength="5" autocomplete="off">
                        <small class="field-help">Format 24 jam, contoh: 07:00</small>
                    </div>

                    <div class="form-group">
                        <label for="closing_time">Jam Tutup</label>
                        <input type="text" id="closing_time" name="closing_time" class="time-24-input"
                            value="{{ old('closing_time') }}" placeholder="22:00"
                            inputmode="numeric" maxlength="5" autocomplete="off">
                        <small class="field-help">Format 24 jam, contoh: 22:00</small>
                    </div>
                </div>
            </section>

            <section class="form-card" id="mobileBusinessSection">
                <span class="step-number">02</span>

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
                                <label>Pilih Lokasi</label>

                                <div class="mobile-map-search">
                                    <button type="button" class="mobile-search-button mobile-locate-button">
                                        📍 Cari Lokasi Saya
                                    </button>

                                    <input type="text" class="mobile-search-input"
                                        placeholder="Cari nama jalan / daerah..." autocomplete="off">
                                </div>
                            </div>

                            <div class="mobile-map"></div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" class="mobile-address"
                                    name="locations[{{ $index }}][address]"
                                    value="{{ $location['address'] ?? '' }}"
                                    placeholder="Alamat akan muncul setelah memilih titik di peta" readonly>

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

            <section class="form-card poster-manager-card">
                <span class="step-number">03</span>

                <div class="card-header-title">
                    <h2>Poster Menu / Katalog <span class="optional-badge">Opsional</span></h2>
                    <p>Sudah punya poster menu, daftar harga, atau paket layanan? Upload di sini. Kamu tetap bisa menambahkan produk manual dari dashboard.</p>
                </div>

                @php $oldPosters = old('posters', []); @endphp

                <div class="poster-list" data-poster-container data-next-index="{{ count($oldPosters) }}">
                    @foreach ($oldPosters as $index => $poster)
                        <div class="poster-field-card" data-poster-card>
                            <div class="poster-field-head">
                                <div>
                                    <span class="poster-kicker">POSTER {{ $index + 1 }}</span>
                                    <strong class="poster-field-title">Poster / Katalog</strong>
                                </div>

                                <button type="button" class="poster-remove" data-remove-poster>Hapus</button>
                            </div>

                            <div class="poster-field-grid">
                                <div class="form-group">
                                    <label>Judul Poster <span>(opsional)</span></label>
                                    <input type="text" name="posters[{{ $index }}][title]"
                                        value="{{ $poster['title'] ?? '' }}"
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

                                        <input type="file" name="posters[{{ $index }}][image]"
                                            accept="image/jpeg,image/png,image/webp" data-poster-file>
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

            <section class="form-card">
                <span class="step-number">04</span>

                <div class="card-header-title">
                    <h2>Foto Usaha</h2>
                    <p>Tambahkan logo dan foto sampul usaha kamu.</p>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label>Logo UMKM</label>

                        <div class="file-upload-box image-upload-box" data-image-upload>
                            <img data-image-preview data-original="" alt="" hidden>

                            <div class="upload-content" data-upload-content>
                                <div class="upload-icon">↑</div>
                                <strong>Pilih Logo</strong>
                                <span class="file-name">JPG, PNG, atau WEBP · maksimal 2 MB</span>
                            </div>

                            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" data-image-input>

                            <button type="button" class="image-upload-cancel" data-image-cancel hidden>
                                Batal
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Sampul</label>

                        <div class="file-upload-box image-upload-box" data-image-upload>
                            <img data-image-preview data-original="" alt="" hidden>

                            <div class="upload-content" data-upload-content>
                                <div class="upload-icon">↑</div>
                                <strong>Pilih Sampul</strong>
                                <span class="file-name">JPG, PNG, atau WEBP · maksimal 4 MB</span>
                            </div>

                            <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" data-image-input>

                            <button type="button" class="image-upload-cancel" data-image-cancel hidden>
                                Batal
                            </button>
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
                <input type="text" data-poster-title placeholder="Contoh: Menu Minuman, Paket Pijat A">
            </div>

            <div class="form-group">
                <label>Gambar Poster</label>

                <label class="poster-upload">
                    <span class="poster-upload-icon">↑</span>

                    <span>
                        <strong>Pilih gambar</strong>
                        <small data-poster-file-name>JPG, PNG, atau WEBP · maks. 5 MB</small>
                    </span>

                    <input type="file" accept="image/jpeg,image/png,image/webp" data-poster-file>
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
                placeholder="Alamat akan muncul setelah memilih titik di peta" readonly>

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
<script>
    const createUmkmForm = document.getElementById('createUmkmForm');

     if (createUmkmForm) {
        createUmkmForm.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;
            if (event.target.tagName === 'TEXTAREA') return;
            if (event.target.tagName === 'BUTTON') return;

            event.preventDefault();

            const fields = [...createUmkmForm.querySelectorAll(
                'input:not([type="hidden"]):not([type="file"]), select, textarea'
            )].filter(field => !field.disabled && !field.readOnly && field.offsetParent !== null);

            const currentIndex = fields.indexOf(event.target);
            const nextField = fields[currentIndex + 1];

            if (nextField) nextField.focus();
            else event.target.blur();
        });
     }

    const slugInput = document.getElementById('slug');

    slugInput?.addEventListener('input', function () {
        this.value = this.value
            .toLowerCase()
            .trimStart()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')
            .replace(/-+/g, '-');
    });
</script>
@endsection
