<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Listeners;

use Proovit\FilamentProovit\Models\TokenReservation;
use Proovit\LaravelProovit\Events\Tokens\TokenReserved;

final class RecordTokenReservationHistory
{
    public function handle(TokenReserved $event): void
    {
        TokenReservation::query()->updateOrCreate(
            ['fingerprint' => $this->fingerprint($event)],
            [
                'reservation_id' => $event->reservation->reservationId,
                'status' => $event->reservation->status,
                'response' => $event->response,
            ],
        );
    }

    private function fingerprint(TokenReserved $event): string
    {
        $reservationId = trim((string) $event->reservation->reservationId);

        if ($reservationId !== '') {
            return $reservationId;
        }

        return sha1(json_encode($event->response, JSON_THROW_ON_ERROR));
    }
}
