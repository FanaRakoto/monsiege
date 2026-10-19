<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Afficher le billet du client qui a effectué la réservation.
     */
    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load([
            'reservation.trip.agency',
            'reservation.trip.vehicle',
            'reservation.trip.driver.user',
            'reservation.passengers',
            'reservation.reservationSeats.seat',
        ]);

        abort_unless(
            (string) $ticket->reservation->user_id
                === (string) $request->user()->id,
            403,
            'Vous ne pouvez pas consulter le billet d’un autre client.'
        );

        // Le QR Code contient une URL de vérification, pas les données personnelles.
        $verificationUrl = route('tickets.verify', [
            'token' => $ticket->qr_token,
        ]);

        $qrCode = (new QRCode())->render($verificationUrl);

        return view('tickets.show', [
            'ticket' => $ticket,
            'qrCode' => $qrCode,
        ]);
    }

    /**
     * Vérifier un billet : accès réservé à l'agence concernée
     * ou au chauffeur affecté au voyage.
     */
    public function verify(Request $request, string $token)
    {
        $ticket = Ticket::query()
            ->where('qr_token', $token)
            ->firstOrFail();

        $ticket->load([
            'reservation.trip.agency',
            'reservation.trip.driver.user',
            'reservation.trip.vehicle',
            'reservation.passengers',
            'reservation.reservationSeats.seat',
        ]);

        $this->authorizeStaff($request->user(), $ticket);

        return view('tickets.verify', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Marquer un billet valide comme utilisé.
     */
    public function check(Request $request, string $token)
    {
        DB::transaction(function () use ($request, $token) {
            $ticket = Ticket::query()
                ->where('qr_token', $token)
                ->lockForUpdate()
                ->firstOrFail();

            $ticket->load([
                'reservation.trip.agency',
                'reservation.trip.driver.user',
            ]);

            $this->authorizeStaff($request->user(), $ticket);

            abort_unless(
                $ticket->status === 'valid',
                409,
                'Ce billet a déjà été utilisé ou annulé.'
            );

            $ticket->update([
                'status' => 'used',
                'checked_at' => now(),
            ]);
        });

        return redirect()
            ->route('tickets.verify', ['token' => $token])
            ->with('ticket_used', true);
    }

    /**
     * Autoriser uniquement l'agence propriétaire du voyage
     * ou le chauffeur affecté à ce voyage.
     */
    private function authorizeStaff(User $user, Ticket $ticket): void
    {
        $ticket->loadMissing([
            'reservation.trip.agency',
            'reservation.trip.driver.user',
        ]);

        $trip = $ticket->reservation->trip;

        $allowed = false;

        if ($user->isAgency()) {
            $allowed = $trip->agency !== null
                && (string) $trip->agency->owner_user_id
                === (string) $user->id;
        } elseif ($user->isDriver()) {
            $allowed = $trip->driver !== null
                && (string) $trip->driver->user_id
                === (string) $user->id;
        }

        abort_unless(
            $allowed,
            403,
            'Seuls l’agence concernée ou le chauffeur affecté peuvent vérifier ce billet.'
        );
    }
}
