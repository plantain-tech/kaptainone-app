(function () {
    const gallery = document.querySelector('[data-listing-gallery]');
    const lightbox = document.querySelector('[data-listing-lightbox]');
    if (!gallery || !lightbox) return;

    const images = Array.from(gallery.querySelectorAll('[data-lightbox-image]'));
    const fullImage = lightbox.querySelector('[data-lightbox-full]');
    const closeButtons = lightbox.querySelectorAll('[data-lightbox-close]');
    const prev = lightbox.querySelector('[data-lightbox-prev]');
    const next = lightbox.querySelector('[data-lightbox-next]');
    let activeIndex = 0;

    function show(index) {
        if (!images.length) return;
        activeIndex = (index + images.length) % images.length;
        const image = images[activeIndex];
        fullImage.src = image.dataset.fullSrc || image.src;
        fullImage.alt = image.alt || 'Listing photo';
        lightbox.hidden = false;
        lightbox.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lightbox.classList.remove('is-open');
        lightbox.hidden = true;
        fullImage.removeAttribute('src');
        document.body.style.overflow = '';
    }

    images.forEach((image, index) => {
        image.addEventListener('click', () => show(index));
        image.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                show(index);
            }
        });
    });

    closeButtons.forEach((button) => button.addEventListener('click', close));
    prev?.addEventListener('click', () => show(activeIndex - 1));
    next?.addEventListener('click', () => show(activeIndex + 1));

    document.addEventListener('keydown', (event) => {
        if (lightbox.hidden) return;
        if (event.key === 'Escape') close();
        if (event.key === 'ArrowLeft') show(activeIndex - 1);
        if (event.key === 'ArrowRight') show(activeIndex + 1);
    });
})();
