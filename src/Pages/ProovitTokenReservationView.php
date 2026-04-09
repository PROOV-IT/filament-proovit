<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Proovit\FilamentProovit\Models\TokenReservation;
use Proovit\FilamentProovit\Support\Filament\Schemas\Tokens\TokenReservationViewFormSchema;

final class ProovitTokenReservationView extends Page
{
    public array $data = [];

    public ?int $reservationId = null;

    protected static ?string $slug = 'proovit/token-reservations/{reservation}';

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-token-reservation-view';
    }

    public function getTitle(): string
    {
        return __('filament-proovit::filament-proovit.token_reservations.view.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.token_reservations.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-proovit::filament-proovit.navigation.label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(string $reservation): void
    {
        $this->reservationId = (int) $reservation;
        $this->loadReservation();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(TokenReservationViewFormSchema::schema());
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('filament-proovit::filament-proovit.token_reservations.view.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $this->loadReservation();
                }),
        ];
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form');
    }

    private function loadReservation(): void
    {
        if ($this->reservationId === null) {
            $this->data = [];

            return;
        }

        $reservation = TokenReservation::query()->find($this->reservationId);
        if (! $reservation instanceof TokenReservation) {
            $this->data = [];

            return;
        }

        $this->form->fill([
            'fingerprint' => $reservation->fingerprint,
            'reservation_id' => $reservation->reservation_id,
            'status' => $reservation->status,
            'created_at' => optional($reservation->created_at)->toDateTimeString(),
            'response' => $this->encodeJson($reservation->response),
        ]);
    }

    /**
     * @param  array<string, mixed>  $value
     */
    private function encodeJson(array $value): string
    {
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return is_string($json) ? $json : '';
    }
}
