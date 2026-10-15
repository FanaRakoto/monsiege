<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\ReservationSeat;
use App\Models\Trip;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    /**
     * Display the seat map for a trip.
     */
    public function create(Request $request, Trip $trip)
    {
        abort_unless(
            $trip->status === 'scheduled' && $trip->departure_at->isFuture(),
            404
        );

        $validated = $request->validate([
            'passengers' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $passengerCount = (int) ($validated['passengers'] ?? 1);

        $trip->load([
            'agency',
            'vehicle.seats' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('row_number')
                    ->orderBy('column_number');
            },
            'driver.user',
        ]);

        $seats = $trip->vehicle->seats;
        $seatIds = $seats->pluck('id');

        $reservedSeats = ReservationSeat::query()
            ->where('trip_id', $trip->id)
            ->whereIn('seat_id', $seatIds)
            ->where(function ($query) {
                $query->where('status', 'confirmed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'locked')
                            ->where('locked_until', '>', now());
                    });
            })
            ->get()
            ->keyBy('seat_id');

        $seatCount = $seats->count();
        $occupiedSeatCount = $reservedSeats->count();
        $availableSeatCount = max(0, $seatCount - $occupiedSeatCount);

        return view('reservations.create', [
            'trip' => $trip,
            'seats' => $seats,
            'reservedSeats' => $reservedSeats,
            'seatCount' => $seatCount,
            'occupiedSeatCount' => $occupiedSeatCount,
            'availableSeatCount' => $availableSeatCount,
            'passengerCount' => $passengerCount,
            'canBook' => $passengerCount <= $availableSeatCount,
        ]);
    }

    /**
     * Validate the selected seats and lock them for ten minutes.
     */
    public function storeSelection(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'seat_ids' => ['required', 'array', 'min:1', 'max:20'],
            'seat_ids.*' => ['required', 'uuid', 'distinct'],
            'passengers' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $seatIds = collect($validated['seat_ids'])->unique()->values();
        $passengerCount = (int) $validated['passengers'];

        if ($seatIds->count() !== $passengerCount) {
            return back()->withErrors([
                'seat_ids' => 'Le nombre de sièges sélectionnés doit correspondre au nombre de passagers.',
            ])->withInput();
        }

        abort_unless(
            $trip->status === 'scheduled' && $trip->departure_at->isFuture(),
            404
        );

        $trip->loadMissing('vehicle');

        $validSeatCount = $trip->vehicle->seats()
            ->where('is_active', true)
            ->whereIn('id', $seatIds)
            ->count();

        if ($validSeatCount !== $seatIds->count()) {
            return back()->withErrors([
                'seat_ids' => 'Certains sièges ne correspondent pas à ce véhicule ou ne sont plus actifs.',
            ])->withInput();
        }

        try {
            $result = DB::transaction(function () use ($request, $trip, $seatIds, $passengerCount) {
                $now = now();

                // Remove expired locks for this trip, freeing the unique trip/seat pair.
                $expiredRows = ReservationSeat::query()
                    ->where('trip_id', $trip->id)
                    ->where('status', 'locked')
                    ->where(function ($query) use ($now) {
                        $query->whereNull('locked_until')
                            ->orWhere('locked_until', '<=', $now);
                    })
                    ->lockForUpdate()
                    ->get(['id', 'reservation_id']);

                $expiredReservationIds = $expiredRows
                    ->pluck('reservation_id')
                    ->unique()
                    ->values();

                if ($expiredReservationIds->isNotEmpty()) {
                    ReservationSeat::query()
                        ->whereIn('reservation_id', $expiredReservationIds)
                        ->where('status', 'locked')
                        ->delete();

                    Reservation::query()
                        ->whereIn('id', $expiredReservationIds)
                        ->where('status', 'pending')
                        ->update(['status' => 'expired']);
                }

                // Locate the reservation previously created by this visitor's session.
                $previousId = $request->session()->get('monsiege.reservation_id');
                $previousReservation = null;

                if ($previousId) {
                    $previousReservation = Reservation::query()
                        ->whereKey($previousId)
                        ->where('trip_id', $trip->id)
                        ->lockForUpdate()
                        ->first();

                    if (
                        !$previousReservation
                        || $previousReservation->status !== 'pending'
                        || !$previousReservation->expires_at
                        || $previousReservation->expires_at->lessThanOrEqualTo($now)
                    ) {
                        $previousReservation = null;
                    }
                }

                // Re-check requested seats against current database state.
                $occupiedQuery = ReservationSeat::query()
                    ->where('trip_id', $trip->id)
                    ->whereIn('seat_id', $seatIds)
                    ->where(function ($query) use ($now) {
                        $query->where('status', 'confirmed')
                            ->orWhere(function ($query) use ($now) {
                                $query->where('status', 'locked')
                                    ->where('locked_until', '>', $now);
                            });
                    });

                if ($previousReservation) {
                    $occupiedQuery->where('reservation_id', '!=', $previousReservation->id);
                }

                if ($occupiedQuery->exists()) {
                    return [
                        'error' => 'Un siège vient d’être réservé ou bloqué par un autre client. Actualisez le plan et réessayez.',
                    ];
                }

                // Release the visitor's previous temporary selection on this trip.
                if ($previousReservation) {
                    ReservationSeat::query()
                        ->where('reservation_id', $previousReservation->id)
                        ->where('status', 'locked')
                        ->delete();

                    $previousReservation->update([
                        'status' => 'cancelled',
                        'cancelled_at' => $now,
                    ]);
                }

                $expiresAt = $now->copy()->addMinutes(10);

                $reservation = Reservation::create([
                    'user_id' => $request->user()?->id,
                    'trip_id' => $trip->id,
                    'reservation_code' => 'MS-' . $now->format('Ymd') . '-' . Str::upper(Str::random(8)),
                    'passenger_count' => $passengerCount,
                    'total_amount' => (int) $trip->price * $seatIds->count(),
                    'status' => 'pending',
                    'expires_at' => $expiresAt,
                ]);

                foreach ($seatIds as $seatId) {
                    ReservationSeat::create([
                        'reservation_id' => $reservation->id,
                        'trip_id' => $trip->id,
                        'seat_id' => $seatId,
                        'price' => $trip->price,
                        'status' => 'locked',
                        'locked_until' => $expiresAt,
                    ]);
                }

                return ['reservation' => $reservation];
            }, attempts: 3);

            if (isset($result['error'])) {
                return back()->withErrors([
                    'seat_ids' => $result['error'],
                ])->withInput();
            }

            /** @var Reservation $reservation */
            $reservation = $result['reservation'];

            $request->session()->put('monsiege.reservation_id', $reservation->id);

            return redirect()->route('reservations.passengers', [
                'trip' => $trip,
            ]);
        } catch (QueryException $exception) {
            // MySQL duplicate-key errors commonly use SQLSTATE 23000 / driver code 1062.
            $driverCode = $exception->errorInfo[1] ?? null;

            if ((string) $driverCode === '1062') {
                return back()->withErrors([
                    'seat_ids' => 'Un siège vient d’être pris par un autre client. Actualisez le plan et réessayez.',
                ])->withInput();
            }

            throw $exception;
        }
    }

    /**
     * Display passenger details for the temporary reservation.
     */
    public function passengers(Request $request, Trip $trip)
    {
        $reservationId = $request->session()->get('monsiege.reservation_id');

        $reservation = Reservation::query()
            ->with(['reservationSeats.seat', 'trip'])
            ->whereKey($reservationId)
            ->where('trip_id', $trip->id)
            ->first();

        if (
            !$reservation
            || $reservation->status !== 'pending'
            || !$reservation->expires_at
            || $reservation->expires_at->isPast()
        ) {
            if ($reservation && $reservation->status === 'pending') {
                DB::transaction(function () use ($reservation) {
                    ReservationSeat::query()
                        ->where('reservation_id', $reservation->id)
                        ->where('status', 'locked')
                        ->delete();

                    $reservation->update(['status' => 'expired']);
                });
            }

            $request->session()->forget('monsiege.reservation_id');

            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Votre sélection a expiré. Veuillez choisir de nouveau vos sièges.',
                ]);
        }

        $trip->load([
            'agency',
            'vehicle',
            'driver.user',
        ]);

        $seats = $reservation->reservationSeats
            ->pluck('seat')
            ->filter()
            ->sortBy(fn ($seat) => sprintf(
                '%04d-%04d',
                $seat->row_number,
                $seat->column_number
            ))
            ->values();

        if ($seats->count() !== $reservation->passenger_count) {
            return redirect()
                ->route('reservations.create', ['trip' => $trip])
                ->withErrors([
                    'reservation' => 'Les sièges de cette réservation ne sont plus valides. Veuillez recommencer.',
                ]);
        }

        return view('reservations.passengers', [
            'trip' => $trip,
            'reservation' => $reservation,
            'seats' => $seats,
            'passengerCount' => $reservation->passenger_count,
            'reservationUser' => $request->user(),
        ]);
    }
}