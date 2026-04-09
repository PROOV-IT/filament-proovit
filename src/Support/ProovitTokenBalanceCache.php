<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Proovit\LaravelProovit\DTOs\TokenBalanceData;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

final class ProovitTokenBalanceCache
{
    public function __construct(
        private readonly ProovitClient $client,
        private readonly ProovitSettingsRepository $settingsRepository,
    ) {}

    public function get(): TokenBalanceData
    {
        return Cache::remember(
            $this->cacheKey(),
            now()->addMinutes(1),
            fn (): TokenBalanceData => $this->client->tokens()->balance(),
        );
    }

    private function cacheKey(): string
    {
        $settings = $this->settingsRepository->all();
        $connection = (array) Arr::get($settings, 'connection', []);

        return sprintf(
            'proovit.token_balance.%s',
            md5(implode('|', [
                (string) Arr::get($connection, 'base_url', ''),
                (string) Arr::get($connection, 'selected_company_uuid', ''),
                (string) Arr::get($connection, 'login_email', ''),
            ])),
        );
    }
}
