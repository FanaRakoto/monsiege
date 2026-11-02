<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TicketPdfController extends Controller
{
    /**
     * Download a ticket PDF only for its authenticated owner or the guest
     * browser session that received the ticket after confirmation.
     */
    public function download(Request $request, Ticket $ticket)
    {
        $ticket->load([
            'reservation.trip.agency',
            'reservation.trip.vehicle',
            'reservation.trip.driver.user',
            'reservation.passengers',
            'reservation.reservationSeats.seat',
        ]);

        $reservation = $ticket->reservation;
        abort_unless($reservation !== null, 404, 'Réservation introuvable.');

        $user = $request->user();
        $isAuthenticatedOwner = $user !== null
            && $reservation->user_id !== null
            && hash_equals((string) $reservation->user_id, (string) $user->id);

        $sessionTicketId = (string) $request->session()->get('monsiege.ticket_id', '');
        $isGuestOwner = $reservation->user_id === null
            && $sessionTicketId !== ''
            && hash_equals($sessionTicketId, (string) $ticket->id);

        abort_unless(
            $isAuthenticatedOwner || $isGuestOwner,
            403,
            'Vous ne pouvez pas télécharger le billet d’une autre réservation.'
        );

        $trip = $reservation->trip;
        abort_unless($trip !== null, 404, 'Voyage introuvable.');

        $verificationUrl = route('tickets.verify', ['token' => $ticket->qr_token]);
        $qrCode = (new QRCode())->render($verificationUrl);

        $reservationSeats = $reservation->reservationSeats
            ->sortBy(fn ($reservationSeat) => sprintf(
                '%04d-%04d-%s',
                $reservationSeat->seat?->row_number ?? 9999,
                $reservationSeat->seat?->column_number ?? 9999,
                $reservationSeat->seat?->seat_number ?? ''
            ))
            ->values();

        $passengerRows = $reservation->passengers->values()->map(
            fn ($passenger, $index) => (object) [
                'passenger' => $passenger,
                'seat' => $reservationSeats->get($index)?->seat,
            ]
        );

        // Le logo est intégré en base64 pour rester visible dans Dompdf sans accès réseau.
        $logoData = null;
        $logoCandidates = [
            public_path('images/monsiege-icone-anime.png'),
            public_path('images/monsiege-logo.png'),
            public_path('image/monsiege-logo.png'),
        ];

        foreach ($logoCandidates as $logoPath) {
            if (is_file($logoPath) && is_readable($logoPath)) {
                $logoMime = mime_content_type($logoPath) ?: 'image/png';
                $logoData = 'data:' . $logoMime . ';base64,' . base64_encode((string) file_get_contents($logoPath));
                break;
            }
        }

        $pdf = Pdf::loadView('tickets.pdf', [
            'logoData' => $logoData,
            'ticket' => $ticket,
            'reservation' => $reservation,
            'trip' => $trip,
            'vehicle' => $trip->vehicle,
            'agency' => $trip->agency,
            'driver' => $trip->driver?->user,
            'receiverName' => $trip->receiver_name,
            'issuedAtDisplay' => $ticket->issued_at
                ? Carbon::parse($ticket->issued_at)->format('d/m/Y à H:i')
                : ($ticket->created_at ? Carbon::parse($ticket->created_at)->format('d/m/Y à H:i') : 'Non renseignée'),
            'passengerRows' => $passengerRows,
            'qrCode' => $qrCode,
        ])->setPaper('a4', 'portrait');

        $safeTicketNumber = preg_replace('/[^A-Za-z0-9_-]/', '-', $ticket->ticket_number);

        return $pdf->download('billet-monsiege-' . ($safeTicketNumber ?: $ticket->id) . '.pdf');
    }
}