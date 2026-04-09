<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Tables\Proofs;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Proovit\FilamentProovit\Support\Filament\Actions\Proofs\ExportProofsAction;

final class ProofsTable
{
    /**
     * @param  callable(): array<int, array<string, mixed>>  $records
     * @param  callable(string): string|null  $viewProof
     */
    public static function make(Table $table, callable $records, callable $revokeProof, ?callable $viewProof = null): Table
    {
        return $table
            ->records($records)
            ->paginated(false)
            ->defaultSort('signed_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-proovit::filament-proovit.proofs.columns.name'))
                    ->searchable()
                    ->wrap()
                    ->weight('medium'),
                TextColumn::make('status')
                    ->label(__('filament-proovit::filament-proovit.proofs.columns.status'))
                    ->badge()
                    ->formatStateUsing(static fn (?string $state): string => filled($state) ? Str::headline($state) : __('filament-proovit::filament-proovit.proofs.status.unknown')),
                TextColumn::make('signed_at')
                    ->label(__('filament-proovit::filament-proovit.proofs.columns.signed_at'))
                    ->placeholder(__('filament-proovit::filament-proovit.proofs.placeholders.not_signed')),
                TextColumn::make('description')
                    ->label(__('filament-proovit::filament-proovit.proofs.columns.description'))
                    ->placeholder(__('filament-proovit::filament-proovit.proofs.placeholders.no_description'))
                    ->wrap(),
                TextColumn::make('certificate_url')
                    ->label(__('filament-proovit::filament-proovit.proofs.columns.certificate'))
                    ->formatStateUsing(static fn (?string $state): string => filled($state) ? __('filament-proovit::filament-proovit.proofs.actions.open_certificate') : __('filament-proovit::filament-proovit.proofs.placeholders.not_available'))
                    ->url(static fn (array $record): ?string => $record['certificate_url'] ?? null, true)
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('filament-proovit::filament-proovit.proofs.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->visible(static fn (array $record): bool => is_callable($viewProof))
                    ->url(static fn (array $record) => $viewProof !== null ? $viewProof((string) ($record['id'] ?? '')) : null),
                Action::make('open_certificate')
                    ->label(__('filament-proovit::filament-proovit.proofs.actions.open_certificate'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(static fn (array $record): bool => filled($record['certificate_url'] ?? null))
                    ->url(static fn (array $record): ?string => $record['certificate_url'] ?? null)
                    ->openUrlInNewTab(),
                Action::make('revoke')
                    ->label(__('filament-proovit::filament-proovit.proofs.actions.revoke'))
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(static fn (array $record): bool => ! in_array((string) ($record['status'] ?? ''), ['revoked', 'deleted'], true))
                    ->action(function (array $record) use ($revokeProof): void {
                        $revokeProof((string) ($record['id'] ?? ''));

                        Notification::make()
                            ->title(__('filament-proovit::filament-proovit.proofs.notifications.revoked_title'))
                            ->body(__('filament-proovit::filament-proovit.proofs.notifications.revoked_body'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                ExportProofsAction::make(),
            ])
            ->emptyStateHeading(__('filament-proovit::filament-proovit.proofs.empty.heading'))
            ->emptyStateDescription(__('filament-proovit::filament-proovit.proofs.empty.description'))
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}
