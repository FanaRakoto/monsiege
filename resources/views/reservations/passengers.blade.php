@extends('layouts.app')

@section('title', 'Informations des passagers - MonSiège')

@section('content')
@php
    $primaryNameParts = preg_split('/\s+/', trim((string) ($reservationUser?->name ?? '')), 2);
@endphp
<section class="min-h-[75vh] bg-slate-50 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl">
        <div class="mb-7">
            <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">Étape 2 sur 3 · Passagers</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Qui voyage avec vous ?</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Renseignez les informations de chaque passager. Un formulaire correspond à un siège sélectionné.</p>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="mb-6 grid gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-3">
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Itinéraire</p><p class="mt-1 font-extrabold text-slate-900">{{ $trip->departure_city }} → {{ $trip->arrival_city }}</p></div>
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Départ</p><p class="mt-1 font-extrabold text-slate-900">{{ $trip->departure_at->format('d/m/Y à H:i') }}</p></div>
            <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total provisoire</p><p class="mt-1 text-xl font-black text-[#0B6E4F]">{{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar</p></div>
        </div>

        <form method="POST" action="{{ route('reservations.passengers.store', ['trip' => $trip]) }}" class="space-y-5">
            @csrf
            @foreach ($seats as $index => $seat)
                <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 bg-emerald-50/70 px-5 py-4 sm:px-6">
                        <div><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Passager {{ $index + 1 }}</p><h2 class="mt-1 text-lg font-black text-slate-900">Informations personnelles</h2></div>
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#0B6E4F] px-3 py-2 text-sm font-extrabold text-white"><i class="fa-solid fa-chair" aria-hidden="true"></i> Siège {{ $seat->seat_number }}</span>
                    </div>
                    <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6">
                        <div>
                            <label for="first_name_{{ $index }}" class="mb-1.5 block text-sm font-bold text-slate-700">Prénom <span class="text-rose-600">*</span></label>
                            <input id="first_name_{{ $index }}" name="first_name[{{ $index }}]" type="text" maxlength="100" required autocomplete="given-name" value="{{ old('first_name.'.$index, $index === 0 ? ($primaryNameParts[0] ?? '') : '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                            @error('first_name.'.$index)<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="last_name_{{ $index }}" class="mb-1.5 block text-sm font-bold text-slate-700">Nom <span class="text-rose-600">*</span></label>
                            <input id="last_name_{{ $index }}" name="last_name[{{ $index }}]" type="text" maxlength="100" required autocomplete="family-name" value="{{ old('last_name.'.$index, $index === 0 ? ($primaryNameParts[1] ?? '') : '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                            @error('last_name.'.$index)<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone_{{ $index }}" class="mb-1.5 block text-sm font-bold text-slate-700">Téléphone <span class="text-rose-600">*</span></label>
                            <input id="phone_{{ $index }}" name="phone[{{ $index }}]" type="tel" maxlength="30" required autocomplete="tel" placeholder="034 00 000 00" value="{{ old('phone.'.$index, $index === 0 ? ($reservationUser?->phone ?? '') : '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                            @error('phone.'.$index)<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email_{{ $index }}" class="mb-1.5 block text-sm font-bold text-slate-700">E-mail <span class="text-xs font-normal text-slate-400">(facultatif)</span></label>
                            <input id="email_{{ $index }}" name="email[{{ $index }}]" type="email" maxlength="255" autocomplete="email" placeholder="nom@exemple.mg" value="{{ old('email.'.$index, $index === 0 ? ($reservationUser?->email ?? '') : '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100">
                            @error('email.'.$index)<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>
            @endforeach

            <div class="flex flex-col-reverse justify-between gap-3 sm:flex-row sm:items-center">
                <a href="{{ route('reservations.create', ['trip' => $trip, 'passengers' => $passengerCount]) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 font-bold text-slate-700 transition hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour aux sièges</a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-6 py-4 font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-[#08563D]"><i class="fa-solid fa-lock" aria-hidden="true"></i> Continuer vers le paiement <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
            </div>
            <p class="text-center text-xs leading-5 text-slate-400">Vos informations servent à identifier les passagers sur le billet.</p>
        </form>
    </div>
</section>
@endsection