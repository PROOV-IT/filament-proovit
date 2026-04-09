<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Proovit\LaravelProovit\DTOs\TokenBalanceData;
use Proovit\LaravelProovit\ProovitClient;

final class TokenBalanceWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $balance = $this->balance();

        return [
            Stat::make(
                __('filament-proovit::filament-proovit.widgets.tokens.balance'),
                (string) $balance->balance,
            )
                ->description($this->companyLabel($balance)
                    ? __('filament-proovit::filament-proovit.widgets.tokens.company', [
                        'company' => $this->companyLabel($balance),
                    ])
                    : __('filament-proovit::filament-proovit.widgets.tokens.unknown_company')),
        ];
    }

    protected function getHeading(): ?string
    {
        return __('filament-proovit::filament-proovit.widgets.tokens.heading');
    }

    protected function getDescription(): ?string
    {
        return __('filament-proovit::filament-proovit.widgets.tokens.description');
    }

    private function balance(): TokenBalanceData
    {
        try {
            return app(ProovitClient::class)->tokens()->balance();
        } catch (\Throwable) {
            return new TokenBalanceData(balance: 0);
        }
    }

    private function companyLabel(TokenBalanceData $balance): ?string
    {
        $companyName = app(ProovitClient::class)->config()->companyName;

        if (is_string($companyName) && $companyName !== '') {
            return $companyName;
        }

        return $balance->companyId;
    }
}
