<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Proovit\FilamentProovit\Support\Filament\Actions\Proofs\DepositProofAction;

final class ProofDepositWidget extends TableWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->getTableHeading())
            ->headerActions([
                DepositProofAction::make(),
            ])
            ->records(fn (): Collection => collect())
            ->columns([])
            ->paginated(false)
            ->emptyStateHeading(__('filament-proovit::filament-proovit.proof_deposit.empty.heading'))
            ->emptyStateDescription(__('filament-proovit::filament-proovit.proof_deposit.empty.description'))
            ->striped();
    }

    public function getTableHeading(): string|Htmlable|null
    {
        return __('filament-proovit::filament-proovit.proof_deposit.heading');
    }
}
