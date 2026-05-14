(function () {
  const modal = document.querySelector('[data-owner-listing-modal]');
  if (!modal) return;

  const title = modal.querySelector('[data-modal-title]');
  const message = modal.querySelector('[data-modal-message]');
  const listingId = modal.querySelector('[data-modal-listing-id]');
  const actionInput = modal.querySelector('[data-modal-action]');
  const confirmButton = modal.querySelector('[data-modal-confirm]');
  const form = modal.querySelector('[data-modal-form]');
  const filterForm = document.querySelector('[data-owner-listing-filters]');
  const filterButton = document.querySelector('[data-filter-apply]');

  const copy = {
    pause: {
      title: 'Pause this listing?',
      message: 'This will hide the listing from couriers while keeping it available in your dashboard.',
      confirm: 'Pause',
      loading: 'Pausing...'
    },
    activate: {
      title: 'Reactivate this listing?',
      message: 'This will make the listing visible again on the marketplace for Warsaw couriers.',
      confirm: 'Activate',
      loading: 'Activating...'
    },
    archive: {
      title: 'Archive this listing?',
      message: 'This will hide the listing from public view. You can restore it later from archived listings.',
      confirm: 'Archive',
      loading: 'Archiving...'
    }
  };

  function openModal(action, id, listingTitle) {
    const config = copy[action];
    if (!config) return;
    title.textContent = config.title;
    message.textContent = listingTitle ? `${config.message} Listing: "${listingTitle}"` : config.message;
    listingId.value = id;
    actionInput.value = action;
    confirmButton.textContent = config.confirm;
    confirmButton.dataset.loadingText = config.loading;
    confirmButton.disabled = false;
    modal.hidden = false;
    document.body.classList.add('modal-open');
    confirmButton.focus();
  }

  function closeModal() {
    modal.hidden = true;
    document.body.classList.remove('modal-open');
  }

  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-listing-action]');
    if (trigger) {
      openModal(trigger.dataset.listingAction, trigger.dataset.listingId, trigger.dataset.listingTitle);
      return;
    }
    if (event.target.closest('[data-modal-close]')) {
      closeModal();
    }
  });

  modal.addEventListener('click', (event) => {
    if (event.target.classList.contains('owner-listing-modal__overlay')) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.hidden) {
      closeModal();
    }
  });

  form.addEventListener('submit', () => {
    confirmButton.disabled = true;
    confirmButton.textContent = confirmButton.dataset.loadingText || 'Working...';
  });

  if (filterForm && filterButton) {
    const initial = new FormData(filterForm);
    filterForm.addEventListener('change', () => {
      const current = new FormData(filterForm);
      let changed = false;
      for (const [key, value] of current.entries()) {
        if (initial.get(key) !== value) changed = true;
      }
      filterButton.classList.toggle('btn-primary', changed);
      filterButton.classList.toggle('btn-secondary', !changed);
    });
  }
})();
