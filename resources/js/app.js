//
document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    const openIcon = document.getElementById('menu-open-icon');
    const closeIcon = document.getElementById('menu-close-icon');

    if (!menuButton || !mobileMenu || !openIcon || !closeIcon) {
        return;
    }

    const openMenu = () => {
        mobileMenu.classList.remove('hidden');

        openIcon.classList.add('hidden');
        closeIcon.classList.remove('hidden');

        menuButton.setAttribute('aria-expanded', 'true');
        menuButton.setAttribute('aria-label', 'Fermer le menu');
    };

    const closeMenu = () => {
        mobileMenu.classList.add('hidden');

        openIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');

        menuButton.setAttribute('aria-expanded', 'false');
        menuButton.setAttribute('aria-label', 'Ouvrir le menu');
    };

    const toggleMenu = () => {
        const isOpen = menuButton.getAttribute('aria-expanded') === 'true';

        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    };

    // Ouvrir / fermer avec le bouton
    menuButton.addEventListener('click', toggleMenu);

    // Fermer automatiquement après avoir cliqué sur un lien
    const mobileLinks = document.querySelectorAll('.mobile-menu-link');

    mobileLinks.forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    // Fermer avec la touche Échap
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    // Revenir à l'état desktop quand l'écran dépasse md
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            closeMenu();
        }
    });
});