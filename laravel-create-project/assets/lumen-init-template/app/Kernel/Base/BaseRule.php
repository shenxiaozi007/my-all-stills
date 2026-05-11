<?php

namespace App\Kernel\Base;

abstract class BaseRule
{
    protected string $message = 'validation failed';

    public function message(): string
    {
        return $this->message;
    }
}
