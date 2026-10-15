
@extends('layouts.app')

@section('title', 'Mon espace client | MonSiège')

@section('content')
@php
    $upcomingReservations = $upcomingReservations ?? collect();
    $historyReservations = $historyReservations ?? collect();
@endphp

<section class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        {{-- En-tête --}}
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                <span class="text-sm font-bold uppercase tracking-wider text-[#F47C20]">
                    Mon espace
                </span>

                <h1 class="mt-2 text-3xl font-black text-slate-900 sm:text-4xl">
                    Bonjour, {{ auth()->user()->name }} !
                </h1>

                <p class="mt-2 text-slate-500">
                    Gérez vos réservations et préparez vos prochains départs.
                </p>
            </div>

            <a
                href="{{ route('trips.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-[#F47C20] px-5 py-3 font-bold text-white shadow-md transition hover:bg-orange-600">
                + Réserver un voyage
            </a>
        </div>

        {{-- Informations du compte --}}
        <div class="mt-8 grid gap-5 md:grid-cols-3">

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Voyages à venir</p>
                    <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-50 text-xl text-[#0B6E4F]">↗</span>
                </div>

                <p class="mt-4 text-3xl font-black text-slate-900">
                    {{ $upcomingReservations->count() }}
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Réservations prochaines
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Historique</p>
                    <span class="flex size-11 items-center justify-center rounded-xl bg-orange-50 text-xl text-[#F47C20]">◷</span>
                </div>

                <p class="mt-4 text-3xl font-black text-slate-900">
                    {{ $historyReservations->count() }}
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Réservations passées
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-500">Mon compte</p>
                    <span class="flex size-11 items-center justify-center rounded-xl bg-slate-100 text-xl text-slate-600">♙</span>
                </div>

                <p class="mt-4 truncate text-lg font-black text-slate-900">
                    {{ auth()->user()->email }}
                </p>

                <p class="mt-1 text-sm text-slate-400">
                    Client MonSiège
                </p>
            </div>
        </div>

        {{-- Prochains voyages --}}
        <div class="mt-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        Mes prochains voyages
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Retrouvez les détails de vos prochaines réservations.
                    </p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($upcomingReservations as $reservation)
                    <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md sm:p-6">

                        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-[#0B6E4F]">
                                        {{ $reservation->reservation_code }}
                                    </span>

                                    <span class="rounded-full px-3 py-1 text-xs font-bold
                                        {{ $reservation->status === 'confirmed'
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : ($reservation->status === 'pending'
                                                ? 'bg-amber-100 text-amber-700'
                                                : 'bg-slate-100 text-slate-600') }}">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </div>

                                <h3 class="mt-4 text-lg font-black text-slate-900">
                                    {{ $reservation->trip?->departure_city ?? 'Départ inconnu' }}
                                    <span class="mx-2 text-[#F47C20]">→</span>
                                    {{ $reservation->trip?->arrival_city ?? 'Destination inconnue' }}
                                </h3>

                                <p class="mt-2 text-sm text-slate-500">
                                    Départ :
                                    {{ $reservation->trip?->departure_at?->format('d/m/Y à H:i') ?? 'Date indisponible' }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    Passagers : {{ $reservation->passenger_count }}
                                </p>
                            </div>

                            <div class="sm:text-right">
                                <p class="text-2xl font-black text-slate-900">
                                    {{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar
                                </p>

                                @if ($reservation->ticket && \Illuminate\Support\Facades\Route::has('tickets.show'))
                                    <a
                                        href="{{ route('tickets.show', $reservation->ticket) }}"
                                        class="mt-3 inline-flex rounded-xl bg-[#0B6E4F] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                                        Voir mon billet
                                    </a>
                                @else
                                    <p class="mt-2 text-xs text-slate-400">
                                        Billet disponible après confirmation.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl text-[#0B6E4F]">
                            ↗
                        </div>

                        <h3 class="mt-4 text-lg font-black text-slate-900">
                            Aucun voyage à venir
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            Vos prochaines réservations apparaîtront ici. Recherchez un trajet pour préparer votre prochain voyage.
                        </p>

                        <a
                            href="{{ route('trips.index') }}"
                            class="mt-5 inline-flex rounded-xl bg-[#F47C20] px-5 py-3 font-bold text-white transition hover:bg-orange-600">
                            Découvrir les voyages
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Historique --}}
        <div class="mt-12">
            <h2 class="text-2xl font-black text-slate-900">
                Historique de mes voyages
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Retrouvez les réservations et les anciens trajets associés à votre compte.
            </p>

            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                @forelse ($historyReservations as $reservation)
                    <div class="flex flex-col justify-between gap-3 border-b border-slate-100 p-5 last:border-b-0 sm:flex-row sm:items-center">
                        <div>
                            <p class="font-bold text-slate-900">
                                {{ $reservation->trip?->departure_city ?? 'Départ inconnu' }}
                                →
                                {{ $reservation->trip?->arrival_city ?? 'Destination inconnue' }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ $reservation->trip?->departure_at?->format('d/m/Y à H:i') ?? 'Date indisponible' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Référence : {{ $reservation->reservation_code }}
                            </p>
                        </div>

                        <div class="sm:text-right">
                            <p class="font-extrabold text-slate-900">
                                {{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar
                            </p>

                            <span class="mt-1 inline-block rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-slate-500">
                        Votre historique apparaîtra ici après vos premiers voyages.
                    </p>
                @endforelse
            </div>
        </div>

        {{-- Déconnexion --}}
        <div class="mt-10 flex justify-end">
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                    Se déconnecter
                </button>
            </form>
        </div>

    </div>
</section>
@endsection
