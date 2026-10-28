<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Driver;
use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ManagementController extends Controller
{
    private function authorizeAdministrator(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'admin',
            403,
            'Accès réservé à l’administrateur MonSiège.'
        );
    }

    private function tripStatuses(): array
    {
        return ['scheduled', 'boarding', 'departed', 'completed', 'cancelled'];
    }

    private function vehicleStatuses(): array
    {
        return ['active', 'maintenance', 'inactive'];
    }

    private function tripFormData(): array
    {
        return [
            'agencies' => Agency::query()->orderBy('name')->get(),
            'vehicles' => Vehicle::query()->with('agency')->orderBy('brand')->orderBy('model')->get(),
            'drivers' => Driver::query()->with(['user', 'agency'])->orderBy('created_at')->get(),
            'statuses' => $this->tripStatuses(),
        ];
    }

    private function vehicleFormData(): array
    {
        return [
            'agencies' => Agency::query()->orderBy('name')->get(),
            'statuses' => $this->vehicleStatuses(),
        ];
    }

    public function trips(Request $request): View
    {
        $this->authorizeAdministrator();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in($this->tripStatuses())],
        ]);

        $query = Trip::query()->with(['agency', 'vehicle', 'driver.user'])->latest('departure_at');

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function ($builder) use ($term): void {
                $builder->where('departure_city', 'like', "%{$term}%")
                    ->orWhere('arrival_city', 'like', "%{$term}%")
                    ->orWhere('departure_address', 'like', "%{$term}%")
                    ->orWhere('arrival_address', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $trips = $query->paginate(4)->withQueryString();

        return view('admin.trips', compact('trips', 'filters'));
    }

    public function createTrip(): View
    {
        $this->authorizeAdministrator();
        $trip = new Trip();

        return view('admin.trip-form', ['trip' => $trip, 'formMode' => 'create'] + $this->tripFormData());
    }

    public function storeTrip(Request $request): RedirectResponse
    {
        $this->authorizeAdministrator();

        $data = $request->validate([
            'agency_id' => ['required', 'uuid', 'exists:agencies,id'],
            'vehicle_id' => ['required', 'uuid', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'uuid', 'exists:drivers,id'],
            'departure_city' => ['required', 'string', 'max:100'],
            'arrival_city' => ['required', 'string', 'max:100', 'different:departure_city'],
            'departure_address' => ['nullable', 'string', 'max:255'],
            'arrival_address' => ['nullable', 'string', 'max:255'],
            'departure_at' => ['required', 'date', 'after_or_equal:now'],
            'arrival_at' => ['nullable', 'date', 'after:departure_at'],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'status' => ['required', Rule::in($this->tripStatuses())],
            'notes' => ['nullable', 'string', 'max:5000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'image.required' => 'Ajoute une image pour ce voyage.',
            'departure_at.after_or_equal' => 'La date de départ doit être aujourd’hui ou dans le futur.',
            'arrival_at.after' => 'L’arrivée doit être postérieure au départ.',
        ]);

        $this->validateTripRelations($data);
        $imagePath = $this->saveImage($request->file('image'), 'images/voyages/admin');
        $data['image_path'] = $imagePath;
        unset($data['image']);
        $data['currency'] = 'MGA';

        try {
            $trip = Trip::create($data);
        } catch (Throwable $exception) {
            $this->deleteManagedImage($imagePath);
            throw $exception;
        }

        return redirect()->route('admin.trips.edit', $trip)
            ->with('status', 'Voyage créé avec succès. Il apparaît dans la liste des voyages.');
    }

    public function editTrip(Trip $trip): View
    {
        $this->authorizeAdministrator();
        $trip->load(['agency', 'vehicle', 'driver.user']);

        return view('admin.trip-form', ['trip' => $trip, 'formMode' => 'edit'] + $this->tripFormData());
    }

    public function updateTrip(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorizeAdministrator();

        $data = $request->validate([
            'agency_id' => ['required', 'uuid', 'exists:agencies,id'],
            'vehicle_id' => ['required', 'uuid', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'uuid', 'exists:drivers,id'],
            'departure_city' => ['required', 'string', 'max:100'],
            'arrival_city' => ['required', 'string', 'max:100', 'different:departure_city'],
            'departure_address' => ['nullable', 'string', 'max:255'],
            'arrival_address' => ['nullable', 'string', 'max:255'],
            'departure_at' => ['required', 'date'],
            'arrival_at' => ['nullable', 'date', 'after:departure_at'],
            'price' => ['required', 'integer', 'min:1', 'max:100000000'],
            'status' => ['required', Rule::in($this->tripStatuses())],
            'notes' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'arrival_at.after' => 'L’arrivée doit être postérieure au départ.',
        ]);

        $this->validateTripRelations($data);

        if ($trip->reservations()->exists()) {
            $lockedFields = ['agency_id', 'vehicle_id', 'driver_id', 'departure_city', 'arrival_city', 'departure_at', 'arrival_at'];
            foreach ($lockedFields as $field) {
                $submitted = $data[$field] ?? null;
                $current = $trip->{$field};
                if (in_array($field, ['departure_at', 'arrival_at'], true)) {
                    $current = $current?->format('Y-m-d H:i:s');
                    $submitted = $submitted ? date('Y-m-d H:i:s', strtotime($submitted)) : null;
                }
                if ((string) ($submitted ?? '') !== (string) ($current ?? '')) {
                    throw ValidationException::withMessages([
                        $field => 'Ce voyage possède déjà des réservations. Son itinéraire, ses horaires, son agence et son véhicule ne peuvent plus être modifiés.',
                    ]);
                }
            }
        }

        $oldImage = $trip->image_path;
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->saveImage($request->file('image'), 'images/voyages/admin');
        }
        unset($data['image']);
        $data['currency'] = 'MGA';
        $trip->update($data);

        if (!empty($data['image_path']) && $oldImage !== $data['image_path']) {
            $this->deleteManagedImage($oldImage);
        }

        return redirect()->route('admin.trips.edit', $trip)->with('status', 'Voyage mis à jour avec succès.');
    }

    public function destroyTrip(Trip $trip): RedirectResponse
    {
        $this->authorizeAdministrator();

        if ($trip->reservations()->exists() || $trip->reservationSeats()->exists()) {
            return back()->with('status', 'Suppression refusée : ce voyage possède déjà des réservations ou des sièges occupés. Tu peux le passer au statut « cancelled ».');
        }

        $imagePath = $trip->image_path;
        $trip->delete();
        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.trips')->with('status', 'Voyage supprimé.');
    }

    public function vehicles(Request $request): View
    {
        $this->authorizeAdministrator();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in($this->vehicleStatuses())],
        ]);

        $query = Vehicle::query()->with('agency')->withCount('seats')->orderBy('brand')->orderBy('model');

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function ($builder) use ($term): void {
                $builder->where('brand', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhere('registration_number', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $vehicles = $query->paginate(4)->withQueryString();

        return view('admin.vehicles', compact('vehicles', 'filters'));
    }

    public function createVehicle(): View
    {
        $this->authorizeAdministrator();
        $vehicle = new Vehicle();

        return view('admin.vehicle-form', ['vehicle' => $vehicle, 'formMode' => 'create'] + $this->vehicleFormData());
    }

    public function storeVehicle(Request $request): RedirectResponse
    {
        $this->authorizeAdministrator();

        $data = $request->validate([
            'agency_id' => ['required', 'uuid', 'exists:agencies,id'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:50'],
            'registration_number' => ['required', 'string', 'max:50', 'unique:vehicles,registration_number'],
            'capacity' => ['required', 'integer', 'min:4', 'max:80'],
            'status' => ['required', Rule::in($this->vehicleStatuses())],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'image.required' => 'Ajoute une photo du véhicule.',
            'registration_number.unique' => 'Cette immatriculation est déjà enregistrée.',
        ]);

        $imagePath = $this->saveImage($request->file('image'), 'images/vehicles/admin');
        unset($data['image']);
        $data['image_path'] = $imagePath;

        try {
            $vehicle = DB::transaction(function () use ($data): Vehicle {
                $vehicle = Vehicle::create($data);
                $this->createVehicleSeats($vehicle);
                return $vehicle;
            });
        } catch (Throwable $exception) {
            $this->deleteManagedImage($imagePath);
            throw $exception;
        }

        return redirect()->route('admin.vehicles.edit', $vehicle)->with('status', 'Véhicule créé, image enregistrée et sièges générés automatiquement.');
    }

    public function editVehicle(Vehicle $vehicle): View
    {
        $this->authorizeAdministrator();
        $vehicle->load('agency')->loadCount('seats');

        return view('admin.vehicle-form', ['vehicle' => $vehicle, 'formMode' => 'edit'] + $this->vehicleFormData());
    }

    public function updateVehicle(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeAdministrator();

        $data = $request->validate([
            'agency_id' => ['required', 'uuid', 'exists:agencies,id'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:50'],
            'registration_number' => ['required', 'string', 'max:50', Rule::unique('vehicles', 'registration_number')->ignore($vehicle->id, 'id')],
            'capacity' => ['required', 'integer', 'min:4', 'max:80'],
            'status' => ['required', Rule::in($this->vehicleStatuses())],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'registration_number.unique' => 'Cette immatriculation est déjà enregistrée.',
        ]);

        $capacityChanged = (int) $vehicle->capacity !== (int) $data['capacity'];
        if ($capacityChanged && Trip::query()->where('vehicle_id', $vehicle->id)->exists()) {
            throw ValidationException::withMessages([
                'capacity' => 'La capacité ne peut pas être modifiée tant que le véhicule est affecté à un voyage. Crée un autre véhicule ou réaffecte d’abord les voyages.',
            ]);
        }

        if ($capacityChanged) {
            $seatIds = $vehicle->seats()->pluck('id');
            if (DB::table('reservation_seats')->whereIn('seat_id', $seatIds)->exists()) {
                throw ValidationException::withMessages([
                    'capacity' => 'La capacité ne peut pas changer car certains sièges ont déjà été réservés.',
                ]);
            }
        }

        $oldImage = $vehicle->image_path;
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->saveImage($request->file('image'), 'images/vehicles/admin');
        }
        unset($data['image']);

        DB::transaction(function () use ($vehicle, $data, $capacityChanged): void {
            if ($capacityChanged) {
                $vehicle->seats()->delete();
            }
            $vehicle->update($data);
            if ($capacityChanged) {
                $this->createVehicleSeats($vehicle->fresh());
            }
        });

        if (!empty($data['image_path']) && $oldImage !== $data['image_path']) {
            $this->deleteManagedImage($oldImage);
        }

        return redirect()->route('admin.vehicles.edit', $vehicle)->with('status', 'Véhicule mis à jour avec succès.');
    }

    public function destroyVehicle(Vehicle $vehicle): RedirectResponse
    {
        $this->authorizeAdministrator();

        if (Trip::query()->where('vehicle_id', $vehicle->id)->exists()) {
            return back()->with('status', 'Suppression refusée : ce véhicule est déjà associé à un ou plusieurs voyages. Réaffecte les voyages avant de le supprimer.');
        }

        $seatIds = $vehicle->seats()->pluck('id');
        if (DB::table('reservation_seats')->whereIn('seat_id', $seatIds)->exists()) {
            return back()->with('status', 'Suppression refusée : certains sièges de ce véhicule sont encore référencés par des réservations.');
        }

        $imagePath = $vehicle->image_path;
        DB::transaction(function () use ($vehicle): void {
            $vehicle->seats()->delete();
            $vehicle->delete();
        });
        $this->deleteManagedImage($imagePath);

        return redirect()->route('admin.vehicles')->with('status', 'Véhicule supprimé.');
    }

    public function users(Request $request): View
    {
        $this->authorizeAdministrator();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', 'in:admin,client,agency,driver'],
        ]);

        $query = User::query()->orderBy('name');
        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function ($builder) use ($term): void {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        $users = $query->paginate(4)->withQueryString();
        return view('admin.users', compact('users', 'filters'));
    }

    public function destroyUser(User $user): RedirectResponse
    {
        $this->authorizeAdministrator();

        if ((string) $user->id === (string) auth()->id()) {
            return back()->with('status', 'Pour ta sécurité, tu ne peux pas supprimer le compte administrateur que tu utilises actuellement.');
        }

        if ($user->role === 'admin' && User::query()->where('role', 'admin')->count() <= 1) {
            return back()->with('status', 'Le dernier compte administrateur ne peut pas être supprimé.');
        }

        $hasAgency = Agency::query()->where('owner_user_id', $user->id)->exists();
        $hasDriver = Driver::query()->where('user_id', $user->id)->exists();
        $hasReservations = Reservation::query()->where('user_id', $user->id)->exists();
        if ($hasAgency || $hasDriver || $hasReservations) {
            return back()->with('status', 'Suppression refusée : ce compte est associé à une agence, un chauffeur ou à des réservations. Pour préserver l’historique, il faut d’abord transférer ces données ou anonymiser le compte.');
        }

        try {
            $user->delete();
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('status', 'Suppression impossible : des données de la plateforme sont encore liées à ce compte.');
        }

        return redirect()->route('admin.users')->with('status', 'Compte utilisateur supprimé.');
    }

    private function validateTripRelations(array $data): void
    {
        $vehicle = Vehicle::query()->findOrFail($data['vehicle_id']);
        if ((string) $vehicle->agency_id !== (string) $data['agency_id']) {
            throw ValidationException::withMessages([
                'vehicle_id' => 'Le véhicule choisi doit appartenir à l’agence sélectionnée.',
            ]);
        }

        if (!empty($data['driver_id'])) {
            $driver = Driver::query()->findOrFail($data['driver_id']);
            if ((string) $driver->agency_id !== (string) $data['agency_id']) {
                throw ValidationException::withMessages([
                    'driver_id' => 'Le chauffeur choisi doit appartenir à l’agence sélectionnée.',
                ]);
            }
        }
    }

    private function createVehicleSeats(Vehicle $vehicle): void
    {
        $capacity = (int) $vehicle->capacity;
        $seats = [];

        // Preserve MonSiège's requested 18-seat minibus layout: A1/A2 in front, then 16 seats in four rows.
        if ($vehicle->type === 'minibus' && $capacity === 18) {
            $seats[] = ['seat_number' => 'A1', 'row_number' => 0, 'column_number' => 1];
            $seats[] = ['seat_number' => 'A2', 'row_number' => 0, 'column_number' => 2];
            for ($number = 1; $number <= 16; $number++) {
                $seats[] = [
                    'seat_number' => str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                    'row_number' => intdiv($number - 1, 4) + 1,
                    'column_number' => (($number - 1) % 4) + 1,
                ];
            }
        } else {
            for ($number = 1; $number <= $capacity; $number++) {
                $seats[] = [
                    'seat_number' => str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                    'row_number' => intdiv($number - 1, 4) + 1,
                    'column_number' => (($number - 1) % 4) + 1,
                ];
            }
        }

        foreach ($seats as $seat) {
            Seat::create($seat + [
                'vehicle_id' => $vehicle->id,
                'seat_type' => 'standard',
                'is_window' => in_array((int) ($seat['column_number'] ?? 0), [1, 4], true),
                'is_active' => true,
            ]);
        }
    }

    private function saveImage($uploadedFile, string $folder): string
    {
        $directory = public_path($folder);
        File::ensureDirectoryExists($directory);
        $filename = (string) Str::uuid() . '.' . $uploadedFile->extension();
        $uploadedFile->move($directory, $filename);

        return trim($folder, '/') . '/' . $filename;
    }

    private function deleteManagedImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        // Never remove the existing destination photos or the static placeholder.
        if (!Str::startsWith($path, ['images/voyages/admin/', 'images/vehicles/admin/'])) {
            return;
        }

        $absolutePath = public_path($path);
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}