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
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    <style>
        *{box-sizing:border-box}body{margin:0;background:#fafaff;color:#15182b;font-family:"DM Sans",sans-serif}
        .site{max-width:1180px;margin:auto;padding:28px 24px 80px}
        .nav{display:flex;align-items:center;justify-content:space-between;padding:12px 0 32px}
        .brand{font-family:"Plus Jakarta Sans";font-weight:800;font-size:24px}.brand b{color:#5848e8}
        .nav a{color:#5848e8;text-decoration:none;font-weight:700}
        .hero{min-height:420px;border:1px solid #e6e6f0;border-radius:30px;padding:60px;display:flex;align-items:end;overflow:hidden;background:#f0edff url('{{ $umkm->cover ? asset('storage/'.$umkm->cover) : '' }}') center/cover no-repeat}
        .hero-card{background:rgba(255,255,255,.94);backdrop-filter:blur(10px);max-width:650px;padding:38px;border-radius:24px;width:100%}
        .eyebrow{font-size:11px;letter-spacing:.18em;font-weight:800;color:#5848e8}.hero h1{font-family:"Plus Jakarta Sans";font-size:clamp(32px,5vw,64px);line-height:1.05;margin:12px 0}
        .hero-header-row{display:flex;justify-content:space-between;align-items:flex-start;gap:15px}
        .hero p{font-size:18px;line-height:1.7;color:#666b80;margin:10px 0 0}

        /* Tombol Favorit / Like */
        .like-btn {
            background: #ffffff;
            border: 2px solid #e7e7ef;
            padding: 10px 16px;
            border-radius: 50px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #555;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .like-btn:hover {
            border-color: #5848e8;
            color: #5848e8;
        }
        .like-btn.liked {
            background: #fff0f3;
            border-color: #ff3366;
            color: #ff3366;
        }
        .like-icon {
            font-size: 18px;
            transition: transform 0.2s;
        }
        .like-btn:active .like-icon {
            transform: scale(1.3);
        }

        .items{padding-top:70px}.items h2{font-family:"Plus Jakarta Sans";font-size:38px;margin:0 0 28px}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
        .card{background:white;border:1px solid #e7e7ef;border-radius:22px;overflow:hidden}.card img{width:100%;height:210px;object-fit:cover;background:#eee}.card-body{padding:24px}.tag{font-size:11px;color:#5848e8;font-weight:800;letter-spacing:.12em}.card h3{font-family:"Plus Jakarta Sans";font-size:20px;margin:10px 0}.card p{color:#73778a;line-height:1.6}.price{font-weight:800;font-size:18px}
        .contact{margin-top:70px;padding:40px;border-radius:26px;background:#17192b;color:white;display:flex;justify-content:space-between;align-items:center;gap:30px}.contact h2{font-family:"Plus Jakarta Sans";font-size:32px;margin:0}.contact p{color:#bfc2d0}.contact a{display:inline-block;background:#5848e8;color:white;padding:14px 20px;border-radius:12px;text-decoration:none;font-weight:700}
        .umkm-address-link { display: inline-flex; align-items: flex-start; gap: 8px; color: #5848e8; font-weight: 700; line-height: 1.6; text-decoration: none; }
        .umkm-address-link:hover { text-decoration: underline; }
        .umkm-landmark { margin: 6px 0 18px; color: #777b8d; font-size: 14px; }
        @media(max-width:800px){.hero{padding:25px;min-height:500px}.hero-card{padding:25px}.grid{grid-template-columns:1fr}.contact{display:block}.contact a{margin-top:15px}}
    </style>
</head>
<body>
<div class="site">
    <nav class="nav">
        <x-brand-logo />
        <a href="{{ route('home') }}">Dibuat dengan UMKMKita →</a>
    </nav>

    <section class="hero">
        <div class="hero-card">
            <span class="eyebrow">{{ strtoupper($umkm->category) }}</span>
            <div class="hero-header-row">
                <h1>{{ $umkm->name }}</h1>
                <!-- Tombol Favorit Interaktif -->
                <button type="button" id="likeButton" class="like-btn" data-id="{{ $umkm->id }}">
                    <span class="like-icon">❤️</span>
                    <span id="likesCount">{{ $umkm->likes_count ?? 0 }}</span>
                </button>
            </div>
            <p>{{ $umkm->description ?: 'Selamat datang di website resmi usaha kami.' }}</p>
        </div>
    </section>

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
<<<<<<< HEAD
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
=======
                   <div class="card-body">
    <!-- TAMBAHAN BADGE FAVORIT & TERLARIS -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
        <span class="tag">{{ $item->type === 'service' ? 'LAYANAN' : 'PRODUK' }}</span>
        <div style="display: flex; gap: 4px;">
            @if($item->is_favorite)
                <span style="background: #fff8e6; color: #b7791f; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 6px;">⭐ FAVORIT</span>
            @endif

        </div>
    </div>

    <h3>{{ $item->name }}</h3>
    <p>{{ $item->description }}</p>

    @if($item->price !== null)
        <div class="price">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
    @endif

    @if($item->duration)
        <p>Durasi: {{ $item->duration }}</p>
    @endif
</div>
                </article>
            @empty
                <p>Belum ada produk atau layanan yang ditambahkan.</p>
            @endforelse
        </div>
    </section>

    @if($umkm->latitude && $umkm->longitude)
        <section style="margin-top:70px;">
            <span class="eyebrow">LOKASI</span>
            <h2 style="font-family:'Plus Jakarta Sans';font-size:32px;margin:10px 0 20px;">Kunjungi Kami</h2>
            @if($umkm->address)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $umkm->latitude }},{{ $umkm->longitude }}" target="_blank" rel="noopener noreferrer" class="umkm-address-link">
                    <span>📍</span>
                    <span>{{ $umkm->address }}</span>
                    <span>↗</span>
                </a>

                @if($umkm->landmark)
                    <p class="umkm-landmark">
                        Patokan: {{ $umkm->landmark }}
                    </p>
                @endif
            @endif
            <div style="border-radius:22px;overflow:hidden;border:1px solid #e7e7ef;position:relative;">
                <div id="show-map" style="width:100%;height:340px;"></div>
                <a href="https://www.openstreetmap.org/?mlat={{ $umkm->latitude }}&mlon={{ $umkm->longitude }}#map=17/{{ $umkm->latitude }}/{{ $umkm->longitude }}"
                   target="_blank"
                   style="position:absolute;top:16px;left:16px;z-index:1000;background:white;color:#5848e8;font-weight:700;padding:10px 16px;border-radius:12px;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,.12);">
                    Buka di Maps ↗
                </a>
>>>>>>> origin/daffa
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

<<<<<<< HEAD
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
=======
                        <div>
                            <small>HUBUNGI USAHA</small>
                            <h2>Mau pesan atau tanya langsung?</h2>
                            <p>Chat dengan {{ $umkm->name }} melalui WhatsApp.</p>
>>>>>>> origin/daffa
                        </div>
                    </div>

                    <a href="{{ $waLink }}" target="_blank" rel="noopener">Hubungi Sekarang ↗</a>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/umkm-show.js') }}?v={{ filemtime(public_path('js/umkm-show.js')) }}"></script>
<<<<<<< HEAD
=======
</div>

<!-- Script AJAX untuk menangani tombol Like/Favorit tanpa login -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const likeButton = document.getElementById("likeButton");
        const likesCountSpan = document.getElementById("likesCount");

        if (!likeButton) return;

        // Cek status penyimpanan lokal (localStorage) agar tombol tetap merah jika user sudah pernah klik di browser ini
        const umkmId = likeButton.dataset.id;
        const isLikedKey = `umkm_liked_${umkmId}`;

        if (localStorage.getItem(isLikedKey) === "true") {
            likeButton.classList.add("liked");
        }

        likeButton.addEventListener("click", () => {
            const isLiked = likeButton.classList.contains("liked");
            const action = isLiked ? "unlike" : "like";

            // Kirim request ke backend via fetch API
            fetch(`/umkm/${umkmId}/toggle-like`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likesCountSpan.textContent = data.likes_count;
                    if (action === "like") {
                        likeButton.classList.add("liked");
                        localStorage.setItem(isLikedKey, "true");
                    } else {
                        likeButton.classList.remove("liked");
                        localStorage.setItem(isLikedKey, "false");
                    }
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
</script>
>>>>>>> origin/daffa
</body>
</html>
