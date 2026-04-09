<x-filament::section>
    <x-slot name="heading">
        ProovIT Connection
    </x-slot>

    <div class="space-y-2 text-sm">
        <div><strong>Label:</strong> {{ $label }}</div>
        <div><strong>Base URL:</strong> {{ $baseUrl ?? 'not configured' }}</div>
        <div><strong>Widgets enabled:</strong> {{ $enabled ? 'yes' : 'no' }}</div>
    </div>
</x-filament::section>
