@extends('layouts.app')

@section('title', 'Tous les voyages — MonSiège')
@section('meta_description', 'Recherchez et triez les prochains voyages à Madagascar par destination et date de départ.')

@section('content')
<div class="min-h-[65vh] bg-slate-50 font-sans antialiased text-slate-900">
    <section class="relative overflow-hidden bg-[#073F30]">
        <div class="absolute inset-0 bg-gradient-to-r from-[#052f24] via-[#073F30] to-[#0B6E4F]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-20 -top-28 h-72 w-72 rounded-full bg-[#F47C20]/20 blur-3xl" aria-hidden="true"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8" data-reveal>
            <p class="text-xs font-extrabold uppercase tracking-[.18em] text-[#ffad70]">MonSiège · Madagascar</p>
            <h1 class="mt-3 font-sans text-3xl font-extrabold tracking-tight text-white sm:text-5xl">Tous les voyages</h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-white/80">Choisissez votre destination, précisez une date de départ et triez les résultats selon votre préférence.</p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <form action="{{ route('trips.index') }}" method="GET" class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" data-reveal>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <div>
                    <label for="departure" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Départ</label>
                    <input id="departure" name="departure" type="text" maxlength="100" value="{{ $search['departure'] ?? '' }}" placeholder="Antananarivo" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm text-slate-800 outline-none focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                </div>
                <div>
                    <label for="destination" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Destination</label>
                    <input id="destination" name="destination" type="text" maxlength="100" value="{{ $search['destination'] ?? '' }}" placeholder="Toamasina" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm text-slate-800 outline-none focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                </div>
                <div>
                    <label for="departure_date" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Date de départ</label>
                    <input id="departure_date" name="departure_date" type="date" min="{{ now()->toDateString() }}" value="{{ $search['departure_date'] ?? '' }}" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm text-slate-800 outline-none focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                </div>
                <div>
                    <label for="passengers" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Passagers</label>
                    <select id="passengers" name="passengers" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm text-slate-800 outline-none focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" @selected(($search['passengers'] ?? 1) == $i)>{{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="sort" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Trier par date</label>
                    <select id="sort" name="sort" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm text-slate-800 outline-none focus:border-[#0B6E4F] focus:ring-4 focus:ring-emerald-900/10">
                        <option value="date_asc" @selected(($search['sort'] ?? 'date_asc') === 'date_asc')>Les plus proches</option>
                        <option value="date_desc" @selected(($search['sort'] ?? 'date_asc') === 'date_desc')>Les plus éloignés</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="min-h-12 w-full rounded-xl bg-[#0B6E4F] px-5 text-sm font-extrabold text-white transition hover:bg-[#08583f] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-emerald-900/20">Appliquer</button>
                </div>
            </div>
            @if($errors->any())
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <p class="font-bold">Vérifiez les filtres.</p>
                    <ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
        </form>

        @if(session('error'))
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900" role="status">{{ session('error') }}</div>
        @endif

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3" data-reveal>
            <p class="text-sm text-slate-600"><strong class="text-slate-900">{{ $trips->total() }}</strong> {{ $trips->total() === 1 ? 'voyage disponible' : 'voyages disponibles' }}</p>
            @if($trips->total() > 0)
                <p class="text-xs font-medium text-slate-500">Page {{ $trips->currentPage() }} sur {{ max(1, $trips->lastPage()) }} · 6 résultats maximum par page</p>
            @endif
        </div>

        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($trips as $trip)
                @php
                    $destinationKey = mb_strtolower(trim($trip->arrival_city));
                    $fallbackImage = match ($destinationKey) {
                        'toamasina', 'tamatave' => 'images/voyages/toamasina.jpeg',
                        'fianarantsoa' => 'images/voyages/fianarantsoa.jpeg',
                        'toliara', 'tuléar', 'tulear' => 'images/voyages/toliara-rn7.jpeg',
                        'mahajanga' => 'images/voyages/mahajanga.jpeg',
                        'antsiranana', 'diego-suarez', 'diégo-suarez' => 'images/voyages/antsiranana.jpeg',
                        default => 'images/voyages/RN7.jpeg',
                    };
                    $storedImage = (string) ($trip->image_path ?? '');
                    $tripImagePath = str_starts_with($storedImage, 'images/voyages/') && is_file(public_path($storedImage)) ? $storedImage : $fallbackImage;
                    if (!is_file(public_path($tripImagePath))) $tripImagePath = 'images/monsiege-icone-anime.png';
                @endphp
                <article class="group flex min-w-0 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-950/10" data-reveal style="--reveal-delay: {{ $loop->index * 65 }}ms">
                    <a href="{{ route('trips.show', $trip) }}" class="relative block h-52 overflow-hidden bg-slate-100" aria-label="Détails du voyage vers {{ $trip->arrival_city }}">
                        <img src="{{ asset($tripImagePath) }}" alt="Photo illustrative du trajet vers {{ $trip->arrival_city }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-slate-950/10" aria-hidden="true"></div>
                        <div class="absolute inset-x-4 bottom-4 grid grid-cols-[1fr_auto_1fr] items-end gap-2 text-white">
                            <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-widest text-white/70">Départ</p><p class="mt-1 break-words text-base font-extrabold">{{ $trip->departure_city }}</p></div>
                            <span class="mb-1 text-xl font-bold text-[#ffad70]" aria-hidden="true">→</span>
                            <div class="min-w-0 text-right"><p class="text-[10px] font-bold uppercase tracking-widest text-white/70">Destination</p><p class="mt-1 break-words text-base font-extrabold">{{ $trip->arrival_city }}</p></div>
                        </div>
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <p class="text-xs font-semibold text-slate-500">{{ $trip->agency?->name ?? 'Agence partenaire' }}@if($trip->vehicle?->type) · {{ ucfirst($trip->vehicle->type) }}@endif</p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                            <span class="rounded-lg bg-slate-100 px-3 py-2">📅 {{ $trip->departure_at->format('d/m/Y') }}</span>
                            <span class="rounded-lg bg-orange-50 px-3 py-2 text-[#9a4810]">◷ {{ $trip->departure_at->format('H:i') }}</span>
                        </div>
                        @if($trip->vehicle?->capacity)<p class="mt-3 text-sm text-slate-500">{{ $trip->vehicle->capacity }} places dans le véhicule</p>@endif
                        <div class="mt-auto flex items-end justify-between gap-3 border-t border-slate-100 pt-4 mt-5">
                            <div><p class="text-xs font-semibold text-slate-500">Par passager</p><p class="mt-1 text-2xl font-extrabold text-[#0B6E4F]">{{ number_format((float) $trip->price, 0, ',', ' ') }} <span class="text-sm">Ar</span></p></div>
                            <a href="{{ route('trips.show', $trip) }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0B6E4F] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#08583f]">Détails <span class="ml-2" aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center sm:col-span-2 xl:col-span-3" data-reveal>
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl" aria-hidden="true">🚌</span>
                    <h2 class="mt-4 font-sans text-xl font-extrabold text-slate-900">Aucun voyage correspondant</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">Essayez une autre date ou modifiez votre destination. Les voyages futurs apparaîtront ici.</p>
                    @if(request()->query())<a href="{{ route('trips.index') }}" class="mt-5 inline-flex min-h-11 items-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Effacer les filtres</a>@endif
                </div>
            @endforelse
        </div>

        @if($trips->hasPages())
            <nav class="mt-9" aria-label="Pagination des voyages">{{ $trips->onEachSide(1)->links() }}</nav>
        @endif
    </div>
</div>
@endsection