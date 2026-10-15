@extends('layouts.app')

@section('title', 'Choix du siège - MonSiège')

@section('content')

<section class="min-h-screen bg-slate-100">

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- =====================================================
             RETOUR
        ====================================================== --}}

        <a
            href="{{ route('trips.show', $trip) }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-[#0B6E4F] transition hover:text-[#F47C20]">
            ← Retour au voyage
        </a>


        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">

            <div>

                <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#F47C20]">
                    MonSiège
                </span>

                <h1 class="mt-2 text-3xl font-black text-[#1E293B] sm:text-4xl">
                    Choisissez votre siège
                </h1>

                <p class="mt-2 text-slate-500">
                    {{ $trip->departure_city }}
                    <span class="mx-1 text-[#F47C20]">→</span>
                    {{ $trip->arrival_city }}
                    ·
                    {{ $trip->departure_at->translatedFormat('d F Y') }}
                </p>

            </div>


            {{-- Résumé capacité --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="grid grid-cols-3 gap-3 text-center">

                    <div>
                        <p class="text-2xl font-black text-[#1E293B]">
                            {{ $seatCount }}
                        </p>

                        <p class="text-xs text-slate-400">
                            sièges
                        </p>
                    </div>

                    <div>
                        <p class="text-2xl font-black text-red-500">
                            {{ $occupiedSeatCount }}
                        </p>

                        <p class="text-xs text-slate-400">
                            occupés
                        </p>
                    </div>

                    <div>
                        <p class="text-2xl font-black text-green-600">
                            {{ $availableSeatCount }}
                        </p>

                        <p class="text-xs text-slate-400">
                            disponibles
                        </p>
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             ALERTE CAPACITÉ
        ====================================================== --}}

        @if(!$canBook)

        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5">

            <p class="font-bold text-red-700">
                Pas assez de sièges disponibles.
            </p>

            <p class="mt-1 text-sm text-red-600">
                Vous avez demandé {{ $passengerCount }} siège(s),
                mais seulement {{ $availableSeatCount }} sont disponibles.
            </p>

        </div>

        @endif


        <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">


            {{-- =================================================
                 PLAN DU VÉHICULE
            ================================================== --}}

            <div class="flex justify-center">
                <div class="vehicle-shell w-full max-w-[430px]">

                    {{-- =====================================================
         PARE-BRISE
    ====================================================== --}}

                    <div class="vehicle-windshield">

                        <div class="flex items-center justify-between px-5">

                            <span class="text-[9px] font-bold tracking-[0.2em] text-white/50">
                                AVANT
                            </span>

                            <span class="text-[10px] font-black tracking-[0.15em] text-white/80">
                                SPRINTER
                            </span>

                            <span class="text-[9px] font-bold text-white/50">
                                {{ $trip->vehicle->registration_number }}
                            </span>

                        </div>

                    </div>


                    {{-- =====================================================
         CONDUCTEUR + 2 PASSAGERS AVANT
    ====================================================== --}}

                    <div class="front-cabin">

                        {{-- Chauffeur --}}
                        <div class="driver-block">

                            <div class="driver-seat-visual">

                                <div class="driver-seat-back">
                                    <div class="driver-headrest"></div>
                                </div>

                                <div class="driver-seat-cushion"></div>

                            </div>

                            <div class="driver-name">

                                <span class="text-[8px] font-bold uppercase tracking-wider text-slate-400">
                                    Chauffeur
                                </span>

                                <span class="mt-1 block text-xs font-black text-slate-700">
                                    {{ $trip->driver?->user?->name ?? 'Non affecté' }}
                                </span>

                                <span class="mt-1 inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[8px] font-bold uppercase text-slate-500">
                                    Non disponible
                                </span>

                            </div>

                            <div class="steering-wheel">
                                <div class="steering-wheel-center">
                                    MS
                                </div>
                            </div>

                        </div>


                        {{-- Deux passagers avant --}}
                        <div class="front-passengers">

                            @php
                            $frontSeats = $seats
                            ->where('row_number', 0)
                            ->sortBy('column_number');
                            @endphp

                            @foreach($frontSeats as $seat)

                            @php
                            $isReserved = isset($reservedSeats[$seat->id]);
                            $reservationStatus = $reservedSeats[$seat->id]->status ?? null;
                            @endphp

                            @include('reservations.partials.seat', [
                            'seat' => $seat,
                            'isReserved' => $isReserved,
                            'reservationStatus' => $reservationStatus,
                            ])

                            @endforeach

                        </div>

                    </div>


                    {{-- =====================================================
         CABINE ARRIÈRE
    ====================================================== --}}

                    <div class="passenger-cabin">

                        <div class="mb-5 flex items-center justify-between">

                            <div>

                                <p class="text-[9px] font-bold uppercase tracking-[0.18em] text-slate-400">
                                    Cabine passagers
                                </p>

                                <p class="mt-1 text-xs font-semibold text-slate-600">
                                    {{ $seatCount }} sièges voyageurs
                                </p>

                            </div>

                            <div class="rounded-full bg-[#0B6E4F]/10 px-2.5 py-1 text-[9px] font-bold text-[#0B6E4F]">
                                {{ ucfirst($trip->vehicle->type) }}
                            </div>

                        </div>


                        @php
                        $rearRows = $seats
                        ->where('row_number', '>', 0)
                        ->groupBy('row_number');
                        @endphp


                        <div class="space-y-3">

                            @foreach($rearRows as $rowNumber => $rowSeats)

                            @php
                            $seatByColumn = $rowSeats->keyBy('column_number');
                            @endphp

                            <div class="seat-row">

                                @foreach([1, 2] as $column)

                                @if($seat = $seatByColumn->get($column))

                                @php
                                $isReserved = isset($reservedSeats[$seat->id]);
                                $reservationStatus = $reservedSeats[$seat->id]->status ?? null;
                                @endphp

                                @include('reservations.partials.seat', [
                                'seat' => $seat,
                                'isReserved' => $isReserved,
                                'reservationStatus' => $reservationStatus,
                                ])

                                @else
                                <div></div>
                                @endif

                                @endforeach


                                {{-- ALLÉE --}}
                                <div class="aisle">
                                    <span>{{ $rowNumber }}</span>
                                </div>


                                @foreach([3, 4] as $column)

                                @if($seat = $seatByColumn->get($column))

                                @php
                                $isReserved = isset($reservedSeats[$seat->id]);
                                $reservationStatus = $reservedSeats[$seat->id]->status ?? null;
                                @endphp

                                @include('reservations.partials.seat', [
                                'seat' => $seat,
                                'isReserved' => $isReserved,
                                'reservationStatus' => $reservationStatus,
                                ])

                                @else
                                <div></div>
                                @endif

                                @endforeach

                            </div>

                            @endforeach

                        </div>

                    </div>


                    {{-- =====================================================
         ARRIÈRE DU SPRINTER
    ====================================================== --}}

                    <div class="vehicle-rear">

                        <span class="vehicle-rear-light"></span>

                        <span>
                            ARRIÈRE
                        </span>

                        <span class="vehicle-rear-light"></span>

                    </div>

                </div>
            </div>


            {{-- =================================================
                 PANNEAU DE RÉSERVATION
            ================================================== --}}

            <aside class="h-fit lg:sticky lg:top-28">

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    <h2 class="text-xl font-black text-[#1E293B]">
                        Votre sélection
                    </h2>
                    <div class="mt-4 rounded-2xl bg-slate-50 p-4">

                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            Réservateur
                        </p>

                        <p class="mt-1 font-black text-[#1E293B]">
                            {{ auth()->user()?->name ?? 'Visiteur' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ auth()->user()?->phone ?? 'Connexion requise pour finaliser la réservation' }}
                        </p>

                    </div>

                    <p class="mt-1 text-sm text-slate-500">
                        Sélectionnez {{ $passengerCount }} siège(s).
                    </p>


                    {{-- compteur --}}
                    <div class="mt-6 rounded-2xl bg-[#0B6E4F]/5 p-5">

                        <div class="flex items-end justify-between">

                            <div>

                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Sièges sélectionnés
                                </p>

                                <p class="mt-1 text-3xl font-black text-[#0B6E4F]">
                                    <span id="selected-count">0</span>
                                    <span class="text-lg text-slate-400">
                                        / {{ $passengerCount }}
                                    </span>
                                </p>

                            </div>

                            <div class="text-right">

                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Total
                                </p>

                                <p
                                    id="selected-total"
                                    class="mt-1 text-xl font-black text-[#F47C20]">
                                    0 Ar
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- sièges choisis --}}
                    <div class="mt-6">

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Vos places
                        </p>

                        <div
                            id="selected-seats"
                            class="mt-3 flex min-h-10 flex-wrap gap-2">

                            <span class="text-sm text-slate-400">
                                Aucun siège sélectionné
                            </span>

                        </div>

                    </div>


                    {{-- légende --}}
                    <div class="mt-7 border-t border-slate-100 pt-6">

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Légende
                        </p>

                        <div class="mt-4 space-y-3">

                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="legend-seat available"></span>
                                Disponible
                            </div>

                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="legend-seat selected"></span>
                                Votre sélection
                            </div>

                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="legend-seat locked"></span>
                                Temporairement bloqué
                            </div>

                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="legend-seat occupied"></span>
                                Réservé
                            </div>

                        </div>

                    </div>


                    {{-- Bouton --}}
                    <form
                        id="seat-selection-form"
                        action="{{ route('reservations.selection', $trip) }}"
                        method="POST">
                        @csrf

                        <div id="selected-seat-inputs"></div>

                        <button
                            id="continue-reservation"
                            type="submit"
                            disabled
                            class="mt-7 w-full cursor-not-allowed rounded-xl bg-slate-200 px-5 py-4 font-bold text-slate-400 transition">
                            Sélectionnez vos sièges
                        </button>

                    </form>

                </div>

            </aside>

        </div>

    </div>


</section>
<script>
    const maxSeats = Number(
        window.monSiege?.passengerCount ?? 1
    );
</script>

@endsection