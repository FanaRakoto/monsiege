@php
    $currentUser = auth()->user();
    $isAdmin = auth()->check()
        && ($currentUser->role ?? null) === 'admin'
        && \Illuminate\Support\Facades\Route::has('admin.dashboard');
    $historyUrl = auth()->check()
        && ($currentUser->role ?? null) === 'client'
        && \Illuminate\Support\Facades\Route::has('client.dashboard')
            ? route('client.dashboard')
            : (\Illuminate\Support\Facades\Route::has('history.notice') ? route('history.notice') : route('home'));
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 font-sans shadow-sm shadow-slate-900/[0.03] backdrop-blur-xl">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8" aria-label="Navigation principale">
        <a href="{{ route('home') }}" class="group inline-flex min-w-0 items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] focus-visible:ring-offset-2">
            <img src="{{ asset('images/monsiege-icone-anime.png') }}" alt="" class="h-11 w-11 shrink-0 rounded-xl object-cover ring-1 ring-slate-200 transition duration-300 group-hover:rotate-[-3deg] group-hover:scale-105" width="44" height="44">
            <span class="min-w-0"><span class="block font-sans text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Mon<span class="text-[#0B6E4F]">Siège</span></span><span class="hidden text-[10px] font-semibold uppercase tracking-[.15em] text-slate-500 sm:block">Voyages à Madagascar</span></span>
        </a>

        <div class="hidden items-center gap-1 md:flex">
            <a href="{{ route('home') }}" @class(['rounded-xl px-4 py-2.5 text-sm font-bold transition duration-200', request()->routeIs('home') ? 'bg-emerald-50 text-[#0B6E4F]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'])>Accueil</a>
            @if (\Illuminate\Support\Facades\Route::has('trips.index'))
                <a href="{{ route('trips.index') }}" @class(['rounded-xl px-4 py-2.5 text-sm font-bold transition duration-200', request()->routeIs('trips.*') ? 'bg-emerald-50 text-[#0B6E4F]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'])>Voyages</a>
            @endif
        </div>

        <div class="hidden items-center gap-2 md:flex">
            @if ($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-4 py-2 text-sm font-extrabold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-[#08583f] hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] focus-visible:ring-offset-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="4" rx="1.5"/><rect x="14" y="10" width="7" height="11" rx="1.5"/><rect x="3" y="13" width="7" height="8" rx="1.5"/></svg>
                    Dashboard
                </a>
                <form action="{{ route('admin.logout') }}" method="POST">@csrf<button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-red-50 hover:text-red-700">Déconnexion</button></form>
            @else
                <a href="{{ $historyUrl }}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-[#0B6E4F]/20 bg-white px-4 py-2 text-sm font-bold text-[#0B6E4F] transition hover:border-[#0B6E4F]/40 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F]">Historique</a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0B6E4F] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#08583f] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] focus-visible:ring-offset-2">Connexion</a>
                @else
                    @if (\Illuminate\Support\Facades\Route::has('logout'))
                        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500">Déconnexion</button></form>
                    @endif
                @endguest
            @endif
        </div>

        <details class="relative md:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0B6E4F] [&::-webkit-details-marker]:hidden" aria-label="Ouvrir le menu de navigation">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </summary>
            <div class="absolute right-0 top-14 z-50 w-64 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl shadow-slate-900/10">
                <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Accueil</a>
                @if (\Illuminate\Support\Facades\Route::has('trips.index'))
                    <a href="{{ route('trips.index') }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Tous les voyages</a>
                @endif
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="mt-1 flex items-center gap-2 rounded-xl bg-[#0B6E4F] px-4 py-3 text-sm font-extrabold text-white transition hover:bg-[#08583f]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="4" rx="1.5"/><rect x="14" y="10" width="7" height="11" rx="1.5"/><rect x="3" y="13" width="7" height="8" rx="1.5"/></svg> Dashboard administrateur</a>
                    <form action="{{ route('admin.logout') }}" method="POST" class="mt-1">@csrf<button type="submit" class="block w-full rounded-xl bg-slate-100 px-4 py-3 text-left text-sm font-bold text-slate-700 transition hover:bg-red-50 hover:text-red-700">Déconnexion</button></form>
                @else
                    <a href="{{ $historyUrl }}" class="block rounded-xl px-4 py-3 text-sm font-bold text-[#0B6E4F] transition hover:bg-emerald-50">Historique</a>
                    @guest
                        <a href="{{ route('login') }}" class="mt-1 block rounded-xl bg-[#0B6E4F] px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-[#08583f]">Connexion</a>
                    @else
                        @if (\Illuminate\Support\Facades\Route::has('logout'))
                            <form action="{{ route('logout') }}" method="POST" class="mt-1">@csrf<button type="submit" class="block w-full rounded-xl bg-slate-100 px-4 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-slate-200">Déconnexion</button></form>
                        @endif
                    @endguest
                @endif
            </div>
        </details>
    </nav>
</header>