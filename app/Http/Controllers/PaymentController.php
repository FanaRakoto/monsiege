<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationSeat;
use App\Models\Ticket;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Afficher la page de paiement simulé.
     * Le mode de paiement simulé reste réservé à l'environnement local.
     */
    public function show(Request $request, Trip $trip)
    {
        abort_unless(app()->environment('local'), 404);

        $reservationId = $request->session()->get('monsiege.reservation_id');

        $reservation = Reservation::query()
            ->with([
                'passengers',
                'reservationSeats.seat',
                'latestPayment',
                'ticket',
            ])
            ->whereKey($reservationId)
            ->where('trip_id', $trip->id)
            ->first();

        if (!$reservation) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Réservation introuvable. Veuillez recommencer.',
                ]);
        }

        $this->authorizeSessionReservation($request, $reservation);

        // Si le billet existe déjà, ne pas redemander le paiement.
        if ($reservation->status === 'confirmed' && $reservation->ticket) {
            $request->session()->put('monsiege.ticket_id', $reservation->ticket->id);
            $request->session()->forget('monsiege.reservation_id');

            return redirect()->route('tickets.show', [
                'ticket' => $reservation->ticket->id,
            ]);
        }

        if (
            $reservation->status !== 'pending'
            || !$reservation->expires_at
            || !$reservation->expires_at->isFuture()
        ) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Votre réservation a expiré. Veuillez sélectionner de nouveaux sièges.',
                ]);
        }

        if ($reservation->passengers->count() !== $reservation->passenger_count) {
            return redirect()
                ->route('reservations.passengers', ['trip' => $trip])
                ->withErrors([
                    'passengers' => 'Veuillez compléter les informations de tous les passagers.',
                ]);
        }

        $seatsAreValid =
            $reservation->reservationSeats->count() === $reservation->passenger_count
            && $reservation->reservationSeats->every(
                fn ($seat) => $seat->status === 'locked'
                    && $seat->locked_until
                    && $seat->locked_until->isFuture()
            );

        if (!$seatsAreValid) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Les sièges sélectionnés ne sont plus disponibles. Veuillez recommencer.',
                ]);
        }

        return view('reservations.payment', [
            'trip' => $trip,
            'reservation' => $reservation,
        ]);
    }

    /**
     * Simuler la réussite ou l'échec d'un paiement.
     * Ce mécanisme n'effectue aucun transfert d'argent réel.
     */
    public function simulate(Request $request, Trip $trip)
    {
        abort_unless(app()->environment('local'), 404);

        $validated = $request->validate([
            'result' => ['required', 'in:success,failed'],
        ]);

        $reservationId = $request->session()->get('monsiege.reservation_id');

        if (!$reservationId) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Aucune réservation active.',
                ]);
        }

        $ticket = DB::transaction(function () use ($request, $trip, $reservationId, $validated) {
            $reservation = Reservation::query()
                ->whereKey($reservationId)
                ->where('trip_id', $trip->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->authorizeSessionReservation($request, $reservation);

            // Empêcher la création d'un deuxième billet pour la même réservation.
            if ($reservation->status === 'confirmed') {
                $existingTicket = $reservation->ticket()->first();

                if ($existingTicket) {
                    return $existingTicket;
                }

                abort(409, 'La réservation est déjà confirmée mais aucun billet n’a été trouvé.');
            }

            if (
                $reservation->status !== 'pending'
                || !$reservation->expires_at
                || !$reservation->expires_at->isFuture()
            ) {
                abort(409, 'La réservation a expiré. Veuillez recommencer.');
            }

            if ($reservation->passengers()->count() !== $reservation->passenger_count) {
                abort(409, 'Les informations des passagers sont incomplètes.');
            }

            $seats = ReservationSeat::query()
                ->where('reservation_id', $reservation->id)
                ->where('trip_id', $trip->id)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== $reservation->passenger_count) {
                abort(409, 'Le nombre de sièges ne correspond plus à la réservation.');
            }

            foreach ($seats as $seat) {
                if (
                    $seat->status !== 'locked'
                    || !$seat->locked_until
                    || !$seat->locked_until->isFuture()
                ) {
                    abort(409, 'Un siège a expiré ou n’est plus disponible.');
                }
            }

            $successful = $validated['result'] === 'success';

            Payment::create([
                'reservation_id' => $reservation->id,
                'method' => 'simulation',
                'provider' => 'MonSiege Demo',
                'transaction_reference' => 'SIM-' . Str::upper(Str::random(24)),
                'amount' => $reservation->total_amount,
                'currency' => $trip->currency ?: 'MGA',
                'status' => $successful ? 'successful' : 'failed',
                'paid_at' => $successful ? now() : null,
                'provider_response' => [
                    'mode' => 'simulation',
                    'result' => $validated['result'],
                    'environment' => app()->environment(),
                ],
                'notes' => 'Paiement simulé en environnement local.',
            ]);

            // En cas d'échec, ne confirmer ni la réservation ni les sièges.
            if (!$successful) {
                return null;
            }

            $reservation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            ReservationSeat::query()
                ->whereIn('id', $seats->modelKeys())
                ->update([
                    'status' => 'confirmed',
                    'locked_until' => null,
                ]);

            return Ticket::create([
                'reservation_id' => $reservation->id,
                'ticket_number' => 'MS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(10)),
                'status' => 'valid',
                'issued_at' => now(),
            ]);
        }, attempts: 3);

        if ($ticket) {
            // Conserver l'accès au billet pour la session invitée après paiement.
            $request->session()->put('monsiege.ticket_id', $ticket->id);
            $request->session()->forget('monsiege.reservation_id');

            return redirect()->route('tickets.show', [
                'ticket' => $ticket->id,
            ])->with('payment_success', true);
        }

        // La réservation reste dans la session : le client peut réessayer.
        return redirect()
            ->route('reservations.payment.show', ['trip' => $trip])
            ->with('payment_failed', true);
    }

    /**
     * Vérifier que l'utilisateur connecté ou la session invitée possède
     * la réservation concernée. La session seule ne permet pas d'accéder
     * à une réservation dont user_id appartient à un autre compte.
     */
    private function authorizeSessionReservation(Request $request, Reservation $reservation): void
    {
        $user = $request->user();

        $isAuthenticatedOwner = $user !== null
            && $reservation->user_id !== null
            && (string) $reservation->user_id === (string) $user->id;

        $sessionReservationId = (string) $request->session()->get('monsiege.reservation_id', '');
        $isGuestReservation = $reservation->user_id === null
            && $sessionReservationId !== ''
            && hash_equals($sessionReservationId, (string) $reservation->id);

        abort_unless(
            $isAuthenticatedOwner || $isGuestReservation,
            403,
            'Vous ne pouvez pas accéder à cette réservation.'
        );
    }
}