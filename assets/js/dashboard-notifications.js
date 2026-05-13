document.addEventListener('click', (event) => {
  document.querySelectorAll('[data-notification-menu]').forEach((menu) => {
    const toggle = menu.querySelector('[data-notification-toggle]');
    const dropdown = menu.querySelector('[data-notification-dropdown]');
    if (!toggle || !dropdown) return;
    if (toggle.contains(event.target)) {
      dropdown.hidden = !dropdown.hidden;
      return;
    }
    if (!dropdown.contains(event.target)) {
      dropdown.hidden = true;
    }
  });

  if (event.target.matches('[data-flash-dismiss]')) {
    const banner = event.target.closest('[data-flash-banner]');
    if (banner) banner.remove();
  }
});

setTimeout(() => {
  document.querySelectorAll('[data-flash-banner]').forEach((banner) => banner.remove());
}, 5000);
