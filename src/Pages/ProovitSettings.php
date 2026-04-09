<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Proovit\FilamentProovit\Support\Filament\Schemas\ProovitSettingsFormSchema;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitConfigResolver;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

final class ProovitSettings extends Page
{
    public array $data = [];

    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-settings';
    }

    public function getTitle(): string
    {
        return __('filament-proovit::filament-proovit.settings.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.settings.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-proovit::filament-proovit.navigation.label');
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'proovit/settings';

    public function mount(): void
    {
        $this->form->fill(app(ProovitConfig::class)->toArray());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(ProovitSettingsFormSchema::schema());
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-proovit::filament-proovit.settings.note.heading'))
                    ->description(__('filament-proovit::filament-proovit.settings.note.body')),
                $this->getFormContentComponent(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reload')
                ->label(__('filament-proovit::filament-proovit.settings.actions.reload'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn (): void => $this->form->fill(app(ProovitConfig::class)->toArray())),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-proovit::filament-proovit.settings.actions.save'))
            ->icon('heroicon-o-check')
            ->submit('save');
    }

    public function save(): void
    {
        try {
            $state = $this->form->getState();
            $settings = app(ProovitSettingsRepository::class);

            if (! $settings->save($state)) {
                Notification::make()
                    ->title(__('filament-proovit::filament-proovit.settings.notifications.save_failed_title'))
                    ->body(__('filament-proovit::filament-proovit.settings.notifications.save_failed_body'))
                    ->danger()
                    ->send();

                return;
            }

            app()->forgetInstance(ProovitConfig::class);
            app()->forgetInstance(ProovitConfigResolver::class);
            app()->forgetInstance(ProovitClient::class);
            app()->forgetInstance(ProovitFeatureManager::class);

            $this->form->fill(app(ProovitConfig::class)->toArray());

            Notification::make()
                ->title(__('filament-proovit::filament-proovit.settings.notifications.saved_title'))
                ->body(__('filament-proovit::filament-proovit.settings.notifications.saved_body'))
                ->success()
                ->send();
        } catch (Halt) {
            return;
        }
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                SchemaActions::make($this->getFormActions())
                    ->fullWidth(true)
                    ->sticky(true)
                    ->key('form-actions'),
            ]);
    }
}
