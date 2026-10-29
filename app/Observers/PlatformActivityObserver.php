<?php

namespace App\Observers;

use App\Models\Driver;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\PlatformActivityNotification;
use Illuminate\Database\Eloquent\Model;

/**
 * Envoie des notifications de base de données après des événements Eloquent.
 * Les événements console (seeders, commandes artisan, tests CLI) sont ignorés.
 */
class PlatformActivityObserver
{
    public function created(Model $model): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        if ($model instanceof User) {
            if ($model->role === 'client') {
                $this->notifyAdmins(
                    'new_client',
                    'Nouveau client inscrit',
                    'Un nouveau compte client vient d’être créé.',
                    'fa-user-plus',
                    '/admin/utilisateurs'
                );
            }
            return;
        }

        if ($model instanceof Trip) {
            $departure = (string) $model->getAttribute('departure_city');
            $arrival = (string) $model->getAttribute('arrival_city');
            $routeName = trim($departure . ' → ' . $arrival, ' →');
            $this->notifyAdmins(
                'new_trip',
                'Nouveau voyage créé',
                $routeName !== '' ? "Un voyage a été créé : {$routeName}." : 'Un nouveau voyage a été créé.',
                'fa-route',
                '/admin/voyages'
            );
            return;
        }

        if ($model instanceof Vehicle) {
            $vehicleLabel = $this->firstNonEmpty($model, [
                'brand', 'make', 'model', 'name', 'registration_number', 'registration', 'plate_number',
            ]) ?? 'Un véhicule';
            $this->notifyAdmins(
                'new_vehicle',
                'Nouveau véhicule ajouté',
                "{$vehicleLabel} a été ajouté à la flotte.",
                'fa-bus',
                '/admin/vehicules'
            );
            return;
        }

        if ($model instanceof Driver) {
            $driverUserId = $model->getAttribute('user_id');
            $driverUser = $driverUserId ? User::find($driverUserId) : null;
            $driverLabel = $driverUser?->name ?? $this->firstNonEmpty($model, ['name', 'full_name']) ?? 'Un chauffeur';
            $this->notifyAdmins(
                'new_driver',
                'Nouveau chauffeur enregistré',
                "Le profil de {$driverLabel} a été créé.",
                'fa-id-card',
                '/admin/utilisateurs'
            );
            return;
        }

        if ($model instanceof Reservation) {
            $this->notifyAdmins(
                'new_reservation',
                'Nouvelle réservation',
                'Une réservation vient d’être enregistrée sur MonSiège.',
                'fa-ticket',
                '/admin/reservations'
            );
            $this->notifyReservationClient($model, 'reservation_created', 'Réservation enregistrée', 'Votre réservation a bien été enregistrée.', '/client/dashboard');
            return;
        }

        if ($model instanceof Payment) {
            $this->notifyAdmins(
                'new_payment',
                'Nouvelle opération de paiement',
                'Une opération de paiement a été enregistrée. Vérifiez son statut dans le suivi des réservations.',
                'fa-credit-card',
                '/admin/reservations'
            );
            $reservation = $this->reservationFor($model);
            if ($reservation) {
                $this->notifyReservationClient($reservation, 'payment_recorded', 'Mise à jour du paiement', 'Une opération de paiement a été enregistrée pour votre réservation.', '/client/dashboard');
            }
            return;
        }

        if ($model instanceof Ticket) {
            $reservation = $this->reservationFor($model);
            if ($reservation) {
                $ticketId = (string) $model->getAttribute('id');
                $url = $ticketId !== '' ? '/billets/' . rawurlencode($ticketId) : '/client/dashboard';
                $this->notifyReservationClient($reservation, 'ticket_created', 'Votre billet est disponible', 'Votre billet a été généré. Ouvrez-le pour afficher votre QR Code.', $url);
            }
        }
    }

    public function updated(Model $model): void
    {
        if (app()->runningInConsole() || ! $model instanceof Trip || ! $model->wasChanged('status')) {
            return;
        }

        $status = (string) $model->getAttribute('status');
        if ($status !== 'cancelled') {
            return;
        }

        $this->notifyAdmins(
            'trip_cancelled',
            'Voyage annulé',
            'Un voyage a été marqué comme annulé.',
            'fa-calendar-xmark',
            '/admin/voyages'
        );

        Reservation::query()
            ->where('trip_id', $model->getKey())
            ->whereNotNull('user_id')
            ->with('user')
            ->get()
            ->each(function (Reservation $reservation): void {
                $user = $reservation->user;
                if ($user instanceof User) {
                    $user->notify(new PlatformActivityNotification([
                        'event' => 'trip_cancelled',
                        'title' => 'Voyage annulé',
                        'message' => 'Le voyage associé à votre réservation a été annulé. Contactez l’agence pour les informations sur la suite.',
                        'icon' => 'fa-calendar-xmark',
                        'url' => '/client/dashboard',
                    ]));
                }
            });
    }

    private function notifyAdmins(string $event, string $title, string $message, string $icon, string $url): void
    {
        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(fn (User $admin) => $admin->notify(new PlatformActivityNotification([
                'event' => $event,
                'title' => $title,
                'message' => $message,
                'icon' => $icon,
                'url' => $url,
            ])));
    }

    private function notifyReservationClient(Reservation $reservation, string $event, string $title, string $message, string $url): void
    {
        $userId = $reservation->getAttribute('user_id');
        if (! $userId) {
            // Une réservation invitée n'a pas de compte destinataire : son suivi reste lié à sa session/billet.
            return;
        }

        $user = User::find($userId);
        if (! $user) {
            return;
        }

        $user->notify(new PlatformActivityNotification([
            'event' => $event,
            'title' => $title,
            'message' => $message,
            'icon' => 'fa-bell',
            'url' => $url,
        ]));
    }

    private function reservationFor(Model $model): ?Reservation
    {
        $reservationId = $model->getAttribute('reservation_id');
        return $reservationId ? Reservation::find($reservationId) : null;
    }

    /** Return the first non-empty available attribute without assuming a specific vehicle schema. */
    private function firstNonEmpty(Model $model, array $attributes): ?string
    {
        foreach ($attributes as $attribute) {
            $value = trim((string) $model->getAttribute($attribute));
            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }
}