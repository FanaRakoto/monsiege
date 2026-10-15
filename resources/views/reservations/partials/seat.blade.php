@php
$isLocked = $isReserved && $reservationStatus === 'locked';
$isConfirmed = $isReserved && $reservationStatus === 'confirmed';
@endphp

@if(!$isReserved)

<button
    type="button"
    class="seat-button"
    data-seat-id="{{ $seat->id }}"
    data-seat-number="{{ $seat->seat_number }}"
    data-price="{{ $trip->price }}"
    aria-label="Choisir le siège {{ $seat->seat_number }}">

    <span class="seat-back"></span>

    <span class="seat-cushion">

        <span class="seat-number">
            {{ $seat->seat_number }}
        </span>

    </span>

    @if($seat->is_window)

    <span class="seat-window">
        •
    </span>

    @endif

</button>

@else

<div
    class="seat-button
            {{ $isLocked ? 'seat-is-locked' : 'seat-is-occupied' }}"
    aria-label="Siège {{ $seat->seat_number }} indisponible">

    <span class="seat-back"></span>

    <span class="seat-cushion">

        <span class="seat-number">
            {{ $seat->seat_number }}
        </span>

    </span>

    <span class="seat-status-icon">
        {{ $isLocked ? '⏳' : '×' }}
    </span>

</div>

@endif