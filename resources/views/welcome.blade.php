@extends('layouts.app')

@section('title', 'MonSiège — Voyagez à Madagascar')
@section('meta_description', 'Comparez les prochains départs, choisissez votre siège et préparez votre voyage à Madagascar avec MonSiège.')

@section('content')
@php
// Utilise la première photo disponible dans public/images/hero.
// Sans photo de bannière, RN7.jpeg sert d'image de remplacement.
$heroFiles = glob(public_path('images/hero/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}'), GLOB_BRACE) ?: [];
sort($heroFiles, SORT_NATURAL | SORT_FLAG_CASE);
$heroImagePath = !empty($heroFiles)
? 'images/hero/' . basename($heroFiles[0])
: (is_file(public_path('images/voyages/RN7.jpeg')) ? 'images/voyages/RN7.jpeg' : null);
@endphp

<div class="font-sans antialiased text-slate-900">
    {{-- HERO --}}
    <section id="accueil" class="relative isolate overflow-hidden bg-[#063b2c] font-sans">
        @if ($heroImagePath)
        <img src="{{ asset($heroImagePath) }}" alt="" aria-hidden="true" fetchpriority="high" class="absolute inset-0 h-full w-full object-cover object-center opacity-45">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-[#032c21]/95 via-[#063b2c]/85 to-[#063b2c]/45" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#032c21]/85 via-transparent to-black/10" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-24 top-12 h-72 w-72 rounded-full bg-[#F47C20]/20 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute bottom-8 left-12 h-64 w-64 rounded-full bg-emerald-300/10 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-14 pt-16 sm:px-6 sm:pb-20 sm:pt-20 lg:px-8 lg:pb-24 lg:pt-24">
            <div class="max-w-3xl" data-reveal>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-white backdrop-blur-md">
                    <span class="h-2 w-2 rounded-full bg-[#F47C20] shadow-[0_0_0_4px_rgba(244,124,32,.18)]"></span>
                    Le voyage commence ici
                </span>
                <h1 class="mt-6 max-w-3xl font-sans text-4xl font-extrabold leading-[1.06] tracking-tight text-white sm:text-5xl lg:text-7xl">
                    Explorez Madagascar.<br>
                    <span class="text-[#ffad70]">Choisissez votre siège.</span>
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-white/85 sm:text-lg">
                    Comparez les prochains départs, choisissez votre place et préparez votre trajet simplement, de la capitale aux régions de Madagascar.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#recherche" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#F47C20] px-6 py-3 text-sm font-bold text-white shadow-lg shadow-black/20 transition duration-200 hover:-translate-y-0.5 hover:bg-[#df6b13] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#063b2c]">
                        Rechercher un voyage <span aria-hidden="true">→</span>
                    </a>
                    <a href="#prochains-voyages" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-bold text-white backdrop-blur transition duration-200 hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                        Voir les prochains départs
                    </a>
                </div>
                <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-white/90">
                    <span class="inline-flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#ffad70]" aria-hidden="true">✓</span>Choix des sièges</span>
                    <span class="inline-flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#ffad70]" aria-hidden="true">✓</span>Prix en Ariary</span>
                    <span class="inline-flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#ffad70]" aria-hidden="true">✓</span>Billet avec QR Code</span>
                </div>
            </div>

            {{-- Recherche : les noms de paramètres correspondent à TripController@index. --}}
            <div id="recherche" class="mt-12 scroll-mt-28 sm:mt-14" data-reveal>
                <div class="rounded-[1.75rem] border border-white/30 bg-white p-4 shadow-2xl shadow-black/25 sm:p-6">
                    <div class="mb-4 flex items-center gap-3 px-1">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F]" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <circle cx="10.8" cy="10.8" r="6.8" />
                                <path stroke-linecap="round" d="m16 16 4.5 4.5" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-sans text-lg font-extrabold text-slate-900">Où souhaitez-vous aller ?</h2>
                            <p class="text-sm text-slate-500">Recherchez parmi les départs programmés.</p>
                        </div>
                    </div>
                    <form action="{{ route('trips.index') }}" method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label for="home-departure" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Départ</label>
                            <input id="home-departure" name="departure" type="text" maxlength="100" list="monsiege-cities" value="{{ request('departure') }}" placeholder="Antananarivo" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm font-semibold text-slate-800 outline-none transition placeholder:font-normal placeholder:text-slate-400 focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                        </div>
                        <div>
                            <label for="home-destination" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Destination</label>
                            <input id="home-destination" name="destination" type="text" maxlength="100" list="monsiege-cities" value="{{ request('destination') }}" placeholder="Toamasina" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm font-semibold text-slate-800 outline-none transition placeholder:font-normal placeholder:text-slate-400 focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                        </div>
                        <div>
                            <label for="home-departure-date" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Date de départ</label>
                            <input id="home-departure-date" name="departure_date" type="date" min="{{ now()->toDateString() }}" value="{{ request('departure_date') }}" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                        </div>
                        <div>
                            <label for="home-passengers" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Passagers</label>
                            <select id="home-passengers" name="passengers" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm font-semibold text-slate-800 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                                @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" @selected((int) request('passengers', 1)===$i)>{{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-5 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#08583f] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-emerald-900/20">
                                Rechercher <span aria-hidden="true">→</span>
                            </button>
                        </div>
                    </form>
                    @if ($errors->any())
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                        <p class="font-bold">Vérifiez les informations de recherche.</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <datalist id="monsiege-cities">
                        <option value="Antananarivo"></option>
                        <option value="Toamasina"></option>
                        <option value="Fianarantsoa"></option>
                        <option value="Toliara"></option>
                        <option value="Mahajanga"></option>
                        <option value="Antsirabe"></option>
                        <option value="Antsiranana"></option>
                        <option value="Morondava"></option>
                    </datalist>
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm text-white/90">
                    <span class="mr-1 font-semibold">Destinations :</span>
                    @foreach ([['Toamasina','Toamasina'], ['Fianarantsoa','Fianarantsoa'], ['Toliara','Toliara']] as [$label, $destination])
                    <a href="{{ route('trips.index', ['departure' => 'Antananarivo', 'destination' => $destination]) }}" class="rounded-full border border-white/25 bg-white/10 px-3.5 py-2 font-semibold backdrop-blur transition hover:bg-white/20">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="pointer-events-none absolute bottom-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-white/40 to-transparent" aria-hidden="true"></div>
    </section>

    {{-- TROIS PROCHAINS VOYAGES --}}
    <section id="prochains-voyages" class="bg-slate-50 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between" data-reveal>
                <div class="max-w-2xl">
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#F47C20]">Vos prochaines destinations</p>
                    <h2 class="mt-3 font-sans text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Les prochains départs</h2>
                    <p class="mt-3 max-w-xl text-base leading-7 text-slate-600">Des trajets à venir, des prix clairement affichés et le choix du siège avant le départ.</p>
                </div>
                <a href="{{ route('trips.index') }}" class="inline-flex min-h-11 w-fit items-center gap-2 rounded-xl border border-[#0B6E4F]/20 bg-white px-5 py-3 text-sm font-bold text-[#0B6E4F] shadow-sm transition hover:-translate-y-0.5 hover:border-[#0B6E4F]/40 hover:shadow-md">
                    Tous les voyages <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="mt-9 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($popularTrips as $trip)
                @php
                $destinationKey = mb_strtolower(trim($trip->arrival_city));
                $fallbackImage = match ($destinationKey) {
                'toamasina', 'tamatave' => 'images/voyages/toamasina.jpeg',
                'fianarantsoa' => 'images/voyages/fianarantsoa.jpeg',
                'toliara', 'tuléar', 'tulear' => 'images/voyages/toliara-rn7.jpeg',
                'mahajanga' => 'images/voyages/mahajanga.jpeg',
                'antsiranana', 'diego-suarez', 'diégo-suarez' => 'images/voyages/antsiranana.jpeg',
                default => 'images/voyages/RN7.jpeg',
                };
                $storedImage = (string) ($trip->image_path ?? '');
                $tripImagePath = str_starts_with($storedImage, 'images/voyages/') && is_file(public_path($storedImage))
                ? $storedImage
                : $fallbackImage;
                if (!is_file(public_path($tripImagePath))) {
                $tripImagePath = 'images/monsiege-icone-anime.png';
                }
                $departureAt = $trip->departure_at;
                @endphp
                <article class="group flex min-w-0 flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-emerald-200 hover:shadow-[0_22px_50px_-24px_rgba(11,110,79,.35)]" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                    <a href="{{ route('trips.show', $trip) }}" class="relative block h-52 overflow-hidden bg-slate-100" aria-label="Voir le détail du trajet {{ $trip->departure_city }} vers {{ $trip->arrival_city }}">
                        <img src="{{ asset($tripImagePath) }}" alt="Photo illustrative du trajet vers {{ $trip->arrival_city }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/5 to-transparent" aria-hidden="true"></div>
                        <span class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full border border-white/25 bg-slate-950/45 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>Départ programmé
                        </span>
                        <div class="absolute inset-x-4 bottom-4 grid grid-cols-[1fr_auto_1fr] items-end gap-3 text-white">
                            <div class="min-w-0"><span class="text-[10px] font-bold uppercase tracking-[.16em] text-white/70">Départ</span>
                                <p class="mt-1 break-words text-base font-extrabold sm:text-lg">{{ $trip->departure_city }}</p>
                            </div>
                            <span class="mb-1 text-xl font-bold text-[#ffad70]" aria-hidden="true">→</span>
                            <div class="min-w-0 text-right"><span class="text-[10px] font-bold uppercase tracking-[.16em] text-white/70">Arrivée</span>
                                <p class="mt-1 break-words text-base font-extrabold sm:text-lg">{{ $trip->arrival_city }}</p>
                            </div>
                        </div>
                    </a>
                    <div class="flex flex-1 flex-col p-5 sm:p-6">
                        <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2"><span aria-hidden="true">▦</span>{{ $departureAt->format('d/m/Y') }}</span>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-orange-50 px-3 py-2 text-[#9a4810]"><span aria-hidden="true">◷</span>{{ $departureAt->format('H:i') }}</span>
                        </div>
                        <p class="mt-4 text-xs font-medium text-slate-500">{{ $trip->agency?->name ?? 'Agence partenaire' }}@if($trip->vehicle?->type) · {{ ucfirst($trip->vehicle->type) }}@endif</p>
                        <div class="mt-4 flex items-end justify-between gap-3 border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">Prix par passager</p>
                                <p class="mt-1 text-2xl font-extrabold tracking-tight text-[#0B6E4F]">{{ number_format((float) $trip->price, 0, ',', ' ') }} <span class="text-sm">Ar</span></p>
                            </div>
                            @if($trip->vehicle?->capacity)<span class="pb-1 text-xs font-semibold text-slate-400">{{ $trip->vehicle->capacity }} places</span>@endif
                        </div>
                        <div class="mt-5 grid gap-2 sm:grid-cols-2">
                            <a href="{{ route('trips.show', $trip) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-[#0B6E4F]/35 hover:bg-slate-50">Détails</a>
                            <a href="{{ route('reservations.create', ['trip' => $trip, 'passengers' => request('passengers', 1)]) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#F47C20] px-4 py-3 text-sm font-extrabold text-white transition hover:bg-[#df6b13] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/25">Réserver <span aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center md:col-span-2 lg:col-span-3" data-reveal>
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl" aria-hidden="true">🚌</div>
                    <h3 class="mt-4 font-sans text-xl font-extrabold text-slate-900">Aucun départ à venir pour le moment</h3>
                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-600">De nouveaux voyages apparaîtront ici dès que les agences les auront programmés.</p>
                    <a href="{{ route('trips.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-[#0B6E4F] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#08583f]">Consulter tous les voyages</a>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- PARCOURS --}}
    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center" data-reveal>
                <p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#F47C20]">Simple, étape par étape</p>
                <h2 class="mt-3 font-sans text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Préparez votre trajet en toute clarté</h2>
                <p class="mt-3 leading-7 text-slate-600">Consultez les départs, choisissez votre siège et gardez les informations de votre voyage à portée de main.</p>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg" data-reveal>
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-lg font-extrabold text-[#0B6E4F]">01</span>
                    <h3 class="mt-5 font-sans text-lg font-extrabold text-slate-900">Trouvez un départ</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">Recherchez par ville et date, puis comparez les trajets proposés.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg" data-reveal style="--reveal-delay: 90ms">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-lg font-extrabold text-[#b95610]">02</span>
                    <h3 class="mt-5 font-sans text-lg font-extrabold text-slate-900">Choisissez vos sièges</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">Sélectionnez des places disponibles et renseignez les informations des passagers.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg" data-reveal style="--reveal-delay: 180ms">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-lg font-extrabold text-slate-700">03</span>
                    <h3 class="mt-5 font-sans text-lg font-extrabold text-slate-900">Retrouvez votre billet</h3>
                    <p class="mt-2 text-sm leading-7 text-slate-600">Après la confirmation du paiement, affichez votre billet et son QR Code.</p>
                </article>
            </div>
            <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm leading-6 text-amber-900" data-reveal>
                <strong>Note de démonstration :</strong> le paiement actuellement disponible dans cette version est simulé. Aucun paiement réel n'est encaissé.
            </div>
        </div>
    </section>
</div>

<style>
    html {
        scroll-behavior: smooth;
    }

    html.ms-motion-ready [data-reveal]:not(.is-revealed) {
        opacity: 0;
        transform: translate3d(0, 18px, 0);
    }

    [data-reveal] {
        transition: opacity 650ms ease, transform 650ms cubic-bezier(.2, .65, .25, 1);
        transition-delay: var(--reveal-delay, 0ms);
    }

    [data-reveal].is-revealed {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto;
        }

        [data-reveal],
        html.ms-motion-ready [data-reveal]:not(.is-revealed) {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
    }
</style>
<script>
    (() => {
        const elements = Array.from(document.querySelectorAll('[data-reveal]'));
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reducedMotion || !('IntersectionObserver' in window)) {
            elements.forEach((element) => element.classList.add('is-revealed'));
            return;
        }
        document.documentElement.classList.add('ms-motion-ready');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -30px 0px'
        });
        elements.forEach((element) => observer.observe(element));
    })();
</script>
@endsection