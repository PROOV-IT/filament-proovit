<?php

declare(strict_types=1);

use Proovit\FilamentProovit\FilamentProovitPlugin;

it('registers the plugin id', function (): void {
    expect(FilamentProovitPlugin::make()->getId())->toBe('proovit-billing');
});
