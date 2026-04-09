<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas\Proofs;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Proovit\LaravelProovit\DTOs\ProofTemplateCustomFieldData;
use Proovit\LaravelProovit\DTOs\ProofTemplateData;

final class ProofViewFormSchema
{
    /**
     * @return array<int, Component>
     */
    public static function schema(?ProofTemplateData $template = null, array $files = []): array
    {
        $templateFieldComponents = $template === null ? [] : self::templateFieldComponents($template);
        $fileComponents = $files === [] ? [] : self::fileComponents();

        return [
            Section::make(__('filament-proovit::filament-proovit.proof_view.sections.summary'))
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.name'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('title')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.title'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('seq')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.seq'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('status_label')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.status'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('signed_at')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.signed_at'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('certificate_url')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.certificate_url'))
                        ->disabled()
                        ->dehydrated(false),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_view.sections.template'))
                ->columns(2)
                ->visible($template !== null)
                ->schema([
                    TextInput::make('template_name')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.template_name'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('template_slug')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.template_slug'))
                        ->disabled()
                        ->dehydrated(false),
                    Textarea::make('template_description')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.template_description'))
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(3)
                        ->columnSpanFull(),
                    TextInput::make('template_signature')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.template_signature'))
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('template_required_files')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.template_required_files'))
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_view.sections.template_fields'))
                ->columns(2)
                ->schema($templateFieldComponents)
                ->visible($templateFieldComponents !== []),
            Section::make(__('filament-proovit::filament-proovit.proof_view.sections.files'))
                ->visible($fileComponents !== [])
                ->schema([
                    Repeater::make('files')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.files'))
                        ->schema($fileComponents)
                        ->columnSpanFull()
                        ->disabled()
                        ->dehydrated(false),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_view.sections.metadata'))
                ->columns(2)
                ->schema([
                    Textarea::make('description')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.description'))
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(4)
                        ->columnSpanFull(),
                    Textarea::make('metadata')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.metadata'))
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(6)
                        ->columnSpanFull(),
                    Textarea::make('history')
                        ->label(__('filament-proovit::filament-proovit.proof_view.fields.history'))
                        ->disabled()
                        ->dehydrated(false)
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function templateFieldComponents(ProofTemplateData $template): array
    {
        return array_map(
            static fn (ProofTemplateCustomFieldData $field): Component => self::templateFieldComponent($field),
            $template->customFields(),
        );
    }

    private static function templateFieldComponent(ProofTemplateCustomFieldData $field): Component
    {
        $path = sprintf('template_fields.%s', $field->key);

        return match (strtolower($field->type)) {
            'textarea' => Textarea::make($path)
                ->label($field->label)
                ->disabled()
                ->dehydrated(false)
                ->rows(3)
                ->columnSpanFull(),
            'select' => Select::make($path)
                ->label($field->label)
                ->options($field->optionList())
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
            'radio' => Radio::make($path)
                ->label($field->label)
                ->options($field->optionList())
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
            'date' => DatePicker::make($path)
                ->label($field->label)
                ->disabled()
                ->dehydrated(false),
            'toggle', 'checkbox' => Toggle::make($path)
                ->label($field->label)
                ->disabled()
                ->dehydrated(false),
            default => TextInput::make($path)
                ->label($field->label)
                ->disabled()
                ->dehydrated(false),
        };
    }

    /**
     * @return array<int, Component>
     */
    private static function fileComponents(): array
    {
        return [
            TextInput::make('name')
                ->label(__('filament-proovit::filament-proovit.proof_view.file_fields.name'))
                ->disabled()
                ->dehydrated(false),
            TextInput::make('filename')
                ->label(__('filament-proovit::filament-proovit.proof_view.file_fields.filename'))
                ->disabled()
                ->dehydrated(false),
            TextInput::make('mime_type')
                ->label(__('filament-proovit::filament-proovit.proof_view.file_fields.mime_type'))
                ->disabled()
                ->dehydrated(false),
            TextInput::make('size')
                ->label(__('filament-proovit::filament-proovit.proof_view.file_fields.size'))
                ->disabled()
                ->dehydrated(false),
            TextInput::make('download_url')
                ->label(__('filament-proovit::filament-proovit.proof_view.file_fields.download_url'))
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
            Textarea::make('links')
                ->label(__('filament-proovit::filament-proovit.proof_view.file_fields.links'))
                ->disabled()
                ->dehydrated(false)
                ->rows(4)
                ->columnSpanFull(),
        ];
    }
}
