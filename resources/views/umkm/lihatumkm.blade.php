<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Jelajahi UMKM — UMKMKita</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/home.css') }}?v={{ filemtime(public_path('css/home.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/brand.css') }}">
    <link rel="stylesheet" href="{{ asset('css/umkms.css') }}?v={{ filemtime(public_path('css/umkms.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>

<body>

    <header class="navbar" id="navbar">

        <div class="container nav-inner">

            <x-brand-logo :href="route('home')" />

            <nav class="nav-menu">

                <a href="{{ route('home') }}">
                    Beranda
                </a>

                <a href="{{ route('home') . '#cara-kerja' }}">
                    Cara Kerja
                </a>

                <a href="{{ route('umkm.index') }}" class="active">
                    Jelajahi UMKM
                </a>

                <a href="{{ route('radar') }}">
                    Radar UMKM
                </a>

                <a href="{{ route('home') . '#tentang' }}">
                    Tentang
                </a>

            </nav>

            <div class="nav-actions">

            @auth
                <x-profile-menu />
            @else
                <div class="nav-actions">
                    <a href="{{ route('login') }}" class="login-btn">Masuk</a>
                    <a href="{{ route('register') }}" class="nav-button">Buat Website</a>
                </div>
            @endauth
                @auth

                    <a href="{{ route('dashboard') }}" class="nav-button">
                        Kelola UMKM
                    </a>

                @else

                    <a href="{{ route('login') }}" class="login-btn">
                        Masuk
                    </a>

                    <a href="{{ route('register') }}" class="nav-button">
                        Buat Website
                    </a>

                @endauth

            </div>
        </div>

    </header>

    <div class="mobile-menu" id="mobileMenu">

        <a href="{{ route('home') }}">
            Beranda
        </a>

        <a href="{{ route('home') . '#cara-kerja' }}">
            Cara Kerja
        </a>

        <a href="{{ route('umkm.index') }}">
            Jelajahi UMKM
        </a>

        <a href="{{ route('radar') }}">
            Radar UMKM
        </a>

        <a href="{{ route('home') . '#tentang' }}">
            Tentang
        </a>

        <div class="mobile-menu-buttons">

            @auth

        @auth
            <x-profile-menu />
        @else
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="login-btn">Masuk</a>
                <a href="{{ route('register') }}" class="nav-button">Buat Website</a>
            </div>
        @endauth
                <a href="{{ route('dashboard') }}" class="mobile-cta">
                    Kelola UMKM
                </a>

            @else

                <a href="{{ route('login') }}">
                    Masuk
                </a>

                <a href="{{ route('register') }}" class="mobile-cta">
                    Buat Website
                </a>

            @endauth

        </div>

    </div>


    {{-- HERO + FILTER --}}

    <section class="catalog-hero">

        <div class="container">

            <div class="catalog-heading">

                <span class="catalog-label">
                    JELAJAHI UMKM
                </span>

                <h1>
                    Semua UMKM dalam
                    <span>satu tempat.</span>
                </h1>

                <p>
                    Cari, filter, dan temukan usaha lokal favoritmu. Dari kuliner
                    sampai kerajinan, semua sudah tersedia di UMKMKita.
                </p>
            </div>

            <div class="catalog-toolbar">

                <form method="GET" action="{{ route('umkm.index') }}" class="catalog-form" id="catalogForm">

                    <input type="hidden" name="category" id="categoryInput" value="{{ request('category') }}">
                    <input type="hidden" name="type" id="typeInput" value="{{ request('type') }}">

                    <div class="catalog-search">

                        <span class="catalog-search-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <circle cx="11" cy="11" r="7"></circle>
                                <path d="m20 20-3.5-3.5"></path>
                            </svg>
                        </span>

                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama UMKM, kategori, atau lokasi..." autocomplete="off">

                        <a href="{{ route('umkm.index') }}" class="catalog-reset">Reset</a>

                        <button type="submit" class="catalog-search-btn">
                            Cari
                        </button>

                    </div>

                    <div class="catalog-filters">

                        <div class="filter-group">
                            <span class="filter-label">Kategori</span>

                            <div class="chip-row">
                                <button type="button" class="chip {{ !request('category') ? 'active' : '' }}"
                                    data-field="category" data-value="">
                                    Semua
                                </button>

                                @foreach ($categories as $cat)
                                    <button type="button" class="chip {{ request('category') === $cat ? 'active' : '' }}"
                                        data-field="category" data-value="{{ $cat }}">
                                        {{ $cat }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-group">
                            <span class="filter-label">Jenis Usaha</span>

                            <div class="chip-row">
                                <button type="button" class="chip {{ !request('type') ? 'active' : '' }}"
                                    data-field="type" data-value="">
                                    Semua Jenis
                                </button>

                                <button type="button" class="chip {{ request('type') === 'tetap' ? 'active' : '' }}"
                                    data-field="type" data-value="tetap">
                                    Di Tempat
                                </button>

                                <button type="button" class="chip {{ request('type') === 'keliling' ? 'active' : '' }}"
                                    data-field="type" data-value="keliling">
                                    Keliling
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="catalog-heading-actions">
                    <a href="{{ route('radar') }}" class="catalog-map-btn">

                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0Z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>

                        Temukan UMKM di Sekitarmu

                        <span class="catalog-map-btn-arrow"></span>

                    </a>
                </div>
            </div>


            @php
                $filtersActive = request('search') || request('category') || request('type');
            @endphp

            <div class="catalog-meta">

                <span>
                    <strong>{{ $umkms->total() }}</strong>
                    UMKM ditemukan
                    @if ($filtersActive)
                        <em>dari hasil pencarian kamu</em>
                    @endif
                </span>

                @if ($filtersActive)
                    <span class="catalog-meta-note">
                        Hasil difilter · <a href="{{ route('umkm.index') }}">tampilkan semua</a>
                    </span>
                @endif

            </div>

        </div>

    </section>


    {{-- GRID UMKM --}}

    <section class="catalog-section">

        <div class="container">

            <div class="umkm-grid">

                @forelse ($umkms as $umkm)

                    @php
                        $locationSchedule = $umkm->locations
                            ->filter(fn ($loc) => $loc->start_time && $loc->end_time)
                            ->map(fn ($loc) => [
                                'open' => substr((string) $loc->start_time, 0, 5),
                                'close' => substr((string) $loc->end_time, 0, 5),
                            ])
                            ->values()
                            ->toJson();
                    @endphp

                    <article class="umkm-card" data-category="{{ strtolower($umkm->category) }}"
                        data-opening="{{ $umkm->opening_time }}" data-closing="{{ $umkm->closing_time }}"
                        data-manual="{{ $umkm->is_manual_closed ? '1' : '0' }}"
                        data-schedule="{{ $locationSchedule }}">

                        <div class="umkm-image cover-{{ strtolower($umkm->category) }}">

                            @if ($umkm->cover)

                                <img src="{{ asset('storage/' . $umkm->cover) }}" alt="{{ $umkm->name }}">

                            @endif

                            <span class="umkm-initial" aria-hidden="true">
                                {{ strtoupper(mb_substr($umkm->name, 0, 1)) }}
                            </span>

                            <span class="umkm-category">
                                {{ ucfirst($umkm->category) }}
                            </span>

                            <span class="umkm-status" data-status="checking">...</span>

                            <span class="type-pill">
                                {{ $umkm->business_type === 'keliling' ? 'Keliling' : 'Di Tempat' }}
                            </span>

                            <span class="umkm-like" aria-label="Jumlah disukai">
                                ♡ {{ $umkm->likes_count ?? 0 }}
                            </span>

                        </div>

                        <div class="umkm-info">

                            <div>
                                <h3>{{ $umkm->name }}</h3>
                            </div>

                            <span class="location">
                                @if ($umkm->business_type === 'keliling')
                                    {{ $umkm->locations_count }} titik standby
                                @else
                                    {{ $umkm->address ?: 'Lokasi belum ditambahkan' }}
                                @endif
                            </span>

                            <p>
                                {{ \Illuminate\Support\Str::limit($umkm->description ?: 'Belum ada deskripsi UMKM.', 96) }}
                            </p>

                            <a href="{{ route('umkm.show', $umkm->slug) }}">
                                Lihat Website →
                            </a>

                        </div>

                    </article>

                @empty

                    <div class="catalog-empty">

                        <div class="catalog-empty-icon">♡</div>

                        <h3>Belum ada UMKM yang cocok</h3>

                        <p>
                            Coba ubah kata kunci pencarian, pilih kategori lain, atau
                            hapus filter yang sedang aktif.
                        </p>

                        <a href="{{ route('umkm.index') }}" class="catalog-empty-button">
                            Tampilkan Semua UMKM
                        </a>

                    </div>

                @endforelse

            </div>

            {{-- PAGINATION --}}

            @if ($umkms->hasPages())

                @php
                    $current = $umkms->currentPage();
                    $last = $umkms->lastPage();
                    $start = max(2, $current - 2);
                    $end = min($last - 1, $current + 2);
                @endphp

                <nav class="catalog-pagination" aria-label="Paginasi">

                    @if ($umkms->onFirstPage())
                        <span class="page-btn disabled" aria-hidden="true">←</span>
                    @else
                        <a class="page-btn" href="{{ $umkms->previousPageUrl() }}" aria-label="Halaman sebelumnya">←</a>
                    @endif

                    @if ($last > 1)

                        <a class="page-btn {{ $current === 1 ? 'active' : '' }}" href="{{ $umkms->url(1) }}">1</a>

                        @if ($start > 2)
                            <span class="page-btn disabled">…</span>
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            <a class="page-btn {{ $current === $page ? 'active' : '' }}"
                                href="{{ $umkms->url($page) }}">{{ $page }}</a>
                        @endfor

                        @if ($end < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif

                        <a class="page-btn {{ $current === $last ? 'active' : '' }}"
                            href="{{ $umkms->url($last) }}">{{ $last }}</a>

                    @endif

                    @if ($umkms->hasMorePages())
                        <a class="page-btn" href="{{ $umkms->nextPageUrl() }}" aria-label="Halaman berikutnya">→</a>
                    @else
                        <span class="page-btn disabled" aria-hidden="true">→</span>
                    @endif

                </nav>

            @endif

        </div>

    </section>


    {{-- CTA BAWAH --}}

    <section class="cta-section">

        <div class="container">

            <div class="cta">

                <div class="cta-circle circle-left"></div>

                <div class="cta-circle circle-right"></div>

                <span class="section-label">
                    PUNYA UMKM?
                </span>

                <h2>
                    Yuk, bawa usahamu
                    <br>
                    ke dunia digital.
                </h2>

                <p>
                    Buat website usaha kamu secara gratis dan biarkan lebih
                    banyak orang mengenalnya.
                </p>

                <div class="cta-buttons">

                    @auth

                        <a href="{{ route('dashboard') }}" class="cta-primary">
                            Kelola UMKM →
                        </a>

                    @else

                        <a href="{{ route('register') }}" class="cta-primary">
                            Buat Website Gratis →
                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </section>


    @include('umkm.backtotop')


    @include('partials.footer')


<script src="{{ asset('js/umkms.js') }}?v={{ filemtime(public_path('js/umkms.js')) }}"></script>
<script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/umkms.js') }}?v={{ filemtime(public_path('js/umkms.js')) }}"></script>

</body>

</html>
