document.addEventListener('DOMContentLoaded', function () {
    const SEARCH_RADIUS_KM = 10;
    const CATEGORIES = ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Kecantikan', 'Otomotif', 'Lainnya'];
    const umkms = Array.isArray(window.radarUmkms) ? window.radarUmkms : [];

    const mapElement = document.getElementById('radarMap');
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

    const map = L.map('radarMap', {
        zoomControl: true,
        preferCanvas: true
    }).setView([-2.5, 118], 5);

    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        updateWhenIdle: false,
        keepBuffer: 4,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let userMarker = null;
    let radiusCircle = null;
    let umkmMarkers = [];
    let activeUmkmMarker = null;
    let manualLocationMode = false;
    let activeCategory = 'all';
    let locationRequestId = 0;

    function refreshMapSize() {
        requestAnimationFrame(function () {
            map.invalidateSize({ animate: false, pan: false });
        });
    }

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

    function createDetailPanel() {
        let panel = document.getElementById('radarDetailPanel');
        if (panel) return panel;

        panel = document.createElement('aside');
        panel.id = 'radarDetailPanel';
        panel.className = 'radar-detail-panel';
        panel.hidden = true;

        panel.innerHTML = `
            <button type="button" class="radar-detail-close" id="radarDetailClose" aria-label="Tutup detail">×</button>
            <div id="radarDetailContent"></div>
        `;

        mapElement.appendChild(panel);

        panel.querySelector('#radarDetailClose').addEventListener('click', closeDetailPanel);

        return panel;
    }

    function closeDetailPanel() {
        const panel = document.getElementById('radarDetailPanel');
        if (panel) panel.hidden = true;

        activeUmkmMarker = null;
        resetUmkmHighlights();
    }

    function renderDetailPanel(umkm) {
        const panel = createDetailPanel();
        const content = panel.querySelector('#radarDetailContent');
        const image = umkm.cover || umkm.logo;

        content.innerHTML = `
            <div class="radar-detail-image">
                ${
                    image
                        ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(umkm.name)}">`
                        : `<div class="radar-detail-placeholder">
                            <span>${escapeHtml(umkm.category || 'UMKM')}</span>
                            <strong>${escapeHtml(umkm.name)}</strong>
                        </div>`
                }
            </div>

            <div class="radar-detail-body">
                <div class="radar-detail-tags">
                    <span>${escapeHtml(umkm.category || 'UMKM')}</span>
                    <span class="${umkm.business_type === 'keliling' ? 'mobile' : 'fixed'}">
                        ${
                            umkm.business_type === 'keliling'
                                ? `UMKM Keliling · Titik ${escapeHtml(umkm.location_number || '')}`
                                : 'UMKM Di Tempat'
                        }
                    </span>
                </div>

                <h3>${escapeHtml(umkm.name)}</h3>

                <strong class="radar-detail-distance">
                    ${formatDistance(umkm.distance)} dari lokasi kamu
                </strong>

                <div class="radar-detail-info">
                    ${
                        umkm.address
                            ? `<div>
                                <span>Alamat</span>
                                <p>${escapeHtml(umkm.address)}</p>
                            </div>`
                            : ''
                    }

                    ${
                        umkm.landmark
                            ? `<div>
                                <span>Patokan</span>
                                <p>${escapeHtml(umkm.landmark)}</p>
                            </div>`
                            : ''
                    }

                    ${
                        umkm.start_time && umkm.end_time
                            ? `<div>
                                <span>Jam</span>
                                <p>${escapeHtml(umkm.start_time)}–${escapeHtml(umkm.end_time)}</p>
                            </div>`
                            : ''
                    }
                </div>

                <a href="${escapeHtml(umkm.url)}" class="radar-detail-website">
                    Lihat Website →
                </a>
            </div>
        `;

        panel.hidden = false;
    }

    function highlightUmkmLocations(activeMarker, activeUmkm) {
        umkmMarkers.forEach(function (item) {
            const sameUmkm = String(item.umkm.umkm_id) === String(activeUmkm.umkm_id);
            let state = sameUmkm ? 'related' : 'normal';

            if (item.marker === activeMarker) state = 'active';

            item.marker.setIcon(createUmkmIcon(state, item.umkm.business_type));
        });
    }

    function resetUmkmHighlights() {
        umkmMarkers.forEach(function (item) {
            item.marker.setIcon(createUmkmIcon('normal', item.umkm.business_type));
        });
    }

    function scrollToMap() {
        mapElement.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }

    function focusMarker(markerItem, shouldScroll = true) {
        if (!markerItem) return;

        activeUmkmMarker = markerItem.marker;
        highlightUmkmLocations(markerItem.marker, markerItem.umkm);
        renderDetailPanel(markerItem.umkm);

        map.stop();

        map.flyTo(markerItem.marker.getLatLng(), 17, {
            animate: true,
            duration: .65
        });

        if (shouldScroll) {
            setTimeout(scrollToMap, 60);
        }
    }

    function clearUmkmMarkers() {
        umkmMarkers.forEach(function (item) {
            map.removeLayer(item.marker);
        });

        umkmMarkers = [];
        activeUmkmMarker = null;
        closeDetailPanel();
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
                    distance: distanceKm(
                        userLat,
                        userLng,
                        Number(umkm.latitude),
                        Number(umkm.longitude)
                    )
                };
            })
            .filter(function (umkm) {
                return Number.isFinite(umkm.distance) && umkm.distance <= SEARCH_RADIUS_KM;
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
            const lat = Number(umkm.latitude);
            const lng = Number(umkm.longitude);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

            const marker = L.marker([lat, lng], {
                icon: createUmkmIcon('normal', umkm.business_type)
            }).addTo(map);

            const markerItem = {
                marker: marker,
                umkm: umkm
            };

            marker.on('click', function () {
                focusMarker(markerItem, false);
            });

            umkmMarkers.push(markerItem);
        });

        groupedNearby.forEach(function (group) {
            const umkm = group.umkm;

            const locations = [...group.locations].sort(function (a, b) {
                return Number(a.location_number ?? 0) - Number(b.location_number ?? 0);
            });

            const isMobile = umkm.business_type === 'keliling';
            const image = umkm.cover || umkm.logo;

            const card = document.createElement('article');
            card.className = 'nearby-card nearby-card-group';

            const locationsHtml = isMobile
                ? locations.map(function (location) {
                    return `
                        <button type="button" class="nearby-location-item"
                            data-umkm-id="${escapeHtml(location.umkm_id)}"
                            data-location-id="${escapeHtml(location.location_id)}">

                            <span class="nearby-location-copy">
                                <strong>
                                    ${
                                        location.landmark
                                            ? escapeHtml(location.landmark)
                                            : `Titik ${escapeHtml(location.location_number)}`
                                    }
                                </strong>

                                <small>${escapeHtml(location.address || 'Alamat belum tersedia')}</small>
                            </span>

                            <span class="nearby-location-distance">
                                ${formatDistance(location.distance)}
                            </span>
                        </button>
                    `;
                }).join('')
                : '';

            card.innerHTML = `
                <div class="nearby-card-main">
                    <div class="nearby-card-cover">
                        ${
                            image
                                ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(umkm.name)}">`
                                : `<div class="nearby-card-cover-placeholder">
                                    <span>${escapeHtml(umkm.category || 'UMKM')}</span>
                                    <strong>${escapeHtml(umkm.name)}</strong>
                                </div>`
                        }
                    </div>

                    <div class="nearby-card-content">
                        <div class="nearby-card-top">
                            <div class="nearby-card-info">
                                <span class="nearby-card-category">${escapeHtml(umkm.category)}</span>
                                <h3>${escapeHtml(umkm.name)}</h3>

                                <span class="nearby-card-type">
                                    ${
                                        isMobile
                                            ? `UMKM Keliling · ${locations.length} titik standby`
                                            : 'UMKM Di Tempat'
                                    }
                                </span>
                            </div>
                        </div>

                        <div class="nearby-card-bottom">
                            <div class="nearby-card-distance">
                                ${formatDistance(umkm.distance)} dari kamu
                            </div>

                            ${
                                isMobile
                                    ? `<button type="button" class="nearby-expand-button">
                                        Lihat titik
                                        <span>⌄</span>
                                    </button>`
                                    : `<button type="button" class="nearby-focus-button">
                                        Lihat di peta →
                                    </button>`
                            }
                        </div>
                    </div>
                </div>

                ${
                    isMobile
                        ? `<div class="nearby-locations" hidden>${locationsHtml}</div>`
                        : ''
                }
            `;

            const main = card.querySelector('.nearby-card-main');
            const focusButton = card.querySelector('.nearby-focus-button');

            function focusNearestLocation() {
                const markerItem = umkmMarkers
                    .filter(function (item) {
                        return String(item.umkm.umkm_id) === String(umkm.umkm_id);
                    })
                    .sort(function (a, b) {
                        return Number(a.umkm.distance) - Number(b.umkm.distance);
                    })[0];

                focusMarker(markerItem);
            }

            main.addEventListener('click', function (event) {
                if (event.target.closest('button')) return;
                focusNearestLocation();
            });

            focusButton?.addEventListener('click', function (event) {
                event.stopPropagation();
                focusNearestLocation();
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

                    focusMarker(markerItem);
                });
            });

            nearbyGrid.appendChild(card);
        });

        refreshMapSize();
    }

    function activateLocation(position, source = 'automatic') {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        if (userMarker) {
            userMarker.setLatLng([lat, lng]);
        } else {
            userMarker = L.marker([lat, lng], {
                icon: createUserIcon(),
                zIndexOffset: 1000
            }).addTo(map);
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
        refreshMapSize();

        statusDot.classList.add('active');
        mapLoading.classList.add('hidden');

        manualLocationMode = false;
        manualLocationInfo.hidden = true;
        map.getContainer().classList.remove('manual-location-mode');

        locationStatus.textContent = source === 'manual'
            ? 'Lokasi manual digunakan'
            : 'Lokasi berhasil ditemukan';

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
        refreshMapSize();
    }

    function disableManualLocationMode() {
        manualLocationMode = false;
        manualLocationInfo.hidden = true;
        map.getContainer().classList.remove('manual-location-mode');
    }

    function locationError(error) {
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
        disableManualLocationMode();

        const requestId = ++locationRequestId;

        if (!navigator.geolocation) {
            locationStatus.textContent = 'Browser tidak mendukung lokasi';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
            enableManualLocationMode();
            return;
        }

        mapLoading.classList.remove('hidden');
        locationStatus.textContent = 'Menunggu izin lokasi...';
        radarSummary.textContent = 'Izinkan akses lokasi atau pilih titik secara manual.';

        const fallbackTimer = setTimeout(function () {
            if (requestId !== locationRequestId) return;

            mapLoading.classList.add('hidden');
            locationStatus.textContent = 'Belum mendapat izin lokasi';
            radarSummary.textContent = 'Pilih lokasi secara manual di peta.';
            enableManualLocationMode();
        }, 8000);

        navigator.geolocation.getCurrentPosition(
            function (position) {
                if (requestId !== locationRequestId) return;

                clearTimeout(fallbackTimer);
                activateLocation(position, 'automatic');
            },
            function (error) {
                if (requestId !== locationRequestId) return;

                clearTimeout(fallbackTimer);
                locationError(error);
            },
            {
                enableHighAccuracy: false,
                timeout: 12000,
                maximumAge: 120000
            }
        );
    }

    locateAgainButton?.addEventListener('click', requestLocation);

    manualLocationButton?.addEventListener('click', function () {
        locationRequestId++;
        enableManualLocationMode();
    });

    map.on('click', function (event) {
        if (!manualLocationMode) return;

        locationRequestId++;

        activateLocation({
            coords: {
                latitude: event.latlng.lat,
                longitude: event.latlng.lng
            }
        }, 'manual');

        disableManualLocationMode();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeDetailPanel();
    });

    tileLayer.on('tileerror', function (event) {
        console.warn('Tile peta gagal dimuat:', event.coords);
    });

    window.addEventListener('load', function () {
        setTimeout(refreshMapSize, 100);
        setTimeout(refreshMapSize, 500);
    });

    window.addEventListener('resize', refreshMapSize);

    setTimeout(refreshMapSize, 100);
    requestLocation();
});
