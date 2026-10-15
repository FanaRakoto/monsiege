
@extends('layouts.app')

@section('title', 'Connexion | MonSiège')

@section('content')
<section class="flex min-h-[75vh] items-center bg-slate-50 px-4 py-12 sm:py-16">
    <div class="mx-auto w-full max-w-md">

        <div class="mb-8 text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-[#0B6E4F] text-3xl text-white shadow-lg">
                <span>↗</span>
            </div>

            <h1 class="mt-5 text-3xl font-black text-slate-900">
                Bon retour !
            </h1>

            <p class="mt-2 text-slate-500">
                Connectez-vous pour retrouver vos voyages.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">

            @if (session('status'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-bold text-slate-700">
                        Adresse e-mail
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        autofocus
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="vous@exemple.com">
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label for="password" class="text-sm font-bold text-slate-700">
                            Mot de passe
                        </label>

                        @if (\Illuminate\Support\Facades\Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#0B6E4F] hover:text-[#F47C20]">
                                Oublié ?
                            </a>
                        @endif
                    </div>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-100"
                        placeholder="Votre mot de passe">
                </div>

                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="size-4 rounded border-slate-300 text-[#0B6E4F] focus:ring-[#0B6E4F]">
                    Se souvenir de moi
                </label>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-[#0B6E4F] px-5 py-4 font-extrabold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#0B6E4F]">
                    Se connecter
                </button>
            </form>

            <div class="my-6 flex items-center gap-4">
                <div class="h-px flex-1 bg-slate-200"></div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nouveau sur MonSiège ?</span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            <a
                href="{{ route('register') }}"
                class="block w-full rounded-xl border border-[#0B6E4F] px-5 py-3.5 text-center font-bold text-[#0B6E4F] transition hover:bg-emerald-50">
                Créer un compte
            </a>
        </div>
    </div>
</section>
@endsection
