(function () {
  const notes = document.querySelector('[data-owner-notes]');
  const counter = document.querySelector('[data-owner-notes-count]');
  const form = document.querySelector('[data-owner-profile-form]');

  if (notes && counter) {
    const syncCount = () => {
      counter.textContent = String(notes.value.length);
    };
    notes.addEventListener('input', syncCount);
    syncCount();
  }

  if (form && form.dataset.hasErrors === '1') {
    const firstError = form.querySelector('.has-error');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstError.focus({ preventScroll: true });
    }
  }
})();
