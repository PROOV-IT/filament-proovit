<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Proovit\FilamentProovit\Support\Filament\Actions\Proofs\DepositProofAction;
use Proovit\FilamentProovit\Support\Filament\Tables\Proofs\ProofsTable;
use Proovit\FilamentProovit\Support\ProovitApiSessionGuard;
use Proovit\LaravelProovit\ProovitClient;

final class ProovitProofs extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $slug = 'proovit/proofs';

    protected static ?int $navigationSort = 15;

    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-proofs';
    }

    public function getTitle(): string
    {
        return __('filament-proovit::filament-proovit.proofs.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.proofs.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-proovit::filament-proovit.navigation.label');
    }

    public function mount(): void
    {
        $this->mountInteractsWithTable();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-proovit::filament-proovit.proofs.heading'))
                    ->description(__('filament-proovit::filament-proovit.proofs.description')),
                EmbeddedTable::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DepositProofAction::make()
                ->label(__('filament-proovit::filament-proovit.proofs.actions.create'))
                ->icon('heroicon-o-plus'),
            Action::make('refresh')
                ->label(__('filament-proovit::filament-proovit.proofs.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $this->resetTable();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return ProofsTable::make(
            $table,
            fn (): array => $this->loadProofs(),
            function (string $proofId): void {
                $this->revokeProof($proofId);
            },
            fn (string $proofId): string => ProovitProofView::getUrl(['proof' => $proofId]),
        );
    }

    protected function loadProofs(): array
    {
        $proofs = app(ProovitApiSessionGuard::class)->withAutoRefresh(
            fn (): array => app(ProovitClient::class)->proofs()->list([
                'limit' => 25,
            ]),
        );

        $rows = array_values((array) ($proofs['data'] ?? $proofs['items'] ?? $proofs['proofs'] ?? []));

        return array_map(
            static function (array $proof, int $index): array {
                return [
                    '__key' => (string) ($proof['id'] ?? $index),
                    'id' => (string) ($proof['id'] ?? ''),
                    'name' => (string) ($proof['name'] ?? __('filament-proovit::filament-proovit.proofs.placeholders.untitled')),
                    'status' => (string) ($proof['status'] ?? 'unknown'),
                    'description' => (string) ($proof['description'] ?? ''),
                    'signed_at' => (string) ($proof['signed_at'] ?? ''),
                    'certificate_url' => $proof['certificate_url'] ?? ($proof['links']['certificate_pdf'] ?? null),
                ];
            },
            $rows,
            array_keys($rows)
        );
    }

    protected function revokeProof(string $proofId): void
    {
        if ($proofId === '') {
            return;
        }

        app(ProovitApiSessionGuard::class)->withAutoRefresh(
            fn (): mixed => app(ProovitClient::class)->proofs()->revoke($proofId),
        );
        $this->resetTable();
    }
}
