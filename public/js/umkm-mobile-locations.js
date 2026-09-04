document.addEventListener('DOMContentLoaded', function () {
    const businessType = document.getElementById('business_type');
    const fixedSection = document.getElementById('fixedBusinessSection');
    const mobileSection = document.getElementById('mobileBusinessSection');
    const container = document.getElementById('mobileLocationsContainer');
    const addButton = document.getElementById('addMobileLocationButton');
    const template = document.getElementById('mobileLocationTemplate');

    if (!businessType || !fixedSection || !mobileSection || !container) {
        return;
    }

    const maps = new Map();

    function toggleBusinessType() {
        const isMobile = businessType.value === 'keliling';

        fixedSection.style.display = isMobile ? 'none' : '';
        mobileSection.style.display = isMobile ? '' : 'none';

        setFixedInputsDisabled(isMobile);
        setMobileInputsDisabled(!isMobile);

        if (isMobile) {
            setTimeout(function () {
                initializeAllMaps();
            }, 100);
        }
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

        if (!disabled) {
            addButton.disabled = false;
        }
    }

    async function reverseGeocode(lat, lng, addressInput) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
            );

            const data = await response.json();

            if (data && data.display_name) {
                addressInput.value = data.display_name;
            } else {
                addressInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
        } catch (error) {
            console.error(error);

            addressInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    }

    async function searchLocation(card) {
        const input = card.querySelector('.mobile-search-input');

        if (!input.value.trim()) {
            return;
        }

        const button = card.querySelector('.mobile-search-button');

        button.disabled = true;
        button.textContent = 'Mencari...';

        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(input.value)}`
            );

            const results = await response.json();

            if (!results.length) {
                alert('Lokasi tidak ditemukan.');
                return;
            }

            const lat = Number(results[0].lat);
            const lng = Number(results[0].lon);

            setLocation(card, lat, lng, results[0].display_name);
        } catch (error) {
            console.error(error);

            alert('Gagal mencari lokasi.');
        } finally {
            button.disabled = false;
            button.textContent = 'Cari';
        }
    }

    function setLocation(card, lat, lng, address = null) {
        const mapData = maps.get(card);

        if (!mapData) {
            return;
        }

        mapData.map.setView([lat, lng], 16);

        if (mapData.marker) {
            mapData.marker.setLatLng([lat, lng]);
        } else {
            mapData.marker = L.marker(
                [lat, lng],
                {
                    draggable: true
                }
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

        card.querySelector('.mobile-latitude').value = lat;
        card.querySelector('.mobile-longitude').value = lng;

        const addressInput = card.querySelector('.mobile-address');

        if (address) {
            addressInput.value = address;
        } else {
            reverseGeocode(lat, lng, addressInput);
        }
    }

    function updateCoordinates(card, lat, lng) {
        card.querySelector('.mobile-latitude').value = lat;
        card.querySelector('.mobile-longitude').value = lng;

        const addressInput = card.querySelector('.mobile-address');

        reverseGeocode(lat, lng, addressInput);
    }

    function initializeMap(card) {
        if (maps.has(card)) {
            maps.get(card).map.invalidateSize();
            return;
        }

        const mapElement = card.querySelector('.mobile-map');

        if (!mapElement) {
            return;
        }

        const latInput = card.querySelector('.mobile-latitude');
        const lngInput = card.querySelector('.mobile-longitude');

        const storedLat = Number(latInput.value);
        const storedLng = Number(lngInput.value);

        const hasStoredLocation =
            Number.isFinite(storedLat) &&
            Number.isFinite(storedLng) &&
            latInput.value !== '' &&
            lngInput.value !== '';

        const defaultLat = hasStoredLocation
            ? storedLat
            : -2.5489;

        const defaultLng = hasStoredLocation
            ? storedLng
            : 118.0149;

        const defaultZoom = hasStoredLocation
            ? 16
            : 5;

        const map = L.map(mapElement).setView(
            [defaultLat, defaultLng],
            defaultZoom
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
                {
                    draggable: true
                }
            ).addTo(map);

            mapData.marker.on('dragend', function () {
                const position = mapData.marker.getLatLng();

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

        const searchButton = card.querySelector('.mobile-search-button');

        searchButton.addEventListener('click', function () {
            searchLocation(card);
        });

        const searchInput = card.querySelector('.mobile-search-input');

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();

                searchLocation(card);
            }
        });

        setTimeout(function () {
            map.invalidateSize();
        }, 100);
    }

    function initializeAllMaps() {
        container.querySelectorAll('.mobile-location-card').forEach(function (card) {
            initializeMap(card);
        });
    }

    function updateIndexes() {
        const cards = container.querySelectorAll('.mobile-location-card');

        cards.forEach(function (card, index) {
            card.dataset.locationIndex = index;

            const number = card.querySelector('.mobile-location-number');
            const title = card.querySelector('.mobile-location-title');

            if (number) {
                number.textContent = `TITIK STANDBY ${index + 1}`;
            }

            if (title) {
                title.textContent = `Lokasi ${index + 1}`;
            }

            card.querySelector('.mobile-address').name =
                `locations[${index}][address]`;

            card.querySelector('.mobile-landmark').name =
                `locations[${index}][landmark]`;

            card.querySelector('.mobile-latitude').name =
                `locations[${index}][latitude]`;

            card.querySelector('.mobile-longitude').name =
                `locations[${index}][longitude]`;

            card.querySelector('.mobile-start-time').name =
                `locations[${index}][start_time]`;

            card.querySelector('.mobile-end-time').name =
                `locations[${index}][end_time]`;
        });

        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const cards = container.querySelectorAll('.mobile-location-card');

        cards.forEach(function (card) {
            const removeButton = card.querySelector('.remove-mobile-location');

            if (!removeButton) {
                return;
            }

            removeButton.style.display =
                cards.length === 1
                    ? 'none'
                    : '';
        });
    }

    function addLocation() {
        const clone = template.content.cloneNode(true);

        container.appendChild(clone);

        updateIndexes();

        const cards = container.querySelectorAll('.mobile-location-card');
        const newCard = cards[cards.length - 1];

        initializeMap(newCard);
    }

    container.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-mobile-location');

        if (!button) {
            return;
        }

        const cards = container.querySelectorAll('.mobile-location-card');

        if (cards.length <= 1) {
            return;
        }

        const card = button.closest('.mobile-location-card');
        const mapData = maps.get(card);

        if (mapData) {
            mapData.map.remove();
            maps.delete(card);
        }

        card.remove();

        updateIndexes();
    });

    addButton.addEventListener('click', addLocation);

    businessType.addEventListener(
        'change',
        toggleBusinessType
    );

    updateIndexes();
    toggleBusinessType();
});