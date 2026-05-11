<?php

namespace App\Kernel\Base;

use Illuminate\Console\Command;
use Throwable;

abstract class BaseCommand extends Command
{
    protected function runSafely(callable $callback): int
    {
        try {
            $callback();

            return self::SUCCESS;
        } catch (Throwable $throwable) {
            $this->error($throwable->getMessage());

            report($throwable);

            return self::FAILURE;
        }
    }
}
