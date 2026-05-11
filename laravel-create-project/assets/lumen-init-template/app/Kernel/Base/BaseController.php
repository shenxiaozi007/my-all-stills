<?php

namespace App\Kernel\Base;

use App\Kernel\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller;

class BaseController extends Controller
{
    use ApiResponseTrait;

    protected function revert($data = [], string $message = '', int $code = 0): JsonResponse
    {
        if (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        } elseif (!is_null($data) && !is_array($data)) {
            $data = (array) $data;
        }

        return $this->ok([
            'code' => $code,
            'message' => $message ?: 'success!',
            'data' => $data,
            'time' => time(),
            'module' => config('service.name'),
        ]);
    }
}
