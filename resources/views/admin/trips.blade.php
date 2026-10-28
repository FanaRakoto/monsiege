@extends('admin.layout')
@section('title', 'Gestion des voyages — MonSiège')
@section('admin-content')
@php
    $statusLabels = ['scheduled' => 'Programmé', 'boarding' => 'Embarquement', 'departed' => 'Parti', 'completed' => 'Terminé', 'cancelled' => 'Annulé'];
    $statusClasses = ['scheduled' => 'bg-emerald-50 text-emerald-800', 'boarding' => 'bg-amber-50 text-amber-800', 'departed' => 'bg-sky-50 text-sky-800', 'completed' => 'bg-slate-100 text-slate-700', 'cancelled' => 'bg-rose-50 text-rose-800'];
@endphp
<div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><p class="text-xs font-bold uppercase tracking-[.18em] text-[#0B6E4F]">Catalogue des trajets</p><h2 class="mt-1 text-2xl font-black text-slate-900">Gestion des voyages</h2><p class="mt-1 text-sm text-slate-500">Tous les voyages : programmés, passés et annulés.</p></div>
    <a href="{{ route('admin.trips.create') }}" class="admin-btn inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-4 py-3 text-sm font-bold text-white"><i class="fa-solid fa-plus" aria-hidden="true"></i> Créer un voyage</a>
</div>
<form method="GET" action="{{ route('admin.trips') }}" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_190px_auto_auto] sm:items-end">
    <label class="block"><span class="mb-1.5 block text-xs font-bold text-slate-600">Rechercher un trajet ou une ville</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Antananarivo, Toamasina…" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"></label>
    <label class="block"><span class="mb-1.5 block text-xs font-bold text-slate-600">Statut</span><select name="status" class="w-full rounded-xl border-slate-200 text-sm focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"><option value="">Tous les statuts</option>@foreach($statusLabels as $key => $label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach</select></label>
    <button class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-slate-700"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Filtrer</button>
    <a href="{{ route('admin.trips') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50">Réinitialiser</a>
</form>
<div class="mb-3 flex items-center justify-between gap-3"><p class="text-sm text-slate-500">{{ number_format($trips->total(), 0, ',', ' ') }} voyage(s)</p><p class="text-xs text-slate-400">4 voyages par page</p></div>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto"><table class="min-w-[900px] w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Trajet</th><th class="px-4 py-3">Départ</th><th class="px-4 py-3">Agence / Véhicule</th><th class="px-4 py-3">Prix</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3 text-right">Actions</th></tr></thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($trips as $trip)
            <tr class="transition hover:bg-emerald-50/30">
                <td class="px-4 py-4"><div class="font-bold text-slate-900">{{ $trip->departure_city }} <i class="fa-solid fa-arrow-right-long mx-1 text-[#F47C20]" aria-hidden="true"></i> {{ $trip->arrival_city }}</div><div class="mt-1 max-w-[260px] truncate text-xs text-slate-400">{{ $trip->departure_address ?: 'Adresse de départ non précisée' }}</div></td>
                <td class="whitespace-nowrap px-4 py-4"><div class="font-semibold text-slate-700">{{ $trip->departure_at?->format('d/m/Y') ?? '—' }}</div><div class="text-xs text-slate-500">{{ $trip->departure_at?->format('H:i') ?? '—' }}</div></td>
                <td class="px-4 py-4"><div class="font-semibold text-slate-700">{{ $trip->agency?->name ?? 'Agence supprimée' }}</div><div class="mt-1 text-xs text-slate-500">{{ $trip->vehicle?->brand ?? '' }} {{ $trip->vehicle?->model ?? '' }} · {{ $trip->driver?->user?->name ?? 'Chauffeur non affecté' }}</div></td>
                <td class="whitespace-nowrap px-4 py-4 font-extrabold text-[#0B6E4F]">{{ number_format((float) $trip->price, 0, ',', ' ') }} Ar</td>
                <td class="px-4 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClasses[$trip->status] ?? 'bg-slate-100 text-slate-700' }}">{{ $statusLabels[$trip->status] ?? ucfirst($trip->status) }}</span></td>
                <td class="px-4 py-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.trips.edit', $trip) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:border-emerald-300 hover:text-[#0B6E4F]"><i class="fa-solid fa-pen" aria-hidden="true"></i><span>Modifier</span></a><form method="POST" action="{{ route('admin.trips.destroy', $trip) }}" onsubmit="return confirm('Confirmer la suppression de ce voyage ? Les voyages liés à des réservations seront protégés.');">@csrf @method('DELETE')<button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50"><i class="fa-solid fa-trash" aria-hidden="true"></i><span>Supprimer</span></button></form></div></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-6 py-14 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-route text-xl" aria-hidden="true"></i></span><p class="mt-3 font-bold text-slate-700">Aucun voyage trouvé</p><p class="mt-1 text-sm text-slate-500">Modifie les filtres ou crée un nouveau voyage.</p></td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@if($trips->hasPages())<div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs text-slate-500">Affichage de {{ $trips->firstItem() }} à {{ $trips->lastItem() }} sur {{ $trips->total() }}</p>{{ $trips->onEachSide(1)->links() }}</div>@endif
@endsection