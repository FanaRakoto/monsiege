@extends('layouts.app')

@section('title', 'Mon billet - MonSiège')

@section('content')
<section class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto max-w-3xl">

        <div class="mb-8 text-center">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F47C20]">
                MonSiège
            </p>

            <h1 class="mt-2 text-3xl font-black text-slate-800">
                Votre billet électronique
            </h1>

            <p class="mt-2 text-slate-500">
                Présentez ce billet à l’agence ou au chauffeur.
            </p>
        </div>

        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div class="bg-[#0B6E4F] px-6 py-6 text-white">
                <p class="text-sm text-white/80">
                    Numéro du billet
                </p>

                <p class="mt-1 text-2xl font-black">
                    {{ $ticket->ticket_number }}
                </p>

                <p class="mt-3 text-sm">
                    Statut :
                    <span class="font-bold">
                        {{ $ticket->status === 'valid' ? 'Valide' : ($ticket->status === 'used' ? 'Déjà utilisé' : 'Annulé') }}
                    </span>
                </p>
            </div>

            <div class="grid gap-8 p-6 md:grid-cols-[1fr_220px]">

                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Itinéraire
                        </p>

                        <p class="mt-2 text-xl font-black text-slate-800">
                            {{ $ticket->reservation->trip->departure_city }}
                            <span class="text-[#F47C20]">→</span>
                            {{ $ticket->reservation->trip->arrival_city }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Départ
                        </p>

                        <p class="mt-1 font-bold text-slate-700">
                            {{ $ticket->reservation->trip->departure_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Agence
                        </p>

                        <p class="mt-1 font-bold text-slate-700">
                            {{ $ticket->reservation->trip->agency->name ?? 'Agence MonSiège' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Chauffeur
                        </p>

                        <p class="mt-1 font-bold text-slate-700">
                            {{ $ticket->reservation->trip->driver?->user?->name ?? 'Non affecté' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Passagers
                        </p>

                        <div class="mt-2 space-y-2">
                            @foreach ($ticket->reservation->passengers as $passenger)
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="font-bold text-slate-700">
                                    {{ $passenger->first_name }}
                                    {{ $passenger->last_name }}
                                </p>

                                <p class="text-sm text-slate-500">
                                    Siège :
                                    {{ $ticket->reservation->reservationSeats[$loop->index]->seat?->seat_number ?? '—' }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Montant payé
                        </p>

                        <p class="mt-1 text-2xl font-black text-[#0B6E4F]">
                            {{ number_format($ticket->reservation->total_amount, 0, ',', ' ') }} Ar
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center rounded-2xl bg-slate-50 p-4 text-center">
                    @if ($ticket->status === 'valid')
                    <img
                        src="{{ $qrCode }}"
                        alt="QR Code de vérification du billet"
                        class="h-auto w-full max-w-[190px]">

                    <p class="mt-3 text-sm font-bold text-slate-700">
                        QR Code de vérification
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        À présenter au personnel autorisé.
                    </p>
                    @else
                    <p class="font-bold text-slate-600">
                        Ce billet n’est plus valide pour embarquer.
                    </p>
                    @endif
                </div>

            </div>
        </div>

        <p class="mt-6 text-center text-sm text-slate-500">
            Conservez votre numéro de billet et présentez votre QR Code lors du contrôle.
        </p>

    </div>
</section>
@endsection