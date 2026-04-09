<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Pages\Page;

final class ProovitOverview extends Page
{
    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-overview';
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'ProovIT';

    protected static ?string $title = 'ProovIT Overview';

    protected static ?string $navigationLabel = 'ProovIT Overview';
}
