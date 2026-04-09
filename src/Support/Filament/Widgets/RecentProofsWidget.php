<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\Widget;

final class RecentProofsWidget extends Widget
{
    protected static string $view = 'filament-proovit::widgets.recent-proofs';

    protected int | string | array $columnSpan = 'full';
}
