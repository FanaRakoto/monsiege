
@extends('layouts.app')

@section('title', 'Créer un compte | MonSiège')

@section('content')
<section class="min-h-[75vh] bg-slate-50 px-4 py-12 sm:py-16">
    <div class="mx-auto max-w-xl">

        <div class="mb-8 text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-[#0B6E4F] text-3xl text-white shadow-lg">
                <span>✦</span>
            </div>

            <h1 class="mt-5 text-3xl font-black text-slate-900">
                Rejoignez MonSiège
            </h1>

            <p class="mt-2 text-slate-500">
                Créez votre compte et préparez vos prochains voyages.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-9">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-bold">Veuillez vérifier les informations saisies.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-slate-700">
                        Nom complet
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        required
                        autofocus
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="Ex. : Jean Rakoto">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700">
                        Adresse e-mail
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="vous@exemple.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-bold text-slate-700">
                        Téléphone
                    </label>

                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="Ex. : 034 00 000 00">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-700">
                        Mot de passe
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="Créez un mot de passe">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-700">
                        Confirmer le mot de passe
                    </label>

                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="Répétez votre mot de passe">
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#0B6E4F] px-5 py-4 font-extrabold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0B6E4F]">
                    Créer mon compte
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Vous avez déjà un compte ?
                <a href="{{ route('login') }}" class="font-bold text-[#0B6E4F] hover:text-[#F47C20]">
                    Se connecter
                </a>
            </p>
        </div>
    </div>
</section>
@endsection
