(function () {
    const form = document.querySelector('[data-listing-form]');
    if (!form) return;

    const input = form.querySelector('[data-photo-input]');
    const preview = form.querySelector('[data-photo-preview]');
    const errorEl = form.querySelector('[data-photo-error]');
    const primarySubmit = form.querySelector('[data-primary-submit]');
    const overlay = form.querySelector('[data-submit-overlay]');
    const maxFiles = 6;
    const maxSize = 5 * 1024 * 1024;
    let objectUrls = [];

    function formatSize(bytes) {
        if (bytes >= 1024 * 1024) return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
        return `${Math.max(1, Math.round(bytes / 1024))} KB`;
    }

    function setError(message) {
        if (!errorEl) return;
        errorEl.textContent = message || '';
        errorEl.hidden = !message;
        if (primarySubmit) primarySubmit.disabled = Boolean(message);
    }

    function updateInputFiles(files) {
        const transfer = new DataTransfer();
        files.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
    }

    function clearObjectUrls() {
        objectUrls.forEach((url) => URL.revokeObjectURL(url));
        objectUrls = [];
    }

    function validate(files) {
        const existingCount = parseInt(input.dataset.existingCount || '0', 10);
        if (files.length + existingCount > maxFiles) {
            return `You can upload up to ${maxFiles} photos. Please remove some.`;
        }
        const oversized = files.find((file) => file.size > maxSize);
        if (oversized) {
            return `Photo '${oversized.name}' is ${formatSize(oversized.size)} - must be 5MB or smaller.`;
        }
        return '';
    }

    function renderPreview() {
        if (!input || !preview) return;
        clearObjectUrls();
        preview.innerHTML = '';
        const files = Array.from(input.files || []);
        const message = validate(files);
        setError(message);

        files.forEach((file, index) => {
            const url = URL.createObjectURL(file);
            objectUrls.push(url);

            const card = document.createElement('div');
            card.className = 'listing-photo-preview';

            const image = document.createElement('img');
            image.src = url;
            image.alt = file.name;
            card.appendChild(image);

            if (index === 0) {
                const badge = document.createElement('span');
                badge.className = 'listing-photo-preview__badge';
                badge.textContent = 'Primary';
                card.appendChild(badge);
            }

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'listing-photo-preview__remove';
            remove.setAttribute('aria-label', `Remove ${file.name}`);
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                const nextFiles = Array.from(input.files).filter((_, fileIndex) => fileIndex !== index);
                updateInputFiles(nextFiles);
                renderPreview();
            });
            card.appendChild(remove);

            const meta = document.createElement('small');
            meta.textContent = `${file.name} · ${formatSize(file.size)}`;
            card.appendChild(meta);
            preview.appendChild(card);
        });
    }

    if (input) {
        input.addEventListener('change', renderPreview);
    }

    const description = document.getElementById('description');
    const count = document.getElementById('description-count');
    if (description && count) {
        description.addEventListener('input', () => {
            count.textContent = description.value.length;
        });
    }

    if (form.dataset.hasErrors === '1') {
        const firstError = form.querySelector('.form-error:not([hidden])');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const field = firstError.closest('.form-group')?.querySelector('input, select, textarea');
            if (field) field.focus({ preventScroll: true });
        }
    }

    form.addEventListener('submit', (event) => {
        if (input) {
            const message = validate(Array.from(input.files || []));
            if (message) {
                event.preventDefault();
                setError(message);
                errorEl?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
        }
        if (overlay) overlay.hidden = false;
    });
})();
