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


        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">


            {{-- Passagers --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                @for($i = 1; $i <= $passengerCount; $i++)

                    <div class="mb-6 rounded-2xl border border-slate-200 p-5 last:mb-0">

                    <div class="mb-5 flex items-center justify-between">

                        <h2 class="font-black text-[#1E293B]">
                            Passager {{ $i }}
                        </h2>

                        <span class="rounded-full bg-[#F47C20]/10 px-3 py-1 text-xs font-bold text-[#F47C20]">
                            Siège {{ $seats[$i - 1]->seat_number ?? '?' }}
                        </span>

                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Prénom
                            </label>

                            <input
                                type="text"
                                name="first_name[]"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">

                        </div>

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Nom
                            </label>

                            <input
                                type="text"
                                name="last_name[]"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">

                        </div>

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Téléphone
                            </label>

                            <input
                                type="tel"
                                name="phone[]"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">

                        </div>

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email[]"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">

                        </div>

                    </div>

            </div>

            @endfor

        </div>


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

            </div>

        </aside>

    </div>

    </div>

</section>

@endsection