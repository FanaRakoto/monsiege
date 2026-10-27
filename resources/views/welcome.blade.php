@extends('layouts.app')

@section('title', 'MonSiège - Réservez votre voyage simplement')

@push('head')
    {{-- Importation de la police Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        .font-inter { font-family: 'Inter', sans-serif; }
        
        /* Animation de flottement infini pour l'image hero */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Classes pour l'apparition au scroll */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .scroll-reveal.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush

@section('content')

@php
    $heroRoutes = [
        ['Antananarivo', 'Toamasina'],
        ['Antananarivo', 'Antsirabe'],
        ['Antananarivo', 'Mahajanga'],
        ['Antananarivo', 'Fianarantsoa'],
    ];
@endphp

{{-- =========================================================
     HERO MONSIÈGE
========================================================= --}}
<section id="accueil" class="relative isolate overflow-hidden bg-[#073F30] font-inter">
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -right-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-[#F47C20]/20 blur-3xl"></div>
        <div class="absolute -bottom-48 -left-36 h-[32rem] w-[32rem] rounded-full bg-emerald-300/15 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.055]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#052F24]/50 to-transparent"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 pb-14 pt-12 sm:px-6 sm:pb-20 sm:pt-16 lg:px-8 lg:pb-24 lg:pt-20">
        
        {{-- Grille Principale Hero (Texte à gauche, Image à droite) --}}
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
            
            {{-- Colonne de Gauche : Contenu --}}
            <div class="max-w-3xl">
                <div class="hr-rise inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-white/90 shadow-sm backdrop-blur" style="--d: 80ms">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-[#F47C20] shadow-[0_0_0_4px_rgba(244,124,32,0.15)]"></span>
                    Votre réservation, en toute simplicité
                </div>

                <h1 class="ht-title mt-7 text-4xl leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl xl:text-7xl" style="--d: 200ms">
                    <span class="sr-only">Votre prochain voyage commence ici.</span>
                    <span class="block" aria-hidden="true">
                        <span class="ht-mask"><span class="ht-word ht-white" style="--i: 0">Votre</span></span>
                        <span class="ht-mask"><span class="ht-word ht-white" style="--i: 1">prochain</span></span>
                        <span class="ht-mask"><span class="ht-word ht-white" style="--i: 2">voyage</span></span>
                    </span>
                    <span class="mt-1 block" aria-hidden="true">
                        <span class="ht-mask"><span class="ht-word ht-grad" style="--i: 3; --sh: 0s">commence</span></span>
                        <span class="ht-ici">
                            <span class="ht-mask"><span class="ht-word ht-grad" style="--i: 4; --sh: -1.4s">ici.</span></span>
                            <svg class="ht-road" viewBox="0 0 120 14" fill="none" aria-hidden="true">
                                <path class="ht-road-line" pathLength="1" d="M2 9 C 20 2, 38 15, 58 8 S 98 3, 114 8" stroke="#F47C20" stroke-width="4" stroke-linecap="round" />
                                <path class="ht-road-dash" d="M2 9 C 20 2, 38 15, 58 8 S 98 3, 114 8" stroke="#FFFFFF" stroke-width="0.9" stroke-linecap="round" />
                                <g class="ht-pin">
                                    <circle class="ht-pin-ring" cx="114" cy="8" r="2.6" stroke="#FFAC67" stroke-width="0.8" />
                                    <circle cx="114" cy="8" r="2.6" fill="#FFAC67" stroke="#FFFFFF" stroke-width="0.8" />
                                </g>
                            </svg>
                        </span>
                    </span>
                </h1>

                <p class="hr-rise mt-6 max-w-2xl text-base leading-8 text-white/80 sm:text-lg" style="--d: 350ms">
                    Trouvez votre trajet, choisissez votre place et préparez votre départ en toute simplicité.
                    Avec MonSiège, voyager à Madagascar devient plus pratique.
                </p>

                <div class="hr-rise mt-8 flex flex-wrap gap-3" style="--d: 500ms">
                    <a href="#recherche" class="group inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#F47C20] px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-orange-950/25 transition-all duration-300 hover:-translate-y-1 hover:bg-orange-500 hover:shadow-xl active:scale-95">
                        Trouver un voyage
                        <svg class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                        </svg>
                    </a>
                    <a href="#voyages" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/10 px-6 py-3.5 text-sm font-bold text-white backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:border-white/40 hover:bg-white/20 active:scale-95">
                        Découvrir les trajets
                    </a>
                </div>

                <ul class="hr-rise mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-white/90" style="--d: 650ms">
                    <li class="inline-flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#FFAC67]" aria-hidden="true">✓</span>Choix du siège</li>
                    <li class="inline-flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#FFAC67]" aria-hidden="true">✓</span>Prix en Ariary</li>
                    <li class="inline-flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#FFAC67]" aria-hidden="true">✓</span>Billet numérique</li>
                </ul>
            </div>

            {{-- Colonne de Droite : Image Hero (Animation infinie) --}}
            <div class="hr-rise hidden justify-center lg:flex" style="--d: 800ms">
                <div class="relative w-full max-w-md">
                    {{-- Formes décoratives --}}
                    <div class="absolute -inset-4 rounded-[2.5rem] bg-gradient-to-tr from-[#F47C20]/30 to-emerald-400/20 blur-2xl"></div>
                    
                    {{-- Image avec flottement infini --}}
                    <img 
                        src="{{ asset('images/hero/hero.jpeg') }}" 
                        alt="Voyage à Madagascar" 
                        class="animate-float relative rounded-[2rem] border border-white/20 object-cover shadow-2xl"
                    >
                    
                    {{-- Badge flottant --}}
                    <div class="absolute -bottom-6 -left-6 rounded-2xl border border-white/10 bg-white/90 p-4 shadow-xl backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-full bg-emerald-100 text-[#0B6E4F]">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500">Paiement</p>
                                <p class="text-sm font-black text-[#0B6E4F]">100% Sécurisé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barre de recherche --}}
        <div id="recherche" class="hr-rise mt-12 scroll-mt-28 sm:mt-14 lg:mt-20" style="--d: 950ms">
            <div class="hr-glow rounded-[1.8rem] p-[1px] shadow-2xl shadow-emerald-950/30" data-glow>
                <div class="relative rounded-[1.75rem] bg-white p-3 sm:p-4">
                    <form action="{{ route('trips.index') }}" method="GET" class="grid gap-2 md:grid-cols-2 xl:grid-cols-[2.2fr_1fr_1.05fr_auto] xl:gap-0">
                        {{-- Départ + destination --}}
                        <div class="relative grid gap-2 sm:grid-cols-2 sm:gap-0 md:col-span-2 xl:col-span-1 xl:after:absolute xl:after:right-0 xl:after:top-1/4 xl:after:h-1/2 xl:after:w-px xl:after:bg-slate-200">
                            <label for="departure" class="group flex min-h-[72px] cursor-text items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-emerald-50/70 focus-within:ring-2 focus-within:ring-[#0B6E4F]/25">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#0B6E4F] group-focus-within:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8" /><circle cx="12" cy="12" r="3" /></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-semibold text-slate-500">Ville de départ</span>
                                    <input id="departure" name="departure" type="text" list="ms-cities" autocomplete="off" placeholder="Antananarivo" value="{{ request('departure') }}" class="mt-0.5 w-full bg-transparent text-base font-bold text-slate-800 outline-none placeholder:font-medium placeholder:text-slate-300">
                                </span>
                            </label>

                            <label for="destination" class="group flex min-h-[72px] cursor-text items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-orange-50/70 focus-within:ring-2 focus-within:ring-[#F47C20]/30 sm:pl-8">
                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-[#F47C20] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#F47C20] group-focus-within:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" /><circle cx="12" cy="10" r="2.3" /></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-semibold text-slate-500">Destination</span>
                                    <input id="destination" name="destination" type="text" list="ms-cities" autocomplete="off" placeholder="Toamasina" value="{{ request('destination') }}" class="mt-0.5 w-full bg-transparent text-base font-bold text-slate-800 outline-none placeholder:font-medium placeholder:text-slate-300">
                                </span>
                            </label>

                            <button type="button" data-swap aria-label="Inverser le départ et la destination" class="hr-swap absolute right-4 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-md transition hover:border-[#0B6E4F] hover:text-[#0B6E4F] hover:shadow-lg active:scale-90 sm:left-1/2 sm:right-auto sm:-translate-x-1/2">
                                <svg class="size-5 rotate-90 sm:rotate-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h12m0 0-3-3m3 3-3 3M17 16H5m0 0 3-3m-3 3 3 3" /></svg>
                            </button>
                        </div>

                        {{-- Date de départ --}}
                        <label for="departure_date" class="group relative flex min-h-[72px] cursor-pointer items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-emerald-50/70 focus-within:ring-2 focus-within:ring-[#0B6E4F]/25 xl:after:absolute xl:after:right-0 xl:after:top-1/4 xl:after:h-1/2 xl:after:w-px xl:after:bg-slate-200">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#0B6E4F] group-focus-within:text-white">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="3" /><path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17" /></svg>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-xs font-semibold text-slate-500">Date du départ</span>
                                <input id="departure_date" name="departure_date" type="date" min="{{ now()->toDateString() }}" value="{{ request('departure_date') }}" class="mt-0.5 w-full cursor-pointer bg-transparent text-base font-bold text-slate-800 outline-none">
                            </span>
                        </label>

                        {{-- Nombre de passagers --}}
                        <div class="group flex min-h-[72px] items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-emerald-50/70 focus-within:ring-2 focus-within:ring-[#0B6E4F]/25">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#0B6E4F] group-focus-within:text-white">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="9" cy="8" r="3.2" /><path stroke-linecap="round" d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 5.2a3 3 0 0 1 0 5.6M18 14.4c1.8.8 3 2.5 3 5.6" /></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <label for="passengers" class="block text-xs font-semibold text-slate-500">Passagers</label>
                                <input id="passengers" name="passengers" type="number" min="1" max="6" inputmode="numeric" value="{{ request('passengers', 1) }}" class="hr-num mt-0.5 w-12 bg-transparent text-base font-bold text-slate-800 outline-none">
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" data-step="-1" aria-label="Un passager de moins" class="flex size-8 items-center justify-center rounded-full border border-slate-200 text-lg font-bold leading-none text-slate-600 transition hover:border-[#0B6E4F] hover:bg-emerald-50 hover:text-[#0B6E4F] active:scale-90">−</button>
                                <button type="button" data-step="1" aria-label="Un passager de plus" class="flex size-8 items-center justify-center rounded-full border border-slate-200 text-lg font-bold leading-none text-slate-600 transition hover:border-[#0B6E4F] hover:bg-emerald-50 hover:text-[#0B6E4F] active:scale-90">+</button>
                            </div>
                        </div>

                        {{-- Bouton rechercher --}}
                        <div class="flex md:col-span-2 xl:col-span-1 xl:pl-2">
                            <button type="submit" class="hr-shine group relative isolate flex min-h-[72px] w-full items-center justify-center gap-3 overflow-hidden rounded-2xl bg-gradient-to-br from-[#F47C20] to-[#E0640A] px-7 text-base font-extrabold text-white shadow-lg shadow-orange-600/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-600/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F47C20] active:scale-95 xl:w-auto">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" /><path stroke-linecap="round" d="m16 16 4.5 4.5" /></svg>
                                Rechercher
                                <svg class="-ml-1 size-5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                            </button>
                        </div>
                    </form>

                    <datalist id="ms-cities">
                        <option value="Antananarivo"></option>
                        <option value="Toamasina"></option>
                        <option value="Mahajanga"></option>
                        <option value="Antsirabe"></option>
                        <option value="Fianarantsoa"></option>
                        <option value="Toliara"></option>
                        <option value="Antsiranana"></option>
                        <option value="Morondava"></option>
                    </datalist>

                    @if ($errors->any())
                        <div class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                            <p class="font-bold">Vérifiez votre recherche :</p>
                            <ul class="mt-2 list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Raccourcis populaires --}}
            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                <span class="mr-1 font-medium text-white/80">Trajets populaires :</span>
                @foreach ($heroRoutes as [$routeFrom, $routeTo])
                    <button type="button" data-from="{{ $routeFrom }}" data-to="{{ $routeTo }}" class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:border-white/50 hover:bg-white/25 active:scale-95">
                        {{ $routeFrom }} → {{ $routeTo }}
                    </button>
                @endforeach
                <span class="mx-1 hidden h-5 w-px bg-white/25 sm:block" aria-hidden="true"></span>
                <button type="button" data-day="0" class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:border-white/50 hover:bg-white/25 active:scale-95">Aujourd'hui</button>
                <button type="button" data-day="1" class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:border-white/50 hover:bg-white/25 active:scale-95">Demain</button>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================
     TROIS PROCHAINS VOYAGES
========================================================= --}}
<section id="voyages" class="scroll-reveal relative overflow-hidden bg-slate-50 py-16 sm:py-20">
    <div class="pointer-events-none absolute -right-28 top-10 h-72 w-72 rounded-full bg-emerald-100/70 blur-3xl" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-[0.2em] text-[#F47C20]">
                    <span class="h-px w-7 bg-[#F47C20]"></span>À découvrir
                </span>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-[#1E293B] sm:text-4xl">Les prochains départs</h2>
                <p class="mt-3 max-w-xl leading-7 text-slate-600">Découvrez les voyages disponibles et choisissez le trajet qui vous convient.</p>
            </div>
            <a href="/voyages" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-[#0B6E4F]/15 bg-white px-5 py-3 text-sm font-extrabold text-[#0B6E4F] shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#0B6E4F]/30 hover:shadow-md active:scale-95">
                Voir tous les voyages
                <svg class="size-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </a>
        </div>

        <div class="mt-9 grid items-stretch gap-6 sm:mt-10 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($popularTrips as $trip)
                @php
                    $departureAt = \Illuminate\Support\Carbon::parse($trip->departure_at);
                    $destinationKey = strtolower(trim($trip->arrival_city));
                    $fallbackImage = match ($destinationKey) {
                        'toamasina', 'tamatave' => 'images/voyages/toamasina.jpeg',
                        'fianarantsoa' => 'images/voyages/fianarantsoa.jpeg',
                        'toliara', 'tulear' => 'images/voyages/toliara-rn7.jpeg',
                        'mahajanga' => 'images/voyages/mahajanga.jpeg',
                        'antsiranana', 'diego-suarez' => 'images/voyages/antsiranana.jpeg',
                        default => 'images/voyages/RN7.jpeg',
                    };
                    $tripImage = $trip->image_path ?: $fallbackImage;
                @endphp
                <article class="group flex h-full flex-col overflow-hidden rounded-[1.6rem] border border-slate-200/80 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-emerald-200 hover:shadow-[0_22px_55px_-25px_rgba(11,110,79,0.32)] active:scale-[0.98]">
                    {{-- Photo illustrative du trajet --}}
                    <div class="relative h-44 overflow-hidden bg-slate-100 sm:h-48">
                        <img
                            src="{{ asset($tripImage) }}"
                            alt="Photo illustrative du voyage vers {{ $trip->arrival_city }}"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            loading="lazy"
                            decoding="async"
                        >
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/25 via-transparent to-transparent" aria-hidden="true"></div>
                        <span class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-[#0B6E4F]/90 px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-white shadow-sm backdrop-blur">
                            <span class="size-1.5 rounded-full bg-[#FFAC67]"></span>Voyage disponible
                        </span>
                    </div>

                    {{-- Bandeau de carte --}}
                    <div class="relative overflow-hidden bg-gradient-to-br from-[#0B6E4F] via-[#09563F] to-[#073F30] px-6 pb-6 pt-5 text-white">
                        <div class="pointer-events-none absolute -right-7 -top-10 size-40 rounded-full border border-white/10" aria-hidden="true"></div>
                        <div class="pointer-events-none absolute -right-1 -top-4 size-28 rounded-full border border-white/10" aria-hidden="true"></div>
                        <div class="relative flex items-start justify-end gap-3">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-[#FFBC83] ring-1 ring-white/15 transition duration-300 group-hover:rotate-3 group-hover:scale-105" aria-hidden="true">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 16.5 6.5 8A2 2 0 0 1 8.47 6.4h7.06A2 2 0 0 1 17.5 8l1.5 8.5M4.5 16.5h15M6 16.5v2m12-2v2M7 11h10M7.5 13.5h.01M16.5 13.5h.01" /><path stroke-linecap="round" d="M8 6.4 9 4h6l1 2.4" /></svg>
                            </span>
                        </div>

                        <div class="relative mt-6 grid grid-cols-[1fr_auto_1fr] items-center gap-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/60">Départ</p>
                                <h3 class="mt-1 break-words text-lg font-black leading-tight text-white sm:text-xl">{{ $trip->departure_city }}</h3>
                            </div>
                            <div class="flex flex-col items-center gap-1 text-[#FFAC67]" aria-hidden="true">
                                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h15m-5-5 5 5-5 5" /></svg>
                                <span class="h-px w-9 bg-white/25"></span>
                            </div>
                            <div class="min-w-0 text-right">
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/60">Destination</p>
                                <p class="mt-1 break-words text-lg font-black leading-tight text-white sm:text-xl">{{ $trip->arrival_city }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Détails du voyage --}}
                    <div class="flex flex-1 flex-col p-5 sm:p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-bold text-[#0B6E4F]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="3" /><path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17" /></svg>
                                {{ $departureAt->format('d/m/Y') }}
                            </span>
                            <span class="inline-flex items-center gap-2 rounded-lg bg-orange-50 px-3 py-2 text-xs font-bold text-[#B6530C]">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="8.5" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2" /></svg>
                                {{ $departureAt->format('H:i') }}
                            </span>
                        </div>

                        <div class="mt-5 border-t border-dashed border-slate-200"></div>

                        <div class="mt-5 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-500">Prix par passager</p>
                                <p class="mt-1 text-2xl font-black tracking-tight text-[#0B6E4F]">{{ number_format($trip->price, 0, ',', ' ') }} <span class="text-sm font-extrabold">Ar</span></p>
                            </div>
                            <span class="mb-1 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z" /><circle cx="12" cy="10" r="2.2" /></svg>
                                Madagascar
                            </span>
                        </div>

                        <a href="{{ route('reservations.create', ['trip' => $trip, 'passengers' => request('passengers', 1)]) }}" class="group/button mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-extrabold text-white shadow-sm shadow-orange-900/10 transition-all duration-300 hover:-translate-y-1 hover:bg-[#DE6810] hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F47C20] active:scale-95">
                            Réserver ce voyage
                            <svg class="size-4 transition-transform duration-300 group-hover/button:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm md:col-span-2 lg:col-span-3">
                    <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-emerald-50 text-[#0B6E4F]" aria-hidden="true">
                        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M5 16.5 6.5 8A2 2 0 0 1 8.47 6.4h7.06A2 2 0 0 1 17.5 8l1.5 8.5M4.5 16.5h15M6 16.5v2m12-2v2" /></svg>
                    </span>
                    <h3 class="mt-4 text-lg font-black text-slate-800">Aucun voyage disponible pour le moment</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Les prochains départs s'afficheront ici dès qu'ils seront disponibles. Vous pouvez aussi consulter tous les voyages.</p>
                    <a href="{{ route('trips.index') }}" class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0B6E4F] active:scale-95">
                        Consulter les voyages
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- =========================================================
     AVANTAGES MONSIÈGE
========================================================= --}}
<section class="scroll-reveal bg-white py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-xs font-extrabold uppercase tracking-[0.2em] text-[#F47C20]">Pourquoi MonSiège ?</span>
            <h2 class="mt-3 text-3xl font-black tracking-tight text-[#1E293B] sm:text-4xl">Voyagez plus sereinement</h2>
            <p class="mt-3 leading-7 text-slate-500">Les informations essentielles pour préparer votre prochain départ, réunies au même endroit.</p>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-3">
            <article class="group rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-7 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-emerald-900/10 active:scale-95">
                <div class="flex size-13 items-center justify-center rounded-2xl bg-[#0B6E4F] text-white shadow-lg shadow-emerald-900/15 transition duration-300 group-hover:rotate-3 group-hover:scale-110" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                </div>
                <h3 class="mt-5 text-lg font-black text-[#1E293B]">Réservation simple</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">Trouvez un trajet et préparez votre réservation en quelques étapes faciles.</p>
            </article>

            <article class="group rounded-3xl border border-orange-100 bg-gradient-to-br from-orange-50 to-white p-7 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-orange-900/10 active:scale-95">
                <div class="flex size-13 items-center justify-center rounded-2xl bg-[#F47C20] text-white shadow-lg shadow-orange-900/15 transition duration-300 group-hover:rotate-3 group-hover:scale-110" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="16" height="16" rx="3" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 8h3v3H8zM13 8h3M13 11h3M8 14h3v3H8zM13 14h3v3h-3z" /></svg>
                </div>
                <h3 class="mt-5 text-lg font-black text-[#1E293B]">Choix de votre siège</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">Visualisez les places disponibles et choisissez celle qui vous convient.</p>
            </article>

            <article class="group rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-100 to-white p-7 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-slate-900/10 active:scale-95">
                <div class="flex size-13 items-center justify-center rounded-2xl bg-[#1E293B] text-white shadow-lg shadow-slate-900/15 transition duration-300 group-hover:rotate-3 group-hover:scale-110" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="4" width="6" height="6" /><rect x="14" y="4" width="6" height="6" /><rect x="4" y="14" width="6" height="6" /><path stroke-linecap="round" d="M14 14h2v2h-2zM18 14h2M14 18h2M18 18h2v2h-2z" /></svg>
                </div>
                <h3 class="mt-5 text-lg font-black text-[#1E293B]">Billet numérique</h3>
                <p class="mt-2 text-sm leading-7 text-slate-600">Retrouvez votre réservation et les informations utiles de votre voyage en ligne.</p>
            </article>
        </div>
    </div>
</section>

{{-- Script pour déclencher l'animation au scroll --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    // Décommenter pour ne jouer l'animation qu'une seule fois
                    // observer.unobserve(entry.target); 
                }
            });
        }, {
            threshold: 0.1, // L'élément doit être visible à 10% pour se déclencher
            rootMargin: "0px 0px -50px 0px"
        });

        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });
    });
</script>
@endpush

@endsection