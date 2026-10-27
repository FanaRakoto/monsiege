<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Vérifie le rôle sur chacune des actions de cette zone.
     * Le middleware auth est également appliqué aux routes.
     */
    private function authorizeAdministrator(): void
    {
        abort_unless(
            auth()->check() && auth()->user()->role === 'admin',
            403,
            'Accès réservé à l’administrateur MonSiège.'
        );
    }

    public function index(): View
    {
        $this->authorizeAdministrator();

        $stats = [
            ['label' => 'Utilisateurs', 'value' => User::count(), 'description' => 'Tous les comptes'],
            ['label' => 'Agences', 'value' => Agency::count(), 'description' => 'Agences enregistrées'],
            ['label' => 'Véhicules', 'value' => Vehicle::count(), 'description' => 'Véhicules référencés'],
            ['label' => 'Voyages à venir', 'value' => Trip::query()->where('status', 'scheduled')->where('departure_at', '>=', now())->count(), 'description' => 'Départs programmés'],
            ['label' => 'Réservations', 'value' => Reservation::count(), 'description' => 'Toutes les réservations'],
            ['label' => 'Paiements', 'value' => Payment::count(), 'description' => 'Transactions enregistrées'],
            ['label' => 'Billets émis', 'value' => Ticket::count(), 'description' => 'Billets avec QR Code'],
        ];

        $recentTrips = Trip::query()
            ->with(['agency', 'vehicle'])
            ->orderByDesc('departure_at')
            ->limit(6)
            ->get();

        $recentReservations = Reservation::query()
            ->with(['trip', 'ticket'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTrips', 'recentReservations'));
    }

    public function trips(): View
    {
        $this->authorizeAdministrator();

        $trips = Trip::query()
            ->with(['agency', 'vehicle', 'driver.user'])
            ->orderByDesc('departure_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.trips', compact('trips'));
    }

    public function reservations(): View
    {
        $this->authorizeAdministrator();

        $reservations = Reservation::query()
            ->with(['trip', 'ticket'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reservations', compact('reservations'));
    }

    public function users(): View
    {
        $this->authorizeAdministrator();

        $users = User::query()
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function vehicles(): View
    {
        $this->authorizeAdministrator();

        $vehicles = Vehicle::query()
            ->with('agency')
            ->withCount('seats')
            ->orderBy('brand')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vehicles', compact('vehicles'));
    }
}
