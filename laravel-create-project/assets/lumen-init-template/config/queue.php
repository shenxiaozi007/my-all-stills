<?php

return [
    'default' => env('QUEUE_CONNECTION', 'sync'),
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => env('QUEUE_REDIS_CONNECTION', 'default'),
            'queue' => env('QUEUE_NAME', 'default'),
            'retry_after' => (int) env('QUEUE_RETRY_AFTER', 90),
        ],
    ],
];
