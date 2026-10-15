@extends('layouts.app')

@section('title', 'MonSiège - Réservez votre voyage simplement')

@section('content')


{{-- =========================================================
     HERO MONSIÈGE
========================================================= --}}

@php
$heroRoutes = [
['Antananarivo', 'Toamasina'],
['Antananarivo', 'Antsirabe'],
['Antananarivo', 'Mahajanga'],
['Antananarivo', 'Fianarantsoa'],
];
@endphp

<section id="accueil" class="relative isolate overflow-hidden bg-[#0B6E4F]">

    {{-- Fond décoratif (inchangé) --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -right-40 -top-32 h-[32rem] w-[32rem] rounded-full bg-[#F47C20]/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-32 h-[28rem] w-[28rem] rounded-full bg-emerald-300/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.06]"
            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 25px 25px;"></div>
    </div>

    {{--
        Si ta photo de fond rend le texte difficile à lire, décommente ce voile
        (il assombrit surtout le côté gauche, là où se trouve le texte) :

        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-[#063a2a]/85 via-[#0B6E4F]/50 to-transparent"></div>
    --}}

    <div class="relative mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 sm:pt-20 lg:px-8 lg:pb-24 lg:pt-24">

        {{-- ================= Présentation ================= --}}
        <div class="max-w-2xl">

            {{-- Remplace l'ancien <h1> … </h1> par ce bloc --}}
            <h1 class="ht-title mt-7 text-4xl leading-[1.12] tracking-tight text-white sm:text-5xl lg:text-6xl xl:text-7xl"
                style="--d: 140ms">

                {{-- Texte lu par les lecteurs d'écran et les moteurs de recherche --}}
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

                        {{-- Route qui se dessine sous « ici. » avec une épingle de destination --}}
                        <svg class="ht-road" viewBox="0 0 120 14" fill="none" aria-hidden="true">
                            <path class="ht-road-line" pathLength="1" d="M2 9 C 20 2, 38 15, 58 8 S 98 3, 114 8"
                                stroke="#F47C20" stroke-width="4" stroke-linecap="round" />
                            <path class="ht-road-dash" d="M2 9 C 20 2, 38 15, 58 8 S 98 3, 114 8"
                                stroke="#FFFFFF" stroke-width="0.9" stroke-linecap="round" />
                            <g class="ht-pin">
                                <circle class="ht-pin-ring" cx="114" cy="8" r="2.6" stroke="#FFAC67" stroke-width="0.8" />
                                <circle cx="114" cy="8" r="2.6" fill="#FFAC67" stroke="#FFFFFF" stroke-width="0.8" />
                            </g>
                        </svg>
                    </span>
                </span>
            </h1>

            {{--
    Pour que la suite apparaisse APRÈS le titre, change ces valeurs « --d » :
      paragraphe      : style="--d: 700ms"   (avant 240ms)
      boutons         : style="--d: 820ms"   (avant 360ms)
      liste avantages : style="--d: 940ms"   (avant 480ms)
      barre recherche : style="--d: 1080ms"  (avant 620ms)
--}}



            <p class="hr-rise mt-6 max-w-xl text-base leading-8 text-white/80 sm:text-lg" style="--d: 240ms">
                Trouvez votre trajet, choisissez votre place et préparez
                votre départ en toute simplicité. Avec MonSiège, voyager
                à Madagascar devient plus pratique.
            </p>

            {{-- Boutons --}}
            <div class="hr-rise mt-8 flex flex-wrap gap-3" style="--d: 360ms">

                <a href="#recherche"
                    class="group inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#F47C20] px-6 py-3.5 text-sm font-extrabold text-white shadow-lg shadow-orange-950/20 transition duration-300 hover:-translate-y-0.5 hover:bg-orange-500 hover:shadow-xl active:scale-[0.98]">

                    Trouver un voyage

                    <svg class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                    </svg>
                </a>

                <a href="#voyages"
                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/10 px-6 py-3.5 text-sm font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:border-white/40 hover:bg-white/20">
                    Découvrir les trajets
                </a>

            </div>

            {{-- Avantages --}}
            <ul class="hr-rise mt-9 flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-white/90" style="--d: 480ms">
                <li class="inline-flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#FFAC67]" aria-hidden="true">✓</span>
                    Choix du siège
                </li>
                <li class="inline-flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#FFAC67]" aria-hidden="true">✓</span>
                    Prix en Ariary
                </li>
                <li class="inline-flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-white/15 text-[#FFAC67]" aria-hidden="true">✓</span>
                    Billet numérique
                </li>
            </ul>
        </div>


        {{-- =====================================================
             BARRE DE RECHERCHE
        ====================================================== --}}
        <div id="recherche" class="hr-rise mt-14 scroll-mt-28 lg:mt-16" style="--d: 620ms">

            <div class="hr-glow shadow-2xl shadow-emerald-950/30" data-glow>
                <div class="relative rounded-[1.8rem] bg-white p-3 sm:p-4">

                    <form action="{{ route('trips.index') }}" method="GET"
                        class="grid gap-2 md:grid-cols-2 xl:grid-cols-[2.3fr_1fr_1.05fr_auto] xl:gap-0">

                        {{-- ===== Départ + Destination ===== --}}
                        <div class="relative grid gap-2 sm:grid-cols-2 sm:gap-0 md:col-span-2 xl:col-span-1 xl:after:absolute xl:after:right-0 xl:after:top-1/4 xl:after:h-1/2 xl:after:w-px xl:after:bg-slate-200">

                            {{-- Départ --}}
                            <label for="departure"
                                class="group flex min-h-[72px] cursor-text items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-emerald-50/70 focus-within:ring-2 focus-within:ring-[#0B6E4F]/25">

                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#0B6E4F] group-focus-within:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <circle cx="12" cy="12" r="8" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-semibold text-slate-500">Départ</span>
                                    <input id="departure" name="departure" type="text" list="ms-cities" autocomplete="off"
                                        placeholder="Antananarivo"
                                        value="{{ request('departure') }}"
                                        class="mt-0.5 w-full bg-transparent text-base font-bold text-slate-800 outline-none placeholder:font-medium placeholder:text-slate-300">
                                </span>
                            </label>

                            {{-- Destination --}}
                            <label for="destination"
                                class="group flex min-h-[72px] cursor-text items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-orange-50/70 focus-within:ring-2 focus-within:ring-[#F47C20]/30 sm:pl-8">

                                <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-[#F47C20] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#F47C20] group-focus-within:text-white">
                                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z" />
                                        <circle cx="12" cy="10" r="2.3" />
                                    </svg>
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-semibold text-slate-500">Destination</span>
                                    <input id="destination" name="destination" type="text" list="ms-cities" autocomplete="off"
                                        placeholder="Toamasina"
                                        value="{{ request('destination') }}"
                                        class="mt-0.5 w-full bg-transparent text-base font-bold text-slate-800 outline-none placeholder:font-medium placeholder:text-slate-300">
                                </span>
                            </label>

                            {{-- Inverser --}}
                            <button type="button" data-swap aria-label="Inverser le départ et la destination"
                                class="hr-swap absolute right-4 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-md transition hover:border-[#0B6E4F] hover:text-[#0B6E4F] hover:shadow-lg active:scale-90 sm:left-1/2 sm:right-auto sm:-translate-x-1/2">
                                <svg class="size-5 rotate-90 sm:rotate-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h12m0 0-3-3m3 3-3 3M17 16H5m0 0 3-3m-3 3 3 3" />
                                </svg>
                            </button>
                        </div>

                        {{-- ===== Date ===== --}}
                        <label for="departure_date"
                            class="group relative flex min-h-[72px] cursor-pointer items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-emerald-50/70 focus-within:ring-2 focus-within:ring-[#0B6E4F]/25 xl:after:absolute xl:after:right-0 xl:after:top-1/4 xl:after:h-1/2 xl:after:w-px xl:after:bg-slate-200">

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#0B6E4F] group-focus-within:text-white">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="15" rx="3" />
                                    <path stroke-linecap="round" d="M8 3v4M16 3v4M3.5 10h17" />
                                </svg>
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block text-xs font-semibold text-slate-500">Date du départ</span>
                                <input id="departure_date" name="departure_date" type="date"
                                    min="{{ now()->toDateString() }}"
                                    value="{{ request('departure_date') }}"
                                    class="mt-0.5 w-full cursor-pointer bg-transparent text-base font-bold text-slate-800 outline-none">
                            </span>
                        </label>

                        {{-- ===== Passagers ===== --}}
                        <div
                            class="group flex min-h-[72px] items-center gap-3 rounded-2xl px-4 py-3 transition hover:bg-slate-50 focus-within:bg-emerald-50/70 focus-within:ring-2 focus-within:ring-[#0B6E4F]/25">

                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F] transition duration-300 group-focus-within:scale-105 group-focus-within:bg-[#0B6E4F] group-focus-within:text-white">
                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="9" cy="8" r="3.2" />
                                    <path stroke-linecap="round" d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M16 5.2a3 3 0 0 1 0 5.6M18 14.4c1.8.8 3 2.5 3 5.6" />
                                </svg>
                            </span>

                            <div class="min-w-0 flex-1">
                                <label for="passengers" class="block text-xs font-semibold text-slate-500">Passagers</label>
                                <input id="passengers" name="passengers" type="number" min="1" max="6" inputmode="numeric"
                                    value="{{ request('passengers', 1) }}"
                                    class="hr-num mt-0.5 w-12 bg-transparent text-base font-bold text-slate-800 outline-none">
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button type="button" data-step="-1" aria-label="Un passager de moins"
                                    class="flex size-8 items-center justify-center rounded-full border border-slate-200 text-lg font-bold leading-none text-slate-600 transition hover:border-[#0B6E4F] hover:bg-emerald-50 hover:text-[#0B6E4F] active:scale-90">
                                    −
                                </button>
                                <button type="button" data-step="1" aria-label="Un passager de plus"
                                    class="flex size-8 items-center justify-center rounded-full border border-slate-200 text-lg font-bold leading-none text-slate-600 transition hover:border-[#0B6E4F] hover:bg-emerald-50 hover:text-[#0B6E4F] active:scale-90">
                                    +
                                </button>
                            </div>
                        </div>

                        {{-- ===== Bouton ===== --}}
                        <div class="flex md:col-span-2 xl:col-span-1 xl:pl-2">
                            <button type="submit"
                                class="hr-shine group relative isolate flex min-h-[72px] w-full items-center justify-center gap-3 overflow-hidden rounded-2xl bg-gradient-to-br from-[#F47C20] to-[#E0640A] px-8 text-base font-extrabold text-white shadow-lg shadow-orange-600/30 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-orange-600/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#F47C20] active:scale-[0.98] xl:w-auto">

                                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                    <circle cx="10.8" cy="10.8" r="6.8" />
                                    <path stroke-linecap="round" d="m16 16 4.5 4.5" />
                                </svg>

                                Rechercher

                                <svg class="size-5 -ml-1 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    {{-- Suggestions de villes --}}
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

                    {{-- Erreurs de validation éventuelles --}}
                    @if($errors->any())
                    <div class="mt-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                        <p class="font-bold">Vérifiez votre recherche :</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Raccourcis --}}
            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm">
                <span class="mr-1 font-medium text-white/80">Trajets populaires :</span>

                @foreach($heroRoutes as [$routeFrom, $routeTo])
                <button type="button" data-from="{{ $routeFrom }}" data-to="{{ $routeTo }}"
                    class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition hover:-translate-y-0.5 hover:border-white/50 hover:bg-white/25 active:scale-95">
                    {{ $routeFrom }} → {{ $routeTo }}
                </button>
                @endforeach

                <span class="mx-1 hidden h-5 w-px bg-white/25 sm:block" aria-hidden="true"></span>

                <button type="button" data-day="0"
                    class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition hover:-translate-y-0.5 hover:border-white/50 hover:bg-white/25 active:scale-95">
                    Aujourd'hui
                </button>
                <button type="button" data-day="1"
                    class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition hover:-translate-y-0.5 hover:border-white/50 hover:bg-white/25 active:scale-95">
                    Demain
                </button>
            </div>
        </div>

    </div>

</section>
{{-- =========================================================
 VOYAGES POPULAIRES
========================================================= --}}

<section
    id="voyages"
    class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">

        <div>

            <span class="text-sm font-bold uppercase tracking-wider text-[#F47C20]">
                Découvrez
            </span>

            <h2 class="mt-2 text-3xl font-black text-[#1E293B]">
                Voyages populaires
            </h2>

            <p class="mt-2 text-slate-500">
                Les trajets les plus recherchés actuellement.
            </p>

        </div>

        <a
            href="/voyages"
            class="font-bold text-[#0B6E4F] transition hover:text-[#F47C20]">
            Voir tous les voyages →
        </a>

    </div>


    <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">

        {{-- Voyage 1 --}}
        <article class="monsiege-card overflow-hidden">

            <div class="flex h-48 items-center justify-center bg-gradient-to-br from-emerald-700 via-[#0B6E4F] to-emerald-900">

                <svg
                    class="h-20 w-20 text-white/90"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.3">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2" />
                </svg>

            </div>

            <div class="p-6">

                <div class="flex items-center gap-2 text-sm font-bold text-[#0B6E4F]">
                    Antananarivo
                    <span>→</span>
                    Toamasina
                </div>

                <div class="mt-4 flex items-center justify-between text-sm text-slate-500">

                    <span>
                        07:00
                    </span>

                    <span>
                        5h 30min
                    </span>

                </div>

                <div class="mt-5 flex items-center justify-between">

                    <div>
                        <p class="text-2xl font-black text-[#1E293B]">
                            35 000 Ar
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            par personne
                        </p>
                    </div>

                    <a
                        href="{{ route('trips.index', [
                            'departure' => 'Antananarivo',
                        'destination' => 'Toamasina',
                        ]) }}"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-600">
                        Réserver
                    </a>
                </div>

            </div>

        </article>


        {{-- Voyage 2 --}}
        <article class="monsiege-card overflow-hidden">

            <div class="flex h-48 items-center justify-center bg-gradient-to-br from-[#0B6E4F] to-teal-700">

                <svg
                    class="h-20 w-20 text-white/90"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.3">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2" />
                </svg>

            </div>

            <div class="p-6">

                <div class="flex items-center gap-2 text-sm font-bold text-[#0B6E4F]">
                    Antananarivo
                    <span>→</span>
                    Mahajanga
                </div>

                <div class="mt-4 flex items-center justify-between text-sm text-slate-500">

                    <span>
                        06:30
                    </span>

                    <span>
                        8h 00min
                    </span>

                </div>

                <div class="mt-5 flex items-center justify-between">

                    <div>
                        <p class="text-2xl font-black text-[#1E293B]">
                            30 000 Ar
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            par personne
                        </p>
                    </div>

                    <a
                        href="{{ route('trips.index', [
                            'departure' => 'Antananarivo',
                        'destination' => 'Mahajanga',
                        ]) }}"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-600">
                        Réserver
                    </a>

                </div>

            </div>

        </article>


        {{-- Voyage 3 --}}
        <article class="monsiege-card overflow-hidden">

            <div class="flex h-48 items-center justify-center bg-gradient-to-br from-emerald-800 to-[#0B6E4F]">

                <svg
                    class="h-20 w-20 text-white/90"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.3">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2" />
                </svg>

            </div>

            <div class="p-6">

                <div class="flex items-center gap-2 text-sm font-bold text-[#0B6E4F]">
                    Antananarivo
                    <span>→</span>
                    Antsirabe
                </div>

                <div class="mt-4 flex items-center justify-between text-sm text-slate-500">

                    <span>
                        08:00
                    </span>

                    <span>
                        4h 30min
                    </span>

                </div>

                <div class="mt-5 flex items-center justify-between">

                    <div>
                        <p class="text-2xl font-black text-[#1E293B]">
                            25 000 Ar
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            par personne
                        </p>
                    </div>

                    <a
                        href="{{ route('trips.index', [
                            'departure' => 'Antananarivo',
                            'destination' => 'Antsirabe',
                        ]) }}"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-600">
                        Réserver
                    </a>

                </div>

            </div>

        </article>

    </div>

</section>


{{-- =========================================================
 AVANTAGES
========================================================= --}}

<section class="bg-white">

    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">

        <div class="grid gap-6 md:grid-cols-3">

            <div class="rounded-2xl bg-[#0B6E4F]/5 p-7">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#0B6E4F] text-white">
                    ✓
                </div>

                <h3 class="mt-5 text-xl font-black text-[#1E293B]">
                    Réservation simple
                </h3>

                <p class="mt-2 leading-7 text-slate-500">
                    Trouvez votre voyage et réservez votre siège en quelques clics.
                </p>
            </div>

            <div class="rounded-2xl bg-[#F47C20]/5 p-7">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#F47C20] text-white">
                    ★
                </div>

                <h3 class="mt-5 text-xl font-black text-[#1E293B]">
                    Votre siège
                </h3>

                <p class="mt-2 leading-7 text-slate-500">
                    Choisissez exactement la place que vous souhaitez dans le véhicule.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-100 p-7">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#1E293B] text-white">
                    QR
                </div>

                <h3 class="mt-5 text-xl font-black text-[#1E293B]">
                    Billet numérique
                </h3>

                <p class="mt-2 leading-7 text-slate-500">
                    Recevez votre billet numérique et votre QR Code après la réservation.
                </p>
            </div>

        </div>

    </div>

</section>

@endsection