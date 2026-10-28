@extends('admin.layout')

@section('title', 'Administration — MonSiège')

@section('admin-content')
@php
    $donutColors = ['#0B6E4F', '#F47C20', '#3B82F6', '#8B5CF6', '#14B8A6', '#F43F5E', '#64748B'];
    $buildDonut = static function (array $items) use ($donutColors): array {
        $total = array_sum(array_map(static fn ($item) => (int) ($item['value'] ?? 0), $items));
        if ($total < 1) {
            return ['total' => 0, 'gradient' => 'conic-gradient(#e2e8f0 0deg 360deg)', 'items' => []];
        }
        $start = 0.0;
        $stops = [];
        $legend = [];
        foreach ($items as $index => $item) {
            $value = max(0, (int) ($item['value'] ?? 0));
            $end = $start + (($value / $total) * 360);
            $color = $donutColors[$index % count($donutColors)];
            $stops[] = $color . ' ' . round($start, 3) . 'deg ' . round($end, 3) . 'deg';
            $legend[] = ['label' => $item['label'] ?? 'Autre', 'value' => $value, 'color' => $color, 'percent' => (int) round(($value / $total) * 100)];
            $start = $end;
        }
        return ['total' => $total, 'gradient' => 'conic-gradient(' . implode(', ', $stops) . ')', 'items' => $legend];
    };
    $usersChart = $buildDonut($usersByRole ?? []);
    $reservationsChart = $buildDonut($reservationsByStatus ?? []);
    $iconPaths = [
        'Utilisateurs' => '<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'Agences' => '<path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M10 21v-4h4v4"/>',
        'Véhicules' => '<path d="M5 17h14l1-6-2-5H6l-2 5 1 6Z"/><path d="M4 11h16M7 17v2m10-2v2M7 14h.01M17 14h.01"/>',
        'Voyages à venir' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'Réservations' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h4"/>',
        'Paiements' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/>',
        'Billets émis' => '<path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4a3 3 0 0 0 0 6v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4a3 3 0 0 0 0-6V5Z"/><path d="M12 7v2m0 3v2m0 3v1"/>',
    ];
    $kpiTones = [
        'Utilisateurs' => ['bg' => 'bg-emerald-50', 'text' => 'text-[#0B6E4F]', 'glow' => 'rgba(11,110,79,.09)'],
        'Agences' => ['bg' => 'bg-orange-50', 'text' => 'text-[#d86510]', 'glow' => 'rgba(244,124,32,.10)'],
        'Véhicules' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'glow' => 'rgba(59,130,246,.09)'],
        'Voyages à venir' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'glow' => 'rgba(139,92,246,.09)'],
        'Réservations' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'glow' => 'rgba(244,63,94,.08)'],
        'Paiements' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'glow' => 'rgba(6,182,212,.09)'],
        'Billets émis' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'glow' => 'rgba(245,158,11,.09)'],
    ];
@endphp

<section class="mb-7 overflow-hidden rounded-[1.6rem] bg-gradient-to-br from-[#083f30] via-[#0B6E4F] to-[#13845f] p-6 text-white shadow-xl shadow-emerald-950/10 sm:p-8 admin-reveal">
    <div class="relative z-10 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
        <div class="max-w-2xl">
            <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[.16em] text-emerald-50"><span class="admin-pulse-dot h-2 w-2 rounded-full bg-emerald-300"></span> Centre de pilotage</div>
            <h2 class="text-3xl font-black tracking-tight sm:text-4xl">Bonjour, {{ str(auth()->user()->name ?? 'Administrateur')->before(' ') }} <span class="inline-block origin-bottom-right animate-[admin-wave_1.2s_ease-in-out_1]">👋</span></h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-emerald-50/80 sm:text-base">Retrouvez les chiffres clés de MonSiège et surveillez les opérations de transport depuis votre espace sécurisé.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 backdrop-blur-sm"><p class="text-[10px] font-bold uppercase tracking-[.16em] text-emerald-100/70">Aujourd’hui</p><p class="mt-1 text-lg font-extrabold">{{ now()->format('d/m/Y') }}</p></div>
            <a href="{{ route('home') }}" class="admin-btn inline-flex items-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-extrabold text-[#0B6E4F]">Voir le site <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M8 7h9v9" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        </div>
    </div>
    <div class="pointer-events-none absolute"></div>
</section>

<div class="mb-7 flex flex-wrap items-end justify-between gap-3 admin-reveal">
    <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#0B6E4F]">Vue d’ensemble</p><h2 class="mt-1 text-xl font-black tracking-tight text-slate-900">Les chiffres de la plateforme</h2></div>
    <p class="text-xs text-slate-500">Données synchronisées depuis la base de données</p>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $index => $stat)
        @php $tone = $kpiTones[$stat['label']] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'glow' => 'rgba(100,116,139,.08)']; @endphp
        <article class="admin-kpi admin-content-card admin-reveal p-5" style="--kpi-glow: {{ $tone['glow'] }}; transition-delay: {{ min($index, 5) * 45 }}ms">
            <div class="relative z-10 flex items-start justify-between gap-3"><div><p class="text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p><p class="mt-3 text-3xl font-black tracking-tight text-slate-900">{{ number_format($stat['value'], 0, ',', ' ') }}</p><p class="mt-1.5 text-xs text-slate-500">{{ $stat['description'] }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $tone['bg'] }} {{ $tone['text'] }}"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $iconPaths[$stat['label']] ?? '<circle cx="12" cy="12" r="8"/>' !!}</svg></span></div>
            <div class="relative z-10 mt-4 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-[#0B6E4F] to-[#F47C20]" style="width: {{ $stat['value'] > 0 ? max(14, min(100, (int) $stat['value'])) : 3 }}%; transition:width 1s ease"></div></div>
        </article>
    @endforeach
</div>

<div class="mt-7 grid gap-5 xl:grid-cols-2">
    <section class="admin-content-card admin-reveal p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4"><div><p class="text-[10px] font-extrabold uppercase tracking-[.18em] text-[#0B6E4F]">Répartition</p><h2 class="mt-1 text-lg font-black text-slate-900">Comptes utilisateurs</h2><p class="mt-1 text-sm text-slate-500">Répartition par rôle enregistré</p></div><span class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-extrabold text-[#0B6E4F]">{{ number_format($usersChart['total'], 0, ',', ' ') }} comptes</span></div>
        <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row sm:justify-around">
            <div class="admin-donut relative h-44 w-44 shrink-0 rounded-full p-[17px]" style="background: {{ $usersChart['gradient'] }}"><div class="flex h-full w-full flex-col items-center justify-center rounded-full bg-white shadow-inner"><span class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($usersChart['total'], 0, ',', ' ') }}</span><span class="mt-1 text-[10px] font-extrabold uppercase tracking-[.15em] text-slate-400">Comptes</span></div></div>
            <div class="w-full min-w-0 space-y-3 sm:max-w-[230px]">
                @forelse ($usersChart['items'] as $item)
                    <div class="flex items-center justify-between gap-4"><div class="flex min-w-0 items-center gap-2.5"><span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $item['color'] }}"></span><span class="truncate text-sm font-semibold text-slate-600">{{ $item['label'] }}</span></div><div class="shrink-0 text-right"><span class="text-sm font-black text-slate-900">{{ number_format($item['value'], 0, ',', ' ') }}</span><span class="ml-1.5 text-xs text-slate-400">{{ $item['percent'] }}%</span></div></div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full" style="width: {{ $item['percent'] }}%; background: {{ $item['color'] }}"></div></div>
                @empty
                    <p class="text-sm text-slate-500">Aucun compte disponible pour le graphique.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="admin-content-card admin-reveal p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4"><div><p class="text-[10px] font-extrabold uppercase tracking-[.18em] text-orange-600">Opérations</p><h2 class="mt-1 text-lg font-black text-slate-900">État des réservations</h2><p class="mt-1 text-sm text-slate-500">Répartition selon le statut enregistré</p></div><a href="{{ route('admin.reservations') }}" class="admin-btn rounded-xl border border-slate-200 px-3 py-2 text-xs font-extrabold text-slate-600 hover:border-emerald-200 hover:text-[#0B6E4F]">Voir tout ↗</a></div>
        <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row sm:justify-around">
            <div class="admin-donut relative h-44 w-44 shrink-0 rounded-full p-[17px]" style="background: {{ $reservationsChart['gradient'] }}"><div class="flex h-full w-full flex-col items-center justify-center rounded-full bg-white shadow-inner"><span class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($reservationsChart['total'], 0, ',', ' ') }}</span><span class="mt-1 text-[10px] font-extrabold uppercase tracking-[.15em] text-slate-400">Réservations</span></div></div>
            <div class="w-full min-w-0 space-y-3 sm:max-w-[230px]">
                @forelse ($reservationsChart['items'] as $item)
                    <div class="flex items-center justify-between gap-4"><div class="flex min-w-0 items-center gap-2.5"><span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $item['color'] }}"></span><span class="truncate text-sm font-semibold text-slate-600">{{ $item['label'] }}</span></div><div class="shrink-0 text-right"><span class="text-sm font-black text-slate-900">{{ number_format($item['value'], 0, ',', ' ') }}</span><span class="ml-1.5 text-xs text-slate-400">{{ $item['percent'] }}%</span></div></div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full" style="width: {{ $item['percent'] }}%; background: {{ $item['color'] }}"></div></div>
                @empty
                    <p class="text-sm text-slate-500">Aucune réservation à représenter pour le moment.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>

<div class="mt-7 grid gap-5 2xl:grid-cols-[1.05fr_.95fr]">
    <section class="admin-content-card admin-reveal overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><div><h2 class="font-black text-slate-900">Voyages récents</h2><p class="mt-1 text-xs text-slate-500">Les derniers départs enregistrés</p></div><a href="{{ route('admin.trips') }}" class="admin-btn shrink-0 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-extrabold text-[#0B6E4F]">Tous les voyages →</a></div>
        <div class="divide-y divide-slate-100">
            @forelse ($recentTrips as $trip)
                <div class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-emerald-50/35 sm:px-6"><div class="flex min-w-0 items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F]"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 17V6.8A2.8 2.8 0 0 1 6.8 4h10.4A2.8 2.8 0 0 1 20 6.8V17M4 12h16M7 17v2m10-2v2" stroke-linecap="round"/></svg></span><div class="min-w-0"><p class="truncate text-sm font-extrabold text-slate-800">{{ $trip->departure_city }} <span class="text-[#F47C20]">→</span> {{ $trip->arrival_city }}</p><p class="mt-1 truncate text-xs text-slate-500">{{ $trip->agency?->name ?? 'Agence non précisée' }} · {{ $trip->vehicle?->brand ?? '' }} {{ $trip->vehicle?->model ?? '' }}</p></div></div><div class="shrink-0 text-right"><p class="text-sm font-black text-[#0B6E4F]">{{ number_format((float) $trip->price, 0, ',', ' ') }} Ar</p><p class="mt-1 text-[11px] text-slate-400">{{ $trip->departure_at?->format('d/m/Y H:i') ?? 'Date non définie' }}</p></div></div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-slate-500">Aucun voyage enregistré.</p>
            @endforelse
        </div>
    </section>

    <section class="admin-content-card admin-reveal overflow-hidden">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6"><div><h2 class="font-black text-slate-900">Réservations récentes</h2><p class="mt-1 text-xs text-slate-500">Les dernières opérations clients</p></div><a href="{{ route('admin.reservations') }}" class="admin-btn shrink-0 rounded-xl bg-orange-50 px-3 py-2 text-xs font-extrabold text-orange-700">Tout voir →</a></div>
        <div class="divide-y divide-slate-100">
            @forelse ($recentReservations as $reservation)
                <div class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-orange-50/30 sm:px-6"><div class="flex min-w-0 items-center gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h4" stroke-linecap="round"/></svg></span><div class="min-w-0"><p class="truncate text-sm font-extrabold text-slate-800">{{ $reservation->reservation_code ?? 'Réservation' }}</p><p class="mt-1 truncate text-xs text-slate-500">{{ $reservation->trip?->departure_city ?? '—' }} → {{ $reservation->trip?->arrival_city ?? '—' }} · {{ $reservation->passenger_count }} passager(s)</p></div></div><div class="shrink-0 text-right"><p class="text-sm font-black text-slate-800">{{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar</p><span class="mt-1 inline-block rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wide text-slate-600">{{ ucfirst($reservation->status) }}</span></div></div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-slate-500">Aucune réservation enregistrée.</p>
            @endforelse
        </div>
    </section>
</div>

<section class="admin-content-card admin-reveal mt-7 p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3"><div><p class="text-[10px] font-extrabold uppercase tracking-[.18em] text-[#0B6E4F]">Accès rapide</p><h2 class="mt-1 text-lg font-black text-slate-900">Que souhaitez-vous gérer ?</h2></div><span class="rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500">Sélectionnez un module</span></div>
    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.trips') }}" class="admin-btn group rounded-2xl border border-slate-200 p-4 hover:border-emerald-200 hover:bg-emerald-50/40"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F]"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 17V6.8A2.8 2.8 0 0 1 6.8 4h10.4A2.8 2.8 0 0 1 20 6.8V17M4 12h16"/></svg></span><p class="mt-4 font-extrabold text-slate-800">Voyages</p><p class="mt-1 text-xs leading-5 text-slate-500">Consulter itinéraires, départs et tarifs</p></a>
        <a href="{{ route('admin.reservations') }}" class="admin-btn group rounded-2xl border border-slate-200 p-4 hover:border-orange-200 hover:bg-orange-50/40"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-orange-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h4"/></svg></span><p class="mt-4 font-extrabold text-slate-800">Réservations</p><p class="mt-1 text-xs leading-5 text-slate-500">Suivre les passagers et les montants</p></a>
        <a href="{{ route('admin.vehicles') }}" class="admin-btn group rounded-2xl border border-slate-200 p-4 hover:border-blue-200 hover:bg-blue-50/40"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 11 6.5 6.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11M4 11h16v7H4z"/></svg></span><p class="mt-4 font-extrabold text-slate-800">Véhicules</p><p class="mt-1 text-xs leading-5 text-slate-500">Examiner le parc et les capacités</p></a>
        <a href="{{ route('admin.users') }}" class="admin-btn group rounded-2xl border border-slate-200 p-4 hover:border-violet-200 hover:bg-violet-50/40"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0"/></svg></span><p class="mt-4 font-extrabold text-slate-800">Utilisateurs</p><p class="mt-1 text-xs leading-5 text-slate-500">Consulter comptes et rôles</p></a>
    </div>
</section>

<style>
@keyframes admin-wave { 0%,100% { transform:rotate(0deg) } 25% { transform:rotate(18deg) } 55% { transform:rotate(-9deg) } 75% { transform:rotate(12deg) } }
</style>
@endsection