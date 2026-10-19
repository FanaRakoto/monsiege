@extends('layouts.app')

@section('title', 'Vérification du billet - MonSiège')

@section('content')
<section class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto max-w-2xl">

        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#F47C20]">
                Contrôle du billet
            </p>

            <h1 class="mt-2 text-3xl font-black text-slate-800">
                Vérification du billet
            </h1>
        </div>

        @if (session('ticket_used'))
            <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 font-bold text-green-800">
                Billet contrôlé avec succès. Il est désormais marqué comme utilisé.
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">
                        Numéro du billet
                    </p>

                    <p class="mt-1 text-xl font-black text-slate-800">
                        {{ $ticket->ticket_number }}
                    </p>
                </div>

                @if ($ticket->status === 'valid')
                    <span class="rounded-full bg-green-100 px-3 py-2 text-sm font-bold text-green-800">
                        Valide
                    </span>
                @elseif ($ticket->status === 'used')
                    <span class="rounded-full bg-slate-200 px-3 py-2 text-sm font-bold text-slate-700">
                        Déjà utilisé
                    </span>
                @else
                    <span class="rounded-full bg-red-100 px-3 py-2 text-sm font-bold text-red-800">
                        Annulé
                    </span>
                @endif
            </div>

            <div class="mt-6 space-y-4 border-t border-slate-100 pt-6">
                <div>
                    <p class="text-sm text-slate-500">Voyage</p>

                    <p class="mt-1 font-bold text-slate-800">
                        {{ $ticket->reservation->trip->departure_city }}
                        →
                        {{ $ticket->reservation->trip->arrival_city }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Date de départ</p>

                    <p class="mt-1 font-bold text-slate-800">
                        {{ $ticket->reservation->trip->departure_at->format('d/m/Y à H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Nombre de passagers</p>

                    <p class="mt-1 font-bold text-slate-800">
                        {{ $ticket->reservation->passenger_count }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Montant de la réservation</p>

                    <p class="mt-1 font-bold text-slate-800">
                        {{ number_format($ticket->reservation->total_amount, 0, ',', ' ') }} Ar
                    </p>
                </div>

                @if ($ticket->checked_at)
                    <div>
                        <p class="text-sm text-slate-500">Date de contrôle</p>

                        <p class="mt-1 font-bold text-slate-800">
                            {{ $ticket->checked_at->format('d/m/Y à H:i:s') }}
                        </p>
                    </div>
                @endif
            </div>

            @if ($ticket->status === 'valid')
                <form
                    method="POST"
                    action="{{ route('tickets.check', ['token' => $ticket->qr_token]) }}"
                    class="mt-8"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#0B6E4F] px-5 py-4 font-bold text-white transition hover:bg-[#08563D]"
                    >
                        Confirmer le contrôle et marquer le billet comme utilisé
                    </button>
                </form>
            @else
                <p class="mt-8 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                    Aucune nouvelle validation ne peut être effectuée pour ce billet.
                </p>
            @endif
        </div>
    </div>
</section>
@endsection