<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Afficher les voyages disponibles et gérer la recherche.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'departure' => [
                'nullable',
                'string',
                'max:100',
            ],

            'destination' => [
                'nullable',
                'string',
                'max:100',
            ],

            'departure_date' => [
                'nullable',
                'date',
            ],

            'passengers' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
        ]);

        $trips = Trip::query()
            ->with([
                'agency',
                'vehicle',
                'driver.user',
            ])
            ->where('status', 'scheduled')

            // Départ
            ->when(
                $validated['departure'] ?? null,
                function ($query, $departure) {
                    $query->where(
                        'departure_city',
                        'like',
                        '%' . trim($departure) . '%'
                    );
                }
            )

            // Destination
            ->when(
                $validated['destination'] ?? null,
                function ($query, $destination) {
                    $query->where(
                        'arrival_city',
                        'like',
                        '%' . trim($destination) . '%'
                    );
                }
            )

            // Date du voyage
            ->when(
                $validated['departure_date'] ?? null,
                function ($query, $date) {
                    $query->whereDate('departure_at', $date);
                }
            )

            // Uniquement les voyages futurs
            ->where('departure_at', '>=', now())

            ->orderBy('departure_at', 'asc')

            ->paginate(6)

            ->withQueryString();

        return view('trips.index', [
            'trips' => $trips,
            'search' => $validated,
        ]);
    }

    /**
     * Afficher les détails d'un voyage.
     */
    public function show(Trip $trip)
    {
        $trip->load([
            'agency',
            'vehicle',
            'vehicle.seats',
            'driver.user',
        ]);

        return view('trips.show', [
            'trip' => $trip,
        ]);
    }
}