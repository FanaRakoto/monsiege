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
     * Afficher le billet de son propriétaire ou le billet associé à la
     * session invitée ayant effectué la réservation.
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

        $reservation = $ticket->reservation;
        $user = $request->user();

        $isAuthenticatedOwner = $user !== null
            && $reservation->user_id !== null
            && (string) $reservation->user_id === (string) $user->id;

        $sessionTicketId = (string) $request->session()->get('monsiege.ticket_id', '');
        $isGuestOwner = $reservation->user_id === null
            && $sessionTicketId !== ''
            && hash_equals($sessionTicketId, (string) $ticket->id);

        abort_unless(
            $isAuthenticatedOwner || $isGuestOwner,
            403,
            'Vous ne pouvez pas consulter ce billet.'
        );

        // Le QR Code contient une URL et un jeton opaque, pas les données personnelles.
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
     * Afficher le résultat du scan à l'agence propriétaire du voyage
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

        $user = $request->user();
        abort_unless($user instanceof User, 403);
        $this->authorizeStaff($user, $ticket);

        return view('tickets.verify', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Marquer un billet valide comme utilisé. Le verrouillage empêche deux
     * contrôles simultanés d'accepter silencieusement le même billet.
     */
    public function check(Request $request, string $token)
    {
        DB::transaction(function () use ($request, $token): void {
            $ticket = Ticket::query()
                ->where('qr_token', $token)
                ->lockForUpdate()
                ->firstOrFail();

            $ticket->load([
                'reservation.trip.agency',
                'reservation.trip.driver.user',
            ]);

            $user = $request->user();
            abort_unless($user instanceof User, 403);
            $this->authorizeStaff($user, $ticket);

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
     * Seuls l'agence propriétaire du voyage et le chauffeur affecté
     * peuvent consulter le détail de vérification et valider le billet.
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
                && (string) $trip->agency->owner_user_id === (string) $user->id;
        } elseif ($user->isDriver()) {
            $allowed = $trip->driver !== null
                && (string) $trip->driver->user_id === (string) $user->id;
        }

        abort_unless(
            $allowed,
            403,
            'Seuls l’agence concernée ou le chauffeur affecté peuvent vérifier ce billet.'
        );
    }
}