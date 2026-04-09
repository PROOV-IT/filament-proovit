<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Support\ArrayRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Proovit\LaravelProovit\ProovitClient;
use Throwable;

final class RecentProofsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->getTableHeading())
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
            $proofs = app(ProovitClient::class)->proofs()->list([
                'limit' => 5,
            ]);

            return collect(array_values((array) ($proofs['data'] ?? $proofs['items'] ?? $proofs['proofs'] ?? [])))
                ->map(static function (array $proof): array {
                    return [
                        ArrayRecord::getKeyName() => (string) ($proof['id'] ?? uniqid('proof_', true)),
                        'name' => (string) ($proof['name'] ?? __('filament-proovit::filament-proovit.widgets.recent_proofs.heading')),
                        'status' => (string) ($proof['status'] ?? __('filament-proovit::filament-proovit.widgets.connection.unknown')),
                        'description' => (string) ($proof['description'] ?? ''),
                        'signed_at' => (string) ($proof['signed_at'] ?? ''),
                    ];
                });
        } catch (Throwable) {
            return collect();
        }
    }
}
