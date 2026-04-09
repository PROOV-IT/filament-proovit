<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Proovit\FilamentProovit\Support\Filament\ProovitDashboardData;
use Proovit\LaravelProovit\ProovitClient;

final class ConnectionStatusWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $data = $this->dashboardData();

        return [
            Stat::make(
                __('filament-proovit::filament-proovit.widgets.connection.company_name'),
                $data->context['company_name'] ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured'),
            ),
            Stat::make(
                __('filament-proovit::filament-proovit.widgets.connection.login_email'),
                $data->context['login_email'] ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured'),
            ),
            Stat::make(
                __('filament-proovit::filament-proovit.widgets.connection.base_url'),
                $data->context['base_url'] ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured'),
            ),
            Stat::make(
                __('filament-proovit::filament-proovit.widgets.connection.mode'),
                $data->context['mode'] ?? __('filament-proovit::filament-proovit.widgets.connection.unknown'),
            )
                ->description($data->connection['connected'] ? __('filament-proovit::filament-proovit.widgets.connection.connected') : __('filament-proovit::filament-proovit.widgets.connection.not_configured')),
        ];
    }

    protected function getHeading(): ?string
    {
        return __('filament-proovit::filament-proovit.widgets.connection.heading');
    }

    protected function getDescription(): ?string
    {
        return __('filament-proovit::filament-proovit.widgets.connection.description');
    }

    private function dashboardData(): ProovitDashboardData
    {
        try {
            return ProovitDashboardData::fromClient(app(ProovitClient::class));
        } catch (\Throwable) {
            return new ProovitDashboardData(
                context: [
                    'company_name' => null,
                    'login_email' => null,
                    'base_url' => null,
                    'mode' => __('filament-proovit::filament-proovit.widgets.connection.unknown'),
                ],
                connection: [
                    'connected' => false,
                ],
                recentProofs: [],
            );
        }
    }
}
