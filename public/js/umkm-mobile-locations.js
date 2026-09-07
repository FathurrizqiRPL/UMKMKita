document.addEventListener('DOMContentLoaded', function () {
    const businessType = document.getElementById('business_type');
    const fixedSection = document.getElementById('fixedBusinessSection');
    const mobileSection = document.getElementById('mobileBusinessSection');
    const container = document.getElementById('mobileLocationsContainer');
    const addButton = document.getElementById('addMobileLocationButton');
    const template = document.getElementById('mobileLocationTemplate');
    const maps = new Map();

    function setupTimeInputs(root = document) {
        root.querySelectorAll('.time-24-input').forEach(function (input) {
            if (input.dataset.timeReady) return;
            input.dataset.timeReady = 'true';

            input.addEventListener('input', function () {
                let value = input.value.replace(/\D/g, '').slice(0, 4);

                if (value.length >= 3) {
                    value = value.slice(0, 2) + ':' + value.slice(2);
                }

                input.value = value;
                input.setCustomValidity('');
            });

            input.addEventListener('blur', function () {
                if (!input.value) return;

                const match = input.value.match(/^(\d{1,2}):(\d{1,2})$/);

                if (!match || Number(match[1]) > 23 || Number(match[2]) > 59) {
                    input.setCustomValidity('Gunakan format jam 24 jam, contoh 07:00.');
                    input.reportValidity();
                    return;
                }

                input.value =
                    String(Number(match[1])).padStart(2, '0') +
                    ':' +
                    String(Number(match[2])).padStart(2, '0');

                input.setCustomValidity('');
            });
        });
    }

    if (!businessType || !fixedSection || !mobileSection || !container || !addButton || !template) {
        setupTimeInputs();
        return;
    }

    function setFixedInputsDisabled(disabled) {
        fixedSection.querySelectorAll('input, select, textarea, button').forEach(function (input) {
            input.disabled = disabled;
        });
    }

    function setMobileInputsDisabled(disabled) {
        mobileSection.querySelectorAll('input, select, textarea, button').forEach(function (input) {
            input.disabled = disabled;
        });

        if (!disabled) addButton.disabled = false;
    }

    function toggleBusinessType() {
        const isMobile = businessType.value === 'keliling';

        fixedSection.style.display = isMobile ? 'none' : '';
        mobileSection.style.display = isMobile ? '' : 'none';

        setFixedInputsDisabled(isMobile);
        setMobileInputsDisabled(!isMobile);

        if (isMobile) {
            setTimeout(initializeAllMaps, 100);
        }
    }

    async function reverseGeocode(lat, lng, addressInput) {
        try {
            const params = new URLSearchParams({
                format: 'jsonv2',
                lat: lat,
                lon: lng
            });

            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?${params}`
            );

            const data = await response.json();

            addressInput.value =
                data?.display_name ||
                `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        } catch (error) {
            console.error(error);

            addressInput.value =
                `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    }

    async function searchLocation(card) {
        const input = card.querySelector('.mobile-search-input');
        const button = card.querySelector(
            '.mobile-search-button:not(.mobile-locate-button)'
        );

        const query = input.value.trim();

        if (!query) return;

        button.disabled = true;
        button.textContent = 'Mencari...';

        try {
            const params = new URLSearchParams({
                format: 'json',
                q: query,
                countrycodes: 'id',
                limit: '1'
            });

            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?${params}`
            );

            const results = await response.json();

            if (!results.length) {
                alert('Lokasi tidak ditemukan.');
                return;
            }

            setLocation(
                card,
                Number(results[0].lat),
                Number(results[0].lon),
                results[0].display_name
            );
        } catch (error) {
            console.error(error);
            alert('Gagal mencari lokasi.');
        } finally {
            button.disabled = false;
            button.textContent = 'Cari';
        }
    }

    function locateMe(card) {
        const button = card.querySelector('.mobile-locate-button');

        if (!navigator.geolocation) {
            alert('Browser tidak mendukung deteksi lokasi.');
            return;
        }

        button.disabled = true;
        button.textContent = 'Mencari...';

        navigator.geolocation.getCurrentPosition(function (position) {
            setLocation(
                card,
                position.coords.latitude,
                position.coords.longitude
            );

            button.disabled = false;
            button.textContent = 'Cari Lokasi Saya';
        }, function () {
            button.disabled = false;
            button.textContent = 'Cari Lokasi Saya';

            alert(
                'Lokasi tidak dapat diambil. Pastikan izin lokasi browser sudah aktif.'
            );
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 60000
        });
    }

    function setLocation(card, lat, lng, address = null) {
        const mapData = maps.get(card);

        if (!mapData) return;

        mapData.map.setView([lat, lng], 16);

        if (mapData.marker) {
            mapData.marker.setLatLng([lat, lng]);
        } else {
            mapData.marker = L.marker(
                [lat, lng],
                { draggable: true }
            ).addTo(mapData.map);

            mapData.marker.on('dragend', function () {
                const position = mapData.marker.getLatLng();

                updateCoordinates(
                    card,
                    position.lat,
                    position.lng
                );
            });
        }

        card.querySelector('.mobile-latitude').value =
            lat.toFixed(7);

        card.querySelector('.mobile-longitude').value =
            lng.toFixed(7);

        const addressInput =
            card.querySelector('.mobile-address');

        if (address) {
            addressInput.value = address;
        } else {
            reverseGeocode(lat, lng, addressInput);
        }
    }

    function updateCoordinates(card, lat, lng) {
        card.querySelector('.mobile-latitude').value =
            lat.toFixed(7);

        card.querySelector('.mobile-longitude').value =
            lng.toFixed(7);

        reverseGeocode(
            lat,
            lng,
            card.querySelector('.mobile-address')
        );
    }

    function initializeMap(card) {
        if (maps.has(card)) {
            maps.get(card).map.invalidateSize();
            return;
        }

        const mapElement =
            card.querySelector('.mobile-map');

        if (!mapElement) return;

        const latInput =
            card.querySelector('.mobile-latitude');

        const lngInput =
            card.querySelector('.mobile-longitude');

        const storedLat = Number(latInput.value);
        const storedLng = Number(lngInput.value);

        const hasStoredLocation =
            Number.isFinite(storedLat) &&
            Number.isFinite(storedLng) &&
            latInput.value !== '' &&
            lngInput.value !== '';

        const center = hasStoredLocation
            ? [storedLat, storedLng]
            : [-2.5489, 118.0149];

        const map = L.map(mapElement)
            .setView(
                center,
                hasStoredLocation ? 16 : 5
            );

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);

        const mapData = {
            map: map,
            marker: null
        };

        maps.set(card, mapData);

        if (hasStoredLocation) {
            mapData.marker = L.marker(
                [storedLat, storedLng],
                { draggable: true }
            ).addTo(map);

            mapData.marker.on('dragend', function () {
                const position =
                    mapData.marker.getLatLng();

                updateCoordinates(
                    card,
                    position.lat,
                    position.lng
                );
            });
        }

        map.on('click', function (event) {
            setLocation(
                card,
                event.latlng.lat,
                event.latlng.lng
            );
        });

        card.querySelector(
            '.mobile-search-button:not(.mobile-locate-button)'
        )?.addEventListener('click', function () {
            searchLocation(card);
        });

        card.querySelector(
            '.mobile-locate-button'
        )?.addEventListener('click', function () {
            locateMe(card);
        });

        card.querySelector(
            '.mobile-search-input'
        )?.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter') return;

            event.preventDefault();
            searchLocation(card);
        });

        setupTimeInputs(card);

        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }

    function initializeAllMaps() {
        container.querySelectorAll(
            '.mobile-location-card'
        ).forEach(initializeMap);
    }

    function updateIndexes() {
        const cards =
            container.querySelectorAll(
                '.mobile-location-card'
            );

        cards.forEach(function (card, index) {
            card.dataset.locationIndex = index;

            card.querySelector(
                '.mobile-location-number'
            ).textContent =
                `TITIK STANDBY ${index + 1}`;

            card.querySelector(
                '.mobile-location-title'
            ).textContent =
                `Lokasi ${index + 1}`;

            card.querySelector(
                '.mobile-address'
            ).name =
                `locations[${index}][address]`;

            card.querySelector(
                '.mobile-landmark'
            ).name =
                `locations[${index}][landmark]`;

            card.querySelector(
                '.mobile-latitude'
            ).name =
                `locations[${index}][latitude]`;

            card.querySelector(
                '.mobile-longitude'
            ).name =
                `locations[${index}][longitude]`;

            card.querySelector(
                '.mobile-start-time'
            ).name =
                `locations[${index}][start_time]`;

            card.querySelector(
                '.mobile-end-time'
            ).name =
                `locations[${index}][end_time]`;

            card.querySelector(
                '.remove-mobile-location'
            ).style.display =
                cards.length === 1
                    ? 'none'
                    : '';
        });
    }

    function addLocation() {
        container.appendChild(
            template.content.cloneNode(true)
        );

        updateIndexes();

        const cards =
            container.querySelectorAll(
                '.mobile-location-card'
            );

        initializeMap(
            cards[cards.length - 1]
        );
    }

    container.addEventListener('click', function (event) {
        const button =
            event.target.closest(
                '.remove-mobile-location'
            );

        if (!button) return;

        const cards =
            container.querySelectorAll(
                '.mobile-location-card'
            );

        if (cards.length <= 1) return;

        const card =
            button.closest(
                '.mobile-location-card'
            );

        const mapData =
            maps.get(card);

        if (mapData) {
            mapData.map.remove();
            maps.delete(card);
        }

        card.remove();
        updateIndexes();
    });

    addButton.addEventListener(
        'click',
        addLocation
    );

    businessType.addEventListener(
        'change',
        toggleBusinessType
    );

    setupTimeInputs();
    updateIndexes();
    toggleBusinessType();
});
