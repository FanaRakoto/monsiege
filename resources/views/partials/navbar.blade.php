<header
    id="site-header"
    class="fixed inset-x-0 top-0 z-50 border-b border-transparent bg-white/90 backdrop-blur-xl">
    <div
        id="navbar-inner"
        class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- LOGO --}}
        <a href="{{ url('/') }}" class="group flex shrink-0 items-center gap-2.5 sm:gap-3">
            <img
                id="brand-logo"
                src="{{ asset('images/monsiege-icone-anime.png') }}"
                alt="Logo MonSiège"
                class="h-11 w-11 rounded-xl object-cover shadow-sm">

            <div class="leading-none">
                <div class="text-xl font-extrabold tracking-tight sm:text-2xl">
                    <span class="text-[#0B6E4F]">Mon</span><span class="text-[#F47C20]">Siège</span>
                </div>

                <p class="mt-1 hidden text-[9px] font-semibold uppercase tracking-[0.18em] text-slate-400 sm:block">
                    Voyagez autrement
                </p>
            </div>
        </a>

        {{-- NAVIGATION DESKTOP --}}
        <nav class="hidden items-center gap-6 lg:flex xl:gap-8">
            <a
                href="{{ url('/') }}"
                class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                Accueil
            </a>

            <a href="{{ url('/#voyages') }}" class="nav-link">
                Voyages
            </a>

            @auth
            @if(auth()->user()->role === 'client')
            <a href="{{ route('client.dashboard') }}" class="nav-link">
                Mes réservations
            </a>
            @else
            <a href="{{ url('/#reservations') }}" class="nav-link">
                Réservations
            </a>
            @endif
            @else
            <a href="{{ route('login') }}" class="nav-link">
                Mes réservations
            </a>
            @endauth

            <a href="{{ url('/#aide') }}" class="nav-link">
                Aide
            </a>
        </nav>

        {{-- ACTIONS DESKTOP : VISITEUR OU CLIENT CONNECTÉ --}}
        <div class="hidden items-center gap-3 lg:flex">
            @guest
            <a
                href="{{ route('login') }}"
                class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 transition duration-200 hover:bg-emerald-50 hover:text-[#0B6E4F]">
                Connexion
            </a>

            <a
                href="{{ route('register') }}"
                class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-orange-600 hover:shadow-lg">
                S'inscrire
            </a>
            @else
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 font-extrabold text-[#0B6E4F]">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="max-w-32">
                    <p class="text-[10px] text-slate-400">Bienvenue</p>
                    <p class="truncate text-sm font-bold text-slate-800">
                        {{ auth()->user()->name }}
                    </p>
                </div>
            </div>

            @if(auth()->user()->role === 'client')
            <a
                href="{{ route('client.dashboard') }}"
                class="rounded-xl border border-emerald-100 px-4 py-2.5 text-sm font-bold text-[#0B6E4F] transition hover:bg-emerald-50">
                Mon espace
            </a>
            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600">
                    Déconnexion
                </button>
            </form>
            @endguest
        </div>

        {{-- BOUTON DU MENU MOBILE --}}
        <button
            id="mobile-menu-button"
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition duration-200 hover:border-[#0B6E4F] hover:text-[#0B6E4F] focus:outline-none focus:ring-2 focus:ring-emerald-200 lg:hidden"
            aria-label="Ouvrir le menu"
            aria-expanded="false"
            aria-controls="mobile-menu">
            <svg
                id="menu-open-icon"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>

            <svg
                id="menu-close-icon"
                class="hidden h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    {{-- MENU MOBILE ANIMÉ --}}
    <div
        id="mobile-menu"
        class="pointer-events-none max-h-0 -translate-y-2 overflow-y-auto border-t-0 border-slate-100 bg-white opacity-0 shadow-xl transition-[max-height,opacity,transform] duration-300 ease-out lg:hidden"
        aria-hidden="true"
        inert>
        <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4 sm:px-6">
            <a href="{{ url('/') }}" class="mobile-nav-link">
                <span>Accueil</span>
                <span class="text-slate-300">›</span>
            </a>

            <a href="{{ url('/#voyages') }}" class="mobile-nav-link">
                <span>Découvrir les voyages</span>
                <span class="text-slate-300">›</span>
            </a>

            @auth
            @if(auth()->user()->role === 'client')
            <a href="{{ route('client.dashboard') }}" class="mobile-nav-link">
                <span>Mes réservations</span>
                <span class="text-slate-300">›</span>
            </a>
            @else
            <a href="{{ url('/#reservations') }}" class="mobile-nav-link">
                <span>Réservations</span>
                <span class="text-slate-300">›</span>
            </a>
            @endif
            @else
            <a href="{{ route('login') }}" class="mobile-nav-link">
                <span>Mes réservations</span>
                <span class="text-slate-300">›</span>
            </a>
            @endauth

            <a href="{{ url('/#aide') }}" class="mobile-nav-link">
                <span>Aide et assistance</span>
                <span class="text-slate-300">›</span>
            </a>

            <div class="my-3 border-t border-slate-100"></div>

            {{-- ACTIONS MOBILE DYNAMIQUES --}}
            @guest
            <a
                href="{{ route('login') }}"
                class="rounded-xl px-4 py-3 text-center text-sm font-bold text-[#0B6E4F] transition hover:bg-emerald-50">
                Connexion
            </a>

            <a
                href="{{ route('register') }}"
                class="mt-1 rounded-xl bg-[#F47C20] px-4 py-3.5 text-center text-sm font-extrabold text-white shadow-sm transition hover:bg-orange-600">
                Créer un compte
            </a>
            @else
            <div class="mb-2 rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs font-medium text-slate-500">Connecté en tant que</p>
                <p class="mt-1 break-words font-extrabold text-[#0B6E4F]">
                    {{ auth()->user()->name }}
                </p>
            </div>

            @if(auth()->user()->role === 'client')
            <a
                href="{{ route('client.dashboard') }}"
                class="rounded-xl bg-[#0B6E4F] px-4 py-3.5 text-center text-sm font-bold text-white transition hover:bg-emerald-800">
                Accéder à mon espace
            </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="mt-1">
                @csrf
                <button
                    type="submit"
                    class="w-full rounded-xl px-4 py-3.5 text-sm font-bold text-rose-600 transition hover:bg-rose-50">
                    Déconnexion
                </button>
            </form>
            @endguest
        </nav>
    </div>
</header>

{{-- Espace réservé au navbar fixe --}}
<div class="h-20" aria-hidden="true"></div>

<script>
    (() => {
        function initMonSiegeNavbar() {
            const header = document.getElementById('site-header');
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const openIcon = document.getElementById('menu-open-icon');
            const closeIcon = document.getElementById('menu-close-icon');

            if (!header || !menuButton || !mobileMenu) {
                return;
            }

            // Évite de connecter plusieurs fois les mêmes événements.
            if (header.dataset.navbarReady === 'true') {
                return;
            }
            header.dataset.navbarReady = 'true';

            function updateNavbar() {
                header.classList.toggle('is-scrolled', window.scrollY > 12);
            }

            function setMenuOpen(isOpen) {
                menuButton.setAttribute('aria-expanded', String(isOpen));
                menuButton.setAttribute(
                    'aria-label',
                    isOpen ? 'Fermer le menu' : 'Ouvrir le menu'
                );

                mobileMenu.setAttribute('aria-hidden', String(!isOpen));
                mobileMenu.inert = !isOpen;

                mobileMenu.classList.toggle('pointer-events-none', !isOpen);
                mobileMenu.classList.toggle('opacity-0', !isOpen);
                mobileMenu.classList.toggle('opacity-100', isOpen);
                mobileMenu.classList.toggle('-translate-y-2', !isOpen);
                mobileMenu.classList.toggle('translate-y-0', isOpen);
                mobileMenu.classList.toggle('max-h-0', !isOpen);

                if (isOpen) {
                    mobileMenu.style.maxHeight =
                        Math.min(
                            mobileMenu.scrollHeight,
                            window.innerHeight * 0.8
                        ) + 'px';

                    mobileMenu.classList.add('border-t');
                } else {
                    mobileMenu.style.maxHeight = '0px';
                    mobileMenu.classList.remove('border-t');
                }

                if (openIcon) {
                    openIcon.classList.toggle('hidden', isOpen);
                }

                if (closeIcon) {
                    closeIcon.classList.toggle('hidden', !isOpen);
                }
            }

            menuButton.addEventListener('click', () => {
                const currentlyOpen =
                    menuButton.getAttribute('aria-expanded') === 'true';

                setMenuOpen(!currentlyOpen);
            });

            // Ferme le menu lorsqu'un lien est sélectionné.
            mobileMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setMenuOpen(false));
            });

            // Ferme le menu si l'utilisateur clique ailleurs.
            document.addEventListener('click', (event) => {
                if (
                    menuButton.getAttribute('aria-expanded') === 'true' &&
                    !header.contains(event.target)
                ) {
                    setMenuOpen(false);
                }
            });

            // Ferme le menu avec la touche Échap.
            document.addEventListener('keydown', (event) => {
                if (
                    event.key === 'Escape' &&
                    menuButton.getAttribute('aria-expanded') === 'true'
                ) {
                    setMenuOpen(false);
                    menuButton.focus();
                }
            });

            // Surveille le défilement sans recalculer toute la page.
            window.addEventListener('scroll', updateNavbar, {
                passive: true
            });

            // Referme le menu si l'écran passe en mode ordinateur.
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    setMenuOpen(false);
                } else if (
                    menuButton.getAttribute('aria-expanded') === 'true'
                ) {
                    mobileMenu.style.maxHeight =
                        Math.min(
                            mobileMenu.scrollHeight,
                            window.innerHeight * 0.8
                        ) + 'px';
                }
            });

            updateNavbar();
            setMenuOpen(false);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMonSiegeNavbar);
        } else {
            initMonSiegeNavbar();
        }
    })();
</script>