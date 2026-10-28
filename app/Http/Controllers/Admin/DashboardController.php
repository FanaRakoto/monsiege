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
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Every admin endpoint re-checks the role, in addition to the auth middleware. */
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
            ['label' => 'Utilisateurs', 'value' => User::count(), 'description' => 'Comptes inscrits'],
            ['label' => 'Agences', 'value' => Agency::count(), 'description' => 'Agences enregistrées'],
            ['label' => 'Véhicules', 'value' => Vehicle::count(), 'description' => 'Véhicules référencés'],
            ['label' => 'Voyages à venir', 'value' => Trip::query()->where('status', 'scheduled')->where('departure_at', '>=', now())->count(), 'description' => 'Départs programmés'],
            ['label' => 'Réservations', 'value' => Reservation::count(), 'description' => 'Réservations créées'],
            ['label' => 'Paiements', 'value' => Payment::count(), 'description' => 'Transactions enregistrées'],
            ['label' => 'Billets émis', 'value' => Ticket::count(), 'description' => 'Billets avec QR Code'],
        ];

        // Aggregations are based on the actual values stored in the database.
        $usersByRole = User::query()
            ->select('role')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('role')
            ->get()
            ->map(static fn ($row): array => [
                'label' => ucfirst(str_replace('_', ' ', (string) ($row->role ?: 'non défini'))),
                'value' => (int) $row->total,
            ])
            ->values()
            ->all();

        $reservationsByStatus = Reservation::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(static fn ($row): array => [
                'label' => ucfirst(str_replace('_', ' ', (string) ($row->status ?: 'non défini'))),
                'value' => (int) $row->total,
            ])
            ->values()
            ->all();

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

        return view('admin.dashboard', compact(
            'stats',
            'usersByRole',
            'reservationsByStatus',
            'recentTrips',
            'recentReservations'
        ));
    }

    public function trips(): View
    {
        $this->authorizeAdministrator();

        $trips = Trip::query()
            ->with(['agency', 'vehicle', 'driver.user'])
            ->orderByDesc('departure_at')
            ->paginate(4)
            ->withQueryString();

        return view('admin.trips', compact('trips'));
    }

    public function reservations(): View
    {
        $this->authorizeAdministrator();

        $reservations = Reservation::query()
            ->with(['trip', 'ticket'])
            ->latest()
            ->paginate(4)
            ->withQueryString();

        return view('admin.reservations', compact('reservations'));
    }

    public function users(): View
    {
        $this->authorizeAdministrator();

        $users = User::query()
            ->orderBy('name')
            ->paginate(4)
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
            ->paginate(4)
            ->withQueryString();

        return view('admin.vehicles', compact('vehicles'));
    }
}