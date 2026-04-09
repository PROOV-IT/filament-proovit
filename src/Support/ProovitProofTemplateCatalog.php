<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Proovit\LaravelProovit\DTOs\ProofTemplateData;
use Proovit\LaravelProovit\ProovitClient;
use Proovit\LaravelProovit\Support\ProovitSettingsRepository;

final class ProovitProofTemplateCatalog
{
    public function __construct(
        private readonly ProovitClient $client,
        private readonly ProovitSettingsRepository $settingsRepository,
    ) {}

    /**
     * @return array<int, ProofTemplateData>
     */
    public function all(): array
    {
        return array_map(
            static fn (array $template): ProofTemplateData => ProofTemplateData::fromArray($template),
            $this->cachedPayload(),
        );
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->all())
            ->filter(static fn (ProofTemplateData $template): bool => $template->isActive)
            ->sortBy('name')
            ->mapWithKeys(static fn (ProofTemplateData $template): array => [
                $template->id => $template->name,
            ])
            ->all();
    }

    public function find(?string $templateId): ?ProofTemplateData
    {
        if ($templateId === null || $templateId === '') {
            return null;
        }

        return collect($this->all())
            ->first(static fn (ProofTemplateData $template): bool => $template->id === $templateId);
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
                $response = $this->client->proofTemplates()->list([
                    'per_page' => 100,
                ]);

                $templates = Arr::get($response, 'data', $response['items'] ?? $response['templates'] ?? []);

                return array_values(array_filter(
                    array_map(
                        static fn (mixed $template): array => is_array($template) ? $template : [],
                        is_array($templates) ? $templates : [],
                    ),
                ));
            }
        );
    }

    private function cacheKey(): string
    {
        $settings = $this->settingsRepository->all();
        $connection = (array) Arr::get($settings, 'connection', []);

        return sprintf(
            'proovit.proof_templates.%s',
            md5(implode('|', [
                (string) Arr::get($connection, 'base_url', ''),
                (string) Arr::get($connection, 'selected_company_uuid', ''),
                (string) Arr::get($connection, 'login_email', ''),
            ])),
        );
    }
}
