<?php

namespace App\Kernel\Base;

use Illuminate\Support\Facades\Validator;

abstract class BaseValidator
{
    protected array $rules = [];

    protected array $messages = [];

    protected array $attributes = [];

    public function validate(array $data): array
    {
        return Validator::make($data, $this->rules, $this->messages, $this->attributes)
            ->validate();
    }
}
