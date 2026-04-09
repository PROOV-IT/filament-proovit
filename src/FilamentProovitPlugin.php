<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Proovit\FilamentProovit\Pages\ProovitOverview;
use Proovit\FilamentProovit\Pages\ProovitProofs;
use Proovit\FilamentProovit\Pages\ProovitSettings;
use Proovit\FilamentProovit\Support\Filament\Widgets\ConnectionStatusWidget;
use Proovit\FilamentProovit\Support\Filament\Widgets\RecentProofsWidget;

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
                ProovitOverview::class,
                ProovitProofs::class,
                ProovitSettings::class,
            ])
            ->widgets([
                ConnectionStatusWidget::class,
                RecentProofsWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
