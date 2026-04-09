<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Support\ArrayRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Proovit\FilamentProovit\Pages\ProovitProofView;
use Proovit\FilamentProovit\Support\Filament\ProovitDashboardData;
use Proovit\LaravelProovit\ProovitClient;

final class RecentProofsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->getTableHeading())
            ->headerActions([
                Action::make('refresh')
                    ->label(__('filament-proovit::filament-proovit.widgets.recent_proofs.actions.refresh'))
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn (): mixed => $this->dispatch('$refresh')),
            ])
            ->records(fn (): Collection => $this->proofRecords())
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-proovit::filament-proovit.widgets.recent_proofs.name'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('filament-proovit::filament-proovit.widgets.recent_proofs.status'))
                    ->badge(),
                TextColumn::make('description')
                    ->label(__('filament-proovit::filament-proovit.widgets.recent_proofs.description'))
                    ->placeholder(__('filament-proovit::filament-proovit.widgets.recent_proofs.no_description')),
            ])
            ->paginated(false)
            ->recordTitleAttribute('name')
            ->recordActions([
                Action::make('view')
                    ->label(__('filament-proovit::filament-proovit.proofs.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->url(static fn (array $record): string => ProovitProofView::getUrl(['proof' => (string) ($record['id'] ?? '')])),
            ])
            ->striped();
    }

    public function getTableHeading(): string|Htmlable|null
    {
        return __('filament-proovit::filament-proovit.widgets.recent_proofs.heading');
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function proofRecords(): Collection
    {
        try {
            $proofs = ProovitDashboardData::fromClient(app(ProovitClient::class), 5);

            return collect($proofs->recentProofs)
                ->map(static function (array $proof): array {
                    return [
                        ArrayRecord::getKeyName() => (string) ($proof['id'] ?? uniqid('proof_', true)),
                        'name' => (string) ($proof['name'] ?? __('filament-proovit::filament-proovit.widgets.recent_proofs.heading')),
                        'status' => (string) ($proof['status'] ?? __('filament-proovit::filament-proovit.widgets.connection.unknown')),
                        'description' => (string) ($proof['description'] ?? ''),
                        'signed_at' => (string) ($proof['signed_at'] ?? ''),
                    ];
                });
        } catch (\Throwable) {
            return collect();
        }
    }
}
