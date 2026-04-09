<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Pages\Page;

final class ProovitOverview extends Page
{
    protected static string $view = 'filament-proovit::pages.proovit-overview';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'ProovIT';

    protected static ?string $title = 'ProovIT Overview';

    protected static ?string $navigationLabel = 'ProovIT Overview';
}
