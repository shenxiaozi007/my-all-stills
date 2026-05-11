<?php

return [
    'default' => env('LOG_CHANNEL', 'daily'),
    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/lumen.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => (int) env('LOG_DAYS', 14),
        ],
    ],
];
