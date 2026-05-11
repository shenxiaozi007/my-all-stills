<?php

namespace App\Kernel\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function ok(array $response): JsonResponse
    {
        return response()->json($response);
    }

    protected function fail(int $code, string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'time' => time(),
            'module' => config('service.name'),
        ]);
    }
}
