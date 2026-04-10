<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Proovit\FilamentProovit\Support\Filament\Schemas\Proofs\ProofViewFormSchema;
use Proovit\FilamentProovit\Support\ProovitApiSessionGuard;
use Proovit\FilamentProovit\Support\ProovitProofTemplateCatalog;
use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\DTOs\ProofTemplateData;
use Proovit\LaravelProovit\ProovitClient;
use Throwable;

final class ProovitProofView extends Page
{
    public array $data = [];

    public ?string $proofId = null;

    private ?ProofTemplateData $currentTemplate = null;

    protected static ?string $slug = 'proovit/proofs/{proof}';

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-proof-view';
    }

    public function getTitle(): string
    {
        return $this->data['title'] ?? __('filament-proovit::filament-proovit.proof_view.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.proof_view.navigation');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-proovit::filament-proovit.navigation.label');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(string $proof): void
    {
        $this->proofId = $proof;
        $this->loadProof();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components(ProofViewFormSchema::schema($this->currentTemplate, $this->data['files'] ?? []));
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament-proovit::filament-proovit.proof_view.note.heading'))
                ->description(__('filament-proovit::filament-proovit.proof_view.note.body')),
            $this->getFormContentComponent(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('filament-proovit::filament-proovit.proof_view.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $this->loadProof();
                }),
            Action::make('open_certificate')
                ->label(__('filament-proovit::filament-proovit.proof_view.actions.open_certificate'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->visible(fn (): bool => filled($this->data['certificate_url'] ?? null))
                ->url(fn (): ?string => $this->data['certificate_url'] ?? null)
                ->openUrlInNewTab(),
            Action::make('revoke')
                ->label(__('filament-proovit::filament-proovit.proof_view.actions.revoke'))
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (): bool => ! in_array((string) ($this->data['status'] ?? ''), ['revoked', 'deleted'], true))
                ->action(function (): void {
                    if ($this->proofId === null || $this->proofId === '') {
                        return;
                    }

                    app(ProovitApiSessionGuard::class)->withAutoRefresh(
                        fn (): mixed => app(ProovitClient::class)->proofs()->revoke($this->proofId),
                    );
                    $this->loadProof();

                    Notification::make()
                        ->title(__('filament-proovit::filament-proovit.proof_view.notifications.revoked_title'))
                        ->body(__('filament-proovit::filament-proovit.proof_view.notifications.revoked_body'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form');
    }

    private function loadProof(): void
    {
        if ($this->proofId === null || $this->proofId === '') {
            $this->data = [];

            return;
        }

        $client = app(ProovitClient::class);
        $guard = app(ProovitApiSessionGuard::class);
        $proof = $guard->withAutoRefresh(fn (): ProofData => $client->proofs()->show($this->proofId));
        $history = $guard->withAutoRefresh(fn (): array => $client->proofs()->history($this->proofId));
        $certificate = $proof->certificateUrl;
        $template = $this->templateFromProof($proof);
        $this->currentTemplate = $template;

        if (! filled($certificate) && ! in_array($proof->status, ['revoked', 'deleted'], true)) {
            try {
                $certificate = $guard->withAutoRefresh(
                    fn (): ?string => $client->proofs()->getCertificateLink($this->proofId)->url,
                );
            } catch (Throwable) {
                $certificate = null;
            }
        }

        $this->form->fill($this->proofDataToState($proof, $history, $certificate, $template));
    }

    /**
     * @param  array<int, array<string, mixed>>  $history
     * @return array<string, mixed>
     */
    private function proofDataToState(ProofData $proof, array $history, ?string $certificateUrl, ?ProofTemplateData $template): array
    {
        $metadata = $proof->metadata;

        return [
            'name' => $proof->name,
            'title' => $proof->raw['title'] ?? $proof->name,
            'seq' => $proof->raw['seq'] ?? null,
            'status' => $proof->status,
            'status_label' => $proof->raw['status_label'] ?? Str::headline($proof->status),
            'signed_at' => $proof->signedAt,
            'certificate_url' => $certificateUrl,
            'description' => $proof->description,
            'template_name' => $template?->name,
            'template_slug' => $template?->slug,
            'template_description' => $template?->description,
            'template_signature' => $template?->requiresSignature() ? __('filament-proovit::filament-proovit.proof_view.template.signature_required') : __('filament-proovit::filament-proovit.proof_view.template.no_signature_required'),
            'template_required_files' => $template !== null ? implode(', ', $template->requiredFiles()) : null,
            'template_fields' => $this->templateFieldsState($template, $metadata),
            'metadata' => $this->encodeJson($metadata),
            'history' => $this->encodeJson($history),
            'files' => $this->proofFilesState($proof),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function proofFilesState(ProofData $proof): array
    {
        return array_values(array_map(
            function (array $file, int $index): array {
                $links = (array) Arr::get($file, 'links', []);
                $downloadUrl = (string) (
                    $links['api_decrypt']
                    ?? $links['signed']
                    ?? Arr::get($file, 'download_url')
                    ?? Arr::get($file, 'url')
                    ?? ''
                );

                return [
                    'name' => (string) ($file['name'] ?? $file['filename'] ?? sprintf('file-%d', $index + 1)),
                    'filename' => (string) ($file['filename'] ?? $file['original_name'] ?? $file['name'] ?? ''),
                    'mime_type' => (string) ($file['mime_type'] ?? $file['mimeType'] ?? ''),
                    'size' => filled($file['size'] ?? null) ? (string) $file['size'] : '',
                    'download_url' => $downloadUrl,
                    'links' => $this->encodeJson($links),
                ];
            },
            $proof->files,
            array_keys($proof->files)
        ));
    }

    private function templateFromProof(ProofData $proof): ?ProofTemplateData
    {
        $templateId = (string) (Arr::get($proof->raw, 'template.id')
            ?? Arr::get($proof->raw, 'template.uuid')
            ?? Arr::get($proof->raw, 'proof_template.id')
            ?? Arr::get($proof->raw, 'proof_template_id')
            ?? Arr::get($proof->metadata, 'template.id')
            ?? Arr::get($proof->metadata, 'template_id')
            ?? '');

        if ($templateId === '') {
            return null;
        }

        return app(ProovitProofTemplateCatalog::class)->find($templateId);
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    private function templateFieldsState(?ProofTemplateData $template, array $metadata): array
    {
        if ($template === null) {
            return [];
        }

        $customFields = (array) Arr::get($metadata, 'custom_fields', []);

        return array_reduce($template->customFields(), static function (array $carry, $field) use ($customFields): array {
            $carry[$field->key] = $customFields[$field->key] ?? null;

            return $carry;
        }, []);
    }

    /**
     * @param  array<string, mixed>|array<int, mixed>  $value
     */
    private function encodeJson(array $value): string
    {
        $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return is_string($json) ? $json : '';
    }
}
