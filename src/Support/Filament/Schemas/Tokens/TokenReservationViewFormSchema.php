<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas\Tokens;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

final class TokenReservationViewFormSchema
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make(__('filament-proovit::filament-proovit.token_reservations.sections.summary'))
                ->schema([
                    TextInput::make('fingerprint')
                        ->label(__('filament-proovit::filament-proovit.token_reservations.fields.fingerprint'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('reservation_id')
                        ->label(__('filament-proovit::filament-proovit.token_reservations.fields.reservation_id'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('status')
                        ->label(__('filament-proovit::filament-proovit.token_reservations.fields.status'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('created_at')
                        ->label(__('filament-proovit::filament-proovit.token_reservations.fields.created_at'))
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2),
            Section::make(__('filament-proovit::filament-proovit.token_reservations.sections.payload'))
                ->schema([
                    Textarea::make('response')
                        ->label(__('filament-proovit::filament-proovit.token_reservations.fields.response'))
                        ->rows(16)
                        ->columnSpanFull()
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ];
    }
}
