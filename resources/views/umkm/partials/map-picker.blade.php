@php
    $addressFieldId = $addressFieldId ?? 'address';
    $latitude = $latitude ?? null;
    $longitude = $longitude ?? null;
@endphp

<link rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin="">

<div class="map-picker" style="margin-top:6px;">
    <label style="display:block;font-weight:600;margin-bottom:6px;">
        Lokasi di Peta
    </label>

    <p style="margin:0 0 10px;font-size:13px;color:#6b6f80;">
        Cari lokasi kamu, cari nama jalan atau daerah, atau klik langsung di peta untuk menentukan titik usaha.
    </p>

    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
        <button type="button"
            id="mp-locate-btn"
            style="padding:9px 14px;border-radius:10px;border:1px solid #d8d8e6;background:#fff;cursor:pointer;font-weight:600;font-size:13px;">
            📍 Cari Lokasi Saya
        </button>

        <div style="position:relative;flex:1;min-width:220px;">
            <input type="text"
                id="mp-search-input"
                placeholder="Cari nama jalan / daerah..."
                autocomplete="off"
                style="width:100%;padding:9px 12px;border-radius:10px;border:1px solid #d8d8e6;font-size:13px;box-sizing:border-box;">

            <div id="mp-search-results"
                style="display:none;position:absolute;z-index:1000;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1px solid #e2e2ee;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,.08);max-height:220px;overflow-y:auto;">
            </div>
        </div>
    </div>

    <div id="mp-map"
        style="width:100%;height:320px;border-radius:14px;border:1px solid #e2e2ee;">
    </div>

    <p id="mp-status"
        style="margin:8px 0 0;font-size:12px;color:#8a8d9c;">
    </p>

    <input type="hidden" name="latitude" id="mp-latitude" value="{{ $latitude }}">
    <input type="hidden" name="longitude" id="mp-longitude" value="{{ $longitude }}">
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

<script>
(function () {
    const DEFAULT_LAT = -2.5489;
    const DEFAULT_LNG = 118.0149;
    const DEFAULT_ZOOM = 5;

    const addressInput = document.getElementById(@json($addressFieldId));
    const latInput = document.getElementById('mp-latitude');
    const lngInput = document.getElementById('mp-longitude');
    const statusEl = document.getElementById('mp-status');
    const locateButton = document.getElementById('mp-locate-btn');
    const searchInput = document.getElementById('mp-search-input');
    const resultsBox = document.getElementById('mp-search-results');

    const startLat = Number(latInput.value);
    const startLng = Number(lngInput.value);
    const hasStart = Number.isFinite(startLat) && Number.isFinite(startLng) && latInput.value && lngInput.value;

    const map = L.map('mp-map').setView(
        hasStart ? [startLat, startLng] : [DEFAULT_LAT, DEFAULT_LNG],
        hasStart ? 16 : DEFAULT_ZOOM
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    let searchTimer = null;

    function setStatus(message) {
        statusEl.textContent = message || '';
    }

    function updateCoords(lat, lng) {
        latInput.value = Number(lat).toFixed(7);
        lngInput.value = Number(lng).toFixed(7);
    }

    function placeMarker(lat, lng, moveMap = true) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            marker.on('dragend', function () {
                const position = marker.getLatLng();
                updateCoords(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
            });
        }

        updateCoords(lat, lng);
        if (moveMap) map.setView([lat, lng], 16);
    }

    async function reverseGeocode(lat, lng) {
        setStatus('Mengambil alamat...');

        try {
            const params = new URLSearchParams({
                format: 'jsonv2',
                lat: lat,
                lon: lng
            });

            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?${params}`);
            const data = await response.json();

            if (data?.display_name && addressInput) {
                addressInput.value = data.display_name;
            }

            setStatus('Lokasi berhasil dipilih.');
        } catch (error) {
            console.error(error);
            setStatus('Titik lokasi tersimpan, tetapi alamat gagal diambil otomatis.');
        }
    }

    if (hasStart) placeMarker(startLat, startLng, false);

    map.on('click', function (event) {
        placeMarker(event.latlng.lat, event.latlng.lng, false);
        reverseGeocode(event.latlng.lat, event.latlng.lng);
    });

    locateButton.addEventListener('click', function () {
        if (!navigator.geolocation) {
            setStatus('Browser tidak mendukung deteksi lokasi.');
            return;
        }

        locateButton.disabled = true;
        locateButton.textContent = 'Mencari lokasi...';
        setStatus('Mendeteksi lokasi kamu...');

        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            placeMarker(lat, lng);
            reverseGeocode(lat, lng);

            locateButton.disabled = false;
            locateButton.textContent = '📍 Cari Lokasi Saya';
        }, function () {
            setStatus('Lokasi tidak dapat diambil. Pastikan izin lokasi browser sudah aktif.');

            locateButton.disabled = false;
            locateButton.textContent = '📍 Cari Lokasi Saya';
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 60000
        });
    });

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();

        clearTimeout(searchTimer);

        if (query.length < 3) {
            resultsBox.style.display = 'none';
            resultsBox.innerHTML = '';
            return;
        }

        searchTimer = setTimeout(async function () {
            try {
                const params = new URLSearchParams({
                    format: 'jsonv2',
                    q: query,
                    countrycodes: 'id',
                    limit: '6'
                });

                const response = await fetch(`https://nominatim.openstreetmap.org/search?${params}`);
                const results = await response.json();

                resultsBox.innerHTML = '';

                if (!results.length) {
                    resultsBox.style.display = 'none';
                    return;
                }

                results.forEach(function (result) {
                    const item = document.createElement('button');

                    item.type = 'button';
                    item.textContent = result.display_name;
                    item.style.width = '100%';
                    item.style.padding = '9px 12px';
                    item.style.border = '0';
                    item.style.borderBottom = '1px solid #f0f0f6';
                    item.style.background = '#fff';
                    item.style.textAlign = 'left';
                    item.style.fontSize = '13px';
                    item.style.cursor = 'pointer';

                    item.addEventListener('mouseenter', function () {
                        item.style.background = '#f6f6fb';
                    });

                    item.addEventListener('mouseleave', function () {
                        item.style.background = '#fff';
                    });

                    item.addEventListener('click', function () {
                        const lat = Number(result.lat);
                        const lng = Number(result.lon);

                        placeMarker(lat, lng);

                        if (addressInput) {
                            addressInput.value = result.display_name;
                        }

                        searchInput.value = '';
                        resultsBox.style.display = 'none';
                        setStatus('Lokasi berhasil dipilih.');
                    });

                    resultsBox.appendChild(item);
                });

                resultsBox.style.display = 'block';
            } catch (error) {
                console.error(error);
                resultsBox.style.display = 'none';
            }
        }, 400);
    });

    document.addEventListener('click', function (event) {
        if (event.target === searchInput || resultsBox.contains(event.target)) return;
        resultsBox.style.display = 'none';
    });
})();
</script>
