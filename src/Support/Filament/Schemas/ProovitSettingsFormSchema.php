<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Proovit\LaravelProovit\Enums\ProovitMode;

final class ProovitSettingsFormSchema
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make(__('filament-proovit::filament-proovit.settings.sections.connection'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.connection_description'))
                ->columns(2)
                ->schema([
                    TextInput::make('connection.company_name')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.company_name'))
                        ->maxLength(255),
                    TextInput::make('connection.login_email')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.login_email'))
                        ->email()
                        ->maxLength(255),
                    TextInput::make('connection.base_url')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.base_url'))
                        ->url()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('connection.app_url')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.app_url'))
                        ->url()
                        ->maxLength(255),
                    Select::make('connection.mode')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.mode'))
                        ->options(self::modeOptions())
                        ->required(),
                    TextInput::make('connection.workspace_token')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.workspace_token'))
                        ->password()
                        ->revealable()
                        ->autocomplete('off')
                        ->maxLength(255),
                    TextInput::make('connection.api_key')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.api_key'))
                        ->password()
                        ->revealable()
                        ->autocomplete('off')
                        ->maxLength(255),
                    TextInput::make('connection.access_token')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.access_token'))
                        ->password()
                        ->revealable()
                        ->autocomplete('off')
                        ->maxLength(255),
                    TextInput::make('connection.timeout')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.timeout'))
                        ->numeric()
                        ->minValue(1),
                    TextInput::make('connection.connect_timeout')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.connect_timeout'))
                        ->numeric()
                        ->minValue(1),
                    Toggle::make('connection.verify_tls')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.verify_tls')),
                    TextInput::make('connection.retry_attempts')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.retry_attempts'))
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('connection.retry_sleep_ms')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.retry_sleep_ms'))
                        ->numeric()
                        ->minValue(0),
                    TextInput::make('connection.health_endpoint')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.health_endpoint'))
                        ->prefix('/')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make(__('filament-proovit::filament-proovit.settings.sections.api'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.api_description'))
                ->columns(2)
                ->schema([
                    TextInput::make('api.version')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.api_version'))
                        ->required()
                        ->maxLength(32),
                    TextInput::make('api.proofs_path')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.proofs_path'))
                        ->prefix('/')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('api.certificates_path')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.certificates_path'))
                        ->prefix('/')
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make(__('filament-proovit::filament-proovit.settings.sections.features'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.features_description'))
                ->columns(2)
                ->schema([
                    Toggle::make('features.proofs')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.feature_proofs')),
                    Toggle::make('features.certificates')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.feature_certificates')),
                    Toggle::make('features.exports')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.feature_exports')),
                    Toggle::make('features.audit')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.feature_audit')),
                ]),

            Section::make(__('filament-proovit::filament-proovit.settings.sections.certificates'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.certificates_description'))
                ->columns(2)
                ->schema([
                    TextInput::make('certificates.default_extension')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.certificate_extension'))
                        ->required()
                        ->maxLength(16),
                    TextInput::make('certificates.filename_prefix')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.certificate_prefix'))
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make(__('filament-proovit::filament-proovit.settings.sections.exports'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.exports_description'))
                ->columns(2)
                ->schema([
                    TextInput::make('exports.default_disk')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.export_disk'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('exports.keep_days')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.export_keep_days'))
                        ->numeric()
                        ->minValue(0),
                ]),

            Section::make(__('filament-proovit::filament-proovit.settings.sections.audit'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.audit_description'))
                ->columns(2)
                ->schema([
                    Toggle::make('audit.enabled')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.audit_enabled')),
                    TextInput::make('audit.channel')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.audit_channel'))
                        ->required()
                        ->maxLength(255),
                ]),

            Section::make(__('filament-proovit::filament-proovit.settings.sections.docs'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.docs_description'))
                ->columns(2)
                ->schema([
                    Toggle::make('docs.enabled')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.docs_enabled')),
                    TextInput::make('docs.path')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.docs_path'))
                        ->required()
                        ->maxLength(255),
                ]),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function modeOptions(): array
    {
        $options = [];

        foreach (ProovitMode::cases() as $mode) {
            $options[$mode->value] = __('filament-proovit::filament-proovit.settings.modes.'.$mode->value);
        }

        return $options;
    }
}
