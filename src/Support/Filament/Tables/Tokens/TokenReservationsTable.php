<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Tables\Tokens;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Proovit\FilamentProovit\Models\TokenReservation;

final class TokenReservationsTable
{
    public static function make(Table $table, callable $viewReservation): Table
    {
        return $table
            ->query(TokenReservation::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reservation_id')
                    ->label(__('filament-proovit::filament-proovit.token_reservations.columns.reservation_id'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('filament-proovit::filament-proovit.token_reservations.columns.status'))
                    ->badge()
                    ->placeholder(__('filament-proovit::filament-proovit.token_reservations.placeholders.unknown')),
                TextColumn::make('created_at')
                    ->label(__('filament-proovit::filament-proovit.token_reservations.columns.created_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('filament-proovit::filament-proovit.token_reservations.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->url(static fn (TokenReservation $record): string => $viewReservation($record->getKey())),
            ])
            ->emptyStateHeading(__('filament-proovit::filament-proovit.token_reservations.empty.heading'))
            ->emptyStateDescription(__('filament-proovit::filament-proovit.token_reservations.empty.description'))
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
