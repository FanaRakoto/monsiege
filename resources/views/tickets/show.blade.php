@extends('layouts.app')

@section('title', 'Mon billet électronique - MonSiège')

@section('content')
<section class="min-h-[75vh] bg-slate-50 px-4 py-10 sm:py-14">
    <div class="mx-auto max-w-2xl">
        <div class="mb-8 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-solid fa-ticket text-2xl" aria-hidden="true"></i></span>
            <p class="mt-4 text-xs font-extrabold uppercase tracking-[.2em] text-[#F47C20]">MonSiège · Billet électronique</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Votre billet est prêt</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Ce billet unique récapitule toute la réservation du groupe. Présentez le QR Code au personnel autorisé et conservez le PDF.</p>
        </div>

        @if (session('payment_success'))
            <div role="status" class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800"><i class="fa-solid fa-circle-check mr-2" aria-hidden="true"></i>Réservation confirmée. Votre billet électronique a été généré.</div>
        @endif

        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-900/[.06]">
            <div class="bg-gradient-to-r from-[#073b2e] to-[#0B6E4F] px-6 py-6 text-center text-white sm:px-8">
                <p class="text-xs font-extrabold uppercase tracking-[.2em] text-emerald-100/80">QR Code d'embarquement</p>
                <p class="mt-2 text-2xl font-black tracking-tight">{{ $ticket->ticket_number }}</p>
            </div>
            <div class="flex flex-col items-center px-5 py-8 text-center sm:px-8">
                @if ($ticket->status === 'valid')
                    <div class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm sm:p-5">
                        <img src="{{ $qrCode }}" alt="QR Code de vérification du billet" class="mx-auto h-auto w-full max-w-[230px]" width="230" height="230">
                    </div>
                    <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-800"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Billet valide</span>
                    <p class="mt-3 max-w-md text-sm leading-6 text-slate-500">Gardez ce QR Code accessible au moment de l'embarquement. Le contrôle est réservé au personnel autorisé.</p>
                @elseif ($ticket->status === 'used')
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-slate-100 text-slate-500"><i class="fa-solid fa-check-double text-4xl" aria-hidden="true"></i></div>
                    <p class="mt-4 font-extrabold text-slate-800">Billet déjà utilisé</p>
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-rose-50 text-rose-600"><i class="fa-solid fa-ban text-4xl" aria-hidden="true"></i></div>
                    <p class="mt-4 font-extrabold text-slate-800">Billet annulé</p>
                @endif

                <a href="{{ route('tickets.download', ['ticket' => $ticket->id]) }}" class="mt-7 inline-flex w-full max-w-md items-center justify-center gap-3 rounded-xl bg-[#0B6E4F] px-6 py-4 text-sm font-extrabold text-white shadow-lg shadow-emerald-900/10 transition hover:-translate-y-0.5 hover:bg-[#08563D] focus:outline-none focus:ring-4 focus:ring-emerald-200">
                    <i class="fa-solid fa-file-pdf text-lg" aria-hidden="true"></i> Télécharger mon billet en PDF <i class="fa-solid fa-download" aria-hidden="true"></i>
                </a>
                <p class="mt-3 text-xs leading-5 text-slate-400">Le PDF contient le trajet, le contact principal, le nombre de personnes et tous les sièges réservés.</p>

                <div class="mt-6 w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 text-left">
                    <div class="grid grid-cols-2 gap-3 border-b border-slate-100 bg-slate-50 p-4 text-sm">
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Voyage</p><p class="mt-1 font-extrabold text-slate-800">{{ $ticket->reservation->trip->departure_city }} → {{ $ticket->reservation->trip->arrival_city }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Départ</p><p class="mt-1 font-extrabold text-slate-800">{{ $ticket->reservation->trip->departure_at->format('d/m/Y à H:i') }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Passagers / sièges</p><p class="mt-1 font-extrabold text-slate-800">{{ $ticket->reservation->passenger_count }} · {{ $ticket->reservation->reservationSeats->map(fn ($row) => $row->seat?->seat_number)->filter()->join(', ') }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total réservé</p><p class="mt-1 text-lg font-black text-[#0B6E4F]">{{ number_format((float) $ticket->reservation->total_amount, 0, ',', ' ') }} Ar</p></div>
                        @php($contact = $ticket->reservation->passengers->firstWhere('is_primary', true) ?? $ticket->reservation->passengers->first())
                        <div class="col-span-2 border-t border-slate-200 pt-3"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Contact principal</p><p class="mt-1 font-extrabold text-slate-800">{{ $contact ? trim($contact->first_name . ' ' . $contact->last_name) : 'Non renseigné' }} · {{ $contact?->phone ?? 'Téléphone non renseigné' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5 text-center"><a href="{{ route('home') }}" class="text-sm font-bold text-[#0B6E4F] transition hover:text-[#F47C20]"><i class="fa-solid fa-arrow-left mr-2" aria-hidden="true"></i>Retour au site MonSiège</a></div>
    </div>
</section>
@endsection