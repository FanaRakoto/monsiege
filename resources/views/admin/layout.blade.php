@extends('layouts.app')

@section('content')
@php
    $adminPageTitle = match (true) {
        request()->routeIs('admin.trips.create') => 'Créer un voyage',
        request()->routeIs('admin.trips.edit') => 'Modifier un voyage',
        request()->routeIs('admin.trips') => 'Voyages',
        request()->routeIs('admin.vehicles.create') => 'Ajouter un véhicule',
        request()->routeIs('admin.vehicles.edit') => 'Modifier un véhicule',
        request()->routeIs('admin.vehicles') => 'Véhicules',
        request()->routeIs('admin.users') => 'Utilisateurs',
        request()->routeIs('admin.reservations') => 'Réservations',
        default => 'Vue générale',
    };
@endphp

<style>
    .admin-shell { --admin-green: #0b6e4f; --admin-deep: #073b2e; --admin-orange: #f47c20; }
    .admin-sidebar-link { position: relative; display:flex; align-items:center; gap:.8rem; border-radius: .95rem; padding:.78rem .9rem; color:rgba(255,255,255,.73); font-size:.875rem; font-weight:650; transition:background .22s ease,color .22s ease,transform .22s ease,box-shadow .22s ease; }
    .admin-sidebar-link:hover { background:rgba(255,255,255,.09); color:white; transform:translateX(3px); }
    .admin-sidebar-link.active { background:linear-gradient(105deg,rgba(11,110,79,.95),rgba(11,110,79,.55)); color:#fff; box-shadow:inset 3px 0 #f47c20,0 8px 20px rgba(0,0,0,.10); }
    .admin-sidebar-link.active::after { content:''; position:absolute; right:.8rem; width:6px; height:6px; border-radius:99px; background:#f47c20; box-shadow:0 0 0 4px rgba(244,124,32,.13); }
    .admin-icon { display:inline-flex; align-items:center; justify-content:center; width:2.15rem; height:2.15rem; flex:none; border-radius:.75rem; background:rgba(255,255,255,.085); }
    .admin-sidebar-link.active .admin-icon { background:rgba(255,255,255,.14); }
    .admin-content-card { border:1px solid #e7eeeb; border-radius:1.25rem; background:#fff; box-shadow:0 8px 30px rgba(15,45,35,.035); }
    .admin-kpi { position:relative; overflow:hidden; transition:transform .24s ease,box-shadow .24s ease,border-color .24s ease; }
    .admin-kpi:hover { transform:translateY(-4px); box-shadow:0 18px 35px rgba(10,55,40,.09); border-color:#cdded6; }
    .admin-kpi::after { content:''; position:absolute; right:-24px; top:-30px; width:90px; height:90px; border-radius:50%; background:var(--kpi-glow,rgba(11,110,79,.06)); }
    .admin-btn { transition:transform .16s ease,box-shadow .2s ease,background .2s ease; }
    .admin-btn:hover { box-shadow:0 9px 20px rgba(11,110,79,.14); transform:translateY(-1px); }
    .admin-btn:active { transform:scale(.97); }
    .admin-donut { transition:transform .25s ease; animation:admin-donut-in .85s cubic-bezier(.2,.8,.2,1) both; }
    .admin-donut:hover { transform:scale(1.035) rotate(3deg); }
    .admin-reveal { opacity:1; transform:translateY(0); }
    .admin-reveal.admin-reveal-ready:not(.is-visible) { opacity:0; transform:translateY(16px); }
    .admin-reveal.is-visible { opacity:1; transform:translateY(0); transition:opacity .55s ease,transform .55s cubic-bezier(.2,.75,.2,1); }
    .admin-page-enter { animation:admin-page-enter .55s cubic-bezier(.2,.75,.2,1) both; }
    .admin-pulse-dot { animation:admin-pulse 2.2s ease-in-out infinite; }
    @keyframes admin-page-enter { from {opacity:0;transform:translateY(10px)} to {opacity:1;transform:translateY(0)} }
    @keyframes admin-donut-in { from {opacity:0;transform:scale(.82) rotate(-22deg)} to {opacity:1;transform:scale(1) rotate(0)} }
    @keyframes admin-pulse { 0%,100% { box-shadow:0 0 0 0 rgba(52,211,153,.25) } 50% { box-shadow:0 0 0 6px rgba(52,211,153,0) } }
    @media (prefers-reduced-motion: reduce) { *,*::before,*::after { animation-duration:.01ms !important; animation-iteration-count:1 !important; scroll-behavior:auto !important; transition-duration:.01ms !important; } }
</style>

<div class="admin-shell min-h-[calc(100vh-4rem)] bg-[#f5f8f7] font-sans text-slate-900">
    <button id="adminSidebarOverlay" class="fixed inset-0 z-40 hidden bg-slate-950/50 backdrop-blur-[2px] lg:hidden" type="button" aria-label="Fermer le menu"></button>

    <div class="mx-auto flex w-full max-w-[1700px] gap-0 lg:gap-6 lg:px-5 xl:px-7">
        <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-50 flex w-[282px] -translate-x-full flex-col overflow-y-auto bg-[#082f26] px-4 py-5 text-white shadow-2xl shadow-emerald-950/20 transition-transform duration-300 ease-out lg:sticky lg:top-5 lg:my-5 lg:self-start lg:h-[calc(100vh-2.5rem)] lg:translate-x-0 lg:rounded-[1.5rem] lg:shadow-xl">
            <div class="flex items-center justify-between gap-3 px-2 pb-6">
                <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-lg shadow-black/10 ring-1 ring-white/10">
                        <img src="{{ asset('images/monsiege-icone-anime.png') }}" alt="MonSiège" class="h-full w-full object-cover" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="hidden h-full w-full items-center justify-center bg-[#0B6E4F] text-xl font-black">M</span>
                    </span>
                    <span class="min-w-0"><span class="block text-lg font-black tracking-tight">Mon<span class="text-orange-400">Siège</span></span><span class="mt-0.5 block text-[10px] font-bold uppercase tracking-[.18em] text-emerald-100/55">Control center</span></span>
                </a>
                <button id="adminSidebarClose" class="rounded-xl p-2 text-white/65 transition hover:bg-white/10 hover:text-white lg:hidden" type="button" aria-label="Fermer le menu"><i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i></button>
            </div>

            <div class="mx-1 mb-4 rounded-2xl border border-white/10 bg-white/[.055] px-3.5 py-3">
                <div class="flex items-center gap-2 text-[10px] font-extrabold uppercase tracking-[.18em] text-emerald-100/55"><span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span> Espace sécurisé</div>
                <p class="mt-2 text-sm font-bold text-white">Bonjour, {{ str(auth()->user()->name ?? 'Administrateur')->before(' ') }}</p>
                <p class="mt-0.5 truncate text-xs text-white/45">{{ auth()->user()->email ?? 'Compte administrateur' }}</p>
            </div>

            <p class="px-3 pb-2 pt-3 text-[10px] font-extrabold uppercase tracking-[.2em] text-white/35">Menu principal</p>
            <nav class="space-y-1" aria-label="Menu de gestion">
                <a href="{{ route('admin.dashboard') }}" @class(['admin-sidebar-link', 'active' => request()->routeIs('admin.dashboard')])>
                    <span class="admin-icon"><i class="fa-solid fa-gauge-high" aria-hidden="true"></i></span> Vue générale
                </a>
                <a href="{{ route('admin.trips') }}" @class(['admin-sidebar-link', 'active' => request()->routeIs('admin.trips*')])>
                    <span class="admin-icon"><i class="fa-solid fa-route" aria-hidden="true"></i></span> Voyages
                </a>
                <a href="{{ route('admin.reservations') }}" @class(['admin-sidebar-link', 'active' => request()->routeIs('admin.reservations')])>
                    <span class="admin-icon"><i class="fa-solid fa-ticket" aria-hidden="true"></i></span> Réservations
                </a>
                <a href="{{ route('admin.vehicles') }}" @class(['admin-sidebar-link', 'active' => request()->routeIs('admin.vehicles*')])>
                    <span class="admin-icon"><i class="fa-solid fa-bus" aria-hidden="true"></i></span> Véhicules
                </a>
                <a href="{{ route('admin.users') }}" @class(['admin-sidebar-link', 'active' => request()->routeIs('admin.users*')])>
                    <span class="admin-icon"><i class="fa-solid fa-users" aria-hidden="true"></i></span> Utilisateurs
                </a>
            </nav>

            <div class="mt-auto pt-7">
                <a href="{{ route('home') }}" class="admin-sidebar-link mb-2 border border-white/10 bg-white/[.035]"><span class="admin-icon"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span> Voir le site public</a>
                <div class="mb-3 flex items-center gap-2 rounded-xl bg-emerald-950/70 px-3 py-2.5 text-xs text-emerald-100/75"><span class="admin-pulse-dot h-2 w-2 rounded-full bg-emerald-300"></span> Système en ligne</div>
                <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="admin-btn flex w-full items-center justify-center gap-2 rounded-xl border border-red-300/15 bg-red-400/10 px-4 py-3 text-sm font-bold text-red-100 hover:bg-red-400/20" type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Déconnexion</button></form>
            </div>
        </aside>

        <main class="min-w-0 flex-1 px-4 py-5 sm:px-6 lg:px-0 lg:py-7">
            <div class="admin-page-enter mx-auto max-w-[1350px]">
                <header class="mb-7 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <button id="adminSidebarOpen" class="mt-1 flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-emerald-200 hover:text-[#0B6E4F] lg:hidden" type="button" aria-label="Ouvrir le menu"><i class="fa-solid fa-bars text-lg" aria-hidden="true"></i></button>
                        <div><p class="text-[11px] font-extrabold uppercase tracking-[.22em] text-[#0B6E4F]">MonSiège <span class="mx-1 text-slate-300">/</span> Administration</p><h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">{{ $adminPageTitle }}</h1><p class="mt-1 text-sm text-slate-500">Pilotez l’activité de transport depuis un seul espace.</p></div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden text-right sm:block"><p class="text-sm font-bold text-slate-800">{{ auth()->user()->name ?? 'Administrateur' }}</p><p class="text-xs text-slate-500">Administrateur</p></div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-[#0B6E4F] to-[#073b2e] text-sm font-black text-white shadow-lg shadow-emerald-900/15 ring-4 ring-white">{{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                    </div>
                </header>

                @if (session('status'))
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                @endif

                @yield('admin-content')
                <footer class="mt-9 flex flex-wrap items-center justify-between gap-2 border-t border-slate-200/80 py-5 text-xs text-slate-400"><span>© {{ date('Y') }} MonSiège · Administration</span><span>Accès protégé · Rôle administrateur</span></footer>
            </div>
        </main>
    </div>
</div>

<script>
(() => {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminSidebarOverlay');
    const openButton = document.getElementById('adminSidebarOpen');
    const closeButton = document.getElementById('adminSidebarClose');
    const openMenu = () => { sidebar?.classList.remove('-translate-x-full'); overlay?.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
    const closeMenu = () => { sidebar?.classList.add('-translate-x-full'); overlay?.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };
    openButton?.addEventListener('click', openMenu);
    closeButton?.addEventListener('click', closeMenu);
    overlay?.addEventListener('click', closeMenu);
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(); });

    const revealItems = document.querySelectorAll('.admin-reveal');
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        revealItems.forEach((item) => item.classList.add('admin-reveal-ready'));
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -24px 0px' });
        revealItems.forEach((item) => observer.observe(item));
    }
})();
</script>
@endsection