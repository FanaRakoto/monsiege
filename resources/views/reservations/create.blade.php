@extends('layouts.app')

@section('title', 'Choisir ses sièges - MonSiège')

@section('content')
@php
    $initialSeatIds = collect(old('seat_ids', []))->map(fn ($id) => (string) $id)->all();
    $seatRows = $seats->groupBy('row_number');
@endphp

<style>
    .ms-seat-card { position:relative; display:block; cursor:pointer; user-select:none; }
    .ms-seat-check { position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }
    .ms-seat-visual { min-height:76px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.35rem; border:1px solid #dbe5e1; border-radius:1rem; background:#fff; color:#334155; box-shadow:0 3px 10px rgba(15,23,42,.04); transition:transform .18s ease,background .18s ease,color .18s ease,border-color .18s ease,box-shadow .18s ease; }
    .ms-seat-card:hover .ms-seat-visual { border-color:#0b6e4f; transform:translateY(-2px); }
    .ms-seat-check:focus-visible + .ms-seat-visual { outline:3px solid rgba(11,110,79,.3); outline-offset:3px; }
    .ms-seat-check:checked + .ms-seat-visual { border-color:#0b6e4f; background:#0b6e4f; color:#fff; box-shadow:0 8px 22px rgba(11,110,79,.22); transform:translateY(-2px); }
    .ms-seat-taken { min-height:76px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.35rem; border:1px solid #fecaca; border-radius:1rem; background:#fff1f2; color:#9f1239; cursor:not-allowed; opacity:.85; }
    .ms-seat-row { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.65rem; }
    .ms-seat-row-front { padding-left:15%; padding-right:15%; }
    @media (max-width:480px) { .ms-seat-visual,.ms-seat-taken { min-height:64px; border-radius:.75rem; } .ms-seat-row { gap:.4rem; } .ms-seat-row-front { padding-left:0; padding-right:0; } }
    @media (prefers-reduced-motion:reduce) { .ms-seat-visual { transition:none; } }
</style>

<section class="min-h-[75vh] bg-slate-50 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-6xl">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">Étape 1 sur 3 · Réservation</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Choisissez vos sièges</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Touchez un siège disponible pour le sélectionner. Les sièges sélectionnés passent en vert. Vous pouvez en choisir plusieurs pour une réservation groupée.</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Prix par siège</p>
                <p class="mt-1 text-xl font-black text-[#0B6E4F]">{{ number_format((float) $trip->price, 0, ',', ' ') }} Ar</p>
            </div>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(300px,.7fr)]">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Plan du véhicule</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $trip->vehicle->brand ?? '' }} {{ $trip->vehicle->model ?? 'Véhicule' }} · {{ $seatCount }} sièges affichés</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800">{{ $availableSeatCount }} disponible(s)</span>
                </div>

                <div class="mx-auto max-w-md rounded-[2rem] border-2 border-slate-200 bg-slate-50 p-3 sm:p-5">
                    <div class="mb-5 flex items-center justify-between rounded-2xl bg-slate-200/70 px-4 py-3 text-xs font-extrabold uppercase tracking-[.18em] text-slate-500">
                        <span><i class="fa-solid fa-steering-wheel mr-2" aria-hidden="true"></i> Avant du véhicule</span>
                        <span>{{ ucfirst($trip->vehicle->type ?? 'Transport') }}</span>
                    </div>

                    <form id="seatSelectionForm" method="POST" action="{{ route('reservations.selection', ['trip' => $trip]) }}">
                        @csrf
                        <input id="passengerCount" type="hidden" name="passengers" value="{{ max(1, (int) old('passengers', $passengerCount)) }}">

                        <div class="space-y-3">
                            @foreach ($seatRows as $rowNumber => $rowSeats)
                                <div class="ms-seat-row {{ (int) $rowNumber === 0 ? 'ms-seat-row-front' : '' }}" aria-label="Rangée {{ (int) $rowNumber === 0 ? 'avant' : $rowNumber }}">
                                    @if ((int) $rowNumber === 0)
                                        <div class="flex min-h-[76px] flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-100 text-center text-[10px] font-bold uppercase tracking-wide text-slate-400">
                                            <i class="fa-solid fa-steering-wheel mb-1 text-base" aria-hidden="true"></i> Chauffeur
                                        </div>
                                    @endif
                                    @foreach ($rowSeats as $seat)
                                        @php
                                            $seatReservation = $reservedSeats->get($seat->id);
                                            $isTaken = $seatReservation !== null;
                                            $seatInputId = 'seat-' . $seat->id;
                                            $isOldSelected = in_array((string) $seat->id, $initialSeatIds, true);
                                        @endphp
                                        @if ($isTaken)
                                            <div class="ms-seat-taken" aria-label="Siège {{ $seat->seat_number }}, indisponible">
                                                <i class="fa-solid fa-lock text-base" aria-hidden="true"></i>
                                                <span class="text-sm font-extrabold">{{ $seat->seat_number }}</span>
                                                <span class="text-[9px] font-bold uppercase">Occupé</span>
                                            </div>
                                        @else
                                            <label class="ms-seat-card" for="{{ $seatInputId }}">
                                                <input
                                                    id="{{ $seatInputId }}"
                                                    class="ms-seat-check js-seat-checkbox"
                                                    type="checkbox"
                                                    name="seat_ids[]"
                                                    value="{{ $seat->id }}"
                                                    data-seat-number="{{ $seat->seat_number }}"
                                                    @checked($isOldSelected)
                                                >
                                                <span class="ms-seat-visual">
                                                    <i class="fa-solid fa-chair text-base" aria-hidden="true"></i>
                                                    <strong class="text-sm font-black">{{ $seat->seat_number }}</strong>
                                                    <span class="text-[9px] font-bold uppercase tracking-wide">Libre</span>
                                                </span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3 border-t border-slate-200 pt-4 text-xs font-semibold text-slate-600">
                            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded bg-white ring-1 ring-slate-300"></span> Libre</span>
                            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded bg-[#0B6E4F]"></span> Votre sélection</span>
                            <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded bg-rose-100 ring-1 ring-rose-300"></span> Occupé</span>
                        </div>
                    </form>
                </div>
            </div>

            <aside class="space-y-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-solid fa-route text-lg" aria-hidden="true"></i></span>
                        <div><h2 class="font-extrabold text-slate-900">Votre trajet</h2><p class="text-xs text-slate-500">Résumé du voyage</p></div>
                    </div>
                    <div class="mt-5 flex items-center gap-3">
                        <div class="min-w-0 flex-1"><p class="text-xs font-semibold text-slate-500">Départ</p><p class="mt-1 break-words font-extrabold text-slate-900">{{ $trip->departure_city }}</p></div>
                        <i class="fa-solid fa-arrow-right text-[#F47C20]" aria-hidden="true"></i>
                        <div class="min-w-0 flex-1 text-right"><p class="text-xs font-semibold text-slate-500">Arrivée</p><p class="mt-1 break-words font-extrabold text-slate-900">{{ $trip->arrival_city }}</p></div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
                        <div><p class="text-xs text-slate-500">Date</p><p class="mt-1 text-sm font-bold text-slate-800">{{ $trip->departure_at->format('d/m/Y') }}</p></div>
                        <div><p class="text-xs text-slate-500">Départ à</p><p class="mt-1 text-sm font-bold text-slate-800">{{ $trip->departure_at->format('H:i') }}</p></div>
                    </div>
                </div>

                <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-extrabold text-slate-900">Votre sélection</h2>
                    <p class="mt-1 text-sm text-slate-500">Le nombre de passagers s'ajuste automatiquement au nombre de sièges choisis.</p>
                    <div class="mt-5 flex items-end justify-between gap-4">
                        <div><p class="text-sm text-slate-500">Sièges sélectionnés</p><p class="mt-1 text-3xl font-black text-[#0B6E4F]" id="selectedSeatCount">0</p></div>
                        <div class="text-right"><p class="text-sm text-slate-500">Total</p><p class="mt-1 text-2xl font-black text-slate-900" id="selectedTotal" data-unit-price="{{ (int) $trip->price }}">0 Ar</p></div>
                    </div>
                    <div class="mt-4 min-h-10 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600" id="selectedSeatNames" aria-live="polite">Aucun siège sélectionné.</div>
                    <p class="mt-4 flex gap-2 text-xs leading-5 text-slate-500"><i class="fa-solid fa-clock mt-0.5 text-[#F47C20]" aria-hidden="true"></i> Les sièges sont bloqués temporairement pendant 10 minutes pour finaliser votre réservation.</p>
                    <button id="seatSelectionSubmit" form="seatSelectionForm" type="submit" disabled class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-5 py-4 text-sm font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:bg-[#08563D] disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">
                        Continuer avec les sièges <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    <p class="mt-3 text-center text-xs text-slate-400">Aucun paiement n'est demandé à cette étape.</p>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
(() => {
    const form = document.getElementById('seatSelectionForm');
    if (!form) return;
    const boxes = Array.from(form.querySelectorAll('.js-seat-checkbox'));
    const passengersInput = document.getElementById('passengerCount');
    const countLabel = document.getElementById('selectedSeatCount');
    const namesLabel = document.getElementById('selectedSeatNames');
    const totalLabel = document.getElementById('selectedTotal');
    const submitButton = document.getElementById('seatSelectionSubmit');
    const unitPrice = Number(totalLabel?.dataset.unitPrice || 0);
    const money = (value) => new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(value) + ' Ar';

    const refresh = () => {
        const selected = boxes.filter((box) => box.checked);
        passengersInput.value = String(selected.length);
        countLabel.textContent = String(selected.length);
        namesLabel.textContent = selected.length
            ? 'Siège(s) : ' + selected.map((box) => box.dataset.seatNumber).join(', ')
            : 'Aucun siège sélectionné.';
        totalLabel.textContent = money(unitPrice * selected.length);
        submitButton.disabled = selected.length === 0;
        submitButton.innerHTML = selected.length
            ? 'Continuer avec ' + selected.length + ' siège(s) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>'
            : 'Choisissez au moins un siège <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
    };

    boxes.forEach((box) => box.addEventListener('change', refresh));
    refresh();
})();
</script>
@endsection