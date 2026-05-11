<?php

namespace App\Kernel\Base;

abstract class BaseInvoke
{
    abstract public function __invoke(...$arguments);
}
