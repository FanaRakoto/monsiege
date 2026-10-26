<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TripController extends Controller
{
    /** Construire la requête des voyages à venir et réservables. */
    private function availableTripsQuery(): Builder
    {
        return Trip::query()
            ->with(['agency', 'vehicle', 'driver.user'])
            ->where('status', 'scheduled')
            ->where('departure_at', '>=', now());
    }

    /** Accueil : les trois prochains voyages. */
    public function home()
    {
        $popularTrips = $this->availableTripsQuery()
            ->orderBy('departure_at')
            ->limit(3)
            ->get();

        return view('welcome', compact('popularTrips'));
    }

    /** Liste publique avec recherche et pagination. */
    public function index(Request $request)
    {
        $search = $request->validate([
            'departure' => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:100'],
            'departure_date' => ['nullable', 'date', 'after_or_equal:today'],
            'passengers' => ['nullable', 'integer', 'min:1', 'max:6'],
        ]);

        $search['passengers'] = (int) ($search['passengers'] ?? 1);
        $query = $this->availableTripsQuery();

        if (! empty($search['departure'])) {
            $query->where('departure_city', 'like', '%' . trim($search['departure']) . '%');
        }

        if (! empty($search['destination'])) {
            $query->where('arrival_city', 'like', '%' . trim($search['destination']) . '%');
        }

        if (! empty($search['departure_date'])) {
            $query->whereDate('departure_at', $search['departure_date']);
        }

        $trips = $query
            ->orderBy('departure_at')
            ->paginate(6)
            ->withQueryString();

        return view('trips.index', compact('trips', 'search'));
    }

    /** Détails d'un voyage encore disponible. */
    public function show(Trip $trip)
    {
        if (
            $trip->status !== 'scheduled'
            || Carbon::parse($trip->departure_at)->isPast()
        ) {
            return redirect()
                ->route('trips.index')
                ->with('error', 'Ce voyage n’est plus disponible à la réservation.');
        }

        $trip->load(['agency', 'vehicle', 'vehicle.seats', 'driver.user']);

        return view('trips.show', compact('trip'));
    }
}
