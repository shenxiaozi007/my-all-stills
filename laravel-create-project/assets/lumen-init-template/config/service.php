<?php

return [
    'name' => env('SERVICE_NAME', env('APP_NAME', 'service')),
    'http_timeout' => (int) env('SERVICE_HTTP_TIMEOUT', 10),
];
