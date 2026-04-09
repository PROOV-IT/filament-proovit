<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit;

use Illuminate\Support\Facades\Event;
use Proovit\FilamentProovit\Listeners\RecordTokenReservationHistory;
use Proovit\LaravelProovit\Events\Tokens\TokenReserved;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class FilamentProovitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-proovit')
            ->hasConfigFile('proovit-filament')
            ->hasTranslations()
            ->hasViews();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen(TokenReserved::class, RecordTokenReservationHistory::class);
    }
}
