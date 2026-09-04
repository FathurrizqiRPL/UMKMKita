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
    const manualLocationButton = document.getElementById('manualLocationButton');
    const manualLocationInfo = document.getElementById('manualLocationInfo');
    const statusDot = document.querySelector('.status-dot');

    const map = L.map('radarMap').setView([-2.5, 118], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let userMarker = null;
    let radiusCircle = null;
    let umkmMarkers = [];
    let activeUmkmMarker = null;
    let relatedLocationLine = null;
    let manualLocationMode = false;
    

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function toRad(value) {
        return value * Math.PI / 180;
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

    function formatDistance(distance) {
        if (distance < 1) {
            return Math.round(distance * 1000) + ' m';
        }

        return distance.toFixed(1) + ' km';
    }

    function clearUmkmMarkers() {
        umkmMarkers.forEach(function (item) {
            map.removeLayer(item.marker);
        });

        umkmMarkers = [];
    }
    
    function highlightUmkmLocations(activeMarker, activeUmkm) {
    if (relatedLocationLine) {
        map.removeLayer(relatedLocationLine);
        relatedLocationLine = null;
    }

    const relatedLocations = [];

    umkmMarkers.forEach(function (item) {
        const sameUmkm = String(item.umkm.umkm_id) === String(activeUmkm.umkm_id);
        let state = sameUmkm ? 'related' : 'normal';

        if (sameUmkm) relatedLocations.push(item);
        if (item.marker === activeMarker) state = 'active';

        item.marker.setIcon(createUmkmIcon(state, item.umkm.business_type));
    });

    relatedLocations.sort(function (a, b) {
        return Number(a.umkm.location_number) - Number(b.umkm.location_number);
    });

    const relatedPoints = relatedLocations.map(function (item) {
        return item.marker.getLatLng();
    });

    
   if (activeUmkm.business_type === 'keliling' && relatedPoints.length > 1) {
    relatedLocationLine = L.polyline(relatedPoints, {
        color: '#5848e8',
        weight: 5,
        opacity: .9,
        dashArray: '10, 8'
    }).addTo(map);
}
}

    function resetUmkmHighlights() {
    umkmMarkers.forEach(function (item) {
        item.marker.setIcon(createUmkmIcon('normal', item.umkm.business_type));
    });

    if (relatedLocationLine) {
        map.removeLayer(relatedLocationLine);
        relatedLocationLine = null;
    }
}

    function focusUmkmMarker(marker) {
    map.stop();

    map.flyTo(marker.getLatLng(), 18, {
        animate: true,
        duration: 0.65
    });
}
    
   map.on('popupclose', function (event) {
    if (event.popup._source === activeUmkmMarker) {
        activeUmkmMarker = null;
        resetUmkmHighlights();
    }
});
    
    function createUmkmIcon(state = 'normal', businessType = 'tetap') {
    const mobileClass = businessType === 'keliling' ? ' umkm-pin-mobile' : '';
    return L.divIcon({
        className: '',
        html: `<div class="umkm-pin umkm-pin-${state}${mobileClass}"><span>U</span></div>`,
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
            const lat = Number(umkm.latitude);
            const lng = Number(umkm.longitude);

            const marker = L.marker([lat, lng], {
                icon: createUmkmIcon('normal', umkm.business_type)
            }).addTo(map);

            const image = umkm.cover || umkm.logo;

            const popupHtml = `
                <div class="radar-popup">
                    <div class="radar-popup-image">
                        ${
                            image
                                ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(umkm.name)}">`
                                : `<div class="radar-popup-placeholder">UMKMKita</div>`
                        }
                    </div>

                    <div class="radar-popup-body">
                        <span class="radar-popup-category">
                            ${escapeHtml(umkm.category)}
                        </span>
                        
                        ${umkm.business_type === 'keliling'
                            ? `<span class="radar-popup-mobile-badge">Keliling · Titik ${umkm.location_number}</span>`
                            : `<span class="radar-popup-fixed-badge">Di Tempat</span>`
                        }

                        <h3>
                            ${escapeHtml(umkm.name)}
                        </h3>

                        <div class="radar-popup-distance">
                            ${formatDistance(umkm.distance)} dari lokasi kamu
                        </div>

                        <p class="radar-popup-address">
                            ${escapeHtml(umkm.address || 'Alamat belum tersedia')}
                        </p>
                        
                        ${umkm.start_time && umkm.end_time
                            ? `<p class="radar-popup-hours">🕒 ${escapeHtml(umkm.start_time)} - ${escapeHtml(umkm.end_time)}</p>`
                            : ''
                        }
                        
                        ${
                            umkm.landmark
                                ? `<p class="radar-popup-landmark">Patokan: ${escapeHtml(umkm.landmark)}</p>`
                                : ''
                        }

                        <a href="${escapeHtml(umkm.url)}" class="radar-popup-button">
                            Lihat Website →
                        </a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupHtml, {
                autoPan: false
            });
            marker.on('click', function () {
                activeUmkmMarker = marker;
                highlightUmkmLocations(marker, umkm);
                focusUmkmMarker(marker);
            });
            
            umkmMarkers.push({
                marker: marker,
                umkm: umkm
            });

            const card = document.createElement('article');

            card.className = 'nearby-card';

            card.innerHTML = `
                <div class="nearby-card-top">
                    <div class="nearby-card-icon">
                        U
                    </div>

                    <div>
                        <h3>${escapeHtml(umkm.name)}</h3>

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
                activeUmkmMarker = marker;
                highlightUmkmLocations(marker, umkm);
                focusUmkmMarker(marker);
                marker.openPopup();
            });

            nearbyGrid.appendChild(card);
        });
    }

    function activateLocation(position, source = 'automatic') {
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

            userMarker.bindPopup('Lokasi pencarian kamu');
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

        statusDot.classList.add('active');

        mapLoading.classList.add('hidden');

        manualLocationMode = false;
        manualLocationInfo.hidden = true;

        if (source === 'manual') {
            locationStatus.textContent = 'Lokasi manual digunakan';
        } else {
            locationStatus.textContent = 'Lokasi berhasil ditemukan';
        }

        renderNearby(lat, lng);
    }

    function enableManualLocationMode() {
        manualLocationMode = true;

        mapLoading.classList.add('hidden');

        locationStatus.textContent = 'Pilih lokasi di peta';
        radarSummary.textContent = 'Klik titik di peta untuk menentukan lokasi pencarian.';

        statusDot.classList.remove('active');

        manualLocationInfo.hidden = false;

        map.getContainer().classList.add('manual-location-mode');
    }

    function disableManualLocationMode() {
        manualLocationMode = false;

        manualLocationInfo.hidden = true;

        map.getContainer().classList.remove('manual-location-mode');
    }

    function locationError(error) {
        console.error(
            'Geolocation error:',
            error.code,
            error.message
        );

        mapLoading.classList.add('hidden');
        statusDot.classList.remove('active');

        if (error.code === 1) {
            locationStatus.textContent = 'Izin lokasi ditolak';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
        } else if (error.code === 2) {
            locationStatus.textContent = 'Lokasi tidak tersedia';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
        } else if (error.code === 3) {
            locationStatus.textContent = 'Pencarian lokasi terlalu lama';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
        } else {
            locationStatus.textContent = 'Gagal mendeteksi lokasi';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
        }

        enableManualLocationMode();
    }

    function requestLocation() {
        disableManualLocationMode();

        if (!navigator.geolocation) {
            locationStatus.textContent = 'Browser tidak mendukung lokasi';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';

            enableManualLocationMode();

            return;
        }

        mapLoading.classList.remove('hidden');

        locationStatus.textContent = 'Menunggu lokasi...';
        radarSummary.textContent = 'Browser sedang mencoba menentukan posisi kamu.';

        navigator.geolocation.getCurrentPosition(
            function (position) {
                activateLocation(position, 'automatic');
            },
            locationError,
            {
                enableHighAccuracy: false,
                timeout: 60000,
                maximumAge: 120000
            }
        );
    }

    locateAgainButton.addEventListener(
        'click',
        requestLocation
    );

    manualLocationButton.addEventListener(
        'click',
        function () {
            enableManualLocationMode();
        }
    );

    map.on('click', function (event) {
        if (!manualLocationMode) {
            return;
        }

        const lat = event.latlng.lat;
        const lng = event.latlng.lng;

        activateLocation(
            {
                coords: {
                    latitude: lat,
                    longitude: lng
                }
            },
            'manual'
        );

        disableManualLocationMode();
    });

    requestLocation();
});
