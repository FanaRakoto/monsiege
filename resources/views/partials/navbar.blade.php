<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">

    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- =========================================================
             LOGO
        ========================================================== --}}
        <a
            href="{{ url('/') }}"
            class="flex items-center gap-3"
        >
            <img
                src="{{ asset('images/monsiege-icone-anime.png') }}"
                alt="MonSiège"
                class="h-11 w-11 rounded-xl object-cover"
            >

            <div class="leading-none">
                <div class="text-2xl font-extrabold tracking-tight">
                    <span class="text-[#0B6E4F]">Mon</span><span class="text-[#F47C20]">Siège</span>
                </div>

                <div class="mt-1 hidden text-[10px] font-medium uppercase tracking-[0.18em] text-slate-400 sm:block">
                    Voyagez autrement
                </div>
            </div>
        </a>


        {{-- =========================================================
             NAVIGATION DESKTOP
        ========================================================== --}}
        <nav class="hidden items-center gap-8 md:flex">

            <a
                href="{{ url('/') }}"
                class="text-sm font-semibold text-[#0B6E4F] transition hover:text-[#F47C20]"
            >
                Accueil
            </a>

            <a
                href="{{ url('/#voyages') }}"
                class="text-sm font-medium text-slate-600 transition hover:text-[#0B6E4F]"
            >
                Voyages
            </a>

            <a
                href="{{ url('/#reservations') }}"
                class="text-sm font-medium text-slate-600 transition hover:text-[#0B6E4F]"
            >
                Mes réservations
            </a>

            <a
                href="{{ url('/#aide') }}"
                class="text-sm font-medium text-slate-600 transition hover:text-[#0B6E4F]"
            >
                Aide
            </a>

        </nav>


        {{-- =========================================================
             ACTIONS DESKTOP
        ========================================================== --}}
        <div class="hidden items-center gap-3 md:flex">

            <a
                href="#connexion"
                class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
            >
                Connexion
            </a>

            <a
                href="#inscription"
                class="rounded-xl bg-[#F47C20] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-600 hover:shadow-md"
            >
                S'inscrire
            </a>

        </div>


        {{-- =========================================================
             BOUTON MOBILE
        ========================================================== --}}
        <button
            id="mobile-menu-button"
            type="button"
            class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-700 transition hover:border-[#0B6E4F] hover:text-[#0B6E4F] md:hidden"
            aria-label="Ouvrir le menu"
            aria-expanded="false"
            aria-controls="mobile-menu"
        >

            {{-- Icône menu --}}
            <svg
                id="menu-open-icon"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 6h16M4 12h16M4 18h16"
                />
            </svg>

            {{-- Icône X --}}
            <svg
                id="menu-close-icon"
                class="hidden h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 6l12 12M18 6L6 18"
                />
            </svg>

        </button>

    </div>


    {{-- =========================================================
         MENU MOBILE
    ========================================================== --}}
    <div
        id="mobile-menu"
        class="hidden border-t border-slate-200 bg-white md:hidden"
    >

        <nav class="mx-auto max-w-7xl px-4 py-4 sm:px-6">

            <div class="flex flex-col">

                <a
                    href="{{ url('/') }}"
                    class="mobile-menu-link rounded-xl px-4 py-3 text-sm font-semibold text-[#0B6E4F] transition hover:bg-emerald-50"
                >
                    Accueil
                </a>

                <a
                    href="{{ url('/#voyages') }}"
                    class="mobile-menu-link rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-emerald-50 hover:text-[#0B6E4F]"
                >
                    Voyages
                </a>

                <a
                    href="{{ url('/#reservations') }}"
                    class="mobile-menu-link rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-emerald-50 hover:text-[#0B6E4F]"
                >
                    Mes réservations
                </a>

                <a
                    href="{{ url('/#aide') }}"
                    class="mobile-menu-link rounded-xl px-4 py-3 text-sm font-medium text-slate-600 transition hover:bg-emerald-50 hover:text-[#0B6E4F]"
                >
                    Aide
                </a>

                {{-- Séparateur --}}
                <div class="my-3 border-t border-slate-100"></div>

                <a
                    href="#connexion"
                    class="mobile-menu-link rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                >
                    Connexion
                </a>

                <a
                    href="#inscription"
                    class="mobile-menu-link mt-2 rounded-xl bg-[#F47C20] px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-orange-600"
                >
                    S'inscrire
                </a>

            </div>

        </nav>

    </div>

</header>