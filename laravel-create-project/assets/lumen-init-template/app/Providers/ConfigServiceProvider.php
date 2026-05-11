<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $files = Finder::create()
            ->files()
            ->name('*.php')
            ->in($this->app->basePath('config'));

        foreach ($files as $file) {
            $name = basename($file->getFilename(), '.php');
            $prefix = $file->getRelativePath();

            if ($prefix) {
                $name = strtr($prefix, '/', '.') . '.' . $name;
            }

            config([$name => require $file->getRealPath()]);
        }
    }
}
