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
use Illuminate\Support\Str;
use Proovit\FilamentProovit\Support\Filament\Schemas\Proofs\ProofViewFormSchema;
use Proovit\LaravelProovit\DTOs\ProofData;
use Proovit\LaravelProovit\ProovitClient;
use Throwable;

final class ProovitProofView extends Page
{
    public array $data = [];

    public ?string $proofId = null;

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
        return $schema->components(ProofViewFormSchema::schema());
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

                    app(ProovitClient::class)->proofs()->revoke($this->proofId);
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
        $proof = $client->proofs()->show($this->proofId);
        $history = $client->proofs()->history($this->proofId);
        $certificate = $proof->certificateUrl;

        if (! filled($certificate) && ! in_array($proof->status, ['revoked', 'deleted'], true)) {
            try {
                $certificate = $client->proofs()->getCertificateLink($this->proofId)->url;
            } catch (Throwable) {
                $certificate = null;
            }
        }

        $this->form->fill($this->proofDataToState($proof, $history, $certificate));
    }

    /**
     * @param  array<int, array<string, mixed>>  $history
     * @return array<string, mixed>
     */
    private function proofDataToState(ProofData $proof, array $history, ?string $certificateUrl): array
    {
        return [
            'name' => $proof->name,
            'title' => $proof->raw['title'] ?? $proof->name,
            'seq' => $proof->raw['seq'] ?? null,
            'status' => $proof->status,
            'status_label' => $proof->raw['status_label'] ?? Str::headline($proof->status),
            'signed_at' => $proof->signedAt,
            'certificate_url' => $certificateUrl,
            'description' => $proof->description,
            'metadata' => $this->encodeJson($proof->metadata),
            'history' => $this->encodeJson($history),
        ];
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
