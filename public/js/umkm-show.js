const mapElement = document.getElementById('businessMap');
const points = Array.isArray(window.umkmMapData) ? window.umkmMapData : [];
const isMobileBusiness = window.umkmBusinessType === 'keliling';

if (mapElement && points.length && typeof L !== 'undefined') {
    const map = L.map(mapElement, {
        scrollWheelZoom: false,
        zoomControl: true
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const bounds = [];

    points.forEach(point => {
        const lat = Number(point.latitude);
        const lng = Number(point.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

        const markerHtml = isMobileBusiness
            ? `<div class="umkm-map-marker"><span>${point.number}</span></div>`
            : '<div class="umkm-map-marker fixed"><span></span></div>';

        const icon = L.divIcon({
            className: '',
            html: markerHtml,
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        L.marker([lat, lng], { icon })
            .addTo(map)
            .bindTooltip(point.name || point.address || 'Lokasi UMKM', {
                direction: 'top',
                offset: [0, -18]
            });

        bounds.push([lat, lng]);
    });

    if (bounds.length === 1) {
        map.setView(bounds[0], 16);
    } else if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [45, 45], maxZoom: 16 });
    }

    setTimeout(() => map.invalidateSize(), 150);
}

const mobileMenuButton = document.getElementById('mobileMenuButton');
const businessNavigation = document.getElementById('businessNavigation');

if (mobileMenuButton && businessNavigation) {
    mobileMenuButton.addEventListener('click', function () {
        businessNavigation.classList.toggle('open');
    });

    businessNavigation.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function () {
            businessNavigation.classList.remove('open');
        });
    });

    document.addEventListener('click', function (event) {
        if (businessNavigation.contains(event.target) || mobileMenuButton.contains(event.target)) return;
        businessNavigation.classList.remove('open');
    });
}

/* JAM OPERASIONAL */

const hoursDropdownTrigger = document.getElementById('hoursDropdownTrigger');
const hoursDropdown = document.getElementById('hoursDropdown');

if (hoursDropdownTrigger && hoursDropdown) {
    function closeHoursDropdown() {
        hoursDropdown.hidden = true;
        hoursDropdownTrigger.classList.remove('open');
    }

    hoursDropdownTrigger.addEventListener('click', function (event) {
        event.stopPropagation();

        const isOpen = !hoursDropdown.hidden;
        hoursDropdown.hidden = isOpen;
        hoursDropdownTrigger.classList.toggle('open', !isOpen);
    });

    hoursDropdown.addEventListener('click', function (event) {
        event.stopPropagation();
    });

    document.addEventListener('click', closeHoursDropdown);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeHoursDropdown();
    });
}

/* PRODUCT CAROUSEL */

const productCarousel = document.getElementById('productCarousel');
const productPrev = document.getElementById('productPrev');
const productNext = document.getElementById('productNext');

function getProductScrollAmount() {
    if (!productCarousel) return 0;

    const card = productCarousel.querySelector('.product-card');
    if (!card) return 0;

    const gap = parseFloat(getComputedStyle(productCarousel).gap) || 0;
    return card.getBoundingClientRect().width + gap;
}

productPrev?.addEventListener('click', function () {
    productCarousel.scrollBy({
        left: -getProductScrollAmount(),
        behavior: 'smooth'
    });
});

productNext?.addEventListener('click', function () {
    productCarousel.scrollBy({
        left: getProductScrollAmount(),
        behavior: 'smooth'
    });
});

/* POSTER MODAL */

const posterModal = document.getElementById('posterModal');
const posterImage = document.getElementById('posterModalImage');
const posterTitle = document.getElementById('posterModalTitle');
const posterStage = document.getElementById('posterModalStage');
const posterZoomValue = document.getElementById('posterZoomValue');
const zoomInButton = document.getElementById('posterZoomIn');
const zoomOutButton = document.getElementById('posterZoomOut');
const zoomResetButton = document.getElementById('posterZoomReset');

let posterScale = 1;
let posterX = 0;
let posterY = 0;
let posterDragging = false;
let posterDragStartX = 0;
let posterDragStartY = 0;
let posterStartX = 0;
let posterStartY = 0;

function clampScale(value) {
    return Math.min(5, Math.max(.1, value));
}

function renderPosterTransform() {
    if (!posterImage) return;

    posterImage.style.transform = `translate(-50%, -50%) translate(${posterX}px, ${posterY}px) scale(${posterScale})`;

    if (posterZoomValue) {
        posterZoomValue.textContent = `${Math.round(posterScale * 100)}%`;
    }
}

function resetPosterView() {
    posterScale = 1;
    posterX = 0;
    posterY = 0;
    renderPosterTransform();
}

function fitPosterImage() {
    if (!posterImage || !posterStage) return;

    const stageWidth = posterStage.clientWidth;
    const stageHeight = posterStage.clientHeight;
    const imageWidth = posterImage.naturalWidth;
    const imageHeight = posterImage.naturalHeight;

    if (!imageWidth || !imageHeight) return;

    posterImage.style.width = `${imageWidth}px`;
    posterImage.style.height = `${imageHeight}px`;

    posterScale = Math.min(
        (stageWidth - 40) / imageWidth,
        (stageHeight - 40) / imageHeight,
        1
    );

    posterScale = Math.max(.1, posterScale);
    posterX = 0;
    posterY = 0;

    renderPosterTransform();
}

function openPosterModal(src, title) {
    if (!posterModal || !posterImage) return;

    posterModal.hidden = false;
    document.body.classList.add('modal-open');

    posterTitle.textContent = title || 'Poster';
    posterImage.alt = title || 'Poster';

    posterImage.onload = function () {
        requestAnimationFrame(fitPosterImage);
    };

    posterImage.src = src;
}

function closePosterModal() {
    if (!posterModal) return;

    posterModal.hidden = true;
    document.body.classList.remove('modal-open');
    posterDragging = false;
    posterStage?.classList.remove('dragging');
    resetPosterView();
}

document.querySelectorAll('.poster-open-button').forEach(button => {
    button.addEventListener('click', function () {
        openPosterModal(button.dataset.src, button.dataset.title);
    });
});

document.querySelectorAll('[data-close-poster]').forEach(button => {
    button.addEventListener('click', closePosterModal);
});

zoomInButton?.addEventListener('click', function () {
    posterScale = clampScale(posterScale + .2);
    renderPosterTransform();
});

zoomOutButton?.addEventListener('click', function () {
    posterScale = clampScale(posterScale - .2);
    renderPosterTransform();
});

zoomResetButton?.addEventListener('click', fitPosterImage);

posterStage?.addEventListener('wheel', function (event) {
    event.preventDefault();

    posterScale = clampScale(posterScale + (event.deltaY < 0 ? .15 : -.15));
    renderPosterTransform();
}, { passive: false });

posterStage?.addEventListener('pointerdown', function (event) {
    if (event.button !== 0) return;

    posterDragging = true;
    posterDragStartX = event.clientX;
    posterDragStartY = event.clientY;
    posterStartX = posterX;
    posterStartY = posterY;

    posterStage.setPointerCapture(event.pointerId);
    posterStage.classList.add('dragging');
});

posterStage?.addEventListener('pointermove', function (event) {
    if (!posterDragging) return;

    posterX = posterStartX + event.clientX - posterDragStartX;
    posterY = posterStartY + event.clientY - posterDragStartY;

    renderPosterTransform();
});

function stopPosterDrag(event) {
    if (!posterDragging) return;

    posterDragging = false;
    posterStage?.classList.remove('dragging');

    if (event?.pointerId && posterStage?.hasPointerCapture(event.pointerId)) {
        posterStage.releasePointerCapture(event.pointerId);
    }
}

posterStage?.addEventListener('pointerup', stopPosterDrag);
posterStage?.addEventListener('pointercancel', stopPosterDrag);
posterStage?.addEventListener('dblclick', fitPosterImage);

document.addEventListener('keydown', function (event) {
    if (!posterModal || posterModal.hidden) return;

    if (event.key === 'Escape') {
        closePosterModal();
        return;
    }

    if (event.key === '+' || event.key === '=') {
        posterScale = clampScale(posterScale + .2);
        renderPosterTransform();
    }

    if (event.key === '-') {
        posterScale = clampScale(posterScale - .2);
        renderPosterTransform();
    }
});

window.addEventListener('resize', function () {
    if (posterModal && !posterModal.hidden) fitPosterImage();
});
