<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

$app = new Laravel\Lumen\Application(dirname(__DIR__));

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->configure('app');
$app->configure('schedule');

$app->middleware([
    App\Http\Middleware\CorsControl::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'cors' => App\Http\Middleware\CorsControl::class,
    'trim' => App\Http\Middleware\Trim\TrimStrings::class,
    'rid' => App\Http\Middleware\RequestId::class,
    'request.log' => App\Http\Middleware\RequestLog::class,
    'signed' => App\Http\Middleware\ValidateSignature::class,
    'api_signed' => App\Http\Middleware\ValidateApiSignature::class,
    'api_mutex' => App\Http\Middleware\ApiMutex::class,
    'api.cache' => App\Http\Middleware\ApiCache::class,
]);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\ConfigServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\RouteServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\LibrariesProvider::class);
$app->register(App\Providers\Common\ToolProvider::class);

$app->withFacades();
$app->withEloquent();

return $app;
