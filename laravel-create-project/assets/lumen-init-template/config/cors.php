<?php

return [
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),
    'allowed_methods' => explode(',', env('CORS_ALLOWED_METHODS', 'GET,POST,PUT,PATCH,DELETE,OPTIONS')),
    'allowed_headers' => explode(',', env('CORS_ALLOWED_HEADERS', '*')),
];
