<?php

namespace App\Kernel\Base;

use Illuminate\Support\Facades\Http;

abstract class BaseApi
{
    protected string $baseUrl = '';

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout(config('service.http_timeout', 10));
    }
}
