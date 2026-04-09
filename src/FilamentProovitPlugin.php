<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Proovit\FilamentProovit\Pages\ProovitCertificates;
use Proovit\FilamentProovit\Pages\ProovitProofExports;
use Proovit\FilamentProovit\Pages\ProovitProofs;
use Proovit\FilamentProovit\Pages\ProovitProofView;
use Proovit\FilamentProovit\Pages\ProovitSettings;
use Proovit\FilamentProovit\Pages\ProovitTokenReservations;
use Proovit\FilamentProovit\Pages\ProovitTokenReservationView;
use Proovit\FilamentProovit\Support\Filament\Widgets\ConnectionStatusWidget;
use Proovit\FilamentProovit\Support\Filament\Widgets\ProofDepositWidget;
use Proovit\FilamentProovit\Support\Filament\Widgets\RecentProofsWidget;
use Proovit\FilamentProovit\Support\Filament\Widgets\TokenBalanceWidget;

final class FilamentProovitPlugin implements Plugin
{
    public static function make(): self
    {
        return new self;
    }

    public function getId(): string
    {
        return 'proovit';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                ProovitProofs::class,
                ProovitProofExports::class,
                ProovitCertificates::class,
                ProovitProofView::class,
                ProovitTokenReservations::class,
                ProovitTokenReservationView::class,
                ProovitSettings::class,
            ])
            ->widgets([
                ProofDepositWidget::class,
                TokenBalanceWidget::class,
                ConnectionStatusWidget::class,
                RecentProofsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
