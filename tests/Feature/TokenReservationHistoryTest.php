<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Proovit\FilamentProovit\Listeners\RecordTokenReservationHistory;
use Proovit\FilamentProovit\Models\TokenReservation;
use Proovit\LaravelProovit\DTOs\TokenReservationData;
use Proovit\LaravelProovit\Events\Tokens\TokenReserved;

uses(RefreshDatabase::class);

it('persists token reservations locally when the sdk dispatches the event', function (): void {
    $listener = new RecordTokenReservationHistory;

    $listener->handle(new TokenReserved(
        new TokenReservationData(
            reservationId: 'reservation-uuid',
            status: 'held',
            raw: [
                'reservation_id' => 'reservation-uuid',
                'status' => 'held',
            ],
        ),
        [
            'reservation_id' => 'reservation-uuid',
            'status' => 'held',
        ],
    ));

    $reservation = TokenReservation::query()->first();

    expect($reservation)->not->toBeNull()
        ->and($reservation?->reservation_id)->toBe('reservation-uuid')
        ->and($reservation?->status)->toBe('held');
});
