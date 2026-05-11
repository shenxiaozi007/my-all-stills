<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LibrariesProvider extends ServiceProvider
{
    protected bool $defer = true;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register third-party library singletons here.
    }

    public function provides(): array
    {
        return [];
    }
}
