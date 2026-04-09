<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Proovit\LaravelProovit\ProovitClient;
use Throwable;

final class ConnectionStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        try {
            $client = app(ProovitClient::class);
            $context = $client->connection()->context();
            $connection = $client->connection()->test();

            return [
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.company_name'), $context->companyName ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.login_email'), $context->loginEmail ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.base_url'), $context->baseUrl ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.mode'), $context->mode->value)
                    ->description($connection->connected ? __('filament-proovit::filament-proovit.widgets.connection.connected') : __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
            ];
        } catch (Throwable) {
            return [
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.company_name'), __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.login_email'), __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.base_url'), __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
                Stat::make(__('filament-proovit::filament-proovit.widgets.connection.mode'), __('filament-proovit::filament-proovit.widgets.connection.unknown')),
            ];
        }
    }

    protected function getHeading(): ?string
    {
        return __('filament-proovit::filament-proovit.widgets.connection.heading');
    }

    protected function getDescription(): ?string
    {
        return __('filament-proovit::filament-proovit.widgets.connection.description');
    }
}
