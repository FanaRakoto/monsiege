@extends('layouts.app')

@section('title', 'Paiement - MonSiège')

@section('content')
<section class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto max-w-5xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F47C20]">
                Étape 3
            </p>

            <h1 class="mt-2 text-3xl font-black text-slate-800">
                Paiement de votre réservation
            </h1>

            <p class="mt-2 text-slate-500">
                {{ $trip->departure_city }}
                →
                {{ $trip->arrival_city }}
            </p>
        </div>

        @if (session('payment_failed'))
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
            <p class="font-bold">Paiement échoué</p>
            <p class="mt-1 text-sm">
                La réservation n'a pas été confirmée.
                Vous pouvez réessayer tant que le délai n'est pas expiré.
            </p>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0B6E4F]/10">
                            <span class="text-2xl text-[#0B6E4F]">✓</span>
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-slate-800">
                                Paiement de démonstration
                            </h2>
                            <p class="text-sm text-slate-500">
                                Environnement de développement uniquement
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <strong>Mode test :</strong>
                    aucun argent réel ne sera transféré.
                    Les boutons ci-dessous servent uniquement à tester
                    le parcours de réservation.
                </div>

                <div class="mt-6 space-y-3">
                    <p class="font-bold text-slate-800">
                        Passagers enregistrés
                    </p>

                    @foreach ($reservation->passengers as $passenger)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3">
                        <span class="font-medium text-slate-700">
                            {{ $passenger->first_name }}
                            {{ $passenger->last_name }}
                        </span>

                        <span class="text-sm text-slate-500">
                            Passager {{ $loop->iteration }}
                        </span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 border-t border-slate-100 pt-6">
                    <p class="mb-4 font-bold text-slate-800">
                        Choisissez le résultat du test
                    </p>

                    <form
                        method="POST"
                        action="{{ route('reservations.payment.simulate', ['trip' => $trip]) }}">
                        @csrf

                        <button
                            type="submit"
                            name="result"
                            value="success"
                            class="w-full rounded-xl bg-[#0B6E4F] px-5 py-4 font-bold text-white transition hover:bg-[#08563D] focus:outline-none focus:ring-4 focus:ring-[#0B6E4F]/20">
                            Simuler un paiement réussi
                        </button>

                        <button
                            type="submit"
                            name="result"
                            value="failed"
                            class="mt-3 w-full rounded-xl border border-red-200 bg-white px-5 py-4 font-bold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-4 focus:ring-red-100">
                            Simuler un paiement échoué
                        </button>
                    </form>
                </div>
            </div>

            <aside class="h-fit rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black text-slate-800">
                    Résumé du voyage
                </h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Départ
                        </p>
                        <p class="mt-1 font-bold text-slate-700">
                            {{ $trip->departure_city }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{ $trip->departure_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Arrivée
                        </p>
                        <p class="mt-1 font-bold text-slate-700">
                            {{ $trip->arrival_city }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Sièges
                        </p>

                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($reservation->reservationSeats as $reservationSeat)
                            <span class="rounded-lg bg-[#F47C20]/10 px-3 py-2 text-xs font-bold text-[#F47C20]">
                                {{ $reservationSeat->seat?->seat_number ?? 'Siège' }}
                            </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Total à payer
                        </p>

                        <p class="mt-1 text-3xl font-black text-[#0B6E4F]">
                            {{ number_format($reservation->total_amount, 0, ',', ' ') }} Ar
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Réservation {{ $reservation->reservation_code }}
                        </p>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</section>
@endsection