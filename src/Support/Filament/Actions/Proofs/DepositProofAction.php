<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Actions\Proofs;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Proovit\FilamentProovit\Support\Filament\Schemas\Proofs\ProofDepositActionSchema;
use Proovit\FilamentProovit\Support\ProovitProofTemplateCatalog;
use Proovit\LaravelProovit\Builders\Proofs\ProofBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofFilesBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofMetadataBuilder;
use Proovit\LaravelProovit\Builders\Proofs\ProofSignatureBuilder;
use Proovit\LaravelProovit\DTOs\ProofTemplateData;
use Proovit\LaravelProovit\ProovitClient;
use Throwable;

final class DepositProofAction
{
    public static function make(): Action
    {
        return Action::make('deposit_proof')
            ->label(__('filament-proovit::filament-proovit.proof_deposit.actions.deposit'))
            ->icon('heroicon-o-cloud-arrow-up')
            ->color('primary')
            ->schema(ProofDepositActionSchema::schema())
            ->modalWidth('7xl')
            ->action(function (array $data, TableWidget $livewire): void {
                try {
                    $template = self::templateFromData($data);
                    $reservation = app(ProovitClient::class)->tokens()->reserve();
                    $reservationId = (string) ($reservation->reservationId ?? '');

                    if ($reservationId === '') {
                        throw new \RuntimeException(__('filament-proovit::filament-proovit.proof_deposit.notifications.token_reservation_failed'));
                    }

                    $proofBuilder = self::buildProofBuilder($data, $template);
                    $proofBuilder->withTokenReservationId($reservationId);
                    $client = app(ProovitClient::class);
                    $proof = $client->proofs()->init($proofBuilder);

                    if ($proofBuilder->hasFiles()) {
                        $client->proofs()->uploadFiles($proof->id, $proofBuilder);
                    }

                    if ($proofBuilder->hasSignature()) {
                        $client->proofs()->sign($proof->id, $proofBuilder);
                    }

                    Notification::make()
                        ->title(__('filament-proovit::filament-proovit.proof_deposit.notifications.created_title'))
                        ->body(__('filament-proovit::filament-proovit.proof_deposit.notifications.created_body'))
                        ->success()
                        ->send();

                    $livewire->resetTable();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->title(__('filament-proovit::filament-proovit.proof_deposit.notifications.failed_title'))
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function buildProofBuilder(array $data, ?ProofTemplateData $template = null): ProofBuilder
    {
        $builder = app(ProovitClient::class)->proofBuilder()
            ->withName((string) Arr::get($data, 'name', ''))
            ->withDescription(Arr::get($data, 'description'))
            ->withProofTemplateId((string) Arr::get($data, 'proof_template_id', ''));

        $folderId = trim((string) Arr::get($data, 'folder_id', ''));
        if (($template === null || $template->displayFolders()) && $folderId !== '') {
            $builder->withFolderId($folderId);
        }

        $categoryId = trim((string) Arr::get($data, 'category_id', ''));
        if (($template === null || $template->displayCategories()) && $categoryId !== '') {
            $builder->withCategoryId($categoryId);
        }

        $builder->withMetadata(static function (ProofMetadataBuilder $metadata) use ($data, $template): void {
            $metadata
                ->withShareEmails(
                    ($template === null || $template->isShared())
                        ? array_values(array_filter((array) Arr::get($data, 'share_emails', [])))
                        : [],
                )
                ->withAnonymous((bool) Arr::get($data, 'is_anonymous', false))
                ->withKeywords(
                    ($template === null || $template->displayTags())
                        ? array_values(array_filter((array) Arr::get($data, 'keywords', [])))
                        : [],
                )
                ->withCustomFields(self::customFieldsFromData($data, $template));

            $location = trim((string) Arr::get($data, 'location', ''));
            $lat = Arr::get($data, 'lat');
            $lng = Arr::get($data, 'lng');

            if ($location !== '') {
                $metadata->withLocation(
                    $location,
                    filled($lat) ? (float) $lat : null,
                    filled($lng) ? (float) $lng : null,
                );
            }
        });

        $builder->withFiles(static function (ProofFilesBuilder $files) use ($data): void {
            $disk = config('filesystems.default', 'local');
            $uploadedFiles = Arr::wrap(Arr::get($data, 'files', []));

            foreach ($uploadedFiles as $uploadedFile) {
                $path = null;
                $filename = null;
                $mimeType = null;

                if (is_string($uploadedFile)) {
                    $path = $uploadedFile;
                    $filename = basename($uploadedFile);
                } elseif (is_object($uploadedFile) && method_exists($uploadedFile, 'getRealPath')) {
                    $path = (string) $uploadedFile->getRealPath();
                    $filename = method_exists($uploadedFile, 'getClientOriginalName') ? (string) $uploadedFile->getClientOriginalName() : basename($path);
                    $mimeType = method_exists($uploadedFile, 'getMimeType') ? (string) $uploadedFile->getMimeType() : null;
                } elseif (is_array($uploadedFile)) {
                    $path = (string) ($uploadedFile['path'] ?? $uploadedFile['tmp_path'] ?? $uploadedFile['name'] ?? '');
                    $filename = (string) ($uploadedFile['name'] ?? basename($path));
                    $mimeType = $uploadedFile['mime_type'] ?? $uploadedFile['mimeType'] ?? null;
                }

                if ($path === null || $path === '') {
                    continue;
                }

                if (is_file($path)) {
                    $contents = file_get_contents($path);
                } else {
                    $contents = Storage::disk($disk)->get($path);
                }

                if ($contents === false || $contents === '') {
                    continue;
                }

                if ($mimeType === null || $mimeType === '') {
                    try {
                        $mimeType = is_file($path) ? (string) (mime_content_type($path) ?: 'application/octet-stream') : (string) (Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream');
                    } catch (Throwable) {
                        $mimeType = 'application/octet-stream';
                    }
                }

                $files->addFileFromContents(
                    $contents,
                    $filename,
                    $mimeType,
                    false,
                );
            }
        });

        $signatureBase64 = trim((string) Arr::get($data, 'signature_base64', ''));
        if ($signatureBase64 !== '') {
            $builder->withSignature(static function (ProofSignatureBuilder $signature) use ($data, $signatureBase64): void {
                $signature->withSignatureBase64($signatureBase64)
                    ->withClientContext(self::clientContext($data));
            });
        }

        return $builder;
    }

    private static function templateFromData(array $data): ?ProofTemplateData
    {
        return app(ProovitProofTemplateCatalog::class)->find((string) Arr::get($data, 'proof_template_id', ''));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function customFieldsFromData(array $data, ?ProofTemplateData $template): array
    {
        $customFields = (array) Arr::get($data, 'custom_fields', []);

        if ($template === null) {
            return $customFields;
        }

        $allowedKeys = array_flip(array_map(
            static fn ($field): string => $field->key,
            $template->customFields(),
        ));

        return array_intersect_key($customFields, $allowedKeys);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function clientContext(array $data): array
    {
        return array_filter([
            'userAgent' => request()->userAgent(),
            'language' => request()->header('Accept-Language', app()->getLocale()),
            'timeZone' => config('app.timezone'),
            'pageUrl' => request()->fullUrl(),
            'clientSignedAt' => now()->toIso8601String(),
        ], static fn (mixed $value): bool => $value !== null && $value !== '');
    }
}
