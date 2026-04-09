<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('filament-proovit::filament-proovit.overview.heading') }}
            </x-slot>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.overview.cards.company_name') }}</div>
                    <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">
                        {{ $dashboard->context['company_name'] ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.overview.cards.login_email') }}</div>
                    <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">
                        {{ $dashboard->context['login_email'] ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.overview.cards.base_url') }}</div>
                    <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">
                        {{ $dashboard->context['base_url'] ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                    <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.overview.cards.mode') }}</div>
                    <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">
                        {{ $dashboard->context['mode'] ?? __('filament-proovit::filament-proovit.widgets.connection.unknown') }}
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
