<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit;

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
    }
}
