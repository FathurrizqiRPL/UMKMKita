document.querySelectorAll('[data-image-upload]').forEach(upload => {
    const input = upload.querySelector('[data-image-input]');
    const preview = upload.querySelector('[data-image-preview]');
    const content = upload.querySelector('[data-upload-content]');
    const cancel = upload.querySelector('[data-image-cancel]');
    const original = preview?.dataset.original || '';

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;

        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
        content.hidden = true;
        cancel.hidden = false;
    });

    cancel.addEventListener('click', event => {
        event.preventDefault();
        event.stopPropagation();

        input.value = '';
        cancel.hidden = true;

        if (original) {
            preview.src = original;
            preview.hidden = false;
            content.hidden = true;
        } else {
            preview.src = '';
            preview.hidden = true;
            content.hidden = false;
        }
    });
});
