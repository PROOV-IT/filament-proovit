<?php

return [
    'navigation' => [
        'enabled' => env('PROOVIT_FILAMENT_NAVIGATION_ENABLED', true),
        'label' => env('PROOVIT_FILAMENT_LABEL', 'ProovIT'),
        'group' => env('PROOVIT_FILAMENT_GROUP', 'ProovIT'),
        'icon' => env('PROOVIT_FILAMENT_ICON', 'heroicon-o-shield-check'),
        'sort' => (int) env('PROOVIT_FILAMENT_SORT', 50),
    ],

    'widgets' => [
        'enabled' => env('PROOVIT_FILAMENT_WIDGETS_ENABLED', true),
        'refresh_interval' => (int) env('PROOVIT_FILAMENT_WIDGET_REFRESH', 60),
    ],

    'docs' => [
        'enabled' => env('PROOVIT_FILAMENT_DOCS_ENABLED', false),
        'path' => env('PROOVIT_FILAMENT_DOCS_PATH', 'docs/filament/proovit'),
    ],
];
