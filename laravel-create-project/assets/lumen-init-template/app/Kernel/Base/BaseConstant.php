<?php

namespace App\Kernel\Base;

abstract class BaseConstant
{
    public static function all(): array
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }
}
