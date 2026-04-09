<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament;

use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\ProovitClient;

final readonly class ProovitDashboardData
{
    /**
     * @param  array<int, ProofData>  $recentProofs
     */
    public function __construct(
        public array $context,
        public array $connection,
        public array $recentProofs,
    ) {}

    public static function fromClient(ProovitClient $client, int $limit = 5): self
    {
        $context = $client->connection()->context();
        $connection = $client->connection()->test();
        $proofs = $client->proofs()->list([
            'limit' => $limit,
        ]);

        $recentProofs = array_map(
            static fn (array $proof): ProofData => ProofData::fromArray($proof),
            array_values((array) ($proofs['data'] ?? $proofs['items'] ?? $proofs['proofs'] ?? []))
        );

        return new self(
            context: [
                'base_url' => $context->baseUrl,
                'app_url' => $context->appUrl,
                'company_name' => $context->companyName,
                'login_email' => $context->loginEmail,
                'selected_company_uuid' => property_exists($context, 'selectedCompanyUuid') ? $context->selectedCompanyUuid : null,
                'mode' => $context->mode->value,
                'features' => $context->features,
                'docs' => $context->docs,
            ],
            connection: [
                'connected' => $connection->connected,
                'mode' => $connection->mode,
                'base_url' => $connection->baseUrl,
                'selected_company_uuid' => property_exists($connection, 'selectedCompanyUuid') ? $connection->selectedCompanyUuid : null,
                'workspace_token' => property_exists($connection, 'workspaceToken') ? $connection->workspaceToken : null,
                'company_name' => $connection->companyName,
                'login_email' => $connection->loginEmail,
            ],
            recentProofs: $recentProofs,
        );
    }
}
