@extends('layouts.app')

@section('title', 'Coordonnées de réservation - MonSiège')

@section('content')
@php
    $nameSource = trim((string) ($reservationUser?->name ?? ''));
    $nameParts = $nameSource !== '' ? preg_split('/\s+/', $nameSource, 2) : ['', ''];
    $defaultFirstName = $contact?->first_name ?? ($nameParts[0] ?? '');
    $defaultLastName = $contact?->last_name ?? ($nameParts[1] ?? '');
    $defaultPhone = $contact?->phone ?? ($reservationUser?->phone ?? '');
    $defaultEmail = $contact?->email ?? ($reservationUser?->email ?? '');
@endphp
<section class="min-h-[75vh] bg-slate-50 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl">
        <div class="mb-7">
            <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">Étape 2 sur 3 · Coordonnées</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Une réservation, un seul formulaire</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Renseignez les coordonnées du responsable du groupe. Un seul billet récapitulatif couvrira toutes les personnes et tous les sièges sélectionnés.</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="mb-6 grid gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-3">
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Itinéraire</p><p class="mt-1 font-extrabold text-slate-900">{{ $trip->departure_city }} → {{ $trip->arrival_city }}</p></div>
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Départ</p><p class="mt-1 font-extrabold text-slate-900">{{ $trip->departure_at->format('d/m/Y à H:i') }}</p></div>
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Personnes / sièges</p><p class="mt-1 text-xl font-black text-[#0B6E4F]">{{ $reservation->passenger_count }}</p></div>
        </div>

        <div class="mb-6 rounded-3xl border border-emerald-100 bg-emerald-50/80 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-wider text-emerald-800">Sièges sélectionnés</p>
                    <p class="mt-1 text-sm text-emerald-900">Chaque personne du groupe doit disposer de son propre siège.</p>
                </div>
                <span class="rounded-full bg-[#0B6E4F] px-3 py-2 text-sm font-extrabold text-white">{{ $seats->count() }} place(s)</span>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($seats as $seat)
                    <span class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-3 py-2 text-sm font-extrabold text-[#0B6E4F]"><i class="fa-solid fa-chair" aria-hidden="true"></i>{{ $seat->seat_number }}</span>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('reservations.passengers.store', ['trip' => $trip]) }}" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <div class="border-b border-slate-100 bg-slate-50 px-5 py-4 sm:px-7">
                <p class="text-xs font-extrabold uppercase tracking-wider text-[#F47C20]">Contact principal</p>
                <h2 class="mt-1 text-xl font-black text-slate-900">Responsable de la réservation</h2>
                <p class="mt-1 text-sm leading-6 text-slate-500">Cette personne est le contact de référence pour l’ensemble du groupe.</p>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
                <div>
                    <label for="first_name" class="mb-1.5 block text-sm font-bold text-slate-700">Prénom <span class="text-rose-600">*</span></label>
                    <input id="first_name" name="first_name" type="text" maxlength="100" required autocomplete="given-name" value="{{ old('first_name', $defaultFirstName) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                    @error('first_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="last_name" class="mb-1.5 block text-sm font-bold text-slate-700">Nom <span class="text-rose-600">*</span></label>
                    <input id="last_name" name="last_name" type="text" maxlength="100" required autocomplete="family-name" value="{{ old('last_name', $defaultLastName) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                    @error('last_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-bold text-slate-700">Téléphone mobile <span class="text-rose-600">*</span></label>
                    <input id="phone" name="phone" type="tel" maxlength="30" required autocomplete="tel" placeholder="034 00 000 00" value="{{ old('phone', $defaultPhone) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                    @error('phone')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-bold text-slate-700">Adresse e-mail <span class="text-xs font-normal text-slate-400">(facultative)</span></label>
                    <input id="email" name="email" type="email" maxlength="255" autocomplete="email" placeholder="nom@exemple.mg" value="{{ old('email', $defaultEmail) }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                    @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-5 py-5 sm:px-7">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Montant calculé</p><p class="mt-1 text-2xl font-black text-[#0B6E4F]">{{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar</p></div>
                    <p class="max-w-xs text-xs leading-5 text-slate-500">{{ $reservation->passenger_count }} siège(s) × {{ number_format((float) $trip->price, 0, ',', ' ') }} Ar par siège.</p>
                </div>
                <div class="flex flex-col-reverse justify-between gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('reservations.create', ['trip' => $trip]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 transition hover:bg-slate-100"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour aux sièges</a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-6 py-4 font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-[#08563D]"><i class="fa-solid fa-lock" aria-hidden="true"></i> Continuer vers le paiement <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>

        <p class="mt-4 text-center text-xs leading-5 text-slate-400">Un billet électronique récapitulera toute la réservation. Il ne permet pas de dépasser la capacité du véhicule : une place payée est requise pour chaque personne.</p>
    </div>
</section>
@endsection