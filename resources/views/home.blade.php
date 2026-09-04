<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>UMKMKita - Digitalisasi UMKM Indonesia</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>

<body>

<header class="navbar" id="navbar">

    <div class="container nav-inner">

        <a href="{{ route('home') }}" class="logo">
            <span class="logo-mark">U</span>
            <span>UMKM<span class="logo-purple">Kita</span></span>
        </a>

        <nav class="nav-menu">

            <a href="#beranda" class="active">
                Beranda
            </a>

            <a href="#cara-kerja">
                Cara Kerja
            </a>

            <a href="#umkm">
                Jelajahi UMKM
            </a>

            <a href="{{ route('radar') }}">
                Radar UMKM
            </a>

            <a href="#tentang">
                Tentang
            </a>

        </nav>

        <div class="nav-actions">

            @auth
                <a href="{{ route('dashboard') }}" class="nav-button">
                    Dashboard UMKM
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

        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>

</header>


{{-- MOBILE MENU --}}

<div class="mobile-menu" id="mobileMenu">

    <a href="#beranda">
        Beranda
    </a>

    <a href="#cara-kerja">
        Cara Kerja
    </a>

    <a href="#umkm">
        Jelajahi UMKM
    </a>

    <a href="{{ route('radar') }}">
        Radar UMKM
    </a>

    <a href="#tentang">
        Tentang
    </a>

    <div class="mobile-menu-buttons">

        @auth

            <a href="{{ route('dashboard') }}">
                Dashboard
            </a>

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


{{-- HERO --}}

<section class="hero" id="beranda">

    <div class="hero-grid"></div>

    <div class="container hero-container">

        <div class="hero-content">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                <span>Platform Digitalisasi UMKM Indonesia</span>
            </div>

            <h1>
                UMKM-mu punya cerita.

                <span class="gradient-text">
                    Biarkan dunia menemukannya.
                </span>
            </h1>

            <p>
                Buat website untuk usaha kamu secara gratis,
                tanpa perlu coding. Cukup masukkan informasi,
                tambahkan foto, dan tampilkan bisnis kamu
                kepada lebih banyak orang.
            </p>

            <div class="hero-buttons">

                @auth

                    <a href="{{ route('dashboard') }}" class="primary-button">
                        Kelola UMKM
                        <span>→</span>
                    </a>

                @else

                    <a href="{{ route('register') }}" class="primary-button">
                        Buat Website Gratis
                        <span>→</span>
                    </a>

                @endauth

                <a href="#umkm" class="outline-button">
                    Jelajahi UMKM
                </a>

                <a href="{{ route('radar') }}" class="outline-button">
                    📍 Radar UMKM
                </a>

            </div>

            <div class="hero-note">

                <div class="people">
                    <span>A</span>
                    <span>B</span>
                    <span>C</span>
                    <span>+</span>
                </div>

                <div>
                    <strong>
                        Temukan berbagai UMKM
                    </strong>

                    <small>
                        yang sudah hadir secara digital
                    </small>
                </div>

            </div>

        </div>


        {{-- WEBSITE PREVIEW --}}

        <div class="hero-visual">

            <div class="hero-orb orb-one"></div>

            <div class="hero-orb orb-two"></div>

            <div class="floating-card floating-top">

                <div class="floating-icon green">
                    ✓
                </div>

                <div>

                    <strong>
                        Website berhasil dibuat
                    </strong>

                    <small>
                        Baru saja
                    </small>

                </div>

            </div>

            <div class="website-preview">

                <div class="preview-browser">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="preview-navbar">

                    <strong>
                        Kedai Senja
                    </strong>

                    <div>
                        Beranda
                        &nbsp;&nbsp;
                        Produk
                        &nbsp;&nbsp;
                        Tentang
                    </div>

                </div>

                <div class="preview-hero">

                    <div>

                        <small>
                            SELAMAT DATANG
                        </small>

                        <h3>
                            Rasa Lokal,
                            <br>
                            Cerita Istimewa.
                        </h3>

                        <p>
                            Nikmati produk pilihan
                            dari usaha lokal kami.
                        </p>

                        <button>
                            Lihat Produk →
                        </button>

                    </div>

                </div>

                <div class="preview-products">

                    <div class="preview-product">

                        <div class="product-img img-one"></div>

                        <strong>
                            Kopi Arabica
                        </strong>

                        <small>
                            Rp25.000
                        </small>

                    </div>

                    <div class="preview-product">

                        <div class="product-img img-two"></div>

                        <strong>
                            Brownies
                        </strong>

                        <small>
                            Rp30.000
                        </small>

                    </div>

                    <div class="preview-product">

                        <div class="product-img img-three"></div>

                        <strong>
                            Cookies
                        </strong>

                        <small>
                            Rp20.000
                        </small>

                    </div>

                </div>

            </div>

            <div class="floating-card floating-bottom">

                <div class="floating-icon purple">
                    ↗
                </div>

                <div>

                    <strong>
                        Bisnis kamu online
                    </strong>

                    <small>
                        Bisa ditemukan kapan saja
                    </small>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- STATISTICS --}}

<section class="stats">

    <div class="container stats-container">

        <div class="stat-item">

            <strong class="counter" data-target="100">
                0
            </strong>

            <span>
                + UMKM
            </span>

            <small>
                bergabung
            </small>

        </div>

        <div class="stat-item">

            <strong class="counter" data-target="120">
                0
            </strong>

            <span>
                + Website
            </span>

            <small>
                telah dibuat
            </small>

        </div>

        <div class="stat-item">

            <strong class="counter" data-target="15">
                0
            </strong>

            <span>
                + Kategori
            </span>

            <small>
                jenis usaha
            </small>

        </div>

        <div class="stat-item">

            <strong>
                100%
            </strong>

            <span>
                Gratis
            </span>

            <small>
                untuk UMKM
            </small>

        </div>

    </div>

</section>


{{-- CARA KERJA --}}

<section class="section how-section" id="cara-kerja">

    <div class="container">

        <div class="section-header">

            <span class="section-label">
                CARA KERJA
            </span>

            <h2>
                Dari usaha kecil menjadi

                <span>
                    lebih mudah ditemukan.
                </span>
            </h2>

            <p>
                Tidak perlu mengerti coding.
                Kami membuat proses pembuatan website
                menjadi sederhana.
            </p>

        </div>

        <div class="steps">

            <div class="step-card">

                <div class="step-number">
                    01
                </div>

                <div class="step-icon">
                    ✎
                </div>

                <h3>
                    Isi Informasi
                </h3>

                <p>
                    Masukkan nama usaha, jenis UMKM,
                    deskripsi, alamat, kontak, dan
                    informasi bisnis lainnya.
                </p>

            </div>

            <div class="step-line"></div>

            <div class="step-card">

                <div class="step-number">
                    02
                </div>

                <div class="step-icon">
                    ◉
                </div>

                <h3>
                    Tambahkan Foto
                </h3>

                <p>
                    Upload foto usaha, produk, logo,
                    atau gambar lainnya agar website
                    terlihat lebih menarik.
                </p>

            </div>

            <div class="step-line"></div>

            <div class="step-card">

                <div class="step-number">
                    03
                </div>

                <div class="step-icon">
                    ✦
                </div>

                <h3>
                    Website Siap
                </h3>

                <p>
                    Informasi usaha kamu akan ditampilkan
                    dalam website yang dapat dilihat
                    oleh orang lain.
                </p>

            </div>

        </div>

    </div>

</section>


{{-- JELAJAHI UMKM --}}

<section class="section umkm-section" id="umkm">

    <div class="container">

        <div class="showcase-top">

            <div>

                <span class="section-label">
                    JELAJAHI UMKM
                </span>

                <h2>
                    Temukan usaha lokal
                    <br>

                    <span>
                        yang menarik.
                    </span>
                </h2>

            </div>

            <a href="#umkm" class="see-all">
                Lihat Semua UMKM →
            </a>

        </div>


        <div class="categories">

            <button class="category active" data-category="all">
                Semua
            </button>

            <button class="category" data-category="kuliner">
                Kuliner
            </button>

            <button class="category" data-category="fashion">
                Fashion
            </button>

            <button class="category" data-category="jasa">
                Jasa
            </button>

            <button class="category" data-category="kerajinan">
                Kerajinan
            </button>

            <button class="category" data-category="lainnya">
                Lainnya
            </button>

        </div>


        <div class="umkm-grid" id="umkmGrid">

            @forelse ($umkms as $umkm)

                <article class="umkm-card" data-category="{{ strtolower($umkm->category) }}">

                    <div class="umkm-image image-{{ strtolower($umkm->category) }}">

                        @if ($umkm->cover)

                            <img src="{{ asset('storage/' . $umkm->cover) }}" alt="{{ $umkm->name }}">

                        @endif

                        <span class="umkm-category">
                            {{ ucfirst($umkm->category) }}
                        </span>

                        <button type="button" class="favorite" aria-label="Favorit {{ $umkm->name }}">
                            ♡
                        </button>

                    </div>

                    <div class="umkm-info">

                        <div>

                            <h3>
                                {{ $umkm->name }}
                            </h3>

                            <span class="location">
                                {{ $umkm->address ?: 'Lokasi belum ditambahkan' }}
                            </span>

                        </div>

                        <p>
                            {{ $umkm->description ?: 'Belum ada deskripsi UMKM.' }}
                        </p>

                        <a href="{{ route('umkm.show', $umkm->slug) }}">
                            Lihat Website →
                        </a>

                    </div>

                </article>

            @empty

                <div class="umkm-empty">

                    <div class="umkm-empty-icon">
                        ♡
                    </div>

                    <h3>
                        Belum ada UMKM yang terdaftar
                    </h3>

                    <p>
                        Saat ini belum ada UMKM yang tersedia
                        untuk dijelajahi.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- TENTANG --}}

<section class="section why-section" id="tentang">

    <div class="container why-container">

        <div class="why-visual">

            <div class="big-number">

                <span>
                    UMKM
                </span>

                <strong>
                    GO
                </strong>

                <span>
                    DIGITAL
                </span>

            </div>

            <div class="circle-decoration"></div>

        </div>


        <div class="why-content">

            <span class="section-label">
                KENAPA UMKMKITA?
            </span>

            <h2>
                Bisnis kecil bukan berarti

                <span>
                    harus terlihat kecil.
                </span>
            </h2>

            <p>
                Kehadiran digital dapat membantu pelanggan
                mengenal usaha kamu dengan lebih mudah.
                Kami ingin membuat langkah pertama tersebut
                menjadi sesederhana mungkin.
            </p>


            <div class="benefits">

                <div class="benefit">

                    <div class="benefit-icon">
                        🌐
                    </div>

                    <div>

                        <h3>
                            Mudah ditemukan
                        </h3>

                        <p>
                            Informasi bisnis tersedia secara
                            online dan dapat diakses kapan saja.
                        </p>

                    </div>

                </div>


                <div class="benefit">

                    <div class="benefit-icon">
                        ✨
                    </div>

                    <div>

                        <h3>
                            Terlihat profesional
                        </h3>

                        <p>
                            Tampilkan identitas usaha dalam
                            halaman website yang rapi.
                        </p>

                    </div>

                </div>


                <div class="benefit">

                    <div class="benefit-icon">
                        💜
                    </div>

                    <div>

                        <h3>
                            Tanpa biaya
                        </h3>

                        <p>
                            Buat website UMKM kamu tanpa
                            perlu mengeluarkan biaya pembuatan.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- CTA BAWAH --}}

<section class="cta-section">

    <div class="container">

        <div class="cta">

            <div class="cta-circle circle-left"></div>

            <div class="cta-circle circle-right"></div>

            <span class="section-label">
                MULAI SEKARANG
            </span>

            <h2>
                Punya UMKM?

                <br>

                Yuk, bawa ke dunia digital.
            </h2>

            <p>
                Buat website usaha kamu secara gratis
                dan biarkan lebih banyak orang mengenalnya.
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

                <a href="#umkm" class="cta-secondary">
                    Lihat UMKM
                </a>

            </div>

        </div>

    </div>

</section>

<div id="backToTop" class="back-to-top">
    <svg class="progress-ring" width="50" height="50" viewBox="0 0 50 50">
        <circle class="progress-ring__bg" cx="25" cy="25" r="20"></circle>
        <circle class="progress-ring__circle" cx="25" cy="25" r="20"></circle>
    </svg>
    <span class="arrow">&#10094;</span>
</div>

{{-- FOOTER --}}

<footer>

    <div class="container footer-container">

        <div class="footer-brand">

            <a href="{{ route('home') }}" class="logo footer-logo">

                <span class="logo-mark">
                    U
                </span>

                <span>
                    UMKM<span class="logo-purple">Kita</span>
                </span>

            </a>

            <p>
                Membantu UMKM Indonesia membangun
                kehadiran digital dengan lebih mudah.
            </p>

        </div>


        <div class="footer-column">

            <strong>
                Platform
            </strong>

            <a href="#beranda">
                Beranda
            </a>

            <a href="#cara-kerja">
                Cara Kerja
            </a>

            <a href="#umkm">
                Jelajahi UMKM
            </a>

        </div>


        <div class="footer-column">

            <strong>
                Bantuan
            </strong>

            <a href="#">
                Panduan
            </a>

            <a href="#">
                FAQ
            </a>

            <a href="#">
                Kontak
            </a>

        </div>


        <div class="footer-column">

            <strong>
                Legal
            </strong>

            <a href="#">
                Kebijakan Privasi
            </a>

            <a href="#">
                Ketentuan
            </a>

        </div>

    </div>


    <div class="container footer-bottom">

        <span>
            © {{ date('Y') }} UMKMKita.
            Semua hak dilindungi.
        </span>

        <span>
            Dibuat untuk UMKM Indonesia 🇮🇩
        </span>

    </div>

</footer>


<script src="{{ asset('js/home.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const backToTopBtn = document.getElementById('backToTop');
    const circle = document.querySelector('.progress-ring__circle');

    if (!backToTopBtn || !circle) return;

    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;

    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    circle.style.strokeDashoffset = circumference;

    function updateProgress() {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollFraction = scrollHeight > 0 ? scrollTop / scrollHeight : 0;
        const offset = circumference - (scrollFraction * circumference);

        circle.style.strokeDashoffset = offset;

        if (scrollTop > 100) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    }

    window.addEventListener('scroll', updateProgress);

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>
</body>

</html>
