<x-filament::section>
    <x-slot name="heading">
        {{ __('filament-proovit::filament-proovit.widgets.recent_proofs.heading') }}
    </x-slot>

    @if (empty($proofs))
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('filament-proovit::filament-proovit.widgets.recent_proofs.empty') }}
        </p>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.recent_proofs.name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.recent_proofs.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('filament-proovit::filament-proovit.widgets.recent_proofs.description') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-950">
                    @foreach ($proofs as $proof)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-950 dark:text-white">
                                {{ $proof['name'] }}
                                <div class="text-xs text-gray-500">{{ $proof['id'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $proof['status'] }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ $proof['description'] ?: __('filament-proovit::filament-proovit.widgets.recent_proofs.no_description') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament::section>
