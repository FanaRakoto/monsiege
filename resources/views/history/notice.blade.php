@extends('layouts.app')

@section('title', 'Historique de vos voyages — MonSiège')
@section('meta_description', 'Créez un compte MonSiège pour retrouver dans votre espace les réservations associées à votre compte.')

@section('content')
<div class="min-h-[70vh] bg-slate-50 px-4 py-12 font-sans antialiased sm:px-6 sm:py-16 lg:px-8">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#0B6E4F] hover:underline"><span aria-hidden="true">←</span> Retour à l'accueil</a>
        <section class="mt-6 overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
            <div class="bg-gradient-to-br from-[#063b2c] via-[#0B6E4F] to-[#0b7655] px-6 py-9 text-white sm:px-10 sm:py-12">
                <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-2xl" aria-hidden="true">▤</span>
                <p class="mt-5 text-xs font-extrabold uppercase tracking-[.18em] text-[#ffbd8d]">Votre espace de voyage</p>
                <h1 class="mt-2 font-sans text-3xl font-extrabold tracking-tight sm:text-4xl">Gardez votre historique avec vous</h1>
                <p class="mt-4 max-w-xl leading-7 text-white/85">Créez un compte MonSiège pour retrouver plus facilement les réservations associées à votre compte et consulter votre espace client.</p>
            </div>
            <div class="px-6 py-8 sm:px-10 sm:py-10">
                @auth
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm leading-6 text-emerald-950">
                        Vous êtes connecté. Les réservations effectuées avec ce compte peuvent apparaître dans votre espace client. Les réservations faites en visiteur ne sont pas automatiquement rattachées à ce compte.
                    </div>
                    @if((auth()->user()->role ?? null) === 'client' && \Illuminate\Support\Facades\Route::has('client.dashboard'))
                        <a href="{{ route('client.dashboard') }}" class="mt-5 inline-flex min-h-12 items-center justify-center rounded-xl bg-[#0B6E4F] px-5 py-3 text-sm font-extrabold text-white transition hover:bg-[#08583f]">Ouvrir mon espace client <span class="ml-2" aria-hidden="true">→</span></a>
                    @endif
                @else
                    <h2 class="font-sans text-xl font-extrabold text-slate-900">Vous réservez sans compte ? C'est possible.</h2>
                    <p class="mt-3 text-sm leading-7 text-slate-600">Vous pouvez continuer à réserver en tant que visiteur et obtenir votre billet avec QR Code. Pour conserver un historique dans votre espace, créez un compte puis connectez-vous avant votre prochaine réservation.</p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-[#F47C20] px-5 py-3 text-sm font-extrabold text-white transition hover:bg-[#df6b13] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/25">Créer mon compte</a>
                        <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">J'ai déjà un compte</a>
                    </div>
                    <p class="mt-5 rounded-xl bg-slate-50 p-4 text-xs leading-6 text-slate-500">À savoir : un compte créé après une réservation visiteur ne récupère pas automatiquement cette réservation. Conservez donc le lien de votre billet ou les informations communiquées à la fin de la réservation.</p>
                @endauth
                <a href="{{ route('trips.index') }}" class="mt-6 inline-flex min-h-11 items-center justify-center gap-2 text-sm font-bold text-[#0B6E4F] hover:underline">Continuer à consulter les voyages <span aria-hidden="true">→</span></a>
            </div>
        </section>
    </div>
</div>
@endsection