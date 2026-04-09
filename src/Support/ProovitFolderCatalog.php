<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Proovit\LaravelProovit\DTOs\FolderData;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

final class ProovitFolderCatalog
{
    public function __construct(
        private readonly ProovitClient $client,
        private readonly ProovitSettingsRepository $settingsRepository,
    ) {}

    /**
     * @return array<int, FolderData>
     */
    public function all(): array
    {
        return array_map(
            static fn (array $folder): FolderData => FolderData::fromArray($folder),
            $this->cachedPayload(),
        );
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->all())
            ->filter(static fn (FolderData $folder): bool => $folder->isActive)
            ->sortBy('name')
            ->mapWithKeys(static fn (FolderData $folder): array => [
                $folder->id => $folder->name,
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
                $folders = $this->client->folders()->all([
                    'per_page' => 200,
                    'accessible_by' => 'write',
                ]);

                return array_values(array_map(
                    static fn (FolderData $folder): array => $folder->raw,
                    $folders,
                ));
            }
        );
    }

    private function cacheKey(): string
    {
        $settings = $this->settingsRepository->all();
        $connection = (array) Arr::get($settings, 'connection', []);

        return sprintf(
            'proovit.folders.%s',
            md5(implode('|', [
                (string) Arr::get($connection, 'base_url', ''),
                (string) Arr::get($connection, 'selected_company_uuid', ''),
                (string) Arr::get($connection, 'login_email', ''),
            ])),
        );
    }
}
