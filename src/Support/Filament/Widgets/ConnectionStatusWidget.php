<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\Widget;
use Proovit\LaravelProovit\ProovitClient;

final class ConnectionStatusWidget extends Widget
{
    protected string $view = 'filament-proovit::widgets.connection-status';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $client = app(ProovitClient::class);
        $context = $client->connection()->context();
        $connection = $client->connection()->test();

        return [
            'label' => __('filament-proovit::filament-proovit.navigation.label'),
            'baseUrl' => $context->baseUrl,
            'appUrl' => $context->appUrl,
            'companyName' => $context->companyName,
            'loginEmail' => $context->loginEmail,
            'mode' => $context->mode->value,
            'enabled' => (bool) config('proovit-filament.widgets.enabled', true),
            'connected' => $connection->connected,
            'workspaceToken' => $connection->workspaceToken,
            'features' => $context->features,
        ];
    }
}
