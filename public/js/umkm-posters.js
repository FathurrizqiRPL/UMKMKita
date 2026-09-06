const posterContainer = document.querySelector('[data-poster-container]');
const posterTemplate = document.getElementById('posterFieldTemplate');
const addPosterButton = document.querySelector('[data-add-poster]');

function refreshPosterCards() {
    document.querySelectorAll('[data-poster-card]').forEach((card, index) => {
        const kicker = card.querySelector('.poster-kicker');
        if (kicker) kicker.textContent = `POSTER ${index + 1}`;
    });
}

function bindPosterCard(card) {
    const fileInput = card.querySelector('[data-poster-file]');
    const fileName = card.querySelector('[data-poster-file-name]');
    const removeButton = card.querySelector('[data-remove-poster]');

    fileInput?.addEventListener('change', () => {
        if (fileName) fileName.textContent = fileInput.files[0]?.name || 'JPG, PNG, atau WEBP · maks. 5 MB';
    });

    removeButton?.addEventListener('click', () => {
        card.remove();
        refreshPosterCards();
    });
}

document.querySelectorAll('[data-poster-card]').forEach(bindPosterCard);

document.querySelectorAll('[data-existing-poster]').forEach(card => {
    const fileInput = card.querySelector('[data-poster-file]');
    const fileName = card.querySelector('[data-poster-file-name]');
    const deleteInput = card.querySelector('[data-delete-poster-input]');
    const deleteButton = card.querySelector('[data-delete-existing-poster]');

    fileInput?.addEventListener('change', () => {
        if (fileName) fileName.textContent = fileInput.files[0]?.name || 'Kosongkan jika tidak ingin mengganti';
    });

    deleteButton?.addEventListener('click', () => {
        deleteInput.checked = !deleteInput.checked;
        card.classList.toggle('marked-for-delete', deleteInput.checked);
        deleteButton.textContent = deleteInput.checked ? 'Batalkan Hapus' : 'Hapus Poster';
    });
});

addPosterButton?.addEventListener('click', () => {
    if (!posterContainer || !posterTemplate) return;

    const index = Number(posterContainer.dataset.nextIndex || 0);
    const fragment = posterTemplate.content.cloneNode(true);
    const card = fragment.querySelector('[data-poster-card]');
    const title = card.querySelector('[data-poster-title]');
    const file = card.querySelector('[data-poster-file]');

    title.name = `posters[${index}][title]`;
    file.name = `posters[${index}][image]`;
    posterContainer.dataset.nextIndex = index + 1;
    posterContainer.appendChild(fragment);
    bindPosterCard(posterContainer.lastElementChild);
    refreshPosterCards();
});
