@extends('layouts.app')

@section('title', 'MonSiège - Réservez votre voyage simplement')

@section('content')

{{-- =========================================================
 HERO
========================================================= --}}

<section class="relative overflow-hidden">

    {{-- Background --}}
    <div
        class="absolute inset-0 bg-gradient-to-br from-[#0B6E4F] via-[#0B6E4F] to-emerald-900"
    ></div>

    {{-- Décoration --}}
    <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#F47C20]/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">

        <div class="grid items-center gap-12 lg:grid-cols-2">

            {{-- Texte --}}
            <div class="max-w-2xl">

                <span class="inline-flex items-center rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur">
                    🇲🇬 Voyagez à Madagascar
                </span>

                <h1 class="mt-6 text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    Réservez votre voyage
                    <span class="text-[#F47C20]">
                        simplement
                    </span>
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-white/80">
                    Trouvez votre trajet, choisissez votre siège et
                    voyagez confortablement avec MonSiège.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">

                    <a
                        href="#recherche"
                        class="monsiege-button"
                    >
                        Rechercher un voyage
                    </a>

                    <a
                        href="#voyages"
                        class="rounded-xl border border-white/30 bg-white/10 px-5 py-3 font-bold text-white backdrop-blur transition hover:bg-white/20"
                    >
                        Voir les voyages
                    </a>

                </div>

            </div>

            {{-- Illustration bus --}}
            <div class="hidden lg:block">

                <div class="relative mx-auto max-w-xl">

                    <div class="absolute inset-0 rounded-[3rem] bg-[#F47C20]/20 blur-3xl"></div>

                    <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/10 p-8 backdrop-blur">

                        <div class="flex items-center justify-center py-16">

                            <div class="text-center">

                                <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-3xl bg-white/10">

                                    <svg
                                        class="h-16 w-16 text-[#F47C20]"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2"
                                        />
                                    </svg>

                                </div>

                                <p class="mt-5 text-lg font-bold text-white">
                                    Votre voyage, notre priorité
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Recherche --}}
        <div
            id="recherche"
            class="relative mt-12 rounded-3xl border border-white/30 bg-white p-4 shadow-2xl sm:p-6"
        >

            <div class="mb-5">
                <h2 class="text-xl font-extrabold text-[#1E293B]">
                    Rechercher un voyage
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Où souhaitez-vous aller ?
                </p>
            </div>

            <form
                action="{{ url('/voyages') }}"
                method="GET"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-5"
            >

                {{-- Départ --}}
                <div>
                    <label
                        for="departure"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Départ
                    </label>

                    <div class="relative">
                        <input
                            id="departure"
                            name="departure"
                            type="text"
                            placeholder="Antananarivo"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10"
                        >
                    </div>
                </div>

                {{-- Destination --}}
                <div>
                    <label
                        for="destination"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Destination
                    </label>

                    <input
                        id="destination"
                        name="destination"
                        type="text"
                        placeholder="Toamasina"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10"
                    >
                </div>

                {{-- Date --}}
                <div>
                    <label
                        for="departure_date"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Date du départ
                    </label>

                    <input
                        id="departure_date"
                        name="departure_date"
                        type="date"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10"
                    >
                </div>

                {{-- Passagers --}}
                <div>
                    <label
                        for="passengers"
                        class="mb-2 block text-sm font-semibold text-slate-700"
                    >
                        Passagers
                    </label>

                    <select
                        id="passengers"
                        name="passengers"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10"
                    >
                        <option value="1">1 passager</option>
                        <option value="2">2 passagers</option>
                        <option value="3">3 passagers</option>
                        <option value="4">4 passagers</option>
                        <option value="5">5 passagers</option>
                        <option value="6">6 passagers</option>
                    </select>
                </div>

                {{-- Bouton --}}
                <div class="flex items-end">

                    <button
                        type="submit"
                        class="monsiege-button h-[50px] w-full"
                    >

                        <svg
                            class="mr-2 h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"
                            />
                        </svg>

                        Rechercher

                    </button>

                </div>

            </form>

        </div>

    </div>

</section>


{{-- =========================================================
 VOYAGES POPULAIRES
========================================================= --}}

<section
    id="voyages"
    class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8"
>

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
            class="font-bold text-[#0B6E4F] transition hover:text-[#F47C20]"
        >
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
                    stroke-width="1.3"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2"
                    />
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
                        href="/voyages"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-600"
                    >
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
                    stroke-width="1.3"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2"
                    />
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
                        href="/voyages"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-600"
                    >
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
                    stroke-width="1.3"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 17h14M6 17V6a2 2 0 012-2h8a2 2 0 012 2v11M4 17h16M7 17v2m10-2v2M6 9h12M7 13h2m6 0h2"
                    />
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
                        href="/voyages"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-bold text-white transition hover:bg-orange-600"
                    >
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