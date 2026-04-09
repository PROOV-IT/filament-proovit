<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas\Proofs;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

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
                        ->required()
                        ->maxLength(255),
                    TextInput::make('category_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.category_id'))
                        ->maxLength(255),
                    TextInput::make('token_reservation_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.token_reservation_id'))
                        ->required()
                        ->maxLength(255),
                    TextInput::make('proof_template_id')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.proof_template_id'))
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.metadata'))
                ->description(__('filament-proovit::filament-proovit.proof_deposit.sections.metadata_description'))
                ->columns(2)
                ->schema([
                    TagsInput::make('share_emails')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.share_emails'))
                        ->placeholder(__('filament-proovit::filament-proovit.proof_deposit.placeholders.share_emails'))
                        ->columnSpanFull(),
                    TagsInput::make('keywords')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.keywords'))
                        ->placeholder(__('filament-proovit::filament-proovit.proof_deposit.placeholders.keywords'))
                        ->columnSpanFull(),
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
                    KeyValue::make('custom_fields')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.custom_fields'))
                        ->columnSpanFull(),
                ]),
            Section::make(__('filament-proovit::filament-proovit.proof_deposit.sections.files'))
                ->description(__('filament-proovit::filament-proovit.proof_deposit.sections.files_description'))
                ->columns(1)
                ->schema([
                    FileUpload::make('files')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.files'))
                        ->multiple()
                        ->required()
                        ->preserveFilenames()
                        ->directory('proovit/proofs')
                        ->columnSpanFull(),
                    Textarea::make('signature_base64')
                        ->label(__('filament-proovit::filament-proovit.proof_deposit.fields.signature_base64'))
                        ->rows(5)
                        ->helperText(__('filament-proovit::filament-proovit.proof_deposit.helpers.signature_base64'))
                        ->columnSpanFull(),
                ]),
        ];
    }
}
