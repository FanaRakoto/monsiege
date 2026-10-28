@extends('layouts.app')

@section('title', 'Connexion administrateur — MonSiège')

@section('content')
<section class="flex min-h-[calc(100vh-8rem)] items-center justify-center bg-slate-50 px-4 py-12 font-sans">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5 md:grid-cols-2">
        <div class="relative hidden min-h-[520px] flex-col justify-between overflow-hidden bg-[#063b2c] p-10 text-white md:flex">
            <div class="pointer-events-none absolute inset-0 opacity-15" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 22px 22px"></div>
            <div class="relative"><p class="text-xs font-extrabold uppercase tracking-[.2em] text-orange-300">MonSiège · Gestion</p><h1 class="mt-5 text-4xl font-extrabold leading-tight">Pilotez la plateforme.</h1><p class="mt-4 max-w-sm leading-7 text-white/75">Consultez les données de transport et suivez les opérations depuis un espace réservé aux administrateurs.</p></div>
            <div class="relative rounded-2xl border border-white/15 bg-white/10 p-5 backdrop-blur"><p class="text-sm font-bold">Accès contrôlé</p><p class="mt-1 text-sm text-white/70">Cette connexion n'accepte que les comptes avec le rôle admin.</p></div>
        </div>
        <div class="p-6 sm:p-10 lg:p-12">
            <a href="{{ route('home') }}" class="text-sm font-bold text-[#0B6E4F] hover:underline">← Retour au site</a>
            <p class="mt-8 text-xs font-extrabold uppercase tracking-[.18em] text-[#F47C20]">Espace sécurisé</p>
            <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Connexion admin</h2>
            <p class="mt-3 text-sm leading-6 text-slate-500">Entrez les identifiants de votre compte administrateur.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.store') }}" method="POST" class="mt-7 space-y-5">
                @csrf
                <div><label for="email" class="mb-2 block text-sm font-bold text-slate-700">Adresse e-mail</label><input id="email" name="email" type="email" autocomplete="username" required maxlength="255" value="{{ old('email') }}" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10"></div>
                <div><label for="password" class="mb-2 block text-sm font-bold text-slate-700">Mot de passe</label><input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10"></div>
                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-[#0B6E4F] focus:ring-[#0B6E4F]"> Se souvenir de moi</label>
                <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-[#0B6E4F] px-5 text-sm font-extrabold text-white shadow-sm transition hover:bg-[#08583f] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-emerald-900/20">Se connecter à l'administration →</button>
            </form>
            <p class="mt-6 text-xs leading-5 text-slate-400">L'inscription publique ne permet pas de créer un compte administrateur.</p>
        </div>
    </div>
</section>
@endsection