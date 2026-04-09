<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Models;

use Illuminate\Database\Eloquent\Model;

final class TokenReservation extends Model
{
    protected $table = 'proovit_token_reservations';

    protected $fillable = [
        'fingerprint',
        'reservation_id',
        'status',
        'response',
    ];

    protected $casts = [
        'response' => 'array',
    ];
}
