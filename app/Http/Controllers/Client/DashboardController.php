<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Réservations actives dont le départ est encore à venir.
        $upcomingReservations = Reservation::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('trip', function ($query) {
                $query->where('departure_at', '>=', now());
            })
            ->with(['trip', 'ticket'])
            ->orderByDesc('created_at')
            ->get();

        // Historique : anciens trajets et réservations annulées ou expirées.
        $historyReservations = Reservation::query()
            ->where('user_id', $user->id)
            ->with(['trip', 'ticket'])
            ->where(function ($query) {
                $query->whereIn('status', [
                    'cancelled',
                    'expired',
                    'completed',
                ])->orWhereHas('trip', function ($tripQuery) {
                    $tripQuery->where('departure_at', '<', now());
                });
            })
            ->orderByDesc('created_at')
            ->get();

        // Envoyer les deux listes à la vue Blade.
        return view('client.dashboard', [
            'upcomingReservations' => $upcomingReservations,
            'historyReservations' => $historyReservations,
        ]);
    }
}
