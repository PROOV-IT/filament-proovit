<x-filament::section>
    <x-slot name="heading">
        {{ __('filament-proovit::filament-proovit.widgets.connection.heading') }}
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.label') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $label }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.company_name') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $companyName ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.login_email') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $loginEmail ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.base_url') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $baseUrl ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.app_url') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $appUrl ?? __('filament-proovit::filament-proovit.widgets.connection.not_configured') }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.mode') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $mode ?? __('filament-proovit::filament-proovit.widgets.connection.unknown') }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.connected') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $connected ? __('filament-proovit::filament-proovit.widgets.connection.yes') : __('filament-proovit::filament-proovit.widgets.connection.no') }}</div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
            <div class="text-xs uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.connection.widgets_enabled') }}</div>
            <div class="mt-1 text-sm font-medium text-gray-950 dark:text-white">{{ $enabled ? __('filament-proovit::filament-proovit.widgets.connection.yes') : __('filament-proovit::filament-proovit.widgets.connection.no') }}</div>
        </div>
    </div>

    @if (! empty($features))
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($features as $feature => $enabled)
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $enabled ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' }}">
                    {{ $feature }}: {{ $enabled ? __('filament-proovit::filament-proovit.widgets.connection.enabled') : __('filament-proovit::filament-proovit.widgets.connection.disabled') }}
                </span>
            @endforeach
        </div>
    @endif
</x-filament::section>
