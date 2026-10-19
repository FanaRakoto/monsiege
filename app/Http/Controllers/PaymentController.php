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
     */
    public function show(Request $request, Trip $trip)
    {
        // Le paiement simulé doit rester réservé au développement.
        abort_unless(app()->environment('local'), 404);

        $reservationId = $request->session()->get(
            'monsiege.reservation_id'
        );

        $reservation = Reservation::query()
            ->with([
                'passengers',
                'reservationSeats.seat',
                'latestPayment',
                'ticket',
            ])
            ->whereKey($reservationId)
            ->where('trip_id', $trip->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$reservation) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Réservation introuvable. Veuillez recommencer.',
                ]);
        }

        // Si le billet existe déjà, ne pas redemander le paiement.
        if (
            $reservation->status === 'confirmed'
            && $reservation->ticket
        ) {
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

        // Les informations des passagers doivent être complètes.
        if (
            $reservation->passengers->count()
            !== $reservation->passenger_count
        ) {
            return redirect()
                ->route('reservations.passengers', ['trip' => $trip])
                ->withErrors([
                    'passengers' => 'Veuillez compléter les informations de tous les passagers.',
                ]);
        }

        // Tous les sièges doivent encore être verrouillés pour cette réservation.
        $seatsAreValid =
            $reservation->reservationSeats->count()
            === $reservation->passenger_count
            && $reservation->reservationSeats->every(
                fn($seat) =>
                $seat->status === 'locked'
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
     */
    public function simulate(Request $request, Trip $trip)
    {
        // Cette route ne doit jamais simuler un paiement en production.
        abort_unless(app()->environment('local'), 404);

        $validated = $request->validate([
            'result' => ['required', 'in:success,failed'],
        ]);

        $reservationId = $request->session()->get(
            'monsiege.reservation_id'
        );

        if (!$reservationId) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Aucune réservation active.',
                ]);
        }

        $ticket = DB::transaction(function () use (
            $request,
            $trip,
            $reservationId,
            $validated
        ) {
            // Verrouiller la réservation pendant le traitement.
            $reservation = Reservation::query()
                ->whereKey($reservationId)
                ->where('trip_id', $trip->id)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Éviter de créer deux billets pour la même réservation.
            if ($reservation->status === 'confirmed') {
                $existingTicket = $reservation->ticket()->first();

                if ($existingTicket) {
                    return $existingTicket;
                }

                abort(409, 'La réservation est déjà confirmée.');
            }

            if (
                $reservation->status !== 'pending'
                || !$reservation->expires_at
                || !$reservation->expires_at->isFuture()
            ) {
                abort(
                    409,
                    'La réservation a expiré. Veuillez recommencer.'
                );
            }

            // Vérifier que les informations des passagers existent.
            if (
                $reservation->passengers()->count()
                !== $reservation->passenger_count
            ) {
                abort(
                    409,
                    'Les informations des passagers sont incomplètes.'
                );
            }

            // Recontrôler les sièges à l'intérieur de la transaction.
            $seats = ReservationSeat::query()
                ->where('reservation_id', $reservation->id)
                ->where('trip_id', $trip->id)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== $reservation->passenger_count) {
                abort(
                    409,
                    'Le nombre de sièges ne correspond plus à la réservation.'
                );
            }

            foreach ($seats as $seat) {
                if (
                    $seat->status !== 'locked'
                    || !$seat->locked_until
                    || !$seat->locked_until->isFuture()
                ) {
                    abort(
                        409,
                        'Un siège a expiré ou n’est plus disponible.'
                    );
                }
            }

            $successful = $validated['result'] === 'success';

            // Il s'agit d'un paiement de démonstration, pas d'un transfert réel.
            Payment::create([
                'reservation_id' => $reservation->id,
                'method' => 'simulation',
                'provider' => 'MonSiege Demo',
                'transaction_reference' => 'SIM-'
                    . Str::upper(Str::random(24)),
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

            // Un échec ne confirme ni la réservation ni les sièges.
            if (!$successful) {
                return null;
            }

            // Confirmer la réservation.
            $reservation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Confirmer les sièges déjà verrouillés.
            ReservationSeat::query()
                ->whereIn('id', $seats->modelKeys())
                ->update([
                    'status' => 'confirmed',
                    'locked_until' => null,
                ]);

            // Créer le billet seulement après confirmation.
            return Ticket::create([
                'reservation_id' => $reservation->id,
                'ticket_number' => 'MS-'
                    . now()->format('Ymd')
                    . '-'
                    . Str::upper(Str::random(10)),
                'status' => 'valid',
                'issued_at' => now(),
            ]);
        });

        if ($ticket) {
            $request->session()->forget('monsiege.reservation_id');

            return redirect()->route('tickets.show', [
                'ticket' => $ticket->id,
            ])->with('payment_success', true);
        }

        // Le client peut réessayer tant que la réservation n'a pas expiré.
        return redirect()
            ->route('reservations.payment.show', ['trip' => $trip])
            ->with('payment_failed', true);
    }
}
