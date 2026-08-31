{{--
    Komponen pemilih lokasi di peta.

    Cara pakai di form (create/edit):
        @include('umkm.partials.map-picker', [
            'addressFieldId' => 'address',        // id input alamat yang sudah ada di form
            'latitude'       => old('latitude', $umkm->latitude ?? null),
            'longitude'      => old('longitude', $umkm->longitude ?? null),
        ])

    Tidak butuh API key apapun (pakai OpenStreetMap + Leaflet + Nominatim, semua gratis).
    User bisa:
      1. Klik langsung di peta di titik mana saja (walau UMKM-nya belum "terdaftar" di manapun)
      2. Tekan tombol "Gunakan Lokasi Saya Sekarang" (GPS/browser geolocation)
      3. Cari nama jalan/daerah di kotak pencarian lalu pilih dari hasil
    Ketiga cara di atas otomatis mengisi kolom alamat + koordinat, tapi kolom alamat
    tetap bisa diketik/diedit manual kapan saja.
--}}
@php
    $addressFieldId = $addressFieldId ?? 'address';
    $latitude = $latitude ?? null;
    $longitude = $longitude ?? null;
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<div class="map-picker" style="margin-top:6px;">
    <label style="display:block;font-weight:600;margin-bottom:6px;">Lokasi di Peta</label>
    <p style="margin:0 0 10px;font-size:13px;color:#6b6f80;">
        Klik di peta untuk menandai lokasi usahamu, geser pin untuk menyesuaikan,
        atau gunakan tombol/pencarian di bawah. Belum terdaftar di Google Maps pun tetap bisa —
        kamu bisa taruh pin di titik mana saja.
    </p>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
        <button type="button" id="mp-locate-btn"
                style="padding:9px 14px;border-radius:10px;border:1px solid #d8d8e6;background:#fff;cursor:pointer;font-weight:600;font-size:13px;">
            📍 Gunakan Lokasi Saya Sekarang
        </button>
        <div style="position:relative;flex:1;min-width:220px;">
            <input type="text" id="mp-search-input" placeholder="Cari nama jalan / daerah..."
                   autocomplete="off"
                   style="width:100%;padding:9px 12px;border-radius:10px;border:1px solid #d8d8e6;font-size:13px;box-sizing:border-box;">
            <div id="mp-search-results"
                 style="display:none;position:absolute;z-index:1000;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #e2e2ee;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,.08);max-height:220px;overflow-y:auto;"></div>
        </div>
    </div>

    <div id="mp-map" style="width:100%;height:320px;border-radius:14px;border:1px solid #e2e2ee;"></div>
    <p id="mp-status" style="margin:8px 0 0;font-size:12px;color:#8a8d9c;"></p>

    <input type="hidden" name="latitude" id="mp-latitude" value="{{ $latitude }}">
    <input type="hidden" name="longitude" id="mp-longitude" value="{{ $longitude }}">
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    var DEFAULT_LAT = -6.2088; // Jakarta, dipakai kalau belum ada titik sama sekali
    var DEFAULT_LNG = 106.8456;
    var DEFAULT_ZOOM = 5;

    var addressInput = document.getElementById(@json($addressFieldId));
    var latInput = document.getElementById('mp-latitude');
    var lngInput = document.getElementById('mp-longitude');
    var statusEl = document.getElementById('mp-status');

    var startLat = parseFloat(latInput.value);
    var startLng = parseFloat(lngInput.value);
    var hasStart = !isNaN(startLat) && !isNaN(startLng);

    var map = L.map('mp-map').setView(
        hasStart ? [startLat, startLng] : [DEFAULT_LAT, DEFAULT_LNG],
        hasStart ? 16 : DEFAULT_ZOOM
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = null;

    function setStatus(text) {
        statusEl.textContent = text || '';
    }

    function placeMarker(lat, lng, panTo) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                var pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
                reverseGeocode(pos.lat, pos.lng);
            });
        }
        if (panTo) {
            map.setView([lat, lng], 16);
        }
        updateCoords(lat, lng);
    }

    function updateCoords(lat, lng) {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    function reverseGeocode(lat, lng) {
        setStatus('Mengambil alamat...');
        fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data && data.display_name && addressInput) {
                    addressInput.value = data.display_name;
                }
                setStatus('Alamat terisi otomatis — silakan sunting jika kurang tepat.');
            })
            .catch(function () {
                setStatus('Gagal mengambil nama alamat, tapi titik lokasi tetap tersimpan. Isi alamat manual ya.');
            });
    }

    if (hasStart) {
        placeMarker(startLat, startLng, false);
    }

    map.on('click', function (e) {
        placeMarker(e.latlng.lat, e.latlng.lng, false);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // Tombol "Gunakan Lokasi Saya Sekarang"
    document.getElementById('mp-locate-btn').addEventListener('click', function () {
        if (!navigator.geolocation) {
            setStatus('Browser kamu tidak mendukung deteksi lokasi otomatis.');
            return;
        }
        setStatus('Mendeteksi lokasi kamu...');
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                placeMarker(pos.coords.latitude, pos.coords.longitude, true);
                reverseGeocode(pos.coords.latitude, pos.coords.longitude);
            },
            function () {
                setStatus('Tidak bisa mengambil lokasi. Pastikan izin lokasi diaktifkan, atau pilih manual di peta.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Kotak pencarian (Nominatim search, dibatasi ke Indonesia)
    var searchInput = document.getElementById('mp-search-input');
    var resultsBox = document.getElementById('mp-search-results');
    var searchTimer = null;

    searchInput.addEventListener('input', function () {
        var q = searchInput.value.trim();
        clearTimeout(searchTimer);
        if (q.length < 3) {
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
            return;
        }
        searchTimer = setTimeout(function () {
            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&countrycodes=id&limit=6&q=' + encodeURIComponent(q))
                .then(function (res) { return res.json(); })
                .then(function (results) {
                    resultsBox.innerHTML = '';
                    if (!results.length) {
                        resultsBox.style.display = 'none';
                        return;
                    }
                    results.forEach(function (r) {
                        var item = document.createElement('div');
                        item.textContent = r.display_name;
                        item.style.padding = '9px 12px';
                        item.style.fontSize = '13px';
                        item.style.cursor = 'pointer';
                        item.style.borderBottom = '1px solid #f0f0f6';
                        item.addEventListener('mouseenter', function () { item.style.background = '#f6f6fb'; });
                        item.addEventListener('mouseleave', function () { item.style.background = '#fff'; });
                        item.addEventListener('click', function () {
                            var lat = parseFloat(r.lat), lng = parseFloat(r.lon);
                            placeMarker(lat, lng, true);
                            if (addressInput) addressInput.value = r.display_name;
                            searchInput.value = '';
                            resultsBox.style.display = 'none';
                            setStatus('Lokasi dipilih dari hasil pencarian — geser pin jika perlu penyesuaian.');
                        });
                        resultsBox.appendChild(item);
                    });
                    resultsBox.style.display = 'block';
                })
                .catch(function () {
                    resultsBox.style.display = 'none';
                });
        }, 400);
    });

    document.addEventListener('click', function (e) {
        if (e.target !== searchInput) {
            resultsBox.style.display = 'none';
        }
    });
})();
</script>
