<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected array $routes = [
        'management_proxy' => [
            'domain' => 'domain.management_proxy',
            'prefix' => 'management/proxy',
            'namespace' => 'App\Http\Controllers\Management\Proxy',
            'middleware' => ['cors', 'trim', 'rid', 'request.log'],
            'files' => ['routes/management/proxy'],
        ],
        'service_v1' => [
            'domain' => 'domain.service',
            'prefix' => 'service/v1',
            'namespace' => 'App\Http\Controllers\Service\V1',
            'middleware' => ['cors', 'signed'],
            'files' => ['routes/service/v1'],
        ],
        'server_v1' => [
            'domain' => 'domain.server',
            'prefix' => 'server/api/v1',
            'namespace' => 'App\Http\Controllers\Server\V1',
            'middleware' => ['cors', 'api_signed'],
            'files' => ['routes/server/v1'],
        ],
        'common' => [
            'domain' => '*',
            'prefix' => 'common',
            'namespace' => 'App\Http\Controllers\Common',
            'middleware' => ['cors'],
            'files' => ['routes/common'],
        ],
    ];

    public function boot(): void
    {
        foreach ($this->routes as $route) {
            $this->loadRouteGroup($route);
        }
    }

    private function loadRouteGroup(array $route): void
    {
        $domain = app('request')->server('HTTP_HOST', '');

        if ($route['domain'] !== '*' && $domain && $domain !== config($route['domain'])) {
            return;
        }

        foreach ($route['files'] as $path) {
            foreach (glob(base_path($path . '/*.php')) ?: [] as $file) {
                $this->app->router->group(
                    array_only($route, ['namespace', 'prefix', 'middleware']),
                    fn () => require $file
                );
            }
        }
    }
}
