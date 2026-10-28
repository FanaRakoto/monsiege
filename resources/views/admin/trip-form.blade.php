@extends('admin.layout')
@section('title', ($formMode === 'create' ? 'Créer un voyage' : 'Modifier un voyage').' — MonSiège')
@section('admin-content')
@php
    $isEdit = $formMode === 'edit';
    $statusLabels = ['scheduled' => 'Programmé', 'boarding' => 'Embarquement', 'departed' => 'Parti', 'completed' => 'Terminé', 'cancelled' => 'Annulé'];
@endphp
<div class="mb-5 flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-[#0B6E4F]">Administration / Voyages</p><h2 class="mt-1 text-2xl font-black text-slate-900">{{ $isEdit ? 'Modifier le voyage' : 'Créer un voyage' }}</h2><p class="mt-1 text-sm text-slate-500">Renseigne le trajet, le prix, le véhicule et une image.</p></div><a href="{{ route('admin.trips') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour aux voyages</a></div>
<form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.trips.update', $trip) : route('admin.trips.store') }}" class="space-y-5">
    @csrf @if($isEdit) @method('PUT') @endif
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><div class="mb-5 flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-[#0B6E4F]"><i class="fa-solid fa-route" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-900">Itinéraire</h3><p class="text-xs text-slate-500">Départ, destination et affectations.</p></div></div>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Ville de départ *</span><input name="departure_city" required maxlength="100" value="{{ old('departure_city', $trip->departure_city) }}" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">@error('departure_city')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Ville d'arrivée *</span><input name="arrival_city" required maxlength="100" value="{{ old('arrival_city', $trip->arrival_city) }}" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">@error('arrival_city')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Adresse de départ</span><input name="departure_address" maxlength="255" value="{{ old('departure_address', $trip->departure_address) }}" placeholder="Ex. Gare routière, Analakely" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"></label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Adresse d'arrivée</span><input name="arrival_address" maxlength="255" value="{{ old('arrival_address', $trip->arrival_address) }}" placeholder="Ex. Gare routière de Toamasina" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"></label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Agence *</span><select id="tripAgency" name="agency_id" required class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"><option value="">Choisir une agence</option>@foreach($agencies as $agency)<option value="{{ $agency->id }}" @selected(old('agency_id', $trip->agency_id) == $agency->id)>{{ $agency->name }}</option>@endforeach</select>@error('agency_id')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Véhicule *</span><select id="tripVehicle" name="vehicle_id" required class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"><option value="">Choisir un véhicule</option>@foreach($vehicles as $vehicle)<option data-agency="{{ $vehicle->agency_id }}" value="{{ $vehicle->id }}" @selected(old('vehicle_id', $trip->vehicle_id) == $vehicle->id)>{{ $vehicle->brand }} {{ $vehicle->model }} · {{ $vehicle->registration_number }} ({{ $vehicle->agency?->name }})</option>@endforeach</select>@error('vehicle_id')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block md:col-span-2"><span class="mb-1.5 block text-sm font-bold text-slate-700">Chauffeur (facultatif)</span><select id="tripDriver" name="driver_id" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]"><option value="">Affecter plus tard</option>@foreach($drivers as $driver)<option data-agency="{{ $driver->agency_id }}" value="{{ $driver->id }}" @selected(old('driver_id', $trip->driver_id) == $driver->id)>{{ $driver->user?->name ?? 'Chauffeur sans compte' }} · {{ $driver->agency?->name }}</option>@endforeach</select>@error('driver_id')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
        </div>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><div class="mb-5 flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 text-[#F47C20]"><i class="fa-regular fa-calendar-days" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-900">Horaire et tarif</h3><p class="text-xs text-slate-500">Les prix sont exprimés en ariary malgaches (MGA).</p></div></div>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Départ *</span><input type="datetime-local" name="departure_at" required value="{{ old('departure_at', $trip->departure_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">@error('departure_at')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Arrivée (facultatif)</span><input type="datetime-local" name="arrival_at" value="{{ old('arrival_at', $trip->arrival_at?->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">@error('arrival_at')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Prix par passager (Ar) *</span><input type="number" name="price" min="1" max="100000000" step="1" required value="{{ old('price', $trip->price) }}" placeholder="35000" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">@error('price')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
            <label class="block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Statut *</span><select name="status" required class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">@foreach($statuses as $status)<option value="{{ $status }}" @selected(old('status', $trip->status ?: 'scheduled') === $status)>{{ $statusLabels[$status] ?? ucfirst($status) }}</option>@endforeach</select>@error('status')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
        </div>
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><div class="mb-5 flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700"><i class="fa-regular fa-image" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-900">Image du voyage</h3><p class="text-xs text-slate-500">JPG, PNG ou WebP · 5 Mo maximum.</p></div></div>
        @if($isEdit && $trip->image_path)<div class="mb-4 flex items-center gap-4 rounded-xl bg-slate-50 p-3"><img src="{{ asset($trip->image_path) }}" alt="Image actuelle du voyage" class="h-20 w-28 rounded-lg object-cover"><div><p class="text-sm font-bold text-slate-700">Image actuelle</p><p class="text-xs text-slate-500">Sélectionne un nouveau fichier uniquement pour la remplacer.</p></div></div>@endif
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" {{ !$isEdit ? 'required' : '' }} class="block w-full rounded-xl border border-slate-200 p-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:font-bold file:text-[#0B6E4F]">@error('image')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror
        <label class="mt-4 block"><span class="mb-1.5 block text-sm font-bold text-slate-700">Note / information passagers</span><textarea name="notes" rows="3" maxlength="5000" placeholder="Consignes d'embarquement, bagages, point de rendez-vous…" class="w-full rounded-xl border-slate-200 focus:border-[#0B6E4F] focus:ring-[#0B6E4F]">{{ old('notes', $trip->notes) }}</textarea>@error('notes')<span class="mt-1 block text-xs text-rose-600">{{ $message }}</span>@enderror</label>
    </section>
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="{{ route('admin.trips') }}" class="inline-flex justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700">Annuler</a><button type="submit" class="admin-btn inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B6E4F] px-5 py-3 text-sm font-extrabold text-white"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> {{ $isEdit ? 'Enregistrer les modifications' : 'Créer le voyage' }}</button></div>
</form>
<script>
(() => {
    const agency = document.getElementById('tripAgency');
    const vehicle = document.getElementById('tripVehicle');
    const driver = document.getElementById('tripDriver');
    const filterByAgency = (select, resetInvalid = true) => {
        const chosenAgency = agency?.value || '';
        if (!select) return;
        [...select.options].forEach((option) => {
            if (!option.value || !option.dataset.agency) return;
            const visible = !chosenAgency || option.dataset.agency === chosenAgency;
            option.hidden = !visible;
            option.disabled = !visible;
        });
        const selected = select.options[select.selectedIndex];
        if (resetInvalid && selected?.dataset.agency && chosenAgency && selected.dataset.agency !== chosenAgency) select.value = '';
    };
    agency?.addEventListener('change', () => { filterByAgency(vehicle); filterByAgency(driver); });
    filterByAgency(vehicle, false); filterByAgency(driver, false);
})();
</script>
@endsection