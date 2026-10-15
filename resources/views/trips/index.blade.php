@extends('layouts.app')

@section('title', 'Voyages disponibles - MonSiège')

@section('content')

<section class="bg-slate-50">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- ======================================================
             EN-TÊTE
        ======================================================= --}}

        <div class="mb-8">

            <span class="text-sm font-bold uppercase tracking-wider text-[#F47C20]">
                MonSiège
            </span>

            <h1 class="mt-2 text-3xl font-black text-[#1E293B] sm:text-4xl">
                Voyages disponibles
            </h1>

            @if(
            !empty($search['departure']) ||
            !empty($search['destination']) ||
            !empty($search['departure_date'])
            )

            <p class="mt-2 text-slate-500">
                Résultats correspondant à votre recherche.
            </p>

            @else

            <p class="mt-2 text-slate-500">
                Découvrez les prochains voyages disponibles.
            </p>

            @endif

        </div>


        {{-- ======================================================
             BARRE DE RECHERCHE
        ======================================================= --}}

        <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

            <form
                action="{{ route('trips.index') }}"
                method="GET"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">

                <div>
                    <label
                        for="departure"
                        class="mb-2 block text-sm font-semibold text-slate-700">
                        Départ
                    </label>

                    <input
                        id="departure"
                        name="departure"
                        type="text"
                        value="{{ $search['departure'] ?? '' }}"
                        placeholder="Antananarivo"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                </div>


                <div>
                    <label
                        for="destination"
                        class="mb-2 block text-sm font-semibold text-slate-700">
                        Destination
                    </label>

                    <input
                        id="destination"
                        name="destination"
                        type="text"
                        value="{{ $search['destination'] ?? '' }}"
                        placeholder="Toamasina"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                </div>


                <div>
                    <label
                        for="departure_date"
                        class="mb-2 block text-sm font-semibold text-slate-700">
                        Date
                    </label>

                    <input
                        id="departure_date"
                        name="departure_date"
                        type="date"
                        value="{{ $search['departure_date'] ?? '' }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                </div>


                <div>
                    <label
                        for="passengers"
                        class="mb-2 block text-sm font-semibold text-slate-700">
                        Passagers
                    </label>

                    <select
                        id="passengers"
                        name="passengers"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                        @for($i = 1; $i <= 6; $i++)
                            <option
                            value="{{ $i }}"
                            @selected(($search['passengers'] ?? 1)==$i)>
                            {{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}
                            </option>
                            @endfor
                    </select>
                </div>


                <div class="flex items-end">

                    <button
                        type="submit"
                        class="h-[50px] w-full rounded-xl bg-[#F47C20] px-5 font-bold text-white shadow-sm transition hover:bg-orange-600 hover:shadow-md">
                        Rechercher
                    </button>

                </div>

            </form>

        </div>


        {{-- ======================================================
             MESSAGES DE VALIDATION
        ======================================================= --}}

        @if($errors->any())

        <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5">

            <h2 class="font-bold text-red-700">
                Vérifiez votre recherche
            </h2>

            <ul class="mt-2 text-sm text-red-600">

                @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

                @endforeach

            </ul>

        </div>

        @endif


        {{-- ======================================================
             NOMBRE DE RÉSULTATS
        ======================================================= --}}

        <div class="mb-5 flex items-center justify-between">

            <p class="text-sm text-slate-500">

                <span class="font-bold text-[#1E293B]">
                    {{ $trips->total() }}
                </span>

                {{ $trips->total() > 1 ? 'voyages trouvés' : 'voyage trouvé' }}

            </p>

        </div>


        {{-- ======================================================
             VOYAGES
        ======================================================= --}}

        @forelse($trips as $trip)

        <article class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">

            <div class="grid gap-6 p-6 lg:grid-cols-[1fr_auto]">

                <div>

                    {{-- Agence --}}
                    <div class="flex flex-wrap items-center gap-3">

                        <span class="rounded-lg bg-[#0B6E4F]/10 px-3 py-1 text-xs font-bold text-[#0B6E4F]">
                            {{ $trip->agency->name }}
                        </span>

                        <span class="text-xs font-medium text-slate-400">
                            {{ ucfirst($trip->vehicle->type) }}
                        </span>

                    </div>


                    {{-- Trajet --}}
                    <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-center">

                        <div>
                            <p class="text-2xl font-black text-[#1E293B]">
                                {{ $trip->departure_at->format('H:i') }}
                            </p>

                            <p class="mt-1 font-semibold text-slate-700">
                                {{ $trip->departure_city }}
                            </p>

                        </div>


                        <div class="flex flex-1 items-center gap-3">

                            <div class="h-px flex-1 bg-slate-200"></div>

                            <span class="rounded-full bg-[#0B6E4F]/10 px-3 py-1 text-xs font-bold text-[#0B6E4F]">
                                {{ $trip->departure_at->diffForHumans($trip->arrival_at, true) }}
                            </span>

                            <div class="h-px flex-1 bg-slate-200"></div>

                        </div>


                        @if($trip->arrival_at)

                        <div>
                            <p class="text-2xl font-black text-[#1E293B]">
                                {{ $trip->arrival_at->format('H:i') }}
                            </p>

                            <p class="mt-1 font-semibold text-slate-700">
                                {{ $trip->arrival_city }}
                            </p>
                        </div>

                        @endif

                    </div>


                    {{-- Informations --}}
                    <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500">

                        <span>
                            🚐 {{ $trip->vehicle->brand ?? '' }}
                            {{ $trip->vehicle->model ?? '' }}
                        </span>

                        <span>
                            💺 {{ $trip->vehicle->capacity }} places
                        </span>

                        <span>
                            📅 {{ $trip->departure_at->translatedFormat('d F Y') }}
                        </span>

                    </div>

                </div>


                {{-- Prix + bouton --}}
                <div class="flex flex-row items-end justify-between gap-6 border-t border-slate-100 pt-5 lg:min-w-[190px] lg:flex-col lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">
                            À partir de
                        </p>

                        <p class="mt-1 text-2xl font-black text-[#1E293B]">
                            {{ number_format($trip->price, 0, ',', ' ') }} Ar
                        </p>

                    </div>


                    <a
                        href="{{ route('trips.show', $trip) }}"
                        class="rounded-xl bg-[#F47C20] px-5 py-3 text-center text-sm font-bold text-white transition hover:bg-orange-600 hover:shadow-md">
                        Choisir
                    </a>

                </div>

            </div>

        </article>

        @empty

        <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#0B6E4F]/10 text-3xl">
                🚐
            </div>

            <h2 class="mt-5 text-2xl font-black text-[#1E293B]">
                Aucun voyage trouvé
            </h2>

            <p class="mx-auto mt-2 max-w-md text-slate-500">
                Aucun voyage ne correspond à votre recherche.
                Essayez une autre destination ou une autre date.
            </p>

            <a
                href="{{ route('trips.index') }}"
                class="mt-6 inline-flex rounded-xl bg-[#0B6E4F] px-5 py-3 font-bold text-white transition hover:bg-emerald-800">
                Voir tous les voyages
            </a>

        </div>

        @endforelse


        {{-- ======================================================
             PAGINATION
        ======================================================= --}}

        @if($trips->hasPages())

        <div class="mt-8">
            {{ $trips->links() }}
        </div>

        @endif

    </div>

</section>

@endsection