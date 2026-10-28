/*
|--------------------------------------------------------------------------
| MonSiège – JavaScript principal
|--------------------------------------------------------------------------
| Chaque fonctionnalité est dans sa propre fonction. Si ses éléments ne sont
| pas dans la page, elle s'arrête toute seule SANS bloquer les autres
| (avant, un « return » dans le menu bloquait aussi la recherche du hero).
*/


/*
|--------------------------------------------------------------------------
| 1. Menu mobile
|--------------------------------------------------------------------------
*/
import '@fortawesome/fontawesome-free/css/all.min.css';

function initMobileMenu() {
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
        isOpen ? closeMenu() : openMenu();
    };

    // Ouvrir / fermer avec le bouton
    menuButton.addEventListener('click', toggleMenu);

    // Fermer automatiquement après avoir cliqué sur un lien
    document.querySelectorAll('.mobile-menu-link').forEach((link) => {
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
}


/*
|--------------------------------------------------------------------------
| 2. Hero : barre de recherche
|--------------------------------------------------------------------------
*/

function initHeroSearch() {
    const form = document.querySelector('#recherche form');

    if (!form) {
        return;
    }

    const from = document.getElementById('departure');
    const to = document.getElementById('destination');
    const date = document.getElementById('departure_date');
    const pax = document.getElementById('passengers');

    const flash = (el) => {
        if (!el) return;
        el.classList.remove('hr-flash');
        void el.offsetWidth; // relance l'animation
        el.classList.add('hr-flash');
    };

    // Date au format AAAA-MM-JJ selon l'heure locale (et non UTC)
    const iso = (d) =>
        `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

    // Trajets populaires : remplissent départ et destination
    document.querySelectorAll('[data-from][data-to]').forEach((btn) => {
        btn.addEventListener('click', () => {
            from.value = btn.dataset.from;
            to.value = btn.dataset.to;
            flash(from.closest('label'));
            flash(to.closest('label'));
            date?.focus();
        });
    });

    // Raccourcis de date : aujourd'hui / demain
    document.querySelectorAll('[data-day]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const d = new Date();
            d.setDate(d.getDate() + Number(btn.dataset.day));
            date.value = iso(d);
            flash(date.closest('label'));
        });
    });

    // Inverser départ et destination
    const swap = document.querySelector('[data-swap]');

    swap?.addEventListener('click', () => {
        [from.value, to.value] = [to.value, from.value];
        swap.dataset.flipped = String(swap.dataset.flipped !== 'true');
        flash(from.closest('label'));
        flash(to.closest('label'));
    });

    // Nombre de passagers : boutons − et +
    document.querySelectorAll('[data-step]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const min = Number(pax.min) || 1;
            const max = Number(pax.max) || 6;
            const next = Math.min(max, Math.max(min, (Number(pax.value) || min) + Number(btn.dataset.step)));

            pax.value = next;
            pax.classList.remove('hr-bump');
            void pax.offsetWidth;
            pax.classList.add('hr-bump');
        });
    });

    // La bordure lumineuse s'arrête quand la barre n'est plus visible
    const glow = document.querySelector('[data-glow]');

    if (glow && 'IntersectionObserver' in window) {
        new IntersectionObserver(([entry]) => {
            glow.classList.toggle('hr-paused', !entry.isIntersecting);
        }).observe(glow);
    }
}


/*
|--------------------------------------------------------------------------
| 3. Sélection des sièges (page de réservation uniquement)
|--------------------------------------------------------------------------
*/

function initSeatSelection() {
    const seatButtons = document.querySelectorAll('.seat-button[data-seat-id]');
    const selectedCountElement = document.getElementById('selected-count');
    const selectedTotalElement = document.getElementById('selected-total');
    const selectedSeatsElement = document.getElementById('selected-seats');
    const continueButton = document.getElementById('continue-reservation');

    // Formulaire d'envoi (déclarés ICI, avant leur utilisation :
    // avant, ils étaient déclarés plus bas et provoquaient une erreur)
    const reservationForm = document.getElementById('seat-selection-form');
    const selectedSeatInputs = document.getElementById('selected-seat-inputs');

    if (
        !seatButtons.length ||
        !selectedCountElement ||
        !selectedTotalElement ||
        !selectedSeatsElement ||
        !continueButton
    ) {
        return;
    }

    // Nombre maximum de sièges = nombre de passagers
    const maxSeats = Number(window.monSiege?.passengerCount ?? 1);

    // Sièges sélectionnés
    const selectedSeats = new Map();

    const formatMGA = (amount) => new Intl.NumberFormat('fr-FR').format(amount) + ' Ar';

    // Actualiser le résumé
    const updateSummary = () => {
        const seats = Array.from(selectedSeats.values());
        const count = seats.length;
        const total = seats.reduce((sum, seat) => sum + seat.price, 0);

        selectedCountElement.textContent = count;
        selectedTotalElement.textContent = formatMGA(total);

        if (!count) {
            selectedSeatsElement.innerHTML = `
                <span class="text-sm text-slate-400">
                    Aucun siège sélectionné
                </span>
            `;
        } else {
            selectedSeatsElement.innerHTML = seats
                .map((seat) => `
                    <span class="rounded-lg bg-orange-50 px-3 py-2 text-sm font-bold text-[#F47C20]">
                        Siège ${seat.number}
                    </span>
                `)
                .join('');
        }

        if (count === maxSeats) {
            continueButton.disabled = false;
            continueButton.className =
                'mt-7 w-full rounded-xl bg-[#F47C20] px-5 py-4 font-bold text-white shadow-sm transition hover:bg-orange-600 hover:shadow-lg';
            continueButton.textContent = 'Continuer';
        } else {
            continueButton.disabled = true;
            continueButton.className =
                'mt-7 w-full cursor-not-allowed rounded-xl bg-slate-200 px-5 py-4 font-bold text-slate-400 transition';
            continueButton.textContent =
                count === 0
                    ? 'Sélectionnez vos sièges'
                    : `Sélectionnez encore ${maxSeats - count} siège(s)`;
        }
    };

    // Cliquer sur un siège
    seatButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const seatId = button.dataset.seatId;
            const seatNumber = button.dataset.seatNumber;
            const price = Number(button.dataset.price);

            // Déjà sélectionné → retirer
            if (selectedSeats.has(seatId)) {
                selectedSeats.delete(seatId);
                button.classList.remove('is-selected');
                updateSummary();
                return;
            }

            // Limite atteinte
            if (selectedSeats.size >= maxSeats) {
                return;
            }

            // Ajouter
            selectedSeats.set(seatId, { id: seatId, number: seatNumber, price });
            button.classList.add('is-selected');
            updateSummary();
        });
    });

    // Envoyer les sièges sélectionnés au serveur
    if (reservationForm && selectedSeatInputs) {
        reservationForm.addEventListener('submit', (event) => {
            if (selectedSeats.size !== maxSeats) {
                event.preventDefault();
                return;
            }

            selectedSeatInputs.innerHTML = '';

            selectedSeats.forEach((seat) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'seat_ids[]';
                input.value = seat.id;
                selectedSeatInputs.appendChild(input);
            });

            // Nombre de passagers
            const passengerInput = document.createElement('input');
            passengerInput.type = 'hidden';
            passengerInput.name = 'passengers';
            passengerInput.value = maxSeats;
            selectedSeatInputs.appendChild(passengerInput);
        });
    }

    updateSummary();
}


/*
|--------------------------------------------------------------------------
| Démarrage
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initHeroSearch();
    initSeatSelection();
});