@extends('layouts.app')

@section('title', 'Informations des passagers - MonSiège')

@section('content')

<section class="min-h-screen bg-slate-50">

    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">

        <div class="mb-8">

            <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#F47C20]">
                Étape 2
            </span>

            <h1 class="mt-2 text-3xl font-black text-[#1E293B]">
                Informations des passagers
            </h1>

            <p class="mt-2 text-slate-500">
                {{ $trip->departure_city }}
                →
                {{ $trip->arrival_city }}
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('reservations.passengers.store', ['trip' => $trip]) }}"
            class="grid gap-6 lg:grid-cols-[1fr_320px]">
            @csrf

            {{-- Résumé --}}
            <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:sticky lg:top-28">

                <h2 class="text-xl font-black text-[#1E293B]">
                    Résumé
                </h2>

                <div class="mt-5 space-y-4">

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Chauffeur
                        </p>

                        <p class="mt-1 font-bold text-slate-700">
                            {{ $trip->driver?->user?->name ?? 'Non affecté' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Réservateur
                        </p>

                        <p class="mt-1 font-bold text-slate-700">
                            {{ $reservationUser?->name ?? 'Visiteur' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Sièges
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">

                            @foreach($seats as $seat)

                            <span class="rounded-lg bg-[#F47C20]/10 px-3 py-2 text-xs font-bold text-[#F47C20]">
                                {{ $seat->seat_number }}
                            </span>

                            @endforeach

                        </div>

                    </div>

                    <div class="border-t border-slate-100 pt-4">

                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Prix total
                        </p>

                        <p class="mt-1 text-2xl font-black text-[#0B6E4F]">
                            {{ number_format($trip->price * $passengerCount, 0, ',', ' ') }} Ar
                        </p>

                    </div>

                    <button
                        type="submit"
                        class="mt-6 w-full rounded-xl bg-[#0B6E4F] px-5 py-4
           font-bold text-white transition hover:bg-[#08563D]
           focus:outline-none focus:ring-4 focus:ring-[#0B6E4F]/20">
                        Continuer vers le paiement
                        <span aria-hidden="true">→</span>
                    </button>

                </div>

            </aside>
        </form>
    </div>

    </div>

</section>

@endsection