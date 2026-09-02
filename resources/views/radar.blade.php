<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Radar UMKM — UMKMKita</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="{{ asset('css/radar.css') }}">
</head>

<body>

<header class="radar-navbar">
    <a href="{{ route('home') }}" class="radar-brand">
        <span class="radar-logo">U</span>
        <span>UMKM<span class="purple">Kita</span></span>
    </a>

    <a href="{{ route('home') }}" class="back-home">← Kembali ke Beranda</a>
</header>

<main class="radar-page">
    <section class="radar-heading">
        <div>
            <span class="radar-label">RADAR UMKM</span>

            <h1>
                Temukan UMKM
                <em>di sekitarmu.</em>
            </h1>

            <p>
                Izinkan akses lokasi agar UMKMKita dapat menampilkan usaha lokal terdekat dari posisi kamu saat ini.
                Jika lokasi otomatis tidak tersedia, kamu tetap bisa memilih titik secara manual di peta.
            </p>
        </div>

        <div class="radar-status-card">
            <span class="status-dot"></span>

            <div>
                <strong id="locationStatus">Menunggu izin lokasi...</strong>
                <small id="radarSummary">Radar belum aktif</small>
            </div>
        </div>
    </section>

    <section class="radar-map-card">
        <div class="radar-map-toolbar">
            <div class="radar-map-info">
                <strong>UMKM di sekitar kamu</strong>
                <small>Radius pencarian 10 km</small>
            </div>

            <div class="radar-map-actions">
                <button type="button" id="locateAgainButton">📍 Cari Lokasi Saya</button>
                <button type="button" id="manualLocationButton" class="manual-button">Pilih Lokasi di Peta</button>
            </div>
        </div>

        <div id="manualLocationInfo" class="manual-location-info" hidden>
            Klik titik di peta yang ingin kamu gunakan sebagai lokasi pencarian.
        </div>

        <div id="radarMap"></div>

        <div class="map-loading" id="mapLoading">
            <div class="loading-spinner"></div>

            <strong>Mendeteksi lokasi kamu...</strong>

            <span>Browser mungkin akan meminta izin akses lokasi.</span>
        </div>
    </section>

    <section class="nearby-section">
        <div class="nearby-heading">
            <div>
                <span class="radar-label">TERDEKAT</span>
                <h2>UMKM di sekitar kamu</h2>
            </div>

            <span id="nearbyCount">0 UMKM ditemukan</span>
        </div>

        <div class="nearby-grid" id="nearbyGrid"></div>

        <div class="nearby-empty" id="nearbyEmpty" hidden>
            <div>📍</div>

            <h3>Belum ada UMKM di dekat kamu</h3>

            <p>Belum ada UMKM terdaftar dalam radius 10 km dari posisi yang dipilih.</p>
        </div>
    </section>
</main>

<script>
    window.radarUmkms = {{ Illuminate\Support\Js::from($radarUmkms) }};
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/radar.js') }}"></script>

</body>
</html>
