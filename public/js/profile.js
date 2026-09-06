document.querySelectorAll('[data-profile-menu]').forEach(menu => {
    const trigger = menu.querySelector('[data-profile-trigger]');
    const dropdown = menu.querySelector('[data-profile-dropdown]');

    if (!trigger || !dropdown) return;

    function closeProfileMenu() {
        dropdown.hidden = true;
        menu.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', event => {
        event.stopPropagation();

        const open = dropdown.hidden;
        dropdown.hidden = !open;
        menu.classList.toggle('open', open);
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', event => {
        if (!menu.contains(event.target)) closeProfileMenu();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeProfileMenu();
    });
});
