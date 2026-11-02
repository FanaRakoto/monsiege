@extends('layouts.app')

@section('title', 'Choisir ses sièges - MonSiège')

@section('content')
@php
    $initialSeatIds = collect(old('seat_ids', []))->map(fn ($id) => (string) $id)->all();
    $seatRows = $seats->groupBy('row_number');
    $vehicleName = trim(($trip->vehicle->brand ?? '') . ' ' . ($trip->vehicle->model ?? '')) ?: 'Véhicule';
@endphp

<style>
    .ms-booking-page { --ms-green:#0b6e4f; --ms-orange:#f47c20; }
    .ms-car-shell { position:relative; isolation:isolate; width:min(100%,430px); margin-inline:auto; padding:1.1rem .75rem 1rem; border:3px solid #b8c6c1; border-radius:46% 46% 2.2rem 2.2rem / 6rem 6rem 2.2rem 2.2rem; background:linear-gradient(90deg,#e4eae8 0,#fbfcfc 10%,#f5f8f7 50%,#fbfcfc 90%,#e4eae8 100%); box-shadow:inset 0 0 0 5px #e8eeeb,0 22px 48px rgba(15,45,35,.12); }
    .ms-car-shell::before,.ms-car-shell::after { content:""; position:absolute; z-index:-1; top:31%; width:13px; height:75px; border-radius:8px; background:linear-gradient(90deg,#1f2937,#64748b,#1f2937); box-shadow:0 270px 0 #334155; }
    .ms-car-shell::before { left:-8px; } .ms-car-shell::after { right:-8px; }
    .ms-windshield { margin:.25rem .85rem 0; height:60px; border:2px solid #aab9b4; border-radius:62% 62% 1rem 1rem / 78% 78% .8rem .8rem; background:linear-gradient(165deg,#c7d7d3 0%,#e7f0ed 48%,#aabeb7 100%); box-shadow:inset 0 -7px 0 rgba(255,255,255,.6); display:flex; align-items:center; justify-content:center; }
    .ms-dashboard { margin:.2rem .55rem .9rem; border:1px solid #cbd5d1; border-radius:1rem; background:linear-gradient(180deg,#dce4e1,#f0f4f2); padding:.65rem .7rem; display:flex; align-items:center; justify-content:space-between; gap:.6rem; color:#475569; }
    .ms-driver-seat { min-height:76px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.2rem; border:1px dashed #b8c7c1; border-radius:1rem; background:#e7ecea; color:#475569; text-align:center; font-size:.63rem; font-weight:800; text-transform:uppercase; }
    .ms-front-row { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.5rem; padding:.15rem .15rem .9rem; border-bottom:1px dashed #cbd5d1; }
    .ms-seat-row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)) 17px repeat(2,minmax(0,1fr)); align-items:stretch; gap:.45rem; padding:.55rem .05rem; }
    .ms-seat-card { position:relative; display:block; min-width:0; cursor:pointer; user-select:none; }
    .ms-seat-check { position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }
    .ms-seat-visual { min-height:74px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.18rem; border:1px solid #cfdad6; border-bottom:4px solid #aab9b4; border-radius:.95rem .95rem .7rem .7rem; background:linear-gradient(135deg,#fff,#f3f7f5); color:#334155; box-shadow:0 3px 8px rgba(15,23,42,.05); transition:transform .18s ease,background .18s ease,color .18s ease,border-color .18s ease,box-shadow .18s ease; }
    .ms-seat-card:hover .ms-seat-visual { border-color:var(--ms-green); transform:translateY(-2px); box-shadow:0 8px 18px rgba(11,110,79,.12); }
    .ms-seat-check:focus-visible + .ms-seat-visual { outline:3px solid rgba(11,110,79,.3); outline-offset:3px; }
    .ms-seat-check:checked + .ms-seat-visual { border-color:#07583f; border-bottom-color:#064a35; background:linear-gradient(145deg,#169669,#0b6e4f 72%); color:white; box-shadow:0 9px 20px rgba(11,110,79,.26); transform:translateY(-2px); }
    .ms-seat-taken { min-width:0; min-height:74px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.18rem; border:1px solid #fecdd3; border-bottom:4px solid #fda4af; border-radius:.95rem .95rem .7rem .7rem; background:linear-gradient(145deg,#fff1f2,#ffe4e6); color:#9f1239; cursor:not-allowed; opacity:.92; }
    .ms-seat-icon { display:flex; align-items:center; justify-content:center; width:1.6rem; height:1.25rem; border:1px solid currentColor; border-radius:.4rem .4rem .25rem .25rem; font-size:.7rem; }
    .ms-aisle { display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:.55rem; writing-mode:vertical-rl; letter-spacing:.13em; text-transform:uppercase; }
    .ms-rear-luggage { margin:.75rem .7rem .25rem; border:1px dashed #c0ccc7; border-radius:.8rem; background:#e7eeeb; padding:.65rem; text-align:center; color:#64748b; font-size:.63rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .ms-vehicle-type-label { border-radius:999px; background:#ecfdf5; color:#065f46; padding:.35rem .7rem; font-size:.7rem; font-weight:800; }
    @media (max-width:480px) { .ms-car-shell { padding:.8rem .5rem .7rem; border-width:2px; border-radius:37% 37% 1.6rem 1.6rem / 4rem 4rem 1.6rem 1.6rem; } .ms-windshield { height:47px; margin-inline:.65rem; } .ms-seat-row { grid-template-columns:repeat(2,minmax(0,1fr)) 11px repeat(2,minmax(0,1fr)); gap:.3rem; } .ms-front-row { gap:.3rem; } .ms-seat-visual,.ms-seat-taken,.ms-driver-seat { min-height:62px; border-radius:.7rem; } .ms-seat-icon { width:1.3rem; height:1rem; } .ms-car-shell::before,.ms-car-shell::after { width:8px; } }
    @media (prefers-reduced-motion:reduce) { .ms-seat-visual { transition:none; } }
</style>

<section class="ms-booking-page min-h-[75vh] bg-slate-50 px-3 py-7 sm:px-5 sm:py-11">
    <div class="mx-auto max-w-6xl">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">Étape 1 sur 3 · Réservation</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Choisissez vos sièges</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Choisissez autant de sièges libres que nécessaire, dans la limite des places disponibles. Chaque siège sélectionné passe en vert et le total se recalcule automatiquement.</p>
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

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(300px,.9fr)]">
            <section class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Plan intérieur du véhicule</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $vehicleName }} · {{ $trip->vehicle->registration_number ?? 'Immatriculation non renseignée' }}</p>
                    </div>
                    <span class="ms-vehicle-type-label">{{ ucfirst($trip->vehicle->type ?? 'Transport') }} · {{ $seatCount }} places</span>
                </div>

                <div class="rounded-[2rem] bg-gradient-to-b from-slate-100 to-white px-1 py-4 sm:px-4">
                    <div class="ms-car-shell">
                        <div class="ms-windshield"><span class="text-[10px] font-black uppercase tracking-[.28em] text-slate-600">Avant du véhicule</span></div>
                        <div class="ms-dashboard">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider">Tableau de bord</span>
                            <span class="flex items-center gap-2"><i class="fa-solid fa-user-tie text-lg" aria-hidden="true"></i><span class="text-[10px] font-bold">Conduite</span></span>
                        </div>

                        @if (!$seatRows->has(0))
                            <div class="ms-front-row" aria-label="Poste du chauffeur">
                                <div class="ms-driver-seat"><i class="fa-solid fa-user-tie text-xl" aria-hidden="true"></i><span>Chauffeur</span><span class="text-[9px] font-semibold normal-case tracking-normal">Non réservable</span></div>
                                <div class="flex min-h-[76px] items-center justify-center text-[10px] font-bold uppercase tracking-wider text-slate-300">Entrée</div>
                                <div class="flex min-h-[76px] items-center justify-center text-[10px] font-bold uppercase tracking-wider text-slate-300">Avant</div>
                            </div>
                        @endif

                        <form id="seatSelectionForm" method="POST" action="{{ route('reservations.selection', ['trip' => $trip]) }}">
                            @csrf

                            @foreach ($seatRows as $rowNumber => $rowSeats)
                                @if ((int) $rowNumber === 0)
                                    <div class="ms-front-row" aria-label="Rangée avant du véhicule">
                                        <div class="ms-driver-seat"><i class="fa-solid fa-user-tie text-xl" aria-hidden="true"></i><span>Chauffeur</span><span class="text-[9px] font-semibold normal-case tracking-normal">Non réservable</span></div>
                                        @foreach ($rowSeats as $seat)
                                            @php
                                                $seatReservation = $reservedSeats->get($seat->id);
                                                $isTaken = $seatReservation !== null;
                                                $seatInputId = 'seat-' . $seat->id;
                                                $isOldSelected = in_array((string) $seat->id, $initialSeatIds, true);
                                            @endphp
                                            @if ($isTaken)
                                                <div class="ms-seat-taken" aria-label="Siège {{ $seat->seat_number }}, indisponible"><i class="fa-solid fa-lock" aria-hidden="true"></i><strong class="text-sm font-extrabold">{{ $seat->seat_number }}</strong><span class="text-[9px] font-bold uppercase">Occupé</span></div>
                                            @else
                                                <label class="ms-seat-card" for="{{ $seatInputId }}">
                                                    <input id="{{ $seatInputId }}" class="ms-seat-check js-seat-checkbox" type="checkbox" name="seat_ids[]" value="{{ $seat->id }}" data-seat-number="{{ $seat->seat_number }}" @checked($isOldSelected)>
                                                    <span class="ms-seat-visual"><span class="ms-seat-icon"><i class="fa-solid fa-chair" aria-hidden="true"></i></span><strong class="text-sm font-black">{{ $seat->seat_number }}</strong><span class="text-[9px] font-bold uppercase tracking-wide">Libre</span></span>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="ms-seat-row" aria-label="Rangée {{ $rowNumber }}">
                                        @foreach ($rowSeats as $seat)
                                            @php
                                                $seatReservation = $reservedSeats->get($seat->id);
                                                $isTaken = $seatReservation !== null;
                                                $seatInputId = 'seat-' . $seat->id;
                                                $isOldSelected = in_array((string) $seat->id, $initialSeatIds, true);
                                            @endphp
                                            @if ($isTaken)
                                                <div class="ms-seat-taken" aria-label="Siège {{ $seat->seat_number }}, indisponible"><i class="fa-solid fa-lock" aria-hidden="true"></i><strong class="text-sm font-extrabold">{{ $seat->seat_number }}</strong><span class="text-[9px] font-bold uppercase">Occupé</span></div>
                                            @else
                                                <label class="ms-seat-card" for="{{ $seatInputId }}">
                                                    <input id="{{ $seatInputId }}" class="ms-seat-check js-seat-checkbox" type="checkbox" name="seat_ids[]" value="{{ $seat->id }}" data-seat-number="{{ $seat->seat_number }}" @checked($isOldSelected)>
                                                    <span class="ms-seat-visual"><span class="ms-seat-icon"><i class="fa-solid fa-chair" aria-hidden="true"></i></span><strong class="text-sm font-black">{{ $seat->seat_number }}</strong><span class="text-[9px] font-bold uppercase tracking-wide">Libre</span></span>
                                                </label>
                                            @endif
                                            @if ((int) ($seat->column_number ?? 0) === 2)
                                                <span class="ms-aisle" aria-hidden="true">Allée</span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                            <div class="ms-rear-luggage"><i class="fa-solid fa-suitcase-rolling mr-2" aria-hidden="true"></i> Arrière du véhicule · Bagages</div>
                        </form>
                    </div>
                </div>

                <div class="mt-5 flex flex-wrap justify-center gap-x-4 gap-y-2 border-t border-slate-100 pt-4 text-xs font-semibold text-slate-600">
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded bg-white ring-1 ring-slate-300"></span> Libre</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded bg-[#0B6E4F]"></span> Sélectionné</span>
                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded bg-rose-100 ring-1 ring-rose-300"></span> Occupé</span>
                </div>
                <p class="mt-3 text-center text-xs text-slate-400">Illustration schématique du véhicule : la disponibilité des sièges est vérifiée de nouveau par le serveur.</p>
            </section>

            <aside class="space-y-5">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center gap-3"><span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-solid fa-route text-lg" aria-hidden="true"></i></span><div><h2 class="font-extrabold text-slate-900">Votre trajet</h2><p class="text-xs text-slate-500">Résumé du voyage</p></div></div>
                    <div class="mt-5 flex items-center gap-3"><div class="min-w-0 flex-1"><p class="text-xs font-semibold text-slate-500">Départ</p><p class="mt-1 break-words font-extrabold text-slate-900">{{ $trip->departure_city }}</p></div><i class="fa-solid fa-arrow-right text-[#F47C20]" aria-hidden="true"></i><div class="min-w-0 flex-1 text-right"><p class="text-xs font-semibold text-slate-500">Arrivée</p><p class="mt-1 break-words font-extrabold text-slate-900">{{ $trip->arrival_city }}</p></div></div>
                    <div class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4"><div><p class="text-xs text-slate-500">Date</p><p class="mt-1 text-sm font-bold text-slate-800">{{ $trip->departure_at->format('d/m/Y') }}</p></div><div><p class="text-xs text-slate-500">Départ à</p><p class="mt-1 text-sm font-bold text-slate-800">{{ $trip->departure_at->format('H:i') }}</p></div></div>
                </div>

                <div class="rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-lg font-extrabold text-slate-900">Votre sélection</h2>
                    <p class="mt-1 text-sm text-slate-500">Le nombre de passagers est calculé par le serveur à partir des sièges choisis.</p>
                    <div class="mt-5 flex items-end justify-between gap-4"><div><p class="text-sm text-slate-500">Sièges sélectionnés</p><p class="mt-1 text-3xl font-black text-[#0B6E4F]" id="selectedSeatCount">0</p></div><div class="text-right"><p class="text-sm text-slate-500">Total</p><p class="mt-1 text-2xl font-black text-slate-900" id="selectedTotal" data-unit-price="{{ (int) $trip->price }}">0 Ar</p></div></div>
                    <div class="mt-4 min-h-10 rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-600" id="selectedSeatNames" aria-live="polite">Aucun siège sélectionné.</div>
                    <p class="mt-4 flex gap-2 text-xs leading-5 text-slate-500"><i class="fa-solid fa-clock mt-0.5 text-[#F47C20]" aria-hidden="true"></i> Les sièges sont bloqués temporairement pendant 10 minutes après validation.</p>
                    <button id="seatSelectionSubmit" form="seatSelectionForm" type="submit" disabled class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-5 py-4 text-sm font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:bg-[#08563D] disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">Continuer avec les sièges <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
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
    const countLabel = document.getElementById('selectedSeatCount');
    const namesLabel = document.getElementById('selectedSeatNames');
    const totalLabel = document.getElementById('selectedTotal');
    const submitButton = document.getElementById('seatSelectionSubmit');
    const unitPrice = Number(totalLabel?.dataset.unitPrice || 0);
    const money = (value) => new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(value) + ' Ar';
    const refresh = () => {
        const selected = boxes.filter((box) => box.checked);
        if (countLabel) countLabel.textContent = String(selected.length);
        if (namesLabel) namesLabel.textContent = selected.length ? 'Siège(s) : ' + selected.map((box) => box.dataset.seatNumber).join(', ') : 'Aucun siège sélectionné.';
        if (totalLabel) totalLabel.textContent = money(unitPrice * selected.length);
        if (submitButton) {
            submitButton.disabled = selected.length === 0;
            submitButton.innerHTML = selected.length ? 'Continuer avec ' + selected.length + ' siège(s) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>' : 'Choisissez au moins un siège <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>';
        }
        form.querySelectorAll('.js-seat-checkbox').forEach((box) => {
            const status = box.nextElementSibling?.querySelector('span:last-child');
            if (status) status.textContent = box.checked ? 'Choisi' : 'Libre';
        });
    };
    boxes.forEach((box) => box.addEventListener('change', refresh));
    refresh();
})();
</script>
@endsection