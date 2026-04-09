<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
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
use Proovit\LaravelProovit\DTOs\ProovitConnectionData;
use Proovit\LaravelProovit\Http\ProovitApiClient;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitClientFactory;
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
        $this->data = array_replace_recursive(
            app(ProovitConfig::class)->toArray(),
            app(ProovitSettingsRepository::class)->all(),
        );
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(ProovitSettingsFormSchema::schema($this->companyOptions()));
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

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            $this->authenticateFormAction(),
            $this->reloadFormAction(),
            $this->getSaveFormAction(),
        ];
    }

    private function authenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(__('filament-proovit::filament-proovit.settings.actions.authenticate'))
            ->icon('heroicon-o-key')
            ->color('gray')
            ->form([
                TextInput::make('email')
                    ->label(__('filament-proovit::filament-proovit.settings.fields.login_email'))
                    ->email()
                    ->required()
                    ->default($this->currentAuthenticationEmail())
                    ->autocomplete('email')
                    ->maxLength(255),
                TextInput::make('password')
                    ->label(__('filament-proovit::filament-proovit.settings.fields.password'))
                    ->password()
                    ->revealable()
                    ->required()
                    ->autocomplete('current-password')
                    ->maxLength(255),
            ])
            ->action(function (array $data): void {
                try {
                    $connection = $this->authenticate(
                        (string) ($data['email'] ?? $this->currentAuthenticationEmail()),
                        (string) ($data['password'] ?? ''),
                    );
                    $this->persistConnection($connection, false);

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
            });
    }

    private function reloadFormAction(): Action
    {
        return Action::make('reload')
            ->label(__('filament-proovit::filament-proovit.settings.actions.reload'))
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->action(function (): void {
                $this->data = array_replace_recursive(
                    app(ProovitConfig::class)->toArray(),
                    app(ProovitSettingsRepository::class)->all(),
                );
            });
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
            $state = $this->data;
            $settings = app(ProovitSettingsRepository::class);
            $payload = array_replace_recursive(
                app(ProovitSettingsRepository::class)->all(),
                app(ProovitConfig::class)->toArray(),
                $state,
            );
            $payload = $this->syncSelectedCompanyName($payload);

            if (! $settings->save($payload)) {
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

            $this->data = array_replace_recursive(
                app(ProovitConfig::class)->toArray(),
                app(ProovitSettingsRepository::class)->all(),
            );

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
        return (array) data_get(app(ProovitSettingsRepository::class)->all(), 'connection.companies', []);
    }

    private function authenticate(string $email, string $password): ProovitConnectionData
    {
        $currentConfig = app(ProovitConfig::class);
        $state = $this->form->getState();

        $configPayload = array_replace_recursive(app(ProovitSettingsRepository::class)->all(), $currentConfig->toArray(), [
            'connection' => [
                'base_url' => $state['connection']['base_url'] ?? $currentConfig->baseUrl,
                'login_email' => $email !== '' ? $email : ($state['connection']['login_email'] ?? $currentConfig->loginEmail),
            ],
        ]);

        $temporaryConfig = ProovitConfig::fromArray($configPayload);

        $factory = app(ProovitClientFactory::class);
        $loginClient = new ProovitApiClient($factory->make($temporaryConfig));
        $loginPayload = $loginClient->request('POST', '/v1/auth/login', [
            'json' => [
                'email' => (string) ($temporaryConfig->loginEmail ?? $email),
                'password' => $password,
            ],
        ]);

        $token = (string) ($loginPayload['token'] ?? $loginPayload['data']['token'] ?? '');
        $authenticatedConfig = new ProovitConfig(
            baseUrl: $temporaryConfig->baseUrl,
            appUrl: $temporaryConfig->appUrl,
            apiKey: $temporaryConfig->apiKey,
            accessToken: $token,
            workspaceToken: $temporaryConfig->workspaceToken,
            companyName: $temporaryConfig->companyName,
            loginEmail: $temporaryConfig->loginEmail,
            mode: $temporaryConfig->mode,
            timeout: $temporaryConfig->timeout,
            connectTimeout: $temporaryConfig->connectTimeout,
            verifyTls: $temporaryConfig->verifyTls,
            retryAttempts: $temporaryConfig->retryAttempts,
            retrySleepMs: $temporaryConfig->retrySleepMs,
            healthEndpoint: $temporaryConfig->healthEndpoint,
            api: $temporaryConfig->api,
            features: $temporaryConfig->features,
            certificates: $temporaryConfig->certificates,
            exports: $temporaryConfig->exports,
            audit: $temporaryConfig->audit,
            docs: $temporaryConfig->docs,
        );

        $companiesClient = new ProovitApiClient($factory->make($authenticatedConfig));
        $companiesPayload = $companiesClient->request('GET', '/v1/companies');

        return ProovitConnectionData::fromArray([
            'connected' => true,
            'mode' => $temporaryConfig->mode->value,
            'base_url' => $temporaryConfig->baseUrl,
            'bearer_token' => $token,
            'selected_company_uuid' => null,
            'workspace_token' => null,
            'company_name' => null,
            'login_email' => $temporaryConfig->loginEmail ?? $email,
            'companies' => array_values((array) ($companiesPayload['data'] ?? $companiesPayload['items'] ?? $companiesPayload['companies'] ?? $companiesPayload)),
            'payload' => $loginPayload,
        ]);
    }

    private function persistConnection(ProovitConnectionData $connection, bool $preserveSelectedCompany = true): void
    {
        $settings = app(ProovitSettingsRepository::class);
        $currentPayload = array_replace_recursive(
            app(ProovitSettingsRepository::class)->all(),
            app(ProovitConfig::class)->toArray(),
        );
        $payload = array_replace_recursive($currentPayload, [
            'connection' => [
                'base_url' => $connection->baseUrl ?? $currentPayload['connection']['base_url'] ?? null,
                'access_token' => $connection->bearerToken,
                'selected_company_uuid' => $preserveSelectedCompany ? ($currentPayload['connection']['selected_company_uuid'] ?? $currentPayload['connection']['workspace_token'] ?? null) : null,
                'workspace_token' => $preserveSelectedCompany ? ($currentPayload['connection']['selected_company_uuid'] ?? $currentPayload['connection']['workspace_token'] ?? null) : null,
                'company_name' => $connection->companyName,
                'login_email' => $connection->loginEmail,
                'companies' => $connection->companies,
            ],
        ]);

        $payload = $this->syncSelectedCompanyName($payload);
        $settings->save($payload);

        app()->forgetInstance(ProovitConfig::class);
        app()->forgetInstance(ProovitConfigResolver::class);
        app()->forgetInstance(ProovitClient::class);
        app()->forgetInstance(ProovitFeatureManager::class);

        $this->form->fill(array_replace_recursive(
            app(ProovitConfig::class)->toArray(),
            app(ProovitSettingsRepository::class)->all(),
        ));
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

        foreach ($companies as $company) {
            if (($company['uuid'] ?? $company['id'] ?? null) !== $selectedUuid) {
                continue;
            }

            $payload['connection']['company_name'] = $company['name'] ?? null;
            $payload['connection']['selected_company_uuid'] = $selectedUuid;
            $payload['connection']['workspace_token'] = $selectedUuid;
            break;
        }

        return $payload;
    }

    private function currentAuthenticationEmail(): string
    {
        $stateEmail = (string) data_get($this->data, 'connection.login_email', '');
        if ($stateEmail !== '') {
            return $stateEmail;
        }

        return (string) (app(ProovitConfig::class)->loginEmail ?? '');
    }
}
