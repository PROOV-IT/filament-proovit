<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\Widget;

final class ConnectionStatusWidget extends Widget
{
    public function getView(): string
    {
        return 'filament-proovit::widgets.connection-status';
    }

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'label' => config('proovit-filament.navigation.label', 'ProovIT'),
            'baseUrl' => config('proovit.connection.base_url'),
            'enabled' => (bool) config('proovit-filament.widgets.enabled', true),
        ];
    }
}
