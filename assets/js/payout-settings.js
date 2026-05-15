(function () {
  const form = document.querySelector('[data-payout-form]');
  if (!form) return;

  const cards = Array.from(document.querySelectorAll('[data-payout-card]'));
  const delayInput = document.querySelector('[data-payout-delay-input]');
  const saveButton = document.querySelector('[data-payout-save]');
  const selectedLabel = document.querySelector('[data-payout-selected-label]');
  const initialDelay = form.dataset.initialDelay || (delayInput ? delayInput.value : '7');

  function setSelected(delay) {
    if (!delayInput) return;
    delayInput.value = delay;
    cards.forEach((card) => {
      const selected = card.dataset.delay === delay;
      card.classList.toggle('is-selected', selected);
      card.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });
    if (selectedLabel) {
      selectedLabel.textContent = `${delay}-day payout selected`;
    }
    if (saveButton) {
      saveButton.disabled = delay === initialDelay && !form.dataset.dirty;
    }
  }

  cards.forEach((card) => {
    card.addEventListener('click', () => {
      setSelected(card.dataset.delay);
    });
  });

  form.addEventListener('input', () => {
    form.dataset.dirty = '1';
    if (saveButton) saveButton.disabled = false;
  });

  form.addEventListener('submit', () => {
    if (saveButton) {
      saveButton.disabled = true;
      saveButton.textContent = 'Saving...';
    }
  });
})();
