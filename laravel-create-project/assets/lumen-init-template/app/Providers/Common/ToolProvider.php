<?php

namespace App\Providers\Common;

use Illuminate\Support\ServiceProvider;

class ToolProvider extends ServiceProvider
{
    protected bool $defer = true;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register common tool singletons here, such as snowflake id generator.
    }

    public function provides(): array
    {
        return [];
    }
}
