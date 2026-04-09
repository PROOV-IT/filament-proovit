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
use Proovit\FilamentProovit\Support\Filament\Tables\Tokens\TokenReservationsTable;

final class ProovitTokenReservations extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $slug = 'proovit/token-reservations';

    protected static ?int $navigationSort = 18;

    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-token-reservations';
    }

    public function getTitle(): string
    {
        return __('filament-proovit::filament-proovit.token_reservations.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.token_reservations.navigation');
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
        return $schema->components([
            Section::make(__('filament-proovit::filament-proovit.token_reservations.heading'))
                ->description(__('filament-proovit::filament-proovit.token_reservations.description')),
            EmbeddedTable::make(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('filament-proovit::filament-proovit.token_reservations.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $this->resetTable();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return TokenReservationsTable::make(
            $table,
            fn (int $reservationId): string => ProovitTokenReservationView::getUrl(['reservation' => $reservationId]),
        );
    }
}
