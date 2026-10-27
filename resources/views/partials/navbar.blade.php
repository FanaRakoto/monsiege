@php
    $historyUrl = auth()->check()
        && (auth()->user()->role ?? null) === 'client'
        && \Illuminate\Support\Facades\Route::has('client.dashboard')
            ? route('client.dashboard')
            : route('history.notice');
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 font-sans shadow-sm shadow-slate-900/[0.03] backdrop-blur-xl">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8" aria-label="Navigation principale">
        <a href="{{ route('home') }}" class="group inline-flex min-w-0 items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] focus-visible:ring-offset-2">
            <img src="{{ asset('images/monsiege-icone-anime.png') }}" alt="" class="h-11 w-11 shrink-0 rounded-xl object-cover ring-1 ring-slate-200 transition group-hover:scale-[1.03]" width="44" height="44">
            <span class="min-w-0"><span class="block font-sans text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Mon<span class="text-[#0B6E4F]">Siège</span></span><span class="hidden text-[10px] font-semibold uppercase tracking-[.15em] text-slate-500 sm:block">Voyages à Madagascar</span></span>
        </a>

        <div class="hidden items-center gap-1 md:flex">
            <a href="{{ route('home') }}" @class(['rounded-xl px-4 py-2.5 text-sm font-bold transition', request()->routeIs('home') ? 'bg-emerald-50 text-[#0B6E4F]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'])>Accueil</a>
            <a href="{{ route('trips.index') }}" @class(['rounded-xl px-4 py-2.5 text-sm font-bold transition', request()->routeIs('trips.*') ? 'bg-emerald-50 text-[#0B6E4F]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'])>Voyages</a>
        </div>

        <div class="hidden items-center gap-2 md:flex">
            <a href="{{ $historyUrl }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#0B6E4F]/20 bg-white px-4 py-2 text-sm font-bold text-[#0B6E4F] transition hover:border-[#0B6E4F]/40 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F]">Historique</a>
            @guest
                <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0B6E4F] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#08583f] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] focus-visible:ring-offset-2">Connexion</a>
            @else
                @if(\Illuminate\Support\Facades\Route::has('logout'))
                    <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500">Déconnexion</button></form>
                @endif
            @endguest
        </div>

        <details class="relative md:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] [&::-webkit-details-marker]:hidden" aria-label="Ouvrir le menu de navigation">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </summary>
            <div class="absolute right-0 top-14 z-50 w-64 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl shadow-slate-900/10">
                <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Accueil</a>
                <a href="{{ route('trips.index') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Tous les voyages</a>
                <a href="{{ $historyUrl }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-[#0B6E4F] hover:bg-emerald-50">Historique</a>
                @guest
                    <a href="{{ route('login') }}" class="mt-1 block rounded-xl bg-[#0B6E4F] px-4 py-3 text-center text-sm font-bold text-white hover:bg-[#08583f]">Connexion</a>
                @else
                    @if(\Illuminate\Support\Facades\Route::has('logout'))
                        <form action="{{ route('logout') }}" method="POST" class="mt-1">@csrf<button type="submit" class="block w-full rounded-xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-200">Déconnexion</button></form>
                    @endif
                @endguest
            </div>
        </details>
    </nav>
</header>