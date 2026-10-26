<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TripController extends Controller
{
    /**
     * Construire la requête des voyages disponibles.
     */
    private function availableTripsQuery(): Builder
    {
        return Trip::query()
            ->with(['agency', 'vehicle', 'driver.user'])
            ->where('status', 'scheduled')
            ->where('departure_at', '>=', now());
    }

    /**
     * Accueil : afficher les trois prochains voyages.
     */
    public function home()
    {
        $popularTrips = $this->availableTripsQuery()
            ->orderBy('departure_at')
            ->limit(3)
            ->get();

        return view('welcome', compact('popularTrips'));
    }

    /**
     * Liste complète avec recherche et pagination.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'departure' => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:100'],
            'departure_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $query = $this->availableTripsQuery();

        if (! empty($filters['departure'])) {
            $query->where(
                'departure_city',
                'like',
                '%' . trim($filters['departure']) . '%'
            );
        }

        if (! empty($filters['destination'])) {
            $query->where(
                'arrival_city',
                'like',
                '%' . trim($filters['destination']) . '%'
            );
        }

        if (! empty($filters['departure_date'])) {
            $query->whereDate(
                'departure_at',
                $filters['departure_date']
            );
        }

        $trips = $query
            ->orderBy('departure_at')
            ->paginate(9)
            ->withQueryString();

        return view('trips.index', compact('trips'));
    }

    /**
     * Afficher les détails d'un voyage encore disponible.
     */
    public function show(Trip $trip)
    {
        if (
            $trip->status !== 'scheduled'
            || Carbon::parse($trip->departure_at)->isPast()
        ) {
            return redirect()
                ->route('trips.index')
                ->with(
                    'error',
                    'Ce voyage n’est plus disponible à la réservation.'
                );
        }

        $trip->load([
            'agency',
            'vehicle',
            'vehicle.seats',
            'driver.user',
        ]);

        return view('trips.show', compact('trip'));
    }
}
