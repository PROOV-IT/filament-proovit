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
use Proovit\LaravelProovit\Actions\Connection\AuthenticateProovitConnectionAction;
use Proovit\LaravelProovit\Config\ProovitConfig;
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitConfigResolver;
use Proovit\LaravelProovit\Support\ProovitFeatureManager;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;
use Throwable;

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
        $this->data = $this->payloadFromState();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(
            ProovitSettingsFormSchema::schema(
                $this->companyOptions(),
                $this->baseUrlOptions(),
            ),
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament-proovit::filament-proovit.settings.note.heading'))
                ->description(__('filament-proovit::filament-proovit.settings.note.body')),
            $this->getFormContentComponent(),
        ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->createAccountFormAction(),
            $this->testConnectionFormAction(),
            $this->getSaveFormAction(),
        ];
    }

    private function createAccountFormAction(): Action
    {
        return Action::make('create_account')
            ->label(__('filament-proovit::filament-proovit.settings.actions.create_account'))
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->color('gray')
            ->url($this->createAccountUrl())
            ->openUrlInNewTab();
    }

    private function testConnectionFormAction(): Action
    {
        return Action::make('test_connection')
            ->label(__('filament-proovit::filament-proovit.settings.actions.test_connection'))
            ->icon('heroicon-o-bolt')
            ->color('gray')
            ->action(function (): void {
                $this->testConnection();
            });
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-proovit::filament-proovit.settings.actions.save'))
            ->icon('heroicon-o-check')
            ->submit('save');
    }

    public function testConnection(): void
    {
        try {
            $state = $this->payloadFromState();
            $connection = app(AuthenticateProovitConnectionAction::class)->handle(
                (string) data_get($state, 'connection.login_email', ''),
                (string) data_get($state, 'connection.login_password', ''),
                $this->connectionConfigFromState($state),
            );

            $payload = $this->persistedPayloadFromConnection($state, $connection);

            if (! $this->persistPayload($payload)) {
                Notification::make()
                    ->title(__('filament-proovit::filament-proovit.settings.notifications.save_failed_title'))
                    ->body(__('filament-proovit::filament-proovit.settings.notifications.save_failed_body'))
                    ->danger()
                    ->send();

                return;
            }

            $this->data = $this->payloadFromState();
            $this->dispatch('refresh-page');

            Notification::make()
                ->title(__('filament-proovit::filament-proovit.settings.notifications.authenticated_title'))
                ->body(__('filament-proovit::filament-proovit.settings.notifications.authenticated_body'))
                ->success()
                ->send();
        } catch (Throwable $exception) {
            Notification::make()
                ->title(__('filament-proovit::filament-proovit.settings.notifications.authenticate_failed_title'))
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    public function save(): void
    {
        try {
            $payload = $this->syncSelectedCompanyName($this->payloadFromState());

            if (! $this->persistPayload($payload)) {
                Notification::make()
                    ->title(__('filament-proovit::filament-proovit.settings.notifications.save_failed_title'))
                    ->body(__('filament-proovit::filament-proovit.settings.notifications.save_failed_body'))
                    ->danger()
                    ->send();

                return;
            }

            $this->data = $this->payloadFromState();

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

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function companyOptions(): array
    {
        return (array) data_get($this->data, 'connection.companies', data_get(app(ProovitSettingsRepository::class)->all(), 'connection.companies', []));
    }

    /**
     * @return array<string, string>
     */
    protected function baseUrlOptions(): array
    {
        $options = [
            'https://api.proov-it.online/api' => __('filament-proovit::filament-proovit.settings.base_urls.production'),
            'https://api.staging.proov-it.online/api' => __('filament-proovit::filament-proovit.settings.base_urls.staging'),
        ];

        $currentBaseUrl = (string) data_get($this->data, 'connection.base_url', '');
        if ($currentBaseUrl !== '' && ! array_key_exists($currentBaseUrl, $options)) {
            $options = [$currentBaseUrl => $currentBaseUrl] + $options;
        }

        return $options;
    }

    private function createAccountUrl(): string
    {
        if (app()->environment('production')) {
            return 'https://app.proov-it.online';
        }

        return 'https://app.staging.proov-it.online';
    }

    private function connectionConfigFromState(array $state): ProovitConfig
    {
        $payload = array_replace_recursive(
            app(ProovitConfig::class)->toArray(),
            app(ProovitSettingsRepository::class)->all(),
            $state,
        );

        return ProovitConfig::fromArray($payload);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function persistedPayloadFromConnection(array $state, ProovitConnectionData $connection): array
    {
        $currentPayload = array_replace_recursive(
            app(ProovitConfig::class)->toArray(),
            app(ProovitSettingsRepository::class)->all(),
            $state,
        );

        $selectedCompanyUuid = (string) (data_get($state, 'connection.selected_company_uuid')
            ?? data_get($state, 'connection.workspace_token')
            ?? '');
        $selectedCompany = $this->companyFromConnection($connection->companies, $selectedCompanyUuid);

        if ($selectedCompany === null) {
            $selectedCompanyUuid = null;
        }

        $payload = array_replace_recursive($currentPayload, [
            'connection' => [
                'base_url' => $connection->baseUrl ?? data_get($state, 'connection.base_url') ?? $currentPayload['connection']['base_url'] ?? null,
                'access_token' => $connection->bearerToken,
                'selected_company_uuid' => $selectedCompanyUuid,
                'workspace_token' => $selectedCompanyUuid,
                'company_name' => $selectedCompany['name'] ?? null,
                'login_email' => $connection->loginEmail ?? data_get($state, 'connection.login_email'),
                'login_password' => data_get($state, 'connection.login_password'),
                'companies' => $connection->companies,
            ],
        ]);

        return $this->syncSelectedCompanyName($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function syncSelectedCompanyName(array $payload): array
    {
        $selectedUuid = (string) (data_get($payload, 'connection.selected_company_uuid')
            ?? data_get($payload, 'connection.workspace_token')
            ?? '');
        $companies = (array) data_get($payload, 'connection.companies', []);

        if ($selectedUuid === '') {
            $payload['connection']['company_name'] = null;
            $payload['connection']['selected_company_uuid'] = null;
            $payload['connection']['workspace_token'] = null;

            return $payload;
        }

        $company = $this->companyFromConnection($companies, $selectedUuid);
        if ($company === null) {
            $payload['connection']['company_name'] = null;
            $payload['connection']['selected_company_uuid'] = null;
            $payload['connection']['workspace_token'] = null;

            return $payload;
        }

        $payload['connection']['company_name'] = $company['name'] ?? null;
        $payload['connection']['selected_company_uuid'] = $selectedUuid;
        $payload['connection']['workspace_token'] = $selectedUuid;

        return $payload;
    }

    /**
     * @param  array<int, array<string, mixed>>  $companies
     * @return array<string, mixed>|null
     */
    private function companyFromConnection(array $companies, string $selectedUuid): ?array
    {
        if ($selectedUuid === '') {
            return null;
        }

        foreach ($companies as $company) {
            $uuid = (string) ($company['uuid'] ?? $company['id'] ?? '');
            if ($uuid === '' || $uuid !== $selectedUuid) {
                continue;
            }

            return $company;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFromState(): array
    {
        return array_replace_recursive(
            app(ProovitConfig::class)->toArray(),
            app(ProovitSettingsRepository::class)->all(),
            $this->data,
        );
    }

    private function persistPayload(array $payload): bool
    {
        $settings = app(ProovitSettingsRepository::class);

        if (! $settings->save($payload)) {
            return false;
        }

        app()->forgetInstance(ProovitConfig::class);
        app()->forgetInstance(ProovitConfigResolver::class);
        app()->forgetInstance(ProovitClient::class);
        app()->forgetInstance(ProovitFeatureManager::class);

        return true;
    }
}
