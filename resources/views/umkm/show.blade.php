<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $umkm->name }} — UMKMKita</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="{{ asset('css/umkm-show.css') }}?v={{ filemtime(public_path('css/umkm-show.css')) }}">
</head>
<body>
@php
    $isMobile = $umkm->business_type === 'keliling';
    $phone = preg_replace('/\D+/', '', (string) $umkm->phone);

    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }

    $waLink = $phone ? 'https://wa.me/' . $phone : null;
    $locations = $isMobile ? $umkm->locations->values() : collect();

    $mapPoints = $isMobile
        ? $locations->map(fn ($location, $index) => [
            'number' => $index + 1,
            'name' => $location->landmark ?: 'Titik Standby ' . ($index + 1),
            'address' => $location->address,
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
        ])->values()
        : collect($umkm->latitude !== null && $umkm->longitude !== null ? [[
            'number' => null,
            'name' => $umkm->name,
            'address' => $umkm->address,
            'latitude' => (float) $umkm->latitude,
            'longitude' => (float) $umkm->longitude,
        ]] : []);

    $hasProducts = $umkm->items->isNotEmpty();
    $hasPosters = $umkm->posters->isNotEmpty();
    $hasLocation = $isMobile
        ? $locations->isNotEmpty()
        : ($umkm->address || $mapPoints->isNotEmpty());
@endphp

<header class="business-navbar">
    <div class="site-shell navbar-inner">
        <a href="#beranda" class="business-brand">
            @if($umkm->logo)
                <img src="{{ asset('storage/' . $umkm->logo) }}" alt="Logo {{ $umkm->name }}">
            @endif

            <span>{{ $umkm->name }}</span>
        </a>

        <button type="button" class="mobile-menu-button" id="mobileMenuButton" aria-label="Buka navigasi">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="business-navigation" id="businessNavigation">
            <a href="#beranda">Beranda</a>

            @if($hasProducts)
                <a href="#produk">Produk</a>
            @endif

            @if($hasPosters)
                <a href="#katalog">Katalog</a>
            @endif

            @if($hasLocation)
                <a href="#lokasi">Lokasi</a>
            @endif
        </nav>
    </div>
</header>

<main>
    <section class="hero" id="beranda">
        <div class="site-shell hero-layout">
            <div class="hero-content">
                <div class="business-badges">
                    <span>{{ $umkm->category }}</span>
                    <span>{{ $isMobile ? 'UMKM Keliling' : 'UMKM Tetap' }}</span>
                </div>

                <h1>{{ $umkm->name }}</h1>

                @if($umkm->description)
                    <p class="hero-description">{{ $umkm->description }}</p>
                @else
                    <p class="hero-description">Temukan produk, layanan, dan informasi {{ $umkm->name }} di sini.</p>
                @endif

                <div class="hero-actions">
                    @if($waLink)
                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="whatsapp-button">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.4-4.1A8 8 0 1 1 20 11.5Z"></path>
                                <path d="M8.7 8.6c.2 2 2.3 4.2 4.4 4.8.4.1.8-.2 1-.5l.6-.9"></path>
                            </svg>
                            WhatsApp
                        </a>
                    @endif

                    @if($hasLocation)
                        <a href="#lokasi" class="location-button">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                                <circle cx="12" cy="10" r="2.5"></circle>
                            </svg>
                            Lokasi
                        </a>
                    @endif

                   <button type="button" class="btn-favorit-baru {{ $umkm->isLikedByCurrentUser() ? 'liked' : '' }}" data-id="{{ $umkm->id }}" aria-label="Favorit {{ $umkm->name }}">
        <span class="heart-icon">{{ $umkm->isLikedByCurrentUser() ? '♥' : '♡' }}</span>
        <span class="like-count">{{ $umkm->likes_count ?? 0 }}</span>
    </button>
                </div>

                <div class="business-facts">
                    <div>
                        <span>Model Usaha</span>
                        <strong>{{ $isMobile ? 'UMKM Keliling' : 'UMKM Tetap' }}</strong>
                    </div>

                    <div class="business-hours-fact">
                        <span>Jam Operasional</span>

                        @if($isMobile)
                            @if($locations->count() === 1)
                                @php $singleLocation = $locations->first(); @endphp

                                <strong>
                                    @if($singleLocation->start_time || $singleLocation->end_time)
                                        {{ $singleLocation->start_time ? substr($singleLocation->start_time, 0, 5) : '--:--' }}
                                        –
                                        {{ $singleLocation->end_time ? substr($singleLocation->end_time, 0, 5) : '--:--' }}
                                    @else
                                        Belum tersedia
                                    @endif
                                </strong>
                            @elseif($locations->count() > 1)
                                <button type="button" class="hours-dropdown-trigger" id="hoursDropdownTrigger">
                                    <strong>Lihat jadwal</strong>
                                    <i class="hours-chevron"></i>
                                </button>

                                <div class="hours-dropdown" id="hoursDropdown" hidden>
                                    <div class="hours-dropdown-head">
                                        <strong>Jam Operasional</strong>
                                        <small>{{ $locations->count() }} titik standby</small>
                                    </div>

                                    <div class="hours-dropdown-list">
                                        @foreach($locations as $location)
                                            <div class="hours-dropdown-item">
                                                <div>
                                                    <strong>{{ $location->landmark ?: 'Titik Standby ' . $loop->iteration }}</strong>

                                                    @if($location->address)
                                                        <small>{{ $location->address }}</small>
                                                    @endif
                                                </div>

                                                <span>
                                                    @if($location->start_time || $location->end_time)
                                                        {{ $location->start_time ? substr($location->start_time, 0, 5) : '--:--' }}
                                                        –
                                                        {{ $location->end_time ? substr($location->end_time, 0, 5) : '--:--' }}
                                                    @else
                                                        Belum tersedia
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <strong>Belum tersedia</strong>
                            @endif
                        @elseif($umkm->opening_time && $umkm->closing_time)
                            <strong>
                                {{ substr($umkm->opening_time, 0, 5) }}
                                –
                                {{ substr($umkm->closing_time, 0, 5) }}
                            </strong>
                        @else
                            <strong>Belum tersedia</strong>
                        @endif
                    </div>

                    <div>
                        <span>Kategori</span>
                        <strong>{{ $umkm->category }}</strong>
                    </div>
                </div>
            </div>

            <div class="hero-media">
                @if($umkm->cover)
                    <img src="{{ asset('storage/' . $umkm->cover) }}" alt="Foto {{ $umkm->name }}">
                @else
                    <div class="hero-cover-placeholder">
                        <span>{{ $umkm->category }}</span>
                        <strong>{{ $umkm->name }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="product-section" id="produk">
        <div class="site-shell">
            <div class="section-heading product-heading">
                <div>
                    <span class="section-kicker">PRODUK & LAYANAN</span>
                    <h2>Pilihan dari {{ $umkm->name }}</h2>
                </div>

                @if($umkm->items->count() > 3)
                    <div class="carousel-controls">
                        <button type="button" class="carousel-button" id="productPrev" aria-label="Produk sebelumnya">←</button>
                        <button type="button" class="carousel-button" id="productNext" aria-label="Produk berikutnya">→</button>
                    </div>
                @endif
            </div>

            @if($hasProducts)
                <div class="product-carousel-wrap">
                    <div class="product-list {{ $umkm->items->count() > 3 ? 'is-carousel' : '' }}" id="productCarousel">
                        @foreach($umkm->items as $item)
                            <article class="product-card">
                                <div class="product-image">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                                    @else
                                        <div class="product-placeholder">
                                            <span>{{ $item->type === 'service' ? 'Layanan' : 'Produk' }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="product-info">
                                    <div class="product-top">
                                        <span>{{ $item->type === 'service' ? 'Layanan' : 'Produk' }}</span>

                                        @if($item->duration)
                                            <small>{{ $item->duration }}</small>
                                        @endif
                                    </div>

                                    <h3>{{ $item->name }}</h3>

                                    @if($item->description)
                                        <p>{{ $item->description }}</p>
                                    @endif

                                    @if($item->price !== null)
                                        <strong class="product-price">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </strong>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="product-empty">
                    <strong>Produk belum ditambahkan.</strong>
                    <p>Pemilik usaha belum menambahkan produk atau layanan secara manual.</p>
                </div>
            @endif
        </div>
    </section>

    @if($hasPosters)
        <section class="catalog-section" id="katalog">
            <div class="site-shell">
                <div class="section-heading catalog-heading">
                    <div>
                        <span class="section-kicker">POSTER & KATALOG</span>
                        <h2>Menu & Katalog</h2>
                    </div>
                </div>

                <div class="catalog-grid">
                    @foreach($umkm->posters as $poster)
                        <button type="button"
                            class="catalog-card poster-open-button"
                            data-src="{{ asset('storage/' . $poster->image) }}"
                            data-title="{{ $poster->title ?: 'Poster ' . $loop->iteration }}">

                            <div class="catalog-image">
                                <img src="{{ asset('storage/' . $poster->image) }}"
                                    alt="{{ $poster->title ?: 'Poster ' . $umkm->name }}">
                            </div>

                            <div class="catalog-info">
                                <strong>{{ $poster->title ?: 'Poster ' . $loop->iteration }}</strong>
                                <span>Lihat penuh ↗</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

   @if($hasLocation)
        <section class="location-section" id="lokasi">
            <div class="site-shell">
                <div class="section-heading location-heading">
                    <div>
                        <span class="section-kicker">{{ $isMobile ? 'TITIK STANDBY' : 'LOKASI USAHA' }}</span>
                        <h2>{{ $isMobile ? 'Temukan kami di beberapa titik' : 'Temukan lokasi ' . $umkm->name }}</h2>
                    </div>

                    <p>
                        {{ $isMobile
                            ? 'Setiap titik memiliki lokasi dan jadwal standby sendiri.'
                            : 'Gunakan alamat dan peta untuk menuju lokasi usaha.' }}
                    </p>
                </div>

                <div class="location-layout">
                    <div class="location-details">
                        @if($isMobile)
                            <div class="standby-list">
                                @foreach($locations as $location)
                                    <article class="standby-item">
                                        <div class="standby-number">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </div>

                                        <div class="standby-content">
                                            <div class="standby-title">
                                                <h3>{{ $location->landmark ?: 'Titik Standby ' . $loop->iteration }}</h3>

                                                @if($location->start_time || $location->end_time)
                                                    <span>
                                                        {{ $location->start_time ? substr($location->start_time, 0, 5) : '--:--' }}
                                                        –
                                                        {{ $location->end_time ? substr($location->end_time, 0, 5) : '--:--' }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($location->address)
                                                <p>{{ $location->address }}</p>
                                            @endif

                                            @if($location->latitude !== null && $location->longitude !== null)
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $location->latitude }},{{ $location->longitude }}"
                                                    target="_blank" rel="noopener">
                                                    Buka di Google Maps ↗
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="fixed-location">
                                @if($umkm->address)
                                    <div class="fixed-location-item">
                                        <span>Alamat</span>
                                        <strong>{{ $umkm->address }}</strong>
                                    </div>
                                @endif

                                @if($umkm->landmark)
                                    <div class="fixed-location-item">
                                        <span>Patokan</span>
                                        <strong>{{ $umkm->landmark }}</strong>
                                    </div>
                                @endif

                                @if($umkm->opening_time && $umkm->closing_time)
                                    <div class="fixed-location-item">
                                        <span>Jam Operasional</span>
                                        <strong>
                                            {{ substr($umkm->opening_time, 0, 5) }}
                                            –
                                            {{ substr($umkm->closing_time, 0, 5) }}
                                        </strong>
                                    </div>
                                @endif

                                @if($umkm->latitude !== null && $umkm->longitude !== null)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $umkm->latitude }},{{ $umkm->longitude }}"
                                        target="_blank" rel="noopener" class="google-maps-button">
                                        Buka di Google Maps ↗
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($mapPoints->isNotEmpty())
                        <div class="map-container">
                            <div id="businessMap"></div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if($waLink)
        <section class="contact-section">
            <div class="site-shell">
                <div class="contact-banner">
                    <div class="contact-text">
                        <span class="contact-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.4-4.1A8 8 0 1 1 20 11.5Z"></path>
                                <path d="M8.7 8.6c.2 2 2.3 4.2 4.4 4.8.4.1.8-.2 1-.5l.6-.9"></path>
                            </svg>
                        </span>

                        <div>
                            <small>HUBUNGI USAHA</small>
                            <h2>Mau pesan atau tanya langsung?</h2>
                            <p>Chat dengan {{ $umkm->name }} melalui WhatsApp.</p>
                        </div>
                    </div>

                    <a href="{{ $waLink }}" target="_blank" rel="noopener">
                        Hubungi Sekarang ↗
                    </a>
                </div>
            </div>
        </section>
    @endif
</main>

<footer class="business-footer">
    <div class="site-shell footer-inner">
        <div>
            <strong>{{ $umkm->name }}</strong>
            <span>{{ $umkm->category }} · {{ $isMobile ? 'UMKM Keliling' : 'UMKM Tetap' }}</span>
        </div>

        <span>© {{ date('Y') }} {{ $umkm->name }}</span>

        <a href="{{ route('home') }}">Dibuat dengan UMKMKita</a>
    </div>
</footer>

@if($hasPosters)
    <div class="poster-modal" id="posterModal" hidden>
        <div class="poster-modal-backdrop" data-close-poster></div>

        <div class="poster-modal-window">
            <div class="poster-modal-header">
                <strong id="posterModalTitle">Poster</strong>

                <div class="poster-modal-actions">
                    <button type="button" id="posterZoomOut" aria-label="Zoom out">−</button>
                    <span id="posterZoomValue">100%</span>
                    <button type="button" id="posterZoomIn" aria-label="Zoom in">+</button>
                    <button type="button" id="posterZoomReset">Reset</button>
                    <button type="button" class="poster-modal-close" data-close-poster aria-label="Tutup">×</button>
                </div>
            </div>

            <div class="poster-modal-stage" id="posterModalStage">
                <img src="" alt="" id="posterModalImage" draggable="false">
            </div>

            <div class="poster-modal-help">
                Scroll untuk zoom · drag gambar untuk menggeser · Esc untuk menutup
            </div>
        </div>
    </div>
@endif

<script>
    window.umkmMapData = @json($mapPoints);
    window.umkmBusinessType = @json($umkm->business_type);
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const favoritBtn = document.querySelector('.btn-favorit-baru');

        if (favoritBtn) {
            favoritBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (this.classList.contains('is-loading')) {
                    return;
                }

                let umkmId = this.dataset.id;
                let countSpan = this.querySelector('.like-count');
                let iconSpan = this.querySelector('.heart-icon');
                let isLiked = this.classList.contains('liked');
                let action = isLiked ? 'unlike' : 'like';

                this.classList.add('is-loading');
                this.style.opacity = '0.7';
                this.style.cursor = 'not-allowed';

                fetch(`/umkm/${umkmId}/toggle-like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ action: action })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (action === 'like') {
                            this.classList.add('liked');
                            iconSpan.innerText = '♥';
                        } else {
                            this.classList.remove('liked');
                            iconSpan.innerText = '♡';
                        }
                        countSpan.innerText = data.likes_count;
                    }
                })
                .catch(error => console.error('Gagal memproses like:', error))
                .finally(() => {
                    this.classList.remove('is-loading');
                    this.style.opacity = '1';
                    this.style.cursor = 'pointer';
                });
            });
        }
    });
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/umkm-show.js') }}?v={{ filemtime(public_path('js/umkm-show.js')) }}"></script>
</body>
</html>
