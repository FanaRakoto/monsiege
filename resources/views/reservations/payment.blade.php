@extends('layouts.app')

@section('title', 'Paiement de démonstration - MonSiège')

@section('content')
<section class="min-h-[75vh] bg-slate-50 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl">
        <div class="mb-7">
            <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">Étape 3 sur 3 · Paiement</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Finalisez votre réservation</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Cette version utilise un paiement de démonstration local : aucune carte bancaire, aucun code secret et aucune clé API ne sont nécessaires.</p>
        </div>

        @if (session('payment_failed'))
            <div role="alert" class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900"><i class="fa-solid fa-circle-exclamation mr-2" aria-hidden="true"></i>La simulation a échoué. Aucun billet n'a été émis et la réservation reste en attente tant que le délai de blocage n'est pas expiré. Vous pouvez réessayer.</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-solid fa-shield-halved text-xl" aria-hidden="true"></i></span>
                    <div><h2 class="text-xl font-black text-slate-900">Paiement sécurisé de démonstration</h2><p class="mt-1 text-sm leading-6 text-slate-600">MonSiège vérifie la réservation côté serveur, contrôle la disponibilité des sièges et ne demande aucune donnée bancaire. Ce test ne transfère pas d'argent réel.</p></div>
                </div>

                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                    <p class="text-sm font-extrabold text-emerald-900"><i class="fa-solid fa-lock mr-2" aria-hidden="true"></i>Aucune carte bancaire n'est collectée</p>
                    <p class="mt-1 text-sm leading-6 text-emerald-800">N'entrez jamais de numéro de carte ou de mot de passe Mobile Money sur une page de démonstration. Le bouton ci-dessous simule seulement le résultat d'un paiement.</p>
                </div>

                <div class="mt-6">
                    <h3 class="font-extrabold text-slate-900">Mode de test</h3>
                    <p class="mt-1 text-sm text-slate-500">Choisissez le résultat fictif à tester.</p>
                    <form method="POST" action="{{ route('reservations.payment.simulate', ['trip' => $trip]) }}" class="mt-4 space-y-3">
                        @csrf
                        <button type="submit" name="result" value="success" class="flex w-full items-center justify-between gap-4 rounded-2xl bg-[#0B6E4F] px-5 py-4 text-left font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-[#08563D]">
                            <span class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-xl" aria-hidden="true"></i><span><span class="block">Simuler une réussite</span><span class="mt-1 block text-xs font-medium text-emerald-50/80">Confirme la réservation et génère le billet QR.</span></span></span>
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </button>
                        <button type="submit" name="result" value="failed" class="flex w-full items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-4 text-left font-extrabold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50">
                            <span class="flex items-center gap-3"><i class="fa-solid fa-circle-xmark text-xl text-amber-600" aria-hidden="true"></i><span><span class="block">Simuler un échec</span><span class="mt-1 block text-xs font-medium text-slate-500">Permet de tester le parcours de paiement refusé.</span></span></span>
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            </div>

            <aside class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="bg-[#0B6E4F] px-5 py-5 text-white">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-100">Résumé de réservation</p>
                    <h2 class="mt-2 text-xl font-black">{{ $trip->departure_city }} <span class="text-orange-300">→</span> {{ $trip->arrival_city }}</h2>
                    <p class="mt-2 text-sm text-white/75">{{ $trip->departure_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="space-y-4 p-5">
                    <div class="flex items-center justify-between gap-4 text-sm"><span class="text-slate-500">Nombre de passagers</span><span class="font-extrabold text-slate-900">{{ $reservation->passenger_count }}</span></div>
                    <div class="flex items-start justify-between gap-4 text-sm"><span class="text-slate-500">Sièges</span><span class="text-right font-extrabold text-slate-900">{{ $reservation->reservationSeats->map(fn ($row) => $row->seat?->seat_number)->filter()->join(', ') }}</span></div>
                    <div class="flex items-center justify-between gap-4 text-sm"><span class="text-slate-500">Prix par siège</span><span class="font-bold text-slate-800">{{ number_format((float) $trip->price, 0, ',', ' ') }} Ar</span></div>
                    <div class="border-t border-dashed border-slate-200 pt-4"><p class="text-sm font-semibold text-slate-500">Total à confirmer (simulation)</p><p class="mt-1 text-3xl font-black text-[#0B6E4F]">{{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar</p></div>
                    <p class="rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-500"><i class="fa-solid fa-clock mr-1 text-[#F47C20]" aria-hidden="true"></i>La sélection est temporairement bloquée. Finalisez avant l'expiration du délai de réservation.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection