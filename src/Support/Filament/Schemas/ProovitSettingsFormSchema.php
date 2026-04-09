<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

final class ProovitSettingsFormSchema
{
    /**
     * @param  array<int, array<string, mixed>>  $companies
     * @param  array<string, string>  $baseUrlOptions
     * @return array<int, Component>
     */
    public static function schema(array $companies = [], array $baseUrlOptions = []): array
    {
        return [
            Section::make(__('filament-proovit::filament-proovit.settings.sections.connection'))
                ->description(__('filament-proovit::filament-proovit.settings.sections.connection_description'))
                ->columns(2)
                ->schema([
                    Select::make('connection.base_url')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.base_url'))
                        ->options($baseUrlOptions)
                        ->required()
                        ->helperText(__('filament-proovit::filament-proovit.settings.helpers.base_url'))
                        ->native(false)
                        ->searchable(false),
                    TextInput::make('connection.login_email')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.login_email'))
                        ->email()
                        ->required()
                        ->autocomplete('email')
                        ->maxLength(255),
                    TextInput::make('connection.login_password')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.password'))
                        ->password()
                        ->revealable()
                        ->required()
                        ->autocomplete('current-password')
                        ->maxLength(255),
                    Select::make('connection.selected_company_uuid')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.selected_company_uuid'))
                        ->options(self::companyOptions($companies))
                        ->searchable()
                        ->preload()
                        ->disabled($companies === [])
                        ->required($companies !== [])
                        ->helperText(__('filament-proovit::filament-proovit.settings.helpers.selected_company')),
                    TextInput::make('connection.company_name')
                        ->label(__('filament-proovit::filament-proovit.settings.fields.company_name'))
                        ->disabled()
                        ->dehydrated(false),
                ]),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $companies
     * @return array<string, string>
     */
    private static function companyOptions(array $companies): array
    {
        $options = [];

        foreach ($companies as $company) {
            $uuid = (string) ($company['uuid'] ?? $company['id'] ?? '');
            if ($uuid === '') {
                continue;
            }

            $label = trim((string) ($company['name'] ?? $uuid));
            $role = (string) ($company['current_user_role'] ?? '');
            if ($role !== '') {
                $label .= sprintf(' (%s)', $role);
            }

            $options[$uuid] = $label;
        }

        return $options;
    }
}
