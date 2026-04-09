<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Schemas\Proofs;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

final class ProofViewFormSchema
{
    /**
     * @return array<int, Component>
     */
    public static function schema(): array
    {
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
}
