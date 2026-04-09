<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Widgets;

use Filament\Widgets\Widget;
use Proovit\LaravelProovit\ProovitClient;

final class RecentProofsWidget extends Widget
{
    protected string $view = 'filament-proovit::widgets.recent-proofs';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $proofs = app(ProovitClient::class)->proofs()->list([
            'limit' => 5,
        ]);

        return [
            'proofs' => array_map(
                static fn (array $proof): array => [
                    'id' => (string) ($proof['id'] ?? ''),
                    'status' => (string) ($proof['status'] ?? 'unknown'),
                    'name' => (string) ($proof['name'] ?? 'Untitled proof'),
                    'description' => (string) ($proof['description'] ?? ''),
                    'certificate_url' => $proof['certificate_url'] ?? null,
                ],
                array_values((array) ($proofs['data'] ?? $proofs['items'] ?? $proofs['proofs'] ?? []))
            ),
        ];
    }
}
