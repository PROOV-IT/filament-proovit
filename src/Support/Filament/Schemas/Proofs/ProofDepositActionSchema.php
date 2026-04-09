<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas\Proofs;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Proovit\FilamentProovit\Support\ProovitProofTemplateCatalog;
use Proovit\LaravelProovit\DTOs\ProofTemplateCustomFieldData;
use Proovit\LaravelProovit\DTOs\ProofTemplateData;

final class ProofDepositActionSchema
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
        return [
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.proof'))
                ->description(__('filament-proovit::filament-proovit.proof_deposit.sections.proof_description'))
                ->columns(2)
                ->schema([
                    Select::make('proof_template_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.proof_template_id'))
                        ->options(static fn (): array => app(ProovitProofTemplateCatalog::class)->options())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(static function (Set $set): void {
                            $set('custom_fields', []);
                            $set('signature_base64', null);
                        })
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.name'))
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.description'))
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('folder_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.folder_id'))
                        ->maxLength(255)
                        ->visible(static fn (Get $get): bool => self::template($get)?->displayFolders() ?? false),
                    TextInput::make('category_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.category_id'))
                        ->maxLength(255)
                        ->visible(static fn (Get $get): bool => self::template($get)?->displayCategories() ?? false),
                    TextInput::make('token_reservation_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.token_reservation_id'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Placeholder::make('template_hint')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.proof_template_id'))
                        ->content(static fn (Get $get): string => self::templateSummary($get))
                        ->columnSpanFull()
                        ->visible(static fn (Get $get): bool => self::template($get) !== null),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.metadata'))
                ->description(__('filament-proovit::filament-proovit.proof_deposit.sections.metadata_description'))
                ->columns(2)
                ->schema([
                    TagsInput::make('share_emails')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.share_emails'))
                        ->placeholder(__('filament-proovit::filament-proovit.proof_deposit.placeholders.share_emails'))
                        ->columnSpanFull()
                        ->visible(static fn (Get $get): bool => self::template($get)?->isShared() ?? false),
                    TagsInput::make('keywords')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.keywords'))
                        ->placeholder(__('filament-proovit::filament-proovit.proof_deposit.placeholders.keywords'))
                        ->columnSpanFull()
                        ->visible(static fn (Get $get): bool => self::template($get)?->displayTags() ?? false),
                    Toggle::make('is_anonymous')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.is_anonymous'))
                        ->default(false),
                    TextInput::make('location')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.location'))
                        ->maxLength(255),
                    TextInput::make('lat')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.lat'))
                        ->numeric()
                        ->step('0.000001'),
                    TextInput::make('lng')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.lng'))
                        ->numeric()
                        ->step('0.000001'),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.custom_fields'))
                ->description(__('filament-proovit::filament-proovit.proof_deposit.sections.custom_fields_description'))
                ->columns(2)
                ->schema(static function (Get $get): array {
                    $template = self::template($get);

                    if ($template === null) {
                        return [];
                    }

                    $fields = $template->customFields();

                    if ($fields === []) {
                        return [];
                    }

                    return array_map(
                        static fn (ProofTemplateCustomFieldData $field): Component => self::customFieldComponent($field),
                        $fields,
                    );
                })
                ->visible(static fn (Get $get): bool => self::template($get)?->customFields() !== []),
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.files'))
                ->description(static fn (Get $get): string => self::filesDescription($get))
                ->columns(1)
                ->schema([
                    FileUpload::make('files')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.files'))
                        ->multiple()
                        ->required()
                        ->preserveFilenames()
                        ->storeFiles(false)
                        ->visibility('private')
                        ->columnSpanFull(),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.signature'))
                ->description(__('filament-proovit::filament-proovit.proof_deposit.sections.signature_description'))
                ->columns(1)
                ->schema([
                    Textarea::make('signature_base64')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.signature_base64'))
                        ->rows(5)
                        ->helperText(__('filament-proovit::filament-proovit.proof_deposit.helpers.signature_base64'))
                        ->columnSpanFull()
                        ->required(static fn (Get $get): bool => self::template($get)?->requiresSignature() ?? false),
                ])
                ->visible(static fn (Get $get): bool => self::template($get)?->requiresSignature() ?? false),
        ];
    }

    private static function template(Get $get): ?ProofTemplateData
    {
        return app(ProovitProofTemplateCatalog::class)->find($get('proof_template_id'));
    }

    private static function templateSummary(Get $get): string
    {
        $template = self::template($get);
        if ($template === null) {
            return '';
        }

        $parts = [
            $template->description,
            $template->requiresSignature() ? __('filament-proovit::filament-proovit.proof_deposit.template_summary.signature') : __('filament-proovit::filament-proovit.proof_deposit.template_summary.no_signature'),
        ];

        $requiredFiles = $template->requiredFiles();
        if ($requiredFiles !== []) {
            $parts[] = __('filament-proovit::filament-proovit.proof_deposit.template_summary.required_files', [
                'files' => implode(', ', $requiredFiles),
            ]);
        }

        return implode(' • ', array_filter(array_map(static fn ($value): string => (string) $value, $parts)));
    }

    private static function filesDescription(Get $get): string
    {
        $template = self::template($get);
        if ($template === null) {
            return __('filament-proovit::filament-proovit.proof_deposit.sections.files_description');
        }

        $requiredFiles = $template->requiredFiles();
        if ($requiredFiles === []) {
            return __('filament-proovit::filament-proovit.proof_deposit.sections.files_description');
        }

        return __('filament-proovit::filament-proovit.proof_deposit.sections.files_required', [
            'files' => implode(', ', $requiredFiles),
        ]);
    }

    private static function customFieldComponent(ProofTemplateCustomFieldData $field): Component
    {
        $path = sprintf('custom_fields.%s', $field->key);
        $label = $field->label;

        return match (strtolower($field->type)) {
            'textarea' => Textarea::make($path)
                ->label($label)
                ->required($field->required)
                ->columnSpanFull(),
            'select' => Select::make($path)
                ->label($label)
                ->options($field->optionList())
                ->searchable()
                ->preload()
                ->required($field->required)
                ->columnSpanFull(),
            'radio' => Radio::make($path)
                ->label($label)
                ->options($field->optionList())
                ->required($field->required)
                ->columnSpanFull(),
            'date' => DatePicker::make($path)
                ->label($label)
                ->required($field->required),
            'number' => TextInput::make($path)
                ->label($label)
                ->numeric()
                ->required($field->required),
            'toggle', 'checkbox' => Toggle::make($path)
                ->label($label)
                ->required($field->required),
            'email' => TextInput::make($path)
                ->label($label)
                ->email()
                ->required($field->required),
            'url' => TextInput::make($path)
                ->label($label)
                ->url()
                ->required($field->required),
            'tel' => TextInput::make($path)
                ->label($label)
                ->tel()
                ->required($field->required),
            default => TextInput::make($path)
                ->label($label)
                ->required($field->required)
                ->maxLength(255),
        };
    }
}
