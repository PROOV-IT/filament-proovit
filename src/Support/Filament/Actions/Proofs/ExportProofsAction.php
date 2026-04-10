<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Support\Filament\Actions\Proofs;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Proovit\FilamentProovit\Support\ProovitApiSessionGuard;
use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\ProovitClient;
use RuntimeException;
use Throwable;
use ZipArchive;

final class ExportProofsAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('export')
            ->label(__('filament-proovit::filament-proovit.proofs.actions.export'))
            ->icon('heroicon-o-archive-box-arrow-down')
            ->color('gray')
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection|EloquentCollection $records): mixed {
                try {
                    $zipPath = self::buildArchive($records);

                    Notification::make()
                        ->title(__('filament-proovit::filament-proovit.proofs.notifications.exported_title'))
                        ->body(__('filament-proovit::filament-proovit.proofs.notifications.exported_body'))
                        ->success()
                        ->send();

                    return response()->download(
                        $zipPath,
                        basename($zipPath),
                    )->deleteFileAfterSend(true);
                } catch (RuntimeException $exception) {
                    Notification::make()
                        ->title(__('filament-proovit::filament-proovit.proofs.notifications.export_failed_title'))
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();

                    return null;
                }
            });
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $records
     */
    private static function buildArchive(Collection $records): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.zip_extension_required'));
        }

        $proofs = self::fetchProofs($records);
        if ($proofs === []) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.no_proofs_selected'));
        }

        $directory = storage_path('app/proovit/exports');
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.unable_create_directory'));
        }

        $zipPath = sprintf('%s/proovit-proofs-export-%s.zip', $directory, now()->format('Ymd-His'));
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.unable_create_archive'));
        }

        $zip->addFromString('proofs.csv', self::buildCsv($proofs));

        foreach ($proofs as $proof) {
            $proofFolder = sprintf('proofs/%s', $proof->id);

            if (filled($proof->certificateUrl)) {
                $certificate = self::downloadCertificate($proof);
                if ($certificate !== null && $certificate !== '') {
                    $zip->addFromString(sprintf('%s/certificate.pdf', $proofFolder), $certificate);
                }
            }

            foreach ($proof->files as $index => $file) {
                $contents = self::downloadProofFile($file);
                if ($contents === null || $contents === '') {
                    continue;
                }

                $fileName = self::fileNameFromPayload($file, $index + 1);
                $zip->addFromString(sprintf('%s/files/%s', $proofFolder, $fileName), $contents);
            }
        }

        $zip->close();

        return $zipPath;
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $records
     * @return array<int, ProofData>
     */
    private static function fetchProofs(Collection $records): array
    {
        $client = app(ProovitClient::class);
        $guard = app(ProovitApiSessionGuard::class);
        $proofs = [];

        foreach ($records as $record) {
            $proofId = (string) ($record['id'] ?? $record['uuid'] ?? '');
            if ($proofId === '') {
                continue;
            }

            $proofs[] = $guard->withAutoRefresh(fn (): ProofData => $client->proofs()->show($proofId));
        }

        return $proofs;
    }

    /**
     * @param  array<int, ProofData>  $proofs
     */
    private static function buildCsv(array $proofs): string
    {
        $handle = fopen('php://temp', 'w+');
        if ($handle === false) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.unable_create_csv'));
        }

        fputcsv($handle, [
            'id',
            'seq',
            'name',
            'title',
            'status',
            'status_label',
            'signed_at',
            'certificate_url',
            'folder_name',
            'category_name',
            'template_name',
            'files_count',
            'created_at',
            'updated_at',
            'metadata_json',
        ]);

        foreach ($proofs as $proof) {
            $raw = $proof->raw;
            fputcsv($handle, [
                $proof->id,
                (string) ($raw['seq'] ?? ''),
                (string) ($raw['name'] ?? ''),
                (string) ($raw['title'] ?? ''),
                $proof->status,
                (string) ($raw['status_label'] ?? ''),
                (string) ($proof->signedAt ?? ''),
                (string) ($proof->certificateUrl ?? ($raw['links']['certificate_pdf'] ?? '')),
                (string) ($raw['folder']['name'] ?? ''),
                (string) ($raw['category']['name'] ?? ''),
                (string) ($raw['template']['name'] ?? ''),
                (string) ($proof->filesCount ?? count($proof->files)),
                (string) ($raw['created_at'] ?? ''),
                (string) ($raw['updated_at'] ?? ''),
                json_encode($proof->metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        if ($contents === false) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.unable_finalize_csv'));
        }

        return $contents;
    }

    /**
     * @param  array<string, mixed>  $file
     */
    private static function downloadProofFile(array $file): ?string
    {
        $links = (array) ($file['links'] ?? []);
        $url = (string) ($links['api_decrypt'] ?? $links['signed'] ?? '');

        if ($url === '') {
            return null;
        }

        try {
            return self::downloadUrl($url);
        } catch (Throwable $exception) {
            logger()->warning('ProovIT proof file could not be exported.', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private static function downloadCertificate(ProofData $proof): ?string
    {
        try {
            return app(ProovitApiSessionGuard::class)->withAutoRefresh(
                fn (): string => app(ProovitClient::class)->proofs()->downloadCertificate($proof->id),
            );
        } catch (Throwable $exception) {
            logger()->warning('ProovIT proof certificate could not be exported.', [
                'proof_id' => $proof->id,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $file
     */
    private static function fileNameFromPayload(array $file, int $index): string
    {
        $name = (string) ($file['name'] ?? '');
        if ($name === '') {
            $name = sprintf('file-%d', $index);
        }

        $name = Str::of($name)
            ->replaceMatches('/[^\w\-.]+/u', '-')
            ->trim('-')
            ->toString();

        return $name !== '' ? $name : sprintf('file-%d', $index);
    }

    private static function downloadUrl(string $url): string
    {
        $parts = parse_url($url);
        if (! is_array($parts)) {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.invalid_file_url', ['url' => $url]));
        }

        $path = (string) ($parts['path'] ?? '');
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';

        if ($path === '') {
            throw new RuntimeException(__('filament-proovit::filament-proovit.proofs.export.errors.invalid_file_url', ['url' => $url]));
        }

        return app(ProovitApiSessionGuard::class)->withAutoRefresh(
            fn (): string => app(ProovitClient::class)->download($path.$query),
        );
    }
}
