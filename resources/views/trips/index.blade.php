@extends('layouts.app')

@section('title', 'Voyages disponibles - MonSiège')

@section('content')
<section class="min-h-[60vh] bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <header class="mb-8">
            <span class="text-sm font-bold uppercase tracking-[0.18em] text-[#F47C20]">MonSiège</span>
            <h1 class="mt-2 text-3xl font-black text-[#1E293B] sm:text-4xl">Voyages disponibles</h1>
            <p class="mt-2 text-slate-500">
                @if(!empty($search['departure']) || !empty($search['destination']) || !empty($search['departure_date']))
                    Résultats correspondant à votre recherche.
                @else
                    Découvrez les prochains voyages disponibles.
                @endif
            </p>
        </header>

        <div class="mb-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <form action="{{ route('trips.index') }}" method="GET" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label for="departure" class="mb-2 block text-sm font-semibold text-slate-700">Ville de départ</label>
                    <input id="departure" name="departure" value="{{ $search['departure'] ?? '' }}" placeholder="Antananarivo" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                </div>
                <div>
                    <label for="destination" class="mb-2 block text-sm font-semibold text-slate-700">Destination</label>
                    <input id="destination" name="destination" value="{{ $search['destination'] ?? '' }}" placeholder="Toamasina" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                </div>
                <div>
                    <label for="departure_date" class="mb-2 block text-sm font-semibold text-slate-700">Date de départ</label>
                    <input id="departure_date" type="date" name="departure_date" min="{{ now()->toDateString() }}" value="{{ $search['departure_date'] ?? '' }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                </div>
                <div>
                    <label for="passengers" class="mb-2 block text-sm font-semibold text-slate-700">Passagers</label>
                    <select id="passengers" name="passengers" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none focus:border-[#0B6E4F] focus:ring-2 focus:ring-[#0B6E4F]/10">
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" @selected(($search['passengers'] ?? 1) == $i)>{{ $i }} {{ $i > 1 ? 'passagers' : 'passager' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="min-h-[48px] w-full rounded-xl bg-[#F47C20] px-5 font-bold text-white shadow-sm transition hover:bg-orange-600">Rechercher</button>
                </div>
            </form>
        </div>

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
                <p class="font-bold">Vérifiez votre recherche.</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="mb-5 flex items-center justify-between gap-3">
            <p class="text-sm text-slate-500"><span class="font-bold text-[#1E293B]">{{ $trips->total() }}</span> {{ $trips->total() > 1 ? 'voyages trouvés' : 'voyage trouvé' }}</p>
            @if($trips->total() > 0)
                <span class="text-xs text-slate-400">Page {{ $trips->currentPage() }} sur {{ max(1, $trips->lastPage()) }}</span>
            @endif
        </div>

        <div class="space-y-5">
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
                    $tripImage = $trip->image_path ?: $fallbackImage;
                @endphp
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="grid gap-5 p-4 sm:p-5 lg:grid-cols-[260px_minmax(0,1fr)_170px] lg:items-center">
                        <a href="{{ route('trips.show', $trip) }}" class="block overflow-hidden rounded-xl bg-slate-100">
                            <img src="{{ asset($tripImage) }}" alt="Photo illustrative du voyage vers {{ $trip->arrival_city }}" class="h-48 w-full object-cover transition duration-300 hover:scale-[1.03]" loading="lazy" decoding="async">
                        </a>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-[#0B6E4F]/10 px-3 py-1 text-xs font-bold text-[#0B6E4F]">{{ $trip->agency?->name ?? 'Agence partenaire' }}</span>
                                @if($trip->vehicle?->type)
                                    <span class="text-xs font-medium text-slate-400">{{ ucfirst($trip->vehicle->type) }}</span>
                                @endif
                            </div>

                            <h2 class="mt-3 text-xl font-black text-[#1E293B] sm:text-2xl">
                                {{ $trip->departure_city }} <span class="mx-1 text-[#F47C20]">→</span> {{ $trip->arrival_city }}
                            </h2>

                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-600">
                                <span>📅 {{ $trip->departure_at->format('d/m/Y') }}</span>
                                <span>🕒 Départ à {{ $trip->departure_at->format('H:i') }}</span>
                                @if($trip->arrival_at)
                                    <span>🏁 Arrivée à {{ $trip->arrival_at->format('H:i') }}</span>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">
                                @if($trip->vehicle)
                                    <span>🚐 {{ trim(($trip->vehicle->brand ?? '') . ' ' . ($trip->vehicle->model ?? '')) ?: 'Véhicule de transport' }}</span>
                                    @if($trip->vehicle->capacity)
                                        <span>💺 {{ $trip->vehicle->capacity }} places</span>
                                    @endif
                                @endif
                                @if($trip->driver?->user)
                                    <span>👤 Chauffeur : {{ $trip->driver->user->name }}</span>
                                @endif
                            </div>
                            @if($trip->notes)
                                <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $trip->notes }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between gap-4 border-t border-slate-100 pt-4 sm:pt-3 lg:flex-col lg:items-end lg:border-l lg:border-t-0 lg:pl-5 lg:pt-0">
                            <div class="lg:text-right">
                                <p class="text-2xl font-black text-[#0B6E4F]">{{ number_format((float) $trip->price, 0, ',', ' ') }} Ar</p>
                                <p class="text-xs text-slate-500">par personne</p>
                            </div>
                            <a href="{{ route('trips.show', $trip) }}" class="inline-flex items-center justify-center rounded-xl bg-[#0B6E4F] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#08583f] focus:outline-none focus:ring-2 focus:ring-[#0B6E4F]/30">
                                Voir les détails <span class="ml-2" aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#0B6E4F]/10 text-2xl">🚌</div>
                    <h2 class="mt-4 text-xl font-black text-[#1E293B]">Aucun voyage disponible pour le moment</h2>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-500">Essayez une autre destination ou revenez consulter les prochains départs. Les voyages apparaîtront ici lorsqu'ils seront enregistrés.</p>
                    @if(request()->query())
                        <a href="{{ route('trips.index') }}" class="mt-5 inline-flex rounded-xl bg-[#0B6E4F] px-5 py-3 font-bold text-white hover:bg-[#08583f]">Effacer les filtres</a>
                    @endif
                </div>
            @endforelse
        </div>

        @if($trips->hasPages())
            <nav class="mt-8" aria-label="Pagination des voyages">
                {{ $trips->onEachSide(1)->links() }}
            </nav>
        @endif
    </div>
</section>
@endsection