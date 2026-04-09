<?php

declare(strict_types=1);

namespace Proovit\FilamentProovit\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Proovit\FilamentProovit\Support\Filament\ProovitDashboardData;
use Proovit\LaravelProovit\ProovitClient;
use Throwable;

final class ProovitOverview extends Page
{
    public function getView(): string
    {
        return 'filament-proovit::pages.proovit-overview';
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?string $title = null;

    protected static ?string $navigationLabel = null;

    public function getTitle(): string
    {
        return __('filament-proovit::filament-proovit.overview.heading');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-proovit::filament-proovit.navigation.overview');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-proovit::filament-proovit.navigation.label');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label(__('filament-proovit::filament-proovit.overview.actions.test_connection'))
                ->icon('heroicon-o-bolt')
                ->action(function (): void {
                    try {
                        $connection = app(ProovitClient::class)->connection()->test();

                        Notification::make()
                            ->title(__('filament-proovit::filament-proovit.overview.notifications.connection_successful_title'))
                            ->body(sprintf(
                                '%s: %s · %s: %s',
                                __('filament-proovit::filament-proovit.overview.cards.mode'),
                                $connection->mode ?? 'unknown',
                                __('filament-proovit::filament-proovit.overview.cards.base_url'),
                                $connection->baseUrl ?? 'not configured'
                            ))
                            ->success()
                            ->send();
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->title(__('filament-proovit::filament-proovit.overview.notifications.connection_failed_title'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('refresh')
                ->label(__('filament-proovit::filament-proovit.overview.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn (): void => $this->dispatch('$refresh')),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'dashboard' => ProovitDashboardData::fromClient(app(ProovitClient::class)),
        ];
    }
}
