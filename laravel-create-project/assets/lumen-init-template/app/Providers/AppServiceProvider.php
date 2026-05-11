<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Validator::includeUnvalidatedArrayKeys();

        $this->registerRequestMacros();
    }

    private function registerRequestMacros(): void
    {
        Request::macro('getOrFail', function (string $key, string $errorMessage) {
            $value = $this->get($key);

            abort_if(blank($value), 422, $errorMessage);

            return $value;
        });

        Request::macro('headerOrFail', function (string $key, string $errorMessage) {
            $value = $this->header($key);

            abort_if(blank($value), 422, $errorMessage);

            return $value;
        });
    }
}
