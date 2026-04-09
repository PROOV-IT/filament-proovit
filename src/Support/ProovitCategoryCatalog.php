<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Proovit\LaravelProovit\DTOs\CategoryData;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

final class ProovitCategoryCatalog
{
    public function __construct(
        private readonly ProovitClient $client,
        private readonly ProovitSettingsRepository $settingsRepository,
    ) {}

    /**
     * @return array<int, CategoryData>
     */
    public function all(): array
    {
        return array_map(
            static fn (array $category): CategoryData => CategoryData::fromArray($category),
            $this->cachedPayload(),
        );
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->all())
            ->filter(static fn (CategoryData $category): bool => $category->isActive)
            ->sortBy('name')
            ->mapWithKeys(static fn (CategoryData $category): array => [
                $category->id => $category->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cachedPayload(): array
    {
        return Cache::remember(
            $this->cacheKey(),
            now()->addMinutes(10),
            function (): array {
                $categories = $this->client->categories()->all([
                    'per_page' => 200,
                    'include_shared' => true,
                ]);

                return array_values(array_map(
                    static fn (CategoryData $category): array => $category->raw,
                    $categories,
                ));
            }
        );
    }

    private function cacheKey(): string
    {
        $settings = $this->settingsRepository->all();
        $connection = (array) Arr::get($settings, 'connection', []);

        return sprintf(
            'proovit.categories.%s',
            md5(implode('|', [
                (string) Arr::get($connection, 'base_url', ''),
                (string) Arr::get($connection, 'selected_company_uuid', ''),
                (string) Arr::get($connection, 'login_email', ''),
            ])),
        );
    }
}
