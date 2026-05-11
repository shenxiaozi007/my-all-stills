<?php

return [
    'name' => env('APP_NAME', 'service'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'timezone' => env('APP_TIMEZONE', 'Asia/Shanghai'),
    'locale' => env('APP_LOCALE', 'zh_CN'),
    'key' => env('APP_KEY'),
];
