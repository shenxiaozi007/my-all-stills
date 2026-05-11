<?php

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'management'),
    ],
    'guards' => [
        'management' => [
            'driver' => 'management',
        ],
    ],
];
