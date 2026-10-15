@extends('layouts.app')

@section('title', $trip->departure_city . ' → ' . $trip->arrival_city . ' - MonSiège')

@section('content')

<section class="bg-slate-50">

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- Retour --}}
        <a
            href="{{ route('trips.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-[#0B6E4F] hover:text-[#F47C20]">
            ← Retour aux voyages
        </a>


        {{-- Carte principale --}}
        <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            {{-- En-tête --}}
            <div class="bg-gradient-to-br from-[#0B6E4F] to-emerald-900 p-8">

                <div class="flex flex-wrap items-center justify-between gap-4">

                    <div>

                        <span class="text-sm font-semibold text-white/70">
                            {{ $trip->agency->name }}
                        </span>

                        <h1 class="mt-2 text-3xl font-black text-white">
                            {{ $trip->departure_city }}
                            <span class="mx-2 text-[#F47C20]">→</span>
                            {{ $trip->arrival_city }}
                        </h1>

                    </div>

                    <div class="rounded-2xl bg-white/10 px-5 py-3 text-right backdrop-blur">

                        <p class="text-xs uppercase tracking-wider text-white/60">
                            Prix
                        </p>

                        <p class="text-2xl font-black text-white">
                            {{ number_format($trip->price, 0, ',', ' ') }} Ar
                        </p>

                    </div>

                </div>

            </div>


            {{-- Informations --}}
            <div class="grid gap-6 p-8 sm:grid-cols-2">

                <div class="rounded-2xl bg-slate-50 p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Départ
                    </p>

                    <p class="mt-2 text-2xl font-black text-[#1E293B]">
                        {{ $trip->departure_at->format('H:i') }}
                    </p>

                    <p class="mt-1 font-semibold text-slate-600">
                        {{ $trip->departure_at->translatedFormat('d F Y') }}
                    </p>

                    @if($trip->departure_address)

                    <p class="mt-2 text-sm text-slate-500">
                        {{ $trip->departure_address }}
                    </p>

                    @endif

                </div>


                <div class="rounded-2xl bg-slate-50 p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Arrivée
                    </p>

                    @if($trip->arrival_at)

                    <p class="mt-2 text-2xl font-black text-[#1E293B]">
                        {{ $trip->arrival_at->format('H:i') }}
                    </p>

                    <p class="mt-1 font-semibold text-slate-600">
                        {{ $trip->arrival_at->translatedFormat('d F Y') }}
                    </p>

                    @endif

                    @if($trip->arrival_address)

                    <p class="mt-2 text-sm text-slate-500">
                        {{ $trip->arrival_address }}
                    </p>

                    @endif

                </div>


                {{-- Véhicule --}}
                <div class="rounded-2xl border border-slate-200 p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Véhicule
                    </p>

                    <p class="mt-2 text-lg font-black text-[#1E293B]">
                        {{ $trip->vehicle->brand ?? '' }}
                        {{ $trip->vehicle->model ?? '' }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ ucfirst($trip->vehicle->type) }}
                        ·
                        {{ $trip->vehicle->capacity }} places
                    </p>

                </div>


                {{-- Chauffeur --}}
                <div class="rounded-2xl border border-slate-200 p-5">

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Chauffeur
                    </p>

                    <p class="mt-2 text-lg font-black text-[#1E293B]">
                        {{ $trip->driver?->user?->name ?? 'À définir' }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        Chauffeur MonSiège
                    </p>

                </div>

            </div>


            {{-- Bouton --}}
            <div class="border-t border-slate-200 bg-slate-50 p-6">

                <a
                    href="{{ route('reservations.create', [
        'trip' => $trip,
        'passengers' => request('passengers', 1),
]) }}"
                    class="flex w-full items-center justify-center rounded-xl bg-[#F47C20] px-6 py-4 font-bold text-white transition hover:bg-orange-600 hover:shadow-lg">
                    Choisir mon siège
                </a>

            </div>

        </div>

    </div>

</section>

@endsection