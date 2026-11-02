@extends('layouts.app')

@section('title', 'Confirmation de réservation - MonSiège')

@section('content')
<section class="min-h-[75vh] bg-slate-50 px-4 py-8 sm:py-12">
    <div class="mx-auto max-w-4xl">
        <div class="mb-7">
            <p class="text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">Étape 3 sur 3 · Confirmation</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Finalisez votre réservation</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Vérifiez les détails du trajet avant de confirmer et de générer votre billet électronique.</p>
        </div>

        @if (session('payment_failed'))
            <div role="alert" class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900"><i class="fa-solid fa-circle-exclamation mr-2" aria-hidden="true"></i>La confirmation n'a pas abouti. Aucun billet n'a été émis. Vérifiez votre réservation et réessayez avant l'expiration du blocage.</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif

        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-solid fa-shield-halved text-xl" aria-hidden="true"></i></span>
                    <div><h2 class="text-xl font-black text-slate-900">Confirmation sécurisée</h2><p class="mt-1 text-sm leading-6 text-slate-600">La réservation et les sièges seront revérifiés côté serveur avant la génération du billet. Aucune carte bancaire ni donnée Mobile Money ne vous sera demandée ici.</p></div>
                </div>

                <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                    <p class="text-sm font-extrabold text-emerald-900"><i class="fa-solid fa-lock mr-2" aria-hidden="true"></i>Vos données bancaires ne sont pas collectées</p>
                    <p class="mt-1 text-sm leading-6 text-emerald-800">Cette version de démonstration ne débite aucun compte. Le bouton confirme localement la réservation pour vous permettre de présenter le parcours complet jusqu'au billet QR.</p>
                </div>

                <form method="POST" action="{{ route('reservations.payment.simulate', ['trip' => $trip]) }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="result" value="success">
                    <button type="submit" class="flex w-full items-center justify-between gap-4 rounded-2xl bg-[#0B6E4F] px-5 py-4 text-left font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-[#08563D] focus:outline-none focus:ring-4 focus:ring-emerald-200">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-circle-check text-xl" aria-hidden="true"></i><span><span class="block">Confirmer et obtenir mon billet</span><span class="mt-1 block text-xs font-medium text-emerald-50/80">Valider la réservation et afficher le QR Code.</span></span></span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
                <p class="mt-4 text-xs leading-5 text-slate-400">Confirmation locale de démonstration : aucun paiement réel n'est effectué.</p>
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
                    <div class="border-t border-dashed border-slate-200 pt-4"><p class="text-sm font-semibold text-slate-500">Montant total</p><p class="mt-1 text-3xl font-black text-[#0B6E4F]">{{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar</p></div>
                    <p class="rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-500"><i class="fa-solid fa-clock mr-1 text-[#F47C20]" aria-hidden="true"></i>Les sièges restent bloqués temporairement. Confirmez avant l'expiration du délai.</p>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection