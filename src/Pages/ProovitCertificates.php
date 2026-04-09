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
use Proovit\FilamentProovit\Support\Filament\Tables\Proofs\CertificatesTable;
use Proovit\LaravelProovit\ProovitClient;

final class ProovitCertificates extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $slug = 'proovit/certificates';

    protected static ?int $navigationSort = 20;

    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-certificates';
    }

    public function getTitle(): string
    {
        return __('filament-proovit::filament-proovit.certificates.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.certificates.navigation');
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
                Section::make(__('filament-proovit::filament-proovit.certificates.heading'))
                    ->description(__('filament-proovit::filament-proovit.certificates.description')),
                EmbeddedTable::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('filament-proovit::filament-proovit.certificates.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $this->resetTable();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return CertificatesTable::make(
            $table,
            fn (): array => $this->loadProofs(),
            fn (string $proofId): string => ProovitProofView::getUrl(['proof' => $proofId]),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function loadProofs(): array
    {
        $proofs = app(ProovitClient::class)->proofs()->list([
            'limit' => 50,
        ]);

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
                    'certificate_url' => $proof['certificate_url'] ?? null,
                ];
            },
            $rows,
            array_keys($rows)
        );
    }
}
