document.addEventListener('DOMContentLoaded', function () {
    const SEARCH_RADIUS_KM = 10;
    const FOCUS_ZOOM = 16;
    const CATEGORIES = ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Lainnya'];
    const umkms = Array.isArray(window.radarUmkms) ? window.radarUmkms : [];

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

    const mapContainer = map.getContainer();

    let userMarker = null;
    let radiusCircle = null;
    let umkmMarkers = [];
    let activeUmkmMarker = null;
    let activeUmkmData = null;
    let detailPanel = null;
    let manualLocationMode = false;
    let activeCategory = 'all';
    let manualSearchBox = null;
    let previousMapCenter = null;
    let previousMapZoom = null;
    let previousLocationStatus = null;
    let previousRadarSummary = null;
    let focusRequestId = 0;

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
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLng / 2) * Math.sin(dLng / 2);

        return earthRadius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function formatDistance(distance) {
        if (distance < 1) return Math.round(distance * 1000) + ' m';
        return distance.toFixed(1) + ' km';
    }

    function clearPreviousMapState() {
        previousMapCenter = null;
        previousMapZoom = null;
        previousLocationStatus = null;
        previousRadarSummary = null;
    }

    function createUmkmIcon(state = 'normal', businessType = 'tetap') {
        const typeClass = businessType === 'keliling' ? ' umkm-pin-mobile' : ' umkm-pin-fixed';

        return L.divIcon({
            className: '',
            html: `<div class="umkm-pin umkm-pin-${state}${typeClass}"><span>U</span></div>`,
            iconSize: [38, 38],
            iconAnchor: [19, 38]
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

    function resetUmkmHighlights() {
        activeUmkmMarker = null;
        activeUmkmData = null;

        umkmMarkers.forEach(function (item) {
            item.marker.setIcon(createUmkmIcon('normal', item.umkm.business_type));
        });
    }

    function highlightUmkmLocations(activeMarker, activeUmkm) {
        activeUmkmMarker = activeMarker;
        activeUmkmData = activeUmkm;

        umkmMarkers.forEach(function (item) {
            const sameUmkm = String(item.umkm.umkm_id) === String(activeUmkm.umkm_id);
            let state = sameUmkm ? 'related' : 'normal';

            if (item.marker === activeMarker) state = 'active';

            item.marker.setIcon(createUmkmIcon(state, item.umkm.business_type));
        });
    }

    function clearUmkmMarkers() {
        focusRequestId++;
        closeDetailPanel();
        map.stop();

        umkmMarkers.forEach(function (item) {
            map.removeLayer(item.marker);
        });

        umkmMarkers = [];
    }

    function getRelatedLocations(umkm) {
        return umkmMarkers
            .filter(function (item) {
                return String(item.umkm.umkm_id) === String(umkm.umkm_id);
            })
            .sort(function (a, b) {
                return Number(a.umkm.location_number ?? 0) - Number(b.umkm.location_number ?? 0);
            });
    }

    function createDetailPanel() {
        if (detailPanel) return;

        detailPanel = document.createElement('aside');
        detailPanel.id = 'radarDetailPanel';
        detailPanel.className = 'radar-detail-panel';
        detailPanel.hidden = true;

        mapContainer.appendChild(detailPanel);

        L.DomEvent.disableClickPropagation(detailPanel);
        L.DomEvent.disableScrollPropagation(detailPanel);

        detailPanel.addEventListener('click', function (event) {
            const closeButton = event.target.closest('.radar-detail-close');

            if (closeButton) {
                closeDetailPanel();
                return;
            }

            const locationButton = event.target.closest('.radar-detail-location-button');

            if (!locationButton || locationButton.disabled) return;

            const markerItem = umkmMarkers.find(function (item) {
                return String(item.umkm.umkm_id) === String(locationButton.dataset.umkmId) &&
                    String(item.umkm.location_id) === String(locationButton.dataset.locationId);
            });

            if (!markerItem) return;

            focusUmkm(markerItem.marker, markerItem.umkm);
        });
    }

    function renderDetailPanel(umkm) {
        createDetailPanel();

        const image = umkm.cover || umkm.logo;
        const isMobileBusiness = umkm.business_type === 'keliling';
        const locations = getRelatedLocations(umkm);

        const locationButtons = isMobileBusiness && locations.length > 1
            ? locations.map(function (item) {
                const current = String(item.umkm.location_id) === String(umkm.location_id);

                return `
                    <button type="button"
                        class="radar-detail-location-button${current ? ' active' : ''}"
                        data-umkm-id="${escapeHtml(item.umkm.umkm_id)}"
                        data-location-id="${escapeHtml(item.umkm.location_id)}"
                        ${current ? 'disabled' : ''}>
                        Titik ${escapeHtml(item.umkm.location_number)}
                        ${current ? '<span>✓</span>' : ''}
                    </button>
                `;
            }).join('')
            : '';

        detailPanel.innerHTML = `
            <button type="button" class="radar-detail-close" aria-label="Tutup detail">×</button>

            <div class="radar-detail-scroll">
                <div class="radar-detail-image">
                    ${
                        image
                            ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(umkm.name)}">`
                            : '<div class="radar-detail-placeholder">UMKMKita</div>'
                    }
                </div>

                <div class="radar-detail-body">
                    <div class="radar-detail-badges">
                        <span class="radar-detail-category">${escapeHtml(umkm.category)}</span>

                        ${
                            isMobileBusiness
                                ? `<span class="radar-detail-type mobile">Keliling · Titik ${escapeHtml(umkm.location_number)}</span>`
                                : '<span class="radar-detail-type fixed">Di Tempat</span>'
                        }
                    </div>

                    <h3>${escapeHtml(umkm.name)}</h3>

                    <div class="radar-detail-distance">
                        ${formatDistance(umkm.distance)} dari lokasi kamu
                    </div>

                    ${
                        locationButtons
                            ? `
                                <div class="radar-detail-locations">
                                    <div class="radar-detail-section-title">
                                        Titik Standby
                                        <span>${locations.length} lokasi</span>
                                    </div>

                                    <div class="radar-detail-location-list">
                                        ${locationButtons}
                                    </div>
                                </div>
                            `
                            : ''
                    }

                    <div class="radar-detail-info">
                        <div class="radar-detail-info-item">
                            <span class="radar-detail-info-icon">⌖</span>

                            <div>
                                <small>Alamat</small>
                                <p>${escapeHtml(umkm.address || 'Alamat belum tersedia')}</p>
                            </div>
                        </div>

                        ${
                            umkm.start_time && umkm.end_time
                                ? `
                                    <div class="radar-detail-info-item">
                                        <span class="radar-detail-info-icon">◷</span>

                                        <div>
                                            <small>Jam Standby</small>
                                            <p>${escapeHtml(umkm.start_time)} - ${escapeHtml(umkm.end_time)}</p>
                                        </div>
                                    </div>
                                `
                                : ''
                        }

                        ${
                            umkm.landmark
                                ? `
                                    <div class="radar-detail-info-item">
                                        <span class="radar-detail-info-icon">◎</span>

                                        <div>
                                            <small>Patokan</small>
                                            <p>${escapeHtml(umkm.landmark)}</p>
                                        </div>
                                    </div>
                                `
                                : ''
                        }
                    </div>

                    <a href="${escapeHtml(umkm.url)}" class="radar-detail-button">
                        Lihat Website
                        <span>→</span>
                    </a>
                </div>
            </div>
        `;

        detailPanel.hidden = false;
        detailPanel.classList.add('show');
    }

    function closeDetailPanel() {
        focusRequestId++;

        if (detailPanel) {
            detailPanel.classList.remove('show');
            detailPanel.hidden = true;
            detailPanel.innerHTML = '';
        }

        resetUmkmHighlights();
    }

    function focusUmkm(marker, umkm) {
        if (!marker || !umkm) return;

        const requestId = ++focusRequestId;
        const target = marker.getLatLng();
        const targetZoom = Math.max(map.getZoom(), FOCUS_ZOOM);

        highlightUmkmLocations(marker, umkm);
        renderDetailPanel(umkm);

        map.stop();

        const targetPoint = map.latLngToContainerPoint(target);
        const size = map.getSize();
        const desktopPanelWidth = window.innerWidth > 700 ? 350 : 0;

        const idealPoint = L.point(
            desktopPanelWidth ? (size.x - desktopPanelWidth) / 2 : size.x / 2,
            size.y / 2
        );

        const offset = idealPoint.subtract(targetPoint);

        if (map.getZoom() >= FOCUS_ZOOM) {
            map.panBy([-offset.x, -offset.y], {
                animate: true,
                duration: 0.4
            });
            return;
        }

        map.once('moveend', function () {
            if (requestId !== focusRequestId) return;

            if (window.innerWidth <= 700) return;

            const currentPoint = map.latLngToContainerPoint(target);
            const currentSize = map.getSize();
            const desiredPoint = L.point((currentSize.x - 350) / 2, currentSize.y / 2);
            const pan = currentPoint.subtract(desiredPoint);

            if (Math.abs(pan.x) < 5 && Math.abs(pan.y) < 5) return;

            map.panBy(pan, {
                animate: true,
                duration: 0.3
            });
        });

        map.flyTo(target, targetZoom, {
            animate: true,
            duration: 0.55
        });
    }

    function renderPinLegend() {
        if (document.getElementById('radarPinLegend')) return;

        const legend = document.createElement('div');
        legend.id = 'radarPinLegend';
        legend.className = 'radar-pin-legend';

        legend.innerHTML = `
            <span class="radar-pin-legend-title">Keterangan:</span>
            <span class="radar-pin-legend-item">
                <span class="radar-pin-legend-color fixed"></span>
                UMKM Di Tempat
            </span>
            <span class="radar-pin-legend-item">
                <span class="radar-pin-legend-color mobile"></span>
                UMKM Keliling
            </span>
        `;

        nearbyGrid.parentNode.insertBefore(legend, nearbyGrid);
    }

    function renderCategoryFilters(userLat, userLng) {
        renderPinLegend();

        let filters = document.getElementById('radarCategoryFilters');

        if (!filters) {
            filters = document.createElement('div');
            filters.id = 'radarCategoryFilters';
            filters.className = 'radar-filters';
            nearbyGrid.parentNode.insertBefore(filters, nearbyGrid);
        }

        const categories = ['all', ...CATEGORIES];

        filters.innerHTML = categories.map(function (category) {
            const label = category === 'all' ? 'Semua' : category;
            const activeClass = activeCategory === category ? ' active' : '';

            return `<button type="button" class="radar-filter-button${activeClass}" data-category="${escapeHtml(category)}">${escapeHtml(label)}</button>`;
        }).join('');

        filters.querySelectorAll('.radar-filter-button').forEach(function (button) {
            button.addEventListener('click', function () {
                activeCategory = button.dataset.category;
                renderNearby(userLat, userLng);
            });
        });
    }

    function renderNearby(userLat, userLng) {
        clearUmkmMarkers();

        const nearby = umkms
            .map(function (umkm) {
                return {
                    ...umkm,
                    distance: distanceKm(userLat, userLng, Number(umkm.latitude), Number(umkm.longitude))
                };
            })
            .filter(function (umkm) {
                return umkm.distance <= SEARCH_RADIUS_KM;
            })
            .sort(function (a, b) {
                return a.distance - b.distance;
            });

        renderCategoryFilters(userLat, userLng);

        const visibleNearby = activeCategory === 'all'
            ? nearby
            : nearby.filter(function (umkm) {
                return String(umkm.category || '').trim().toLowerCase() === activeCategory.toLowerCase();
            });

        const groupedNearby = Array.from(
            visibleNearby.reduce(function (groups, point) {
                const key = String(point.umkm_id);

                if (!groups.has(key)) {
                    groups.set(key, {
                        umkm: point,
                        locations: []
                    });
                }

                groups.get(key).locations.push(point);
                return groups;
            }, new Map()).values()
        );

        nearbyGrid.innerHTML = '';
        nearbyCount.textContent = groupedNearby.length + ' UMKM ditemukan';

        radarSummary.textContent =
            groupedNearby.length + ' UMKM · ' +
            visibleNearby.length + ' titik lokasi dalam radius ' +
            SEARCH_RADIUS_KM + ' km';

        if (!visibleNearby.length) {
            nearbyEmpty.hidden = false;

            nearbyEmpty.textContent = activeCategory === 'all'
                ? 'Belum ada UMKM yang terdaftar di sekitar lokasimu.'
                : `Belum ada UMKM kategori ${activeCategory} di sekitar lokasimu.`;

            return;
        }

        nearbyEmpty.hidden = true;

        visibleNearby.forEach(function (umkm) {
            const marker = L.marker([Number(umkm.latitude), Number(umkm.longitude)], {
                icon: createUmkmIcon('normal', umkm.business_type)
            }).addTo(map);

            marker.on('click', function () {
                focusUmkm(marker, umkm);
            });

            umkmMarkers.push({ marker: marker, umkm: umkm });
        });

        groupedNearby.forEach(function (group) {
            const umkm = group.umkm;

            const locations = [...group.locations].sort(function (a, b) {
                return Number(a.location_number ?? 0) - Number(b.location_number ?? 0);
            });

            const isMobile = umkm.business_type === 'keliling';

            const card = document.createElement('article');
            card.className = 'nearby-card nearby-card-group';

            const locationsHtml = isMobile
                ? locations.map(function (location) {
                    return `
                        <button type="button" class="nearby-location-item"
                            data-umkm-id="${escapeHtml(location.umkm_id)}"
                            data-location-id="${escapeHtml(location.location_id)}">
                            <span>
                                <strong>Titik ${escapeHtml(location.location_number)}</strong>
                                <small>${escapeHtml(location.address || 'Alamat belum tersedia')}</small>
                            </span>
                            <span>${formatDistance(location.distance)}</span>
                        </button>
                    `;
                }).join('')
                : '';

            card.innerHTML = `
                <div class="nearby-card-main">
                    <div class="nearby-card-top">
                        <div class="nearby-card-icon">U</div>

                        <div>
                            <h3>${escapeHtml(umkm.name)}</h3>
                            <span class="nearby-card-category">${escapeHtml(umkm.category)}</span>
                        </div>
                    </div>

                    <div class="nearby-card-bottom">
                        <div class="nearby-card-distance">${formatDistance(umkm.distance)}</div>

                        ${
                            isMobile
                                ? `<button type="button" class="nearby-expand-button">
                                    ${locations.length} titik standby
                                    <span>⌄</span>
                                </button>`
                                : ''
                        }
                    </div>
                </div>

                ${isMobile ? `<div class="nearby-locations" hidden>${locationsHtml}</div>` : ''}
            `;

            card.querySelector('.nearby-card-main').addEventListener('click', function () {
                const nearestMarker = umkmMarkers.find(function (item) {
                    return String(item.umkm.umkm_id) === String(umkm.umkm_id) &&
                        String(item.umkm.location_id) === String(umkm.location_id);
                });

                if (!nearestMarker) return;

                focusUmkm(nearestMarker.marker, nearestMarker.umkm);
            });

            const expandButton = card.querySelector('.nearby-expand-button');
            const locationsPanel = card.querySelector('.nearby-locations');

            if (expandButton && locationsPanel) {
                expandButton.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const isOpen = !locationsPanel.hidden;
                    locationsPanel.hidden = isOpen;
                    card.classList.toggle('expanded', !isOpen);
                });
            }

            card.querySelectorAll('.nearby-location-item').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const markerItem = umkmMarkers.find(function (item) {
                        return String(item.umkm.umkm_id) === String(button.dataset.umkmId) &&
                            String(item.umkm.location_id) === String(button.dataset.locationId);
                    });

                    if (!markerItem) return;

                    focusUmkm(markerItem.marker, markerItem.umkm);
                });
            });

            nearbyGrid.appendChild(card);
        });
    }

    function createManualSearchBox() {
        if (manualSearchBox) return;

        manualSearchBox = document.createElement('div');
        manualSearchBox.className = 'manual-search-box';

        manualSearchBox.innerHTML = `
            <div class="manual-search-input-wrap">
                <input type="text" id="manualSearchInput" placeholder="Cari alamat, kecamatan, atau kota...">
                <button type="button" id="manualSearchButton">Cari</button>
                <button type="button" id="manualCancelButton" class="manual-cancel-button">Batal</button>
            </div>

            <div id="manualSearchMessage" class="manual-search-message">
                Cari area terlebih dahulu, lalu klik titik yang tepat di peta.
            </div>
        `;

        manualLocationInfo.insertAdjacentElement('afterend', manualSearchBox);

        const input = document.getElementById('manualSearchInput');
        const searchButton = document.getElementById('manualSearchButton');
        const cancelButton = document.getElementById('manualCancelButton');

        searchButton.addEventListener('click', searchManualLocation);
        cancelButton.addEventListener('click', cancelManualLocation);

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cancelManualLocation();
                return;
            }

            if (event.key !== 'Enter') return;

            event.preventDefault();
            searchManualLocation();
        });
    }

    async function searchManualLocation() {
        const input = document.getElementById('manualSearchInput');
        const message = document.getElementById('manualSearchMessage');
        const searchButton = document.getElementById('manualSearchButton');
        const query = input.value.trim();

        if (!query) {
            message.textContent = 'Masukkan alamat atau nama daerah yang ingin dicari.';
            return;
        }

        message.textContent = 'Mencari lokasi...';
        searchButton.disabled = true;

        try {
            const params = new URLSearchParams({
                format: 'json',
                q: query,
                countrycodes: 'id',
                limit: '1',
                addressdetails: '1'
            });

            const response = await fetch(`https://nominatim.openstreetmap.org/search?${params}`);

            if (!response.ok) throw new Error('Gagal mengambil data lokasi');

            const results = await response.json();

            if (!results.length) {
                message.textContent = 'Lokasi tidak ditemukan. Coba gunakan nama tempat yang lebih lengkap.';
                return;
            }

            const lat = Number(results[0].lat);
            const lng = Number(results[0].lon);

            map.stop();

            map.flyTo([lat, lng], 16, {
                animate: true,
                duration: 0.8
            });

            message.textContent = 'Lokasi ditemukan. Klik titik yang tepat di peta untuk mulai mencari UMKM.';
        } catch (error) {
            console.error('Location search error:', error);
            message.textContent = 'Gagal mencari lokasi. Coba lagi beberapa saat.';
        } finally {
            searchButton.disabled = false;
        }
    }

    function cancelManualLocation() {
        disableManualLocationMode();

        if (previousMapCenter && previousMapZoom !== null) {
            map.stop();

            map.flyTo(previousMapCenter, previousMapZoom, {
                animate: true,
                duration: 0.6
            });
        }

        if (previousLocationStatus !== null) {
            locationStatus.textContent = previousLocationStatus;
        }

        if (previousRadarSummary !== null) {
            radarSummary.textContent = previousRadarSummary;
        }

        statusDot.classList.toggle('active', Boolean(userMarker));
        clearPreviousMapState();
    }

    function activateLocation(position, source = 'automatic') {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        closeDetailPanel();

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            userMarker = L.marker([lat, lng], {
                icon: createUserIcon(),
                zIndexOffset: 1000
            }).addTo(map);

            userMarker.bindPopup('Lokasi pencarian kamu');
        }

        if (radiusCircle) {
            radiusCircle.setLatLng([lat, lng]);
        } else {
            radiusCircle = L.circle([lat, lng], {
                radius: SEARCH_RADIUS_KM * 1000,
                color: '#5848e8',
                weight: 1,
                opacity: .35,
                fillColor: '#5848e8',
                fillOpacity: .05
            }).addTo(map);
        }

        map.setView([lat, lng], 14);

        statusDot.classList.add('active');
        mapLoading.classList.add('hidden');
        manualLocationMode = false;
        manualLocationInfo.hidden = true;

        if (manualSearchBox) manualSearchBox.hidden = true;

        mapContainer.classList.remove('manual-location-mode');

        locationStatus.textContent = source === 'manual'
            ? 'Lokasi manual digunakan'
            : 'Lokasi berhasil ditemukan';

        clearPreviousMapState();
        renderNearby(lat, lng);
    }

    function enableManualLocationMode() {
        closeDetailPanel();

        if (!manualLocationMode) {
            previousMapCenter = map.getCenter();
            previousMapZoom = map.getZoom();
            previousLocationStatus = locationStatus.textContent;
            previousRadarSummary = radarSummary.textContent;
        }

        manualLocationMode = true;

        mapLoading.classList.add('hidden');
        locationStatus.textContent = 'Pilih lokasi di peta';
        radarSummary.textContent = 'Cari area tujuan, lalu klik titik yang ingin digunakan sebagai lokasi pencarian.';
        statusDot.classList.remove('active');
        manualLocationInfo.hidden = false;

        createManualSearchBox();
        manualSearchBox.hidden = false;

        mapContainer.classList.add('manual-location-mode');

        setTimeout(function () {
            document.getElementById('manualSearchInput')?.focus();
        }, 100);
    }

    function disableManualLocationMode() {
        manualLocationMode = false;
        manualLocationInfo.hidden = true;

        if (manualSearchBox) manualSearchBox.hidden = true;

        mapContainer.classList.remove('manual-location-mode');
    }

    function locationError(error) {
        console.error('Geolocation error:', error.code, error.message);

        mapLoading.classList.add('hidden');
        statusDot.classList.remove('active');

        if (error.code === 1) {
            locationStatus.textContent = 'Izin lokasi ditolak';
        } else if (error.code === 2) {
            locationStatus.textContent = 'Lokasi tidak tersedia';
        } else if (error.code === 3) {
            locationStatus.textContent = 'Pencarian lokasi terlalu lama';
        } else {
            locationStatus.textContent = 'Gagal mendeteksi lokasi';
        }

        radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
        enableManualLocationMode();
    }

    function requestLocation() {
        closeDetailPanel();
        disableManualLocationMode();
        clearPreviousMapState();

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

    locateAgainButton.addEventListener('click', requestLocation);

    manualLocationButton.addEventListener('click', function () {
        enableManualLocationMode();
    });

    map.on('click', function (event) {
        if (!manualLocationMode) return;

        activateLocation({
            coords: {
                latitude: event.latlng.lat,
                longitude: event.latlng.lng
            }
        }, 'manual');
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        if (manualLocationMode) {
            cancelManualLocation();
            return;
        }

        if (detailPanel && !detailPanel.hidden) {
            closeDetailPanel();
        }
    });

    window.addEventListener('resize', function () {
        map.invalidateSize();

        if (!detailPanel || detailPanel.hidden || !activeUmkmMarker || !activeUmkmData) return;

        renderDetailPanel(activeUmkmData);
    });

    createDetailPanel();
    requestLocation();
});
