document.addEventListener('DOMContentLoaded', function () {
    const SEARCH_RADIUS_KM = 10;

    const umkms = Array.isArray(window.radarUmkms)
        ? window.radarUmkms
        : [];

    const locationStatus = document.getElementById('locationStatus');
    const radarSummary = document.getElementById('radarSummary');
    const nearbyCount = document.getElementById('nearbyCount');
    const nearbyGrid = document.getElementById('nearbyGrid');
    const nearbyEmpty = document.getElementById('nearbyEmpty');
    const mapLoading = document.getElementById('mapLoading');
    const locateAgainButton = document.getElementById('locateAgainButton');

    const statusDot = document.querySelector('.status-dot');

    const map = L.map('radarMap').setView([-2.5, 118], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let userMarker = null;
    let radiusCircle = null;
    let umkmMarkers = [];

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function distanceKm(lat1, lng1, lat2, lng2) {
        const earthRadius = 6371;

        const dLat = toRad(lat2 - lat1);
        const dLng = toRad(lng2 - lng1);

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) *
            Math.cos(toRad(lat2)) *
            Math.sin(dLng / 2) *
            Math.sin(dLng / 2);

        const c = 2 * Math.atan2(
            Math.sqrt(a),
            Math.sqrt(1 - a)
        );

        return earthRadius * c;
    }

    function toRad(value) {
        return value * Math.PI / 180;
    }

    function clearUmkmMarkers() {
        umkmMarkers.forEach(function (marker) {
            map.removeLayer(marker);
        });

        umkmMarkers = [];
    }

    function createUmkmIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="umkm-pin"><span>U</span></div>',
            iconSize: [38, 38],
            iconAnchor: [19, 38],
            popupAnchor: [0, -38]
        });
    }

    function createUserIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="user-location-marker"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });
    }

    function renderNearby(userLat, userLng) {
        clearUmkmMarkers();

        const nearby = umkms
            .map(function (umkm) {
                return {
                    ...umkm,
                    distance: distanceKm(
                        userLat,
                        userLng,
                        Number(umkm.latitude),
                        Number(umkm.longitude)
                    )
                };
            })
            .filter(function (umkm) {
                return umkm.distance <= SEARCH_RADIUS_KM;
            })
            .sort(function (a, b) {
                return a.distance - b.distance;
            });

        nearbyGrid.innerHTML = '';

        nearbyCount.textContent =
            nearby.length + ' UMKM ditemukan';

        radarSummary.textContent =
            nearby.length + ' UMKM dalam radius ' +
            SEARCH_RADIUS_KM + ' km';

        if (!nearby.length) {
            nearbyEmpty.hidden = false;
            return;
        }

        nearbyEmpty.hidden = true;

        nearby.forEach(function (umkm) {
            const marker = L.marker(
                [
                    Number(umkm.latitude),
                    Number(umkm.longitude)
                ],
                {
                    icon: createUmkmIcon()
                }
            ).addTo(map);

            const image = umkm.cover || umkm.logo;

            const popupHtml = `
                <div class="radar-popup">

                    <div class="radar-popup-image">
                        ${
                            image
                                ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(umkm.name)}">`
                                : ''
                        }
                    </div>

                    <div class="radar-popup-body">

                        <span class="radar-popup-category">
                            ${escapeHtml(umkm.category)}
                        </span>

                        <h3>
                            ${escapeHtml(umkm.name)}
                        </h3>

                        <div class="radar-popup-distance">
                            ${formatDistance(umkm.distance)} dari lokasi kamu
                        </div>

                        <p class="radar-popup-address">
                            ${escapeHtml(umkm.address || 'Alamat belum tersedia')}
                        </p>

                        <a href="${escapeHtml(umkm.url)}" class="radar-popup-button">
                            Lihat Website →
                        </a>

                    </div>

                </div>
            `;

            marker.bindPopup(popupHtml);

            umkmMarkers.push(marker);

            const card = document.createElement('article');

            card.className = 'nearby-card';

            card.innerHTML = `
                <div class="nearby-card-top">

                    <div class="nearby-card-icon">
                        U
                    </div>

                    <div>

                        <h3>
                            ${escapeHtml(umkm.name)}
                        </h3>

                        <span class="nearby-card-category">
                            ${escapeHtml(umkm.category)}
                        </span>

                    </div>

                </div>

                <div class="nearby-card-distance">
                    ${formatDistance(umkm.distance)}
                </div>
            `;

            card.addEventListener('click', function () {
                map.setView(
                    [
                        Number(umkm.latitude),
                        Number(umkm.longitude)
                    ],
                    17
                );

                marker.openPopup();
            });

            nearbyGrid.appendChild(card);
        });
    }

    function formatDistance(distance) {
        if (distance < 1) {
            return Math.round(distance * 1000) + ' m';
        }

        return distance.toFixed(1) + ' km';
    }

    function activateLocation(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            userMarker = L.marker(
                [lat, lng],
                {
                    icon: createUserIcon(),
                    zIndexOffset: 1000
                }
            ).addTo(map);

            userMarker.bindPopup('Lokasi kamu saat ini');
        }

        if (radiusCircle) {
            radiusCircle.setLatLng([lat, lng]);
        } else {
            radiusCircle = L.circle(
                [lat, lng],
                {
                    radius: SEARCH_RADIUS_KM * 1000,
                    color: '#5848e8',
                    weight: 1,
                    opacity: 0.35,
                    fillColor: '#5848e8',
                    fillOpacity: 0.05
                }
            ).addTo(map);
        }

        map.setView([lat, lng], 14);

        locationStatus.textContent = 'Lokasi berhasil ditemukan';

        statusDot.classList.add('active');

        mapLoading.classList.add('hidden');

        renderNearby(lat, lng);
    }

    function locationError(error) {
        mapLoading.classList.add('hidden');

        statusDot.classList.remove('active');

        if (error.code === error.PERMISSION_DENIED) {
            locationStatus.textContent = 'Izin lokasi ditolak';

            radarSummary.textContent =
                'Aktifkan izin lokasi untuk menggunakan radar.';
        } else if (error.code === error.POSITION_UNAVAILABLE) {
            locationStatus.textContent = 'Lokasi tidak tersedia';

            radarSummary.textContent =
                'Browser belum bisa menentukan posisi kamu.';
        } else {
            locationStatus.textContent = 'Gagal mendeteksi lokasi';

            radarSummary.textContent =
                'Silakan coba lagi.';
        }
    }

    function requestLocation() {
        if (!navigator.geolocation) {
            mapLoading.classList.add('hidden');

            locationStatus.textContent =
                'Browser tidak mendukung lokasi';

            radarSummary.textContent =
                'Gunakan browser yang mendukung Geolocation API.';

            return;
        }

        mapLoading.classList.remove('hidden');

        locationStatus.textContent =
            'Menunggu izin lokasi...';

        radarSummary.textContent =
            'Browser akan meminta izin lokasi.';

        navigator.geolocation.getCurrentPosition(
            activateLocation,
            locationError,
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 30000
            }
        );
    }

    locateAgainButton.addEventListener(
        'click',
        requestLocation
    );

    requestLocation();
});
