const bookingForm = document.querySelector('[data-booking-request-form]');
if (bookingForm) {
  const start = bookingForm.querySelector('[data-start-date]');
  const end = bookingForm.querySelector('[data-end-date]');
  const total = document.querySelector('[data-booking-total]');
  const counter = bookingForm.querySelector('[data-char-counter]');
  const count = bookingForm.querySelector('[data-char-count]');

  const money = (value) => `${Math.round(value)} PLN`;
  const updateTotal = () => {
    if (!start.value || !end.value || !total) return;
    const startDate = new Date(`${start.value}T00:00:00`);
    const endDate = new Date(`${end.value}T00:00:00`);
    const days = Math.round((endDate - startDate) / 86400000);
    const weekly = Number(total.dataset.weekly || 0);
    const deposit = Number(total.dataset.deposit || 0);
    if (days < 1 || days > 30) {
      total.textContent = 'Choose 1 to 30 rental days for the MVP.';
      return;
    }
    const rental = (days / 7) * weekly;
    total.textContent = `${days} days = ${money(rental)} rental + ${money(deposit)} deposit (refundable) = ${money(rental + deposit)} due at approval`;
  };

  start?.addEventListener('change', updateTotal);
  end?.addEventListener('change', updateTotal);
  counter?.addEventListener('input', () => {
    if (count) count.textContent = counter.value.length;
  });
  updateTotal();
}
